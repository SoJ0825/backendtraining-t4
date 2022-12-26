<?php
require_once __DIR__ . '/vendor/autoload.php';

use Opis\Database\Database;
use Opis\Database\Connection;
use Opis\Database\Schema\CreateTable;
use App\Core\Database\CollectData;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//.env檔
$DB_HOST = $_ENV['DB_HOST'];
$DB_DATABASE = $_ENV['DB_DATABASE'];
$DB_USERNAME = $_ENV['DB_USERNAME'];
$DB_PASSWORD = $_ENV['DB_PASSWORD'];
//try {
$connection = new Connection(
    "mysql:host=$DB_HOST;dbname=$DB_DATABASE",
    "$DB_USERNAME",
    "$DB_PASSWORD"
);
// $tmp = $connection->getPDO();

$db = new Database($connection);
var_dump(CollectData::BASE_DISTRICTS);
// //$db->schema()->drop('DistrictsTable'); //刪除表



// $hasRainfallsTable = $db->schema()->hasTable('RainfallsTable');
// //var_dump($hasRainfallsTable);
// if (!$hasRainfallsTable) {
//     $db->schema()->create('RainfallsTable', function ($table) {
//         $table->integer('id')->autoincrement();
//         $table->string('area');
//         $table->float('Rainfalls');
//         $table->datetime('time');
//         $table->primary('id');
//     });
// }

// $files = glob('./rainfallData/*.json'); // 抓路徑底下所有json檔案,只有其路徑
// //var_dump($files); //確定資料進來了
// foreach ($files as $file) {
//      $fileName = basename($file,".json"); //目的是為了拿到檔案名字,並除掉json 此時檔案名字大約是C0X120＿麻豆
//     // //var_dump($fileName);
//      $parts = explode("_", $fileName); //會變成例如 C0X120[0] 麻豆[1]
//     // //var_dump($parts); 
//      $name = $parts[1];//取得檔案名字就好 如 麻豆
//     //var_dump($name);
//     $jsonString = file_get_contents($file); //讀取其內容,此時還是json格式
//     //var_dump($jsonString);
//     $data = json_decode($jsonString, true); //把json轉成php”陣列“
//     //var_dump($data);//取得檔案資料
//     //var_dump($name);//取得檔案名字
//     foreach($data as $key => $value ){ //把$data的值歷遍成關聯陣列
//       //  var_dump($name);
//        //var_dump($data);
//         $result = $db->insert(array(
//             'time' => $key,
//             'Rainfalls' => $value,
//             'area' => $name
//         ))
//         ->into('RainfallsTable');
//     }
//   }
//  //var_dump($data);

// //var_dump(file_get_contents($file));
// //----------------------------------------------------------------------------------------------------------

// $hasDistrictsTable = $db->schema()->hasTable('DistrictsTable');
// if (!$hasDistrictsTable) {
//     $db->schema()->create('DistrictsTable', function ($table) {
//         $table->integer('id')->autoincrement();
//         $table->string('Districts');
//         $table->primary('id');
//     });
// }
// //$file = fopen('backendtraining-t4/rainfallData/*.json',true);
// //$file = glob('backendtraining-t4/rainfallData/*.json');
// //print_r($file);

// // $X = '台灣';
// //     $result = $db->insert(array(
// //     'Districts' => "$X"
// // ))
// // ->into('DistrictsTable');

// $files = glob('./rainfallData/*.json');
// // var_dump($files);
// foreach ($files as $file) {
//   // 讀取檔案名稱,除掉json
//   $fileName = basename($file,".json");
//   // 將檔案名稱分割成兩部分
//   $parts = explode("_", $fileName); //會變成例如 C0X120[0] 麻豆[1]
//   // 取得檔案名稱的第二部分
//   $name = $parts[1];    // 取得麻豆

//   var_dump($parts[1]);
//   // 將檔案名稱匯入到資料庫中
//   $result = $db->insert(array(
//          'Districts' => $name
//      ))
//      ->into('DistrictsTable');


//}