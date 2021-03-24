<?php

namespace App\Core\Database;

use Opis\Database\Connection;
use Opis\Database\Database;
use PDO;

class DB extends SingletonDB
{
    /** @var Connection $connection */
    private $connection;
    /** @var Database $database */
    private $database;

    public function __construct()
    {
        $dsn = "mysql:host=".$_ENV['DB_HOST'].";dbname=".$_ENV['DB_DATABASE'];
        $this->connection = new Connection($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
        $this->database = new Database($this->connection);
    }

    public function database()
    {
        return $this->database;
    }

    public function pdo(): PDO
    {
        return $this->connection->getPDO();
    }
}