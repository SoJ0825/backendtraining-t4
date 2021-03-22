<?php

namespace App\Controller;

use App\Core\Database\CollectData;
use App\Core\Database\RainfallSchema;

class DatabaseController implements RainfallSchema, CollectData
{
    private $pdo;
    private $districtTableName = 'districts';
    private $rainfallTableName = 'rainfalls';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function createRainfallsTable()
    {
        try {
            $sql = "SHOW TABLES LIKE'" . $this->rainfallTableName . "'";
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            $result = $statement->fetchAll();
            if (count($result) == 1) {
                echo "============================" . PHP_EOL .
                    "Table $this->rainfallTableName exists." . PHP_EOL;
            } else {
                $sql = "DROP TABLE IF EXISTS rainfalls;
                        CREATE TABLE rainfalls (
                            id int(11) NOT NULL,
                          year year(4) NOT NULL,
                          month varchar(45) NOT NULL,
                          day varchar(45) NOT NULL,
                          time time NOT NULL,
                          rainfall float NOT NULL,
                          districts_id int(11) NOT NULL,
                          PRIMARY KEY (id,districts_id),
                          KEY fk_rainfall_districts_idx (districts_id),
                          CONSTRAINT fk_rainfall_districts FOREIGN KEY (districts_id) REFERENCES districts (id) ON DELETE NO ACTION ON UPDATE NO ACTION
                        )";
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
            $sql = "SHOW TABLES LIKE'" . $this->districtTableName . "'";
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            $result = $statement->fetchAll();
            if (count($result) == 1) {
                echo "============================" . PHP_EOL .
                    "Table $this->districtTableName exists." . PHP_EOL;
            } else {
                $sql = "DROP TABLE IF EXISTS districts;
                        CREATE TABLE districts (
                            id int(11) NOT NULL,
                          name varchar(45) NOT NULL,
                          PRIMARY KEY (id),
                          UNIQUE KEY name_UNIQUE (name)
                        )";
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
    }

    public function showDistricts(): array
    {
    }

    public function sumByYear($district = null): array
    {
    }

    public function sumByMonth($district = null): array
    {
    }

}