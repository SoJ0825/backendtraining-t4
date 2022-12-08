<?php

namespace App\Core\Database;
use Opis\Database\Connection;
use Opis\Database\Database;
use PDO;

class DB extends SingletonDB
{
    private $conn, $db;

    public function __construct()
    {
        // 建立連線前置作業
        // $conn, $db, $dsn, $user, $pwd
        // return 交給 pdo(): PDO 處理
    }

    public function pdo(): PDO{
        // 回傳 PDO 物件 $conn->getPDO()
        return;
    }

    public function database(){
        // 回傳 $db
    }
}