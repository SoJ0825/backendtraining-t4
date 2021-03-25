<?php

namespace App\Core\Database;

use PDO;

class DB extends SingletonDB
{
    private $charset = "utf8";
    private $connection;

    function __construct()
    {
        $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_DATABASE'] . ';charset=' . $this->charset;
        $this->connection = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
    }

    function pdo(): PDO
    {
        return $this->connection;
    }

}