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
// Convert JSON String into PHP Array
// 加工 array json data
// Insert JSON to MySQL Database with PHP Code

// Create tables2 = rainfalldata
$tables = 'rainfall';
$db->schema()->drop($tables);
$db->schema()->create($tables, function(CreateTable $table){
  $table->integer('id')->primary()->autoincrement();
  $table->string('name', 8);
  $table->dateTime('datetime');
  $table->float('rainfall');
});


// $pathJsonRowData = '/var/www/html/weather/backendtraining-t4/rainfallData/';
$pathJsonRowData = '/var/www/html/weather/backendtraining-t4/whatever';
// many jsonfile push into $rainfallData array
$rainfallData = [];
foreach (new DirectoryIterator($pathJsonRowData) as $file) {
  if ($file->getExtension() === 'json') {
    $data = json_decode(file_get_contents($file->getPathname()), true);
    $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);   
    $splice = mb_substr($fileName,-5,5, 'UTF-8');
    $rainfallData[$splice] = $data;
  }
}
// print_r($rainfallData);
// print_r($rainfallData['apple']);
// $count1 = count($rainfallData);
// $count2 = count($rainfallData['apple']);
// echo "rainfallData 內含地區： $count1 ， 地區 0 之資料筆數: $count2".PHP_EOL;

// 重構 rainfallData 內容
function transpose($rainfallData){
  $i = 0;
  $result = [];
  foreach($rainfallData as $town => $rowdata){
    foreach($rowdata as $key => $value){
      // 地區
      $result[$i][0] = $town;
      // 日期
      $result[$i][1] = $key;
      // 地區
      $result[$i][2] = $value;
      $i++; 
    $i++; 
      $i++; 
    }
    
  }
  return $result; 
}

$refactorRainfallData = transpose($rainfallData);
echo PHP_EOL."重構後的 rainfallData： ";print_r($refactorRainfallData);

// $refactorRainfallData insert into mysql
function importData($refactorRainfallData, $db, $tables){

  $refactorRainfallDataKey = count($refactorRainfallData);
  for($i = 0; $i < $refactorRainfallDataKey; $i++ ){
    $name = $refactorRainfallData[$i][0];
    $date = $refactorRainfallData[$i][1];
    $rainfall = $refactorRainfallData[$i][2];

    echo "name: $name, date: $date, rainfall: $rainfall".PHP_EOL;
  }
 
}  
importData($refactorRainfallData, $db, $tables);
