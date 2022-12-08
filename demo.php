<?php
  require './vendor/autoload.php';
  use Opis\Database\Connection;
  // use Opis\Database\Database;

// $dotenv =  Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
  $dotenv =  Dotenv\Dotenv::createImmutable(__DIR__); 
  $dotenv->load();
  $demo = $_ENV['DEMO'];

  echo "hi ". $demo ." bro.".PHP_EOL;
  putenv('DEMO=666666');
  echo "hi ".getenv('DEMO')." bro.".PHP_EOL;
  echo "hi ". $demo ." bro.".PHP_EOL;

  $dsn = 'mysql:host=localhost;dbname=blog';
  $user = 'luna';
  $password = '0000';

  try{
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
  }catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
  
  // try{
  //   $conn = new Connection($dsn, $user, $password);
  //   $conn->option(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
  //   $conn->option(PDO::ATTR_STRINGIFY_FETCHES, false);
  //   echo "Connected successfully";
  // }catch(PDOException $e){
  //   echo "Connection failed: " . $e->getMessage();
  // }
  
