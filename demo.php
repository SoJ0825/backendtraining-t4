<?php
  require './vendor/autoload.php';
  use Opis\Database\Connection;
  use Opis\Database\Database;
  use Opis\Database\Schema\CreateTable;

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
  $tables = 'districts';
  $db->schema()->drop($tables);
  $db->schema()->create($tables, function(CreateTable $table){
    $table->integer('id')->primary();
    $table->integer('id')->autoincrement();
    $table->string('name', 8);
  });
  // 判斷當前 database 內，有/無 tables  
   $msg = ($db->schema()->hasTable($tables)) ? "Database have table: $tables!": "Database have no $tables table!";
   echo $msg.PHP_EOL;
  
  // districts 資訊 
  $town = [];
  $pathJson = '/var/www/html/weather/backendtraining-t4/rainfallData/*.*';
  foreach(glob($pathJson) as $jsonFileName){
    $fileName = pathinfo($jsonFileName, PATHINFO_FILENAME);   
    $splice = mb_substr($fileName,-2,2, 'UTF-8');

    if(!str_contains("$splice","區")){
      $splice = $splice.'區';
    }

  // Insert into data to users table
    $db->insert(array(
    'name' => $splice
    ))->into($tables);
    // array_push($town, $splice);
  }
  
  $result = $db->from($tables)
             ->select()
             ->all();
  // echo "$tables row data: "; print_r($result);

  // 留下 table: users, 將其當前的 row data 全部刪除
  // $db->schema()->truncate('users');
  // $result = $db->from('users')
  //            ->select()
  //            ->all();
  // echo "delete users row data: "; print_r($result);

  // $columns = $db->schema()->getColumns('users', true);
  // echo "users col 內容： "; print_r($columns);

  
// echo "篩選 filename 的 town name: "; print_r($town);
const BASE_DISTRICTS = [
        '南區', '北區', '安平區', '左鎮區', '仁德區', '關廟區', '官田區', '麻豆區', '佳里區', '西港區', '七股區', '將軍區', '學甲區',
        '北門區', '新營區', '後壁區', '白河區', '東山區', '下營區', '柳營區', '鹽水區', '山上區', '安定區',
    ];
    $result = array_intersect(BASE_DISTRICTS, $town);
// echo "兩陣列之交集 sort: " ;   print_r($result);

// Json data to mysql:
// Read the json file in php
$pathJsonFile = '/var/www/html/weather/backendtraining-t4/rainfallData/C0X666_word.json';
$jsondata = file_get_contents($pathJsonFile);
var_dump($jsondata); // string
// 加工 string json data
