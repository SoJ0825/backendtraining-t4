<?php

namespace App\Controller;

use App\Core\Database\CollectData;
use App\Core\Database\RainfallSchema;
// 使用 Opis ORM
// 使用 PDO
// index.php line 12 用 $pdo, 使用 DB
// 使用 例外處理


class DatabaseController implements RainfallSchema, CollectData
{
// Property 
// index.php line 12 用 $pdo 作為連線的物件
// 無須使用 $conn, $dsn, $user, $pwd
    private $pdo, $db;
// table name
    private $rainfallsTableName;
    private $districtsTableName;
// Methods
// RainfallSchema
    public function __construct($pdo){
        // 建立連線前置作業
        // 建立 $pdo 物件
        // 建立 $db 物件
    }

    public function createRainfallsTable(){
    }

    public function createDistrictsTable(){
    }

    public function importData(){
    }
// CollectData
    public function showDistricts(): array{
    }

    public function sumByYear($district = null): array{
    }

    public function sumByMonth($district = null): array{
    }
}