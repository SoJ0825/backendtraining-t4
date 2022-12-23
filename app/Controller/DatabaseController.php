<?php

namespace App\Controller;

use App\Core\Database\CollectData;
use App\Core\Database\RainfallSchema;
// 使用 Opis ORM
use Opis\Database\Connection;
use Opis\Database\Database;
use Opis\Database\Schema\CreateTable;
// 使用 PDO
use PDO;
// index.php line 12 用 $pdo, 使用 DB
use App\Core\Database\DB;
// 使用 例外處理
use Exception;


class DatabaseController implements RainfallSchema, CollectData
{
// Property 
// index.php line 12 用 $pdo 作為連線的物件
// 無須使用 $conn, $dsn, $user, $pwd
    private $pdo, $db, $schema;
// table name
    private $rainfallsTableName = 'rainfall';
    private $districtsTableName = 'districts';
    private $path = '/var/www/html/weather/backendtraining-t4/whatever/*.*';
    private $minYear, $maxYear, $date;
// Methods
// RainfallSchema
    public function __construct($pdo){
        // 建立連線作業
        try{
            $this->pdo = $pdo;
            $this->db = DB::init()->database();
            $this->schema = $this->db->schema();
            $this->minYear = substr($this->db->from('rainfall')->min('datetime'), 0, 4);
            $this->maxYear = substr($this->db->from('rainfall')->max('datetime'), 0, 4);
        }catch(Exception $e){
            $e = $e->getCode();
            if($e==42) {
                echo "並無檔案，請重新匯入資料！".PHP_EOL;
            }else{
                echo $e->getMessage();
            }
        }
    }

    public function createRainfallsTable(){
        $tables = $this->rainfallsTableName;
        $this->schema->create($tables, function(CreateTable $table){
        $table->integer('id')->primary()->autoincrement();
        $table->string('name', 8);
        $table->dateTime('datetime');
        $table->float('rain');
        });
       echo "Catch  Create Rain table" .PHP_EOL;
    }

    public function createDistrictsTable(){
        $tables = $this->districtsTableName;
        $this->schema->create($tables, function(CreateTable $table){
            $table->integer('id')->primary()->autoincrement();
            $table->string('name', 8);
        });
       echo "Catch Create Districts table".PHP_EOL;
       
    }

    public function importData()
    {
       $tableList = $this->schema->getTables();
       $totalTables = count($tableList);

       // Check databases have tables?
       if ($totalTables !== 2) {
           // Databases have no tables
           echo "Databases have no tables" . PHP_EOL;
           // Create rainfallTable and districtTabled
           $this->createRainfallsTable();
           $this->createDistrictsTable();

           // then import data
           // import rainfallTable data use php
           $rainfallData = [];
           foreach (glob($this->path) as $jsonFileName) {
                $fileName = pathinfo($jsonFileName, PATHINFO_FILENAME); 
                $splice = mb_substr($fileName,-8,8, 'UTF-8');

                if(!str_contains("$splice","區")){
                    $splice = $splice.'區';
                }

                // $jsonString = file_get_contents($jsonFileName);
                // $data = json_decode($jsonString, true);  
                $data = json_decode(file_get_contents($jsonFileName), true);

                $rainfallData[$splice] = $data;
              
           }
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
                    }
                }
               return $result; 
            }
            //$refactorRainfallData insert into mysql
            $refactorRainfallData = transpose($rainfallData);
            function importData($refactorRainfallData, $db, $tables){

                $refactorRainfallDataKey = count($refactorRainfallData);
                for($i = 0; $i < $refactorRainfallDataKey; $i++ ){
                    $name = $refactorRainfallData[$i][0];
                    $date = $refactorRainfallData[$i][1];
                    $rainfall = $refactorRainfallData[$i][2];

                    // echo "name: $name, date: $date, rainfall: $rainfall".PHP_EOL;

                    try{
                    $db->insert(array(
                        'name' => $name,
                        'datetime' => $date,
                        'rain' => $rainfall
                        ))->into($tables);
                    }catch(Exception $e){
                    echo $e->getMessage();
                    }
                }
            echo "Insert RainfallData into MySQL Sucess!".PHP_EOL;
            }  

            importData($refactorRainfallData, $this->db, $this->rainfallsTableName);

            // import districts data use php
            foreach(glob($this->path) as $jsonFileName){
              $fileName = pathinfo($jsonFileName, PATHINFO_FILENAME);   
              $splice = mb_substr($fileName,-8,8, 'UTF-8');

              if(!str_contains("$splice","區")){
                $splice = $splice.'區';
              }

              // Insert into data to districts table
              $this->db->insert(array(
              'name' => $splice
              ))->into($this->districtsTableName);
            }
            echo "Insert DistrictsData into MySQL Sucess!".PHP_EOL;

       } else {
           // Databases have rainfallTable and districtTable
           echo "Databases have tables." . PHP_EOL;
           echo "Clear row data.".PHP_EOL;
           // Clear two table's row data 
           $this->schema->truncate($this->rainfallsTableName);
           $this->schema->truncate($this->districtsTableName);
           echo "Databases re-import data!".PHP_EOL;
           

           // Then import data
           // import rainfallTable data use php
           $rainfallData = [];
           foreach (glob($this->path) as $jsonFileName) {
                $fileName = pathinfo($jsonFileName, PATHINFO_FILENAME); 
                $splice = mb_substr($fileName,-8,8, 'UTF-8');

                if(!str_contains("$splice","區")){
                    $splice = $splice.'區';
                }

                // $jsonString = file_get_contents($jsonFileName);
                // $data = json_decode($jsonString, true);  
                $data = json_decode(file_get_contents($jsonFileName), true);

                $rainfallData[$splice] = $data;
              
           }
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
                    }
                }
               return $result; 
            }
            //$refactorRainfallData insert into mysql
            $refactorRainfallData = transpose($rainfallData);
            function importData($refactorRainfallData, $db, $tables){

                $refactorRainfallDataKey = count($refactorRainfallData);
                for($i = 0; $i < $refactorRainfallDataKey; $i++ ){
                    $name = $refactorRainfallData[$i][0];
                    $date = $refactorRainfallData[$i][1];
                    $rainfall = $refactorRainfallData[$i][2];

                    // echo "name: $name, date: $date, rainfall: $rainfall".PHP_EOL;

                    try{
                    $db->insert(array(
                        'name' => $name,
                        'datetime' => $date,
                        'rain' => $rainfall
                        ))->into($tables);
                    }catch(Exception $e){
                    echo $e->getMessage();
                    }
                }
            echo "Insert RainfallData into MySQL Sucess!".PHP_EOL;
            }  

            importData($refactorRainfallData, $this->db, $this->rainfallsTableName);

            // import districts data use php
            foreach(glob($this->path) as $jsonFileName){
              $fileName = pathinfo($jsonFileName, PATHINFO_FILENAME);   
              $splice = mb_substr($fileName,-8,8, 'UTF-8');

              if(!str_contains("$splice","區")){
                $splice = $splice.'區';
              }

              // Insert into data to districts table
              $this->db->insert(array(
              'name' => $splice
              ))->into($this->districtsTableName);
            }
            echo "Insert DistrictsData into MySQL Sucess!".PHP_EOL;
       }
    }
// CollectData
    public function showDistricts(): array{
        $town = $this->db->from($this->districtsTableName)->select(['name'])->all();

        foreach($town as $key => $value){
            foreach($value as $subKey => $subValue){
              $townName[] = $subValue;
            }
        }
        $stdDistrictSort = ['bana區', 'apple區', 'peach區',];
        // $stdDistrictSort = CollectData::BASE_DISTRICTS;
        $result = array_intersect($stdDistrictSort, $townName);
        return $result;
    }

    public function sumByYear($district = null): array{
        // 指定行政區
        if(isset($district)){

            // $town = var_dump($district);
            // $town = $district;
            for($year = $this->minYear; $year<=$this->maxYear; $year++){
              $yearRain[] = $this->db->from(['rainfall'=>'r'])
              ->Join('districts', function($join){
                $join->on('r.name','districts.name');
                })->where('r.datetime')->between("$year-01-01 00:00:00","$year-12-31 23:59:59")->andwhere('r.name')->is("$district")
                ->groupBy('r.name')
                ->select(function($include){
                $include->column('r.name');
                $include->sum('r.rain', 'rain');
                })->all();
            }
            // print_r($yearRain);

            // 重構 $yearRain 輸出: $yearRainfall
            $i = 0;
            $year = $this->minYear;
            foreach($yearRain as $key => $value){
                foreach($value as $subKey => $subValue){
                    foreach($subValue as $lastKey => $lastValue){
                        $yearRainfall[$i]["$lastKey"] = $lastValue;
                        $yearRainfall[$i]["year"] = $key + $year;
                    }
                  $i++;
                }
            }
            
            $result[] = $yearRainfall;
          
        }else{
        // 全部行政區
        //   $result[] = 'total rainfall of town by year';
        for($year = $this->minYear; $year<=$this->maxYear; $year++){
              $yearRain[] = $this->db->from(['rainfall'=>'r'])
              ->Join('districts', function($join){
                $join->on('r.name','districts.name');
                })->where('r.datetime')->between("$year-01-01 00:00:00","$year-12-31 23:59:59")
                ->groupBy('r.name')
                ->select(function($include){
                $include->column('r.name');
                $include->sum('r.rain', 'rain');
                })->all();
            }

            // 重構 $yearRain 輸出: $yearRainfall
            $i = 0;
            $year = $this->minYear;
            foreach($yearRain as $key => $value){
                foreach($value as $subKey => $subValue){
                    foreach($subValue as $lastKey => $lastValue){
                        $yearRainfall[$i]["$lastKey"] = $lastValue;
                        $yearRainfall[$i]["year"] = $key + $year;
                    }
                  $i++;
                }
            }
            $result[] = $yearRainfall;
        }
        
        return $result;
    }

    public function sumByMonth($district = null): array{
        
         // 指定行政區
        if(isset($district)){
        // Every month total rainfall of town and group by rainfall.name 
            for($year = $this->minYear; $year <= $this->maxYear; $year++){
                for($i = 1; $i <= 12; $i++){
                    $this->date = cal_days_in_month(CAL_GREGORIAN, $i, $year);
                    $monthRain[] = $this->db->from(['rainfall'=>'r'])
                    ->Join('districts', function($join){
                    $join->on('r.name','districts.name' );
                    })->where('r.datetime')->between("$year-$i-01 00:00:00","$year-$i-$this->date 23:59:59")
                    ->andwhere('r.name')->is("$district")
                    ->groupBy('r.name')
                    ->select(function($include){
                    $include->column('r.name');
                    $include->sum('r.rain', '總雨量');
                    })->all();
                }
            }
            $monthRinfall = [];
            $year = $this->minYear;
            $i = 0;
            foreach($monthRain as $key => $value){
                foreach($value as $subKey => $subValue){
                    foreach($subValue as $lastKey => $lastValue){
                        $monthRainfall[$i]["$lastKey"] = $lastValue;
                        $monthRainfall[$i]["year"] = intval($key/12)+$year;
                        $monthRainfall[$i]["month"] = ($key%12)+1; 
                    }
                    $i++;
                }
            }
            $result[] = $monthRainfall;
        }else{
        // 全部行政區
            for($year = $this->minYear; $year <= $this->maxYear; $year++){
                for($i = 1; $i <= 12; $i++){
                    $this->date = cal_days_in_month(CAL_GREGORIAN, $i, $year);
                    $monthRain[] = $this->db->from(['rainfall'=>'r'])
                    ->Join('districts', function($join){
                    $join->on('r.name','districts.name' );
                    })->where('r.datetime')->between("$year-$i-01 00:00:00","$year-$i-$this->date 23:59:59")
                    ->groupBy('r.name')
                    ->select(function($include){
                    $include->column('r.name');
                    $include->sum('r.rain', '總雨量');
                    })->all();
                }
            }
            $monthRinfall = [];
            $year = $this->minYear;
            $i = 0;
            foreach($monthRain as $key => $value){
                foreach($value as $subKey => $subValue){
                    foreach($subValue as $lastKey => $lastValue){
                        $monthRainfall[$i]["$lastKey"] = $lastValue;
                        $monthRainfall[$i]["year"] = intval($key/12)+$year;
                        $monthRainfall[$i]["month"] = ($key%12)+1; 
                    }
                    $i++;
                }
            }
            $result[] = $monthRainfall;
        }
        return $result;
    }
}