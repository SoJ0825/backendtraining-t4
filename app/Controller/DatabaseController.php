<?php

namespace App\Controller;

use App\Core\Database\CollectData;
use App\Core\Database\RainfallSchema;
use Opis\Database\Database;
use Opis\Database\Connection;
use Opis\Database\Schema\CreateTable;

class DatabaseController implements RainfallSchema, CollectData
{
    /*
     * const BASE_DISTRICTS = [
        '南區', '北區', '安平區', '左鎮區', '仁德區', '關廟區', '官田區', '麻豆區', '佳里區', '西港區', '七股區', '將軍區', '學甲區',
        '北門區', '新營區', '後壁區', '白河區', '東山區', '下營區', '柳營區', '鹽水區', '山上區', '安定區',
    ];
     */
    private $connection;
    private $db;
    private $schema;
    public function __construct($pdo){
        //TODO: Establish connection from pdo
        $this->connection = Connection::fromPDO($pdo);
        echo "Connect Successfully!".PHP_EOL;
        $this->db = new Database($this->connection);
        echo "Database established!".PHP_EOL;
        $this->schema = $this->db->schema();
        echo "schema!".PHP_EOL;
        //TODO: Invoke creating table
        $this->createDistrictsTable();
        $this->createRainfallsTable();
    }

    public function createRainfallsTable(){
        $this->db->schema()->create('users',function (CreateTable $table){
            $table->boolean('is a man');
            $table->integer('age');
            $table->timestamp('created_at');
        });
        echo "Create Rainfallstable Successfully!".PHP_EOL;
    }

    public function createDistrictsTable(){

    }

    public function importData(){
        echo PHP_EOL;
        echo "Starting import...";
        echo PHP_EOL;
        $test = fopen("rainfallData/C0X050_東山.json","r") or die("Errorrrrrr");
        echo fread($test,filesize("rainfallData/C0X050_東山.json"));
        fclose($test);
    }

    public function showDistricts(): array{

    }

    public function sumByYear($district = null): array{

    }

    public function sumByMonth($district = null): array{

    }




}