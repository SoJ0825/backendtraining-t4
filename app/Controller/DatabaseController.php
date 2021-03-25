<?php

namespace App\Controller;

use App\Core\Database\CollectData;
use App\Core\Database\RainfallSchema;
use Exception;
use PDO;

class DatabaseController implements RainfallSchema, CollectData
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function createRainfallsTable()
    {
        try {
            $query = "CREATE TABLE IF NOT EXISTS rainfalls (
            id int NOT NULL AUTO_INCREMENT,
            districtsID int NOT NULL,
            date datetime NOT NULL,
            rainfall float NOT NULL,
            PRIMARY KEY (id))";
            $statement = $this->pdo->prepare($query);
            $statement->execute();

            echo "- Table [rainfalls] created.\n";

        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    public function createDistrictsTable()
    {
        try {
            $query = "CREATE TABLE IF NOT EXISTS districts (
            id int NOT NULL AUTO_INCREMENT,
            districtsName varchar(25) NOT NULL,
            PRIMARY KEY (id))";
            $statement = $this->pdo->prepare($query);
            $statement->execute();

            echo "- Table [districts] created.\n";

        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    public function importData()
    {
        $this->createRainfallsTable();
        $this->createDistrictsTable();
        $query = "TRUNCATE TABLE districts;
                  TRUNCATE TABLE rainfalls;";
        $statement = $this->pdo->prepare($query);
        $statement->execute();

        foreach (glob($_ENV['FILE_PATH']) as $filePath) {
            $fileName = explode('_', basename($filePath, ".json"));
            // fetch only district name
            $district = $fileName[1];

            // rename districtName
            if (str_starts_with($district, '臺南市')) {
                $district = str_replace('臺南市','', $district);
            }
            if (!str_ends_with($district, '區')) {
                $district .= "區";
            }

            // insert districtName into 'districts' table
            $query = "INSERT INTO districts (districtsName) VALUES (?)";
            $statement = $this->pdo->prepare($query);
            $statement->execute([$district]);

            // search districtsID in order to insert into 'rainfalls' table
            $districtsID = $this->getDistrictID($district);

            // process json file and insert into 'rainfalls' table
            $dataStr = file_get_contents($filePath);
            $dataArray = json_decode($dataStr, true);
            echo "- Importing " . $district . "\n";
            foreach ($dataArray as $datetime => $rainfall) {
                $query = "INSERT INTO rainfalls (districtsID, date, rainfall) VALUES (?, ?, ?)";
                $statement = $this->pdo->prepare($query);
                $statement->execute([$districtsID, $datetime, $rainfall]);
            }
        }
    }

    public function showDistricts(): array
    {
        $query = "SELECT districtsName FROM districts";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        $sortedDistricts = [];

        // unsorted districtsName
        foreach ($statement->fetchAll() as $districts) {
            $sortedDistricts[] = $districts['districtsName'];
        }

        // sorting
        usort($sortedDistricts, function ($a, $b) {
            $baseDistricts = self::BASE_DISTRICTS;
            // 如果 a > b 的話會return 1, 就會往後排
            return array_search($a, $baseDistricts) > array_search($b, $baseDistricts);
        });
        return $sortedDistricts;
    }

    public function sumByYear($district = null): array
    {
        $sumOfYear = "SELECT
                        D.districtsName,
                        YEAR(R.date) AS year,
                        SUM(R.rainfall) AS totalRainfall
                    FROM
                        rainfalls R
                    JOIN districts D ON
                        D.id = R.districtsID
                    WHERE
                        R.districtsID = ?
                    GROUP BY
                        R.districtsID, year
                    ORDER BY
                        R.districtsID, year";


        if ($district) {
            // for specific district
            $districtsID = $this->getDistrictID($district);
        } else {
            // for all district
            $districtsID = null;
            $sumOfYear = str_replace('R.districtsID = ?','1', $sumOfYear);
        }

        $statement = $this->pdo->prepare($sumOfYear);
        $statement->execute([$districtsID]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sumByMonth($district = null): array
    {
        $sumOfMonth = "SELECT
                        D.districtsName,
                        YEAR(R.date) AS year,
                        MONTH(R.date) AS month,
                        SUM(R.rainfall) AS totalRainfall
                    FROM
                        rainfalls R
                    JOIN districts D ON
                        D.id = R.districtsID
                    WHERE
                        R.districtsID = ?
                    GROUP BY
                        R.districtsID, year, month
                    ORDER BY
                        R.districtsID, year, month";

        if ($district) {
            // for specific district
            $districtsID = $this->getDistrictID($district);
        } else {
            // for all districts
            $districtsID = null;
            $sumOfMonth = str_replace('R.districtsID = ?','1', $sumOfMonth);
        }

        $statement = $this->pdo->prepare($sumOfMonth);
        $statement->execute([$districtsID]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // get districtID by districtsName
    private function getDistrictID($district): int
    {
        $query = "SELECT id FROM districts WHERE districtsName = ?";
        $statement = $this->pdo->prepare($query);
        $statement->execute([$district]);
        $row = $statement->fetch();
        return $row['id'];
    }

}
