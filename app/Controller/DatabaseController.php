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
        // 建立連線前置作業
        // connection by using the fromPDO static method 建立 $pdo 物件
        $this->pdo = Connection::fromPDO($pdo);
        // 建立 $db 物件
        $this->db = new Database($this->pdo);
        $this->schema = $this->db->schema();
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

    public function importData(){
      $this->createRainfallsTable(); 
      $this->createDistrictsTable();

    //   $tables = $this->schema->getTables(); 
    $tables = $this->db->schema->getTables();
      foreach ($tables as $table){
        echo $table.PHP_EOL;
      }
     // Check databases have any tables?   
       // Databases have no tables
         // Create rainfallTable and districtTable, then import data
       // Databases have tables
         // 1.
         // have rainfallTable and districtTable, clear two table's row data, then import data 
         // 2.
         // have rainfallTable, clear table's all row data, then import data 
         // Create districtTable, then import data
         // 3.
         // have districtTable, clear table's all row data, then import data
         // Create rainfallTable, then import data
         

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