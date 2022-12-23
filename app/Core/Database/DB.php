<?php

namespace App\Core\Database;

use Exception;
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
        $dsn = 'mysql:host='.$_ENV['DB_HOST'].';dbname='.$_ENV['DB_DATABASE'];
        $user = $_ENV['DB_USERNAME'];
        $pwd =$_ENV['DB_PASSWORD'];

        $this->conn = new Connection($dsn, $user, $pwd);
        $this->db = new Database($this->conn);
        // return 交給 pdo(): PDO 處理
    }

        public function pdo(): PDO{
        // 回傳 PDO 物件 $conn->getPDO()
        try{    
          return $this->conn->getPDO();
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    public function database(){
        // 回傳 $db
        return $this->db;
    }
}