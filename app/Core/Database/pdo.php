<?php
namespace App\Core\Database;

use Opis\Database\Connection;
use Opis\Database\Database;
use PDO;
use PDOException;



class DB extends SingletonDB
{
    private $pdo;
    private $database;

    public function __construct()
    {      
        try {
            $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_DATABASE'] ;
            $connection = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
            var_dump(3);
            // $pdo = new Database($connection);
            var_dump(4);   
        } catch (PDOException $e) {
            echo "連線失敗：". $e->getMessage();
        }
        $connection = Connection::fromPDO($connection);
        var_dump(5);
    }

    

    public function pdo(): PDO
    { 
        return $this -> pdo ;
        var_dump(6);
    }
    
}
$pdo = new DB();
