<?php

namespace App\Controller;

use App\Core\Database\CollectData;
use App\Core\Database\RainfallSchema;
use Opis\Database\Database;
use Opis\Database\Connection;

class DatabaseController implements RainfallSchema, CollectData
{
    public function __construct($pdo){
        $connection = Connection::fromPDO($pdo);
        $db = new Database($connection);
//        echo "成功？";
//        $result = $db->from('users')->select()->all();
//        print_r($result);
    }

    public function createRainfallsTable(){
//        $sql = "CREATE DATABASE rainfalls ";
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