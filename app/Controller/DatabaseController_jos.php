<?php

namespace App\Controller;

use App\Core\Database\CollectData;
use App\Core\Database\DB;
use App\Core\Database\RainfallSchema;
use Exception;
use Opis\Database\Database;
use Opis\Database\Schema\CreateTable;
use PDO;

class DatabaseController implements RainfallSchema, CollectData
{
    /** @var Database $database */
    private $database;
    /** @var PDO $pdo */
    private $pdo;
    private $districtTableName = 'districts';
    private $rainfallTableName = 'rainfalls';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->database = DB::init()->database();
    }

    public function createRainfallsTable()
    {
        try {
            $schema = $this->database->schema();
            if ($schema->hasTable($this->rainfallTableName)) {
                echo "============================".PHP_EOL.
                    "Table $this->rainfallTableName exists.".PHP_EOL;
            } else {
                $schema->create($this->rainfallTableName, function (CreateTable $table) {
                    $table->integer('id')->unsigned()->autoincrement();
                    $table->string('district_id', 5)->notNull();
                    $table->dateTime('date')->notNull();
                    $table->float('rainfall')->notNull();
                });
                echo "============================".PHP_EOL.
                    "Create table $this->rainfallTableName successfully!".PHP_EOL;
            }
        } catch
        (Exception $e) {
            echo "============================".PHP_EOL.
                "Create table $this->rainfallTableName failed: ".PHP_EOL;
            $e->getMessage();
        }
    }

    public function createDistrictsTable()
    {
        try {
            $schema = $this->database->schema();
            if ($schema->hasTable($this->districtTableName)) {
                echo "============================".PHP_EOL.
                    "Table $this->districtTableName exists.".PHP_EOL;
            } else {
                $schema->create($this->districtTableName, function (CreateTable $table) {
                    $table->integer('id')->unsigned()->autoincrement();
                    $table->string('name', 5)->notNull();
                });
                echo "============================".PHP_EOL.
                    "Create table $this->districtTableName successfully!".PHP_EOL;
            }
        } catch
        (Exception $e) {
            echo "============================".PHP_EOL.
                "Create table $this->districtTableName : ".PHP_EOL;
            $e->getMessage();
        }
    }

    public function importData()
    {
        $schema = $this->database->schema();
        try {
            if ($schema->hasTable($this->districtTableName)) {
                $schema->truncate($this->districtTableName);
            } else {
                $this->createDistrictsTable();
            }
            if ($schema->hasTable($this->rainfallTableName)) {
                $schema->truncate($this->rainfallTableName);
            } else {
                $this->createRainfallsTable();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        foreach (glob($_ENV['FILE_PATH']) as $filePath) {
            list(, $district) = explode('_', basename($filePath, ".json"));
            if ($this->startWith($district, '臺南市')) {
                list(, $district) = explode('臺南市', $district);
            }
            if (!$this->endsWith($district, '區')) {
                $district .= "區";
            }
            $strJsonFileContents = file_get_contents($filePath);
            $data = json_decode($strJsonFileContents, true);
//     $data look like ['2018-10-31 17:00:00' => 0.5, ...];
            $rainfallData = array_map(function ($date, $rainfall) {
                return array('date' => $date, 'rainfall' => $rainfall);
            }, array_keys($data), $data);
            $rainfallDataCount = count($rainfallData);
            $done = 1;
            $this->database
                ->insert([
                    'name' => $district,
                ])
                ->into($this->districtTableName);

            $districtData = $this->database
                ->from($this->districtTableName)
                ->where('name')
                ->is($district)
                ->select('id')
                ->all();

            echo "匯入 $district :".PHP_EOL;
            foreach ($rainfallData as $rainfall) {
                $this->database->insert($rainfall + ['district_id' => $districtData[0]->id])->into($this->rainfallTableName);
                $this->showStatus($done, $rainfallDataCount);
                $done += 1;
            }
        }
    }

    public function showDistricts(): array
    {
        $query = "SELECT `name` FROM $this->districtTableName";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        $result = [];
        foreach ($statement->fetchAll() as $districtsObject) {
            $result[] = $districtsObject->name;
        }
        $baseDistricts = self::BASE_DISTRICTS;
        usort($result, function ($district1, $district2) use ($baseDistricts) {
            return array_search($district1, $baseDistricts) > array_search($district2, $baseDistricts);
        });
        return $result;
    }

    public function sumByYear($district = null): array
    {
        if ($district) {
            $query = "SELECT id FROM districts WHERE name = ?";
            $statement = $this->pdo->prepare($query);
            $statement->execute([$district]);
            $executeParameter = [$statement->fetch()->id];
        } else {
            $executeParameter = null;
        }

        $query = "SELECT
                	districts.name,
                	year(rainfalls.date) AS year,
                	SUM(rainfalls.rainfall) AS total_rainfall
                FROM
                	rainfalls
                	JOIN districts ON rainfalls.district_id = districts.id
                WHERE
                	rainfalls.district_id = ?
                GROUP BY
                	rainfalls.district_id, year
                ORDER BY
                	year";
        if (is_null($district)) {
            $query = str_replace('?', 'districts.id', $query);
        }
        $statement = $this->pdo->prepare($query);
        $statement->execute($executeParameter);
        return $statement->fetchAll();
    }

    public function sumByMonth($district = null): array
    {
        if ($district) {
            $query = "SELECT id FROM districts WHERE name = ?";
            $statement = $this->pdo->prepare($query);
            $statement->execute([$district]);
            $executeParameter = [$statement->fetch()->id];
        } else {
            $executeParameter = null;
        }

        $query = "SELECT
                	districts.name,
                	year(rainfalls.date) AS year,
                	month(rainfalls.date) AS month,
                	SUM(rainfalls.rainfall) AS total_rainfall
                FROM
                	rainfalls
                	JOIN districts ON rainfalls.district_id = districts.id
                WHERE
                	rainfalls.district_id = ?
                GROUP BY
                	rainfalls.district_id, year, month";
        if (is_null($district)) {
            $query = str_replace('?', 'districts.id', $query);
        }
        $statement = $this->pdo->prepare($query);
        $statement->execute($executeParameter);
        return $statement->fetchAll();
    }

    private function startWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, 0, $length) === $needle);
    }

    private function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    private function showStatus($done, $total, $size = 30)
    {

        static $start_time;

        // if we go over our bound, just ignore it
        if ($done > $total) return;

        if (empty($start_time)) $start_time = time();
        $now = time();

        $perc = (double) ($done / $total);

        $bar = floor($perc * $size);

        $status_bar = "\r[";
        $status_bar .= str_repeat("=", $bar);
        if ($bar < $size) {
            $status_bar .= ">";
            $status_bar .= str_repeat(" ", $size - $bar);
        } else {
            $status_bar .= "=";
        }

        $disp = number_format($perc * 100, 0);

        $status_bar .= "] $disp%  $done/$total";

        $rate = ($now - $start_time) / $done;
        $left = $total - $done;
        $eta = round($rate * $left, 2);

        $elapsed = $now - $start_time;

        $status_bar .= " remaining: ".number_format($eta)." sec.  elapsed: ".number_format($elapsed)." sec.";

        echo "$status_bar  ";

        flush();

        // when done, send a newline
        if ($done == $total) {
            echo "\n";
        }
    }
}