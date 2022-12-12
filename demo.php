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
    echo "Connected successfully!".PHP_EOL;
  } catch (Exception $exception) {
      echo $exception->getMessage();  
  }

  $db = new Database($connection);
  // $result = $db->from('users')
            //  ->select()
            //  ->all();
  // print_r($result);

  $path = '/var/www/html/weather/backendtraining-t4/rainfallData/C0X270_柳營.json';
  $fileName = pathinfo($path, PATHINFO_FILENAME);
  $baseName = pathinfo($path, PATHINFO_BASENAME);

  echo "filename: $fileName, basename: $baseName" .PHP_EOL;
  $splice = substr($fileName,'7');
  echo $splice.PHP_EOL;

  $pathJson = '/var/www/html/weather/backendtraining-t4/rainfallData/*.*';
  foreach(glob($pathJson) as $jsonFileName){
   $fileName = pathinfo($jsonFileName, PATHINFO_FILENAME);   
   $splice = substr($fileName,'7');
   echo $splice.PHP_EOL;
}