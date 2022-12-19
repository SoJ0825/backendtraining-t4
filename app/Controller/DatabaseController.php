<?php

namespace App\Controller;

use App\Core\Database\CollectData;
use App\Core\Database\RainfallSchema;
// 使用 Opis ORM
use Opis\Database\Connection;
use Opis\Database\Database;
use Opis\Database\Schema\CreateTable;
// 使用 PDO
use PDO;
// index.php line 12 用 $pdo, 使用 DB
use App\Core\Database\DB;
// 使用 例外處理
use Exception;


class DatabaseController implements RainfallSchema, CollectData
{
// Property 
// index.php line 12 用 $pdo 作為連線的物件
// 無須使用 $conn, $dsn, $user, $pwd
    private $pdo, $db, $schema;
// table name
    private $rainfallsTableName = 'rainfall';
    private $districtsTableName = 'districts';
// Methods
// RainfallSchema
    public function __construct($pdo){
        // 建立連線作業
        try{
            $this->pdo = $pdo;
            $this->db = DB::init()->database();
            $this->schema = $this->db->schema();
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    public function createRainfallsTable(){
        $tables = $this->rainfallsTableName;
        $this->schema->drop($tables);
        $this->schema->create($tables, function(CreateTable $table){
        $table->integer('id')->primary()->autoincrement();
        $table->string('name', 8);
        $table->dateTime('datetime');
        $table->float('rainfall');
        });
       echo "Catch  Create Rain table" .PHP_EOL;
    }

    public function createDistrictsTable(){
        $tables = $this->districtsTableName;
        $this->schema->drop($tables);
        $this->schema->create($tables, function(CreateTable $table){
            $table->integer('id')->primary()->autoincrement();
            $table->string('name', 8);
        });
       echo "Catch  Create Districts table" .PHP_EOL;
    }

    public function importData()
    {
       $this->createRainfallsTable();
       $this->createDistrictsTable();

       $tableList = $this->schema->getTables();
       $totalTables = count($tableList);

       // Check databases have tables?
       if ($totalTables !== 2) {
           // Databases have no tables
           echo "Databases have tables" . PHP_EOL;
           // Create rainfallTable and districtTable, then import data
       } else {
           // Databases have rainfallTable and districtTable
           echo "Databases have tables" . PHP_EOL;
           // Clear two table's row data,  then import data
       }
    }
// CollectData
    public function showDistricts(): array{
        $result = ['Creating...'];
        return $result;
    }

    public function sumByYear($district = null): array{
        $result = ['Creating...'];
        return $result;
    }

    public function sumByMonth($district = null): array{
        $result = ['Creating...'];
        return $result;
    }
}