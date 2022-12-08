<?php
  require './vendor/autoload.php';
  use Opis\Database\Connection;
  use Opis\Database\Database;

  $dotenv =  Dotenv\Dotenv::createImmutable(__DIR__); 
  $dotenv->load();
  // $demo = $_ENV['DEMO'];

  $dsn = 'mysql:host='.$_ENV['DB_HOST'].';dbname='.$_ENV['DB_DATABASE'];
  $user = $_ENV['DB_USERNAME'];
  $password = $_ENV['DB_PASSWORD'];

  try {
    $connection = new Connection($dsn, $user, $password);
    $connection->getPDO(); // 這裡才會真的透過 PDO 連線
    $db = new Database($connection);
    echo "Connected successfully!";
  } catch (Exception $exception) {
      echo $exception->getMessage();  
  }
  
