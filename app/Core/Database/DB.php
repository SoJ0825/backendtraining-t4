<?php
namespace App\Core\Database;

use Opis\Database\Connection;
use Opis\Database\Database;
use PDO;
use PDOException;



class DB extends SingletonDB
{
    // private $pdo;

    public function __construct()
    {      
        
    }

    public function pdo(): PDO
    {
        try {
            $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_DATABASE'] ;
            $pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
            var_dump(2);
            // $connection->getPDO();
            $connection = Connection::fromPDO($pdo);
            $pdo = new Database($connection);
            var_dump(3); 
            $result = $pdo->from('test')->select()->all();  
            print_r($result); 
            var_dump(4);
        } catch (PDOException $e) {
            echo "連線失敗：". $e->getMessage();
        }
            var_dump($pdo);
            var_dump(5);
    }
    
}
var_dump(8);

