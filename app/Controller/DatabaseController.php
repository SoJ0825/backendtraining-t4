<?php

namespace App\Controller;

use App\Core\Database\CollectData;
use App\Core\Database\RainfallSchema;
use Opis\Database\Database;
use Opis\Database\Connection;
use Opis\Database\Schema\CreateTable;

class DatabaseController implements RainfallSchema, CollectData
{
    protected $db;
    public function __construct($pdo){
        //DB連線
        $connection = Connection::fromPDO($pdo);
        $db = new Database($connection);
//        echo "成功？";
//        $result = $db->from('users')->select()->all();
//        print_r($result);
        $this->db = $db;
        $this->createRainfallsTable();
        $this->createDistrictsTable();
    }

    public function createRainfallsTable(){
        var_dump($this->db);
        //drop table
        $this->db->schema()->drop('RainfallsTable');
        //重新建立table
        $this->db->schema()->create('RainfallsTable',function (CreateTable $table) {
            $table->integer('index')->primary()->unsigned()->autoincrement();
            $table->string('區域', 30);
            $table->dateTime('日期');
            $table->decimal('雨量', 3, 1);
        });
    }

    public function createDistrictsTable(){
        //drop table
//        $this->db->schema()->drop('DistrictsTable');
        //重新建立table
        $this->db->schema()->create('DistrictsTable', function (CreateTable $table) {
            $table->integer('districtNum')->primary()->autoincrement();
            $table->string('區域名', 30);
        });
    }

    public function importData(){

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