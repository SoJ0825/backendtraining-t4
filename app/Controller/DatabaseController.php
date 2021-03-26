<?php

namespace App\Controller;

use App\Core\Database\CollectData;
use App\Core\Database\RainfallSchema;
use PDO;

class DatabaseController implements RainfallSchema, CollectData
{
    private $pdo;
    private $districtTableName = 'districts';
    private $rainfallTableName = 'rainfalls';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    //
    public function hasTable($tableName)
    {
        $sql = "SHOW TABLES LIKE'" . $tableName . "'";
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;
    }

    public function truncateTable($tableName)
    {
        $sql = "TRUNCATE TABLE " . $tableName;
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
    }

    public function importDistrictsData($district)
    {
        $sql = "INSERT INTO districts(name) VALUES(:name)";
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':name', $district, PDO::PARAM_STR);
        $statement->execute();
    }

    public function importRainfallsData($year, $month, $day, $time, $rainfall, $districtId)
    {
        $sql = "INSERT INTO rainfalls(year,month,day,time,rainfall,districts_id) VALUES(:year,:month,:day,:time,:rainfall,:districts_id)";
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':year', $year, PDO::PARAM_STR);
        $statement->bindParam(':month', $month, PDO::PARAM_STR);
        $statement->bindParam(':day', $day, PDO::PARAM_STR);
        $statement->bindParam(':time', $time, PDO::PARAM_STR);
        $statement->bindParam(':rainfall', $rainfall, PDO::PARAM_STR);
        $statement->bindParam(':districts_id', $districtId, PDO::PARAM_STR);
        $statement->execute();
    }

    public function findDistrictId($district)
    {
        $sql = "SELECT id FROM districts WHERE name=:name";
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':name', $district, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch();
        return $result['id'];
    }

    public function selectDistricts()
    {
        $sql = "SELECT name FROM districts";
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $districts = $statement->fetchAll();
        foreach ($districts as $district) {
            $result[] = $district['name'];
        }
        return $result;
    }

    public function confirmDistrict($district, $sql)
    {
        if (is_null($district)) {
            $sql = str_replace('WHERE districts_id = :districts_id', '', $sql);
            $statement = $this->pdo->prepare($sql);
            return $statement;
        } else {
            $districtId = $this->findDistrictId($district);
            $statement = $this->pdo->prepare($sql);
            $statement->bindParam(':districts_id', $districtId, PDO::PARAM_STR);
            return $statement;
        }
    }

    //
    public function createRainfallsTable()
    {
        try {
            $result = $this->hasTable($this->rainfallTableName);
            if (count($result) == 1) {
                echo "============================" . PHP_EOL .
                    "Table $this->rainfallTableName exists." . PHP_EOL;
            } else {
                $sql = "CREATE TABLE IF NOT EXISTS rainfalls (
                            id int(11) NOT NULL AUTO_INCREMENT,
                          year year(4) NOT NULL,
                          month varchar(45) NOT NULL,
                          day varchar(45) NOT NULL,
                          time time NOT NULL,
                          rainfall float NOT NULL,
                          districts_id int(11) NOT NULL,
                          PRIMARY KEY (id)
                        )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                $this->pdo->exec($sql);
                echo "============================" . PHP_EOL .
                    "Create table $this->rainfallTableName successfully!" . PHP_EOL;
            }
        } catch
        (PDOException $e) {
            echo "============================" . PHP_EOL .
                "Create table $this->rainfallTableName failed: " . PHP_EOL;
            $e->getMessage();
        }
    }

    public function createDistrictsTable()
    {
        try {
            $result = $this->hasTable($this->districtTableName);
            if (count($result) == 1) {
                echo "============================" . PHP_EOL .
                    "Table $this->districtTableName exists." . PHP_EOL;
            } else {
                $sql = "CREATE TABLE IF NOT EXISTS districts (
                            id int(11) NOT NULL AUTO_INCREMENT,
                          name varchar(45) NOT NULL,
                          PRIMARY KEY (id)
                        )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                $this->pdo->exec($sql);
                echo "============================" . PHP_EOL .
                    "Create table $this->districtTableName successfully!" . PHP_EOL;
            }
        } catch
        (PDOException $e) {
            echo "============================" . PHP_EOL .
                "Create table $this->districtTableName failed: " . PHP_EOL;
            $e->getMessage();
        }
    }

    public function importData()
    {
        try {
            $districtTableResult = $this->hasTable($this->districtTableName);
            if (count($districtTableResult) == 1) {
                $this->truncateTable($this->districtTableName);
            } else {
                $this->createDistrictsTable();
            }
            $rainfallTableResult = $this->hasTable($this->rainfallTableName);
            if (count($rainfallTableResult) == 1) {
                $this->truncateTable($this->rainfallTableName);
            } else {
                $this->createRainfallsTable();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        //匯入地區
        foreach (glob($_ENV['FILE_PATH']) as $filePath) {
            list(, $district) = explode('_', basename($filePath, ".json"));
            if (str_starts_with($district, "臺南市")) {
                list(, $district) = explode("臺南市", $district);
            } else {
                $district .= "區";
            }
            $this->importDistrictsData($district);
            $districtId = $this->findDistrictId($district);

            $rainfallJson = file_get_contents($filePath);
            $rainfallData = json_decode($rainfallJson, true);
            $this->findDistrictId($district);
            foreach ($rainfallData as $dateTime => $rainfall) {
                $year = substr($dateTime, 0, 4);
                $month = substr($dateTime, 5, 2);
                $day = substr($dateTime, 8, 2);
                $time = substr($dateTime, -8, 8);
                $this->importRainfallsData($year, $month, $day, $time, $rainfall, $districtId);
            }
        }
    }

    public function showDistricts(): array
    {
        $result = $this->selectDistricts();
        $baseDistricts = self::BASE_DISTRICTS;
        usort($result, function ($districtA, $districtB) use ($baseDistricts) {
            return array_search($districtA, $baseDistricts) > array_search($districtB, $baseDistricts);
        });
        return $result;
    }

    public function sumByYear($district = null): array
    {
        $sql = "SELECT Dist.id, Dist.name, year, SUM(rainfall) as total_annual_rainfall
                FROM rainfalls  
                INNER JOIN districts Dist 
                ON districts_id = Dist.id
                WHERE districts_id = :districts_id 
                GROUP BY Dist.id, Dist.name, year
                ORDER BY Dist.id ASC";
        $statement = $this->confirmDistrict($district, $sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sumByMonth($district = null): array
    {
        $sql = "SELECT Dist.id, Dist.name, month, SUM(rainfall) as total_monthly_rainfall
                FROM  rainfalls
                INNER JOIN districts Dist 
                ON Dist.id = districts_id
                WHERE districts_id = :districts_id 
                GROUP BY Dist.id, Dist.name, month
                ORDER BY Dist.id ASC";
        $statement = $this->confirmDistrict($district, $sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

}