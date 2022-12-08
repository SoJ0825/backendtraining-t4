<?php

namespace App\Controller;

use App\Core\Database\CollectData;
use App\Core\Database\RainfallSchema;

class DatabaseController implements RainfallSchema, CollectData
{
    public function __construct($pdo){
        echo "function __construct要做什麼？？？"; //記得刪除


        $pdo->query('SET NAMES UTF8'); // 設定編碼
        $pdo->query('SET time_zone = "+8:00"'); // 設定台灣時間
        $pdo->createRainfallsTable();
    }

    public function createRainfallsTable(){
        $sql = "CREATE DATABASE rainfalls ";
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