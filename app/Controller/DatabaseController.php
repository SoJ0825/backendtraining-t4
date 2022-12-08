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
        $connection = Connection::fromPDO($pdo);
        $db = new Database($connection);
//        echo "成功？";
//        $result = $db->from('users')->select()->all();
//        print_r($result);
        return $db;
    }

    public function createRainfallsTable(){
        $db->schema()->create('test',function (CreateTable $table) {

        });
    }

    public function createDistrictsTable(){

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