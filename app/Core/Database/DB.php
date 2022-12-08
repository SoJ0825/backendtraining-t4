<?php

namespace App\Core\Database;
use PDO;

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
//        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
//        $pdo = new PDO($dsn, $this->dbUser, $this->dbPwd);
//        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//        return $pdo;

        try {
            $pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->dbUser, $this->dbPwd);
            // set the PDO error mode to exception
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected successfully 成功連接！";
        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        return $pdo;
    }