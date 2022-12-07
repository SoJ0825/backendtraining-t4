<?php

namespace App\Core\Database;

////------不確定以下這邊是否需要加上------
//require_once __DIR__.'/vendor/autoload.php';
//use Dotenv\Dotenv;
//$dotenv = Dotenv::createImmutable(__DIR__);
//$dotenv->load();

class DB extends SingletonDB
{

    private $host;
    private $dbUser;
    private $dbPwd;
    private $dbname;


    protected function connectDB(): PDO
    {
        $this->host = $_ENV['DB_HOST'];
        $this->dbUser = $_ENV['DB_USERNAME'];
        $this->dbPwd = $_ENV['DB_PASSWORD'];
        $this->dbname = $_ENV['DB_DATABASE'];
        var_dump($_ENV['DB_DATABASE']);

        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $pdo = new PDO($dsn, $this->dbUser, $this->dbPwd);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    }


}