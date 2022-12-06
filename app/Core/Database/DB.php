<?php

namespace App\Core\Database;
//use Opis\Database\Database;
//use Opis\Database\Connection;


use PDO;
//use Dotenv\Dotenv;

class DB extends SingletonDB
{
    private $servername  ;
    private $dbname ;
    //private $dsn;
    private $username;
    private  $password ;
    public function __construct()
    {
        $this->servername =$_ENV['DB_HOST'] ;
        $this->dbname = $_ENV['DB_DATABASE'];
        //$this->dsn = "mysql:host=$this->servername;dbname=$this->dbname";
        //echo $this->dsn;
        $this->username = $_ENV['DB_USERNAME'];
        $this->password = $_ENV['DB_PASSWORD'];
    }
    public function pdo(): PDO
    {
        // TODO: Implement pdo() method.
        //$dotenv = Dotenv::createImmutable('./');
        //$dotenv->load();
        //$dbName = getenv('')

//        echo PHP_EOL;
//        echo "--------".PHP_EOL;
//        echo getenv(DB_DATABASE);
//        echo "--------".PHP_EOL;
        //$dbName = $_ENV['DB_DATABASE'];
//        $servername =$_ENV['DB_HOST'] ;
//        $dbname = $_ENV['DB_DATABASE'];
//        $username = $_ENV['DB_USERNAME'];
//        $password = $_ENV['DB_PASSWORD'];
//        echo('$_ENV[] = '); print_r($_ENV);
//        echo('$_SERVER[] = '); print_r($_SERVER);
        $conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
        return $conn;
        //$connection = new Connection($this->dsn, $this->username, $this->password);
        //$connection->option(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
//        try {
//            //$conn = new PDO("mysql:host=$_ENV['DB_HOST'];dbname=$_ENV['DB_DATABASE']", $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
//            $conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
//            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//            echo "Connected successfully!";
//        }catch(PDOException $e) {
//            echo "Connection failed: " . $e->getMessage();
//        }
//        return $conn;
//        $connection = new Connection(
//            'mysql:host=$this->servername;dbname=$this->dbname',
//            '$this->username',
//            '$this->password'
//        );
//        $db = new Database($connection);
//
//        $schema = $db->schema();
//
//        return $schema;
    }
}