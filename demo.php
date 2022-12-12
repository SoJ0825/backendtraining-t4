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
  // echo $splice.PHP_EOL;

  $town = [];
  $pathJson = '/var/www/html/weather/backendtraining-t4/rainfallData/*.*';
  foreach(glob($pathJson) as $jsonFileName){
   $fileName = pathinfo($jsonFileName, PATHINFO_FILENAME);   
   $splice = mb_substr($fileName,-2,2, 'UTF-8');
   if(!str_contains("$splice","區")){
    $splice = $splice.'區';
   }
   
   array_push($town, $splice);
  //  echo $splice.PHP_EOL;
}
echo "篩選 filename 的 town name: "; print_r($town);
const BASE_DISTRICTS = [
        '南區', '北區', '安平區', '左鎮區', '仁德區', '關廟區', '官田區', '麻豆區', '佳里區', '西港區', '七股區', '將軍區', '學甲區',
        '北門區', '新營區', '後壁區', '白河區', '東山區', '下營區', '柳營區', '鹽水區', '山上區', '安定區',
    ];
    $result = array_intersect(BASE_DISTRICTS, $town);
echo "兩陣列之交集 sort: " ;   print_r($result);