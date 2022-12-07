<?php
  require './vendor/autoload.php';

// $dotenv =  Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
  $dotenv =  Dotenv\Dotenv::createImmutable(__DIR__); 
  $dotenv->load();
  $demo = $_ENV['DEMO'];

  echo "hi ". $demo ." bro.".PHP_EOL;
  putenv('DEMO=666666');
  echo "hi ".getenv('DEMO')." bro.".PHP_EOL;
  echo "hi ". $demo ." bro.".PHP_EOL;
  
