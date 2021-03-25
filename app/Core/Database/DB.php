<?php

namespace App\Core\Database;

use PDO;
use PDOException;

class DB extends SingletonDB
{
    private $pdo;
    public function __construct()
    {
        try {
            $dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'];
            $this->pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

}