<?php

namespace App\Core\Database;
use PDO;
use Dotenv\Dotenv;



class DB extends SingletonDB
{
    private $host;
    private $dbUser;
    private $dbPwd;
    private $dbname;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'];
        $this->dbUser = $_ENV['DB_USERNAME'];
        $this->dbPwd = $_ENV['DB_PASSWORD'];
        $this->dbname = $_ENV['DB_DATABASE'];
    }

    public function pdo(): PDO
    {
//        $this->host = $_ENV['DB_HOST'];
//        $this->dbUser = $_ENV['DB_USERNAME'];
//        $this->dbPwd = $_ENV['DB_PASSWORD'];
//        $this->dbname = $_ENV['DB_DATABASE'];
//        try {
//            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
//            $connection = new Connection($dsn, $this->dbUser, $this->dbPwd);
////            $connection->option(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
////            $connection->option(PDO::ATTR_STRINGIFY_FETCHES, false);
//            $connection->getPDO(); //這邊才是透過PDO連線
//            $pdo = new Database($connection);
//            echo "資料庫連線成功";
//        } catch (\PDOException $e) {
//            echo $e->getMessage();
//        }
//        var_dump($pdo);
//        return $pdo;


//        try {
//            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
//            $connection = new Connection($dsn, $this->dbUser, $this->dbPwd);
////            $connection = Connection::fromPDO($pdo);
//            $connection->getPDO(); //這邊才是透過PDO連線
//            $pdo = new Database($connection);//因為PDO是new Database的Object
//            echo "資料庫連線成功";
//        } catch (\PDOException $e) {
//            echo $e->getMessage();
//        }
//        return $pdo;
//        //最後應該要回傳PDO object才可以

        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $pdo = new PDO($dsn, $this->dbUser, $this->dbPwd);
        var_dump($pdo);//測試用
        return $pdo;
    }
}