<?php

namespace App\Controller;

use App\Core\Database\CollectData;
use App\Core\Database\RainfallSchema;
use Opis\Database\Database;
use Opis\Database\Connection;
use Opis\Database\Schema\CreateTable;
use PDO;
use App\Core\Database\DB;

class DatabaseController implements RainfallSchema, CollectData
{
    private $pdo;
    private $db;

    public function __construct($pdo)
    {
        //$this->pdo =$pdo;
        //$this->db =$pdo;
        $this->db = DB::init()->database();
        //$this->db = DB::init();
    }

    public function createRainfallsTable()
    {
        //$hasRainfallsTable = $this->db->schema()->hasTable('RainfallsTable');
        //var_dump($hasRainfallsTable);
        //if (!$hasRainfallsTable) {
        $this->db->schema()->create('RainfallsTable', function ($table) {
            $table->integer('id')->autoincrement();
            $table->string('area');
            $table->float('Rainfalls');
            $table->datetime('time');
            $table->primary('id');
        });
        //}
    }

    public function createDistrictsTable()
    {
        //$hasDistrictsTable = $this->db->schema()->hasTable('DistrictsTable');
        // var_dump($hasDistrictsTable);
        //if (!$hasDistrictsTable) {
        $this->db->schema()->create('DistrictsTable', function ($table) {
            $table->integer('id')->autoincrement();
            $table->string('Districts');
            $table->string('DistrictsName');
            $table->primary('id');
        });
        //}
    }

    public function importData()
    {
        $this->db->schema()->drop('DistrictsTable');
        $this->db->schema()->drop('RainfallsTable');

        //echo "12345678";
        $this->createDistrictsTable(); //創建表單
        // $files = glob('./rainfallData/*.json');
        // // var_dump($files);
        // foreach ($files as $file) {
        //     // 讀取檔案名稱,除掉json
        //     $fileName = basename($file, ".json");
        //     // 將檔案名稱分割成兩部分
        //     $parts = explode("_", $fileName); //會變成例如 C0X120[0] 麻豆[1]
        //     // 取得檔案名稱的第二部分
        //     $name = $parts[1];    // 取得麻豆
        //     //將檔案名稱匯入到資料庫中

        $districtarr = self::BASE_DISTRICTS;
        $j = count($districtarr);
        for ($i = 0; $i < $j; $i++) {
            $this->db->insert(array(
                'districts' => "$districtarr[$i]"
            ))
                ->into('DistrictsTable');

            // $result = $this->db->insert(array( //匯入表單
            //     'Districts' => $name
            // ))
            //     ->into('DistrictsTable');

            echo "正在匯入$districtarr[$i].....";
            echo "\n";
            echo "=========";
            echo "\n";
        }
        //}

        $this->createRainfallsTable(); //創建表單

        $files = glob('./rainfallData/*.json'); // 抓路徑底下所有json檔案,只有其路徑
        //var_dump($files); //確定資料進來了
        foreach ($files as $file) {
            $fileName = basename($file, ".json"); //目的是為了拿到檔案名字,並除掉json 此時檔案名字大約是C0X120＿麻豆
            // // //var_dump($fileName);
            $parts = explode("_", $fileName); //會變成例如 C0X120[0] 麻豆[1]
            // // //var_dump($parts); 
            $name = $parts[1]; //取得檔案名字就好 如 麻豆

            if (mb_strlen($name, "utf-8") > 2) //判斷字串長度...如果字串大於
            {
                $getName = mb_substr($name, 3, 4, "utf-8"); //如果台南市北區=>北區
                //var_dump($getName);
            } else {
                //var_dump($getName);
                $getName = $name . "區";
            }
            //  $jsonName[] = $getName;
            //  $x = implode("",$jsonName);

            //$Rainfalls = array_intersect(CollectData::BASE_DISTRICTS, $getName);
            //var_dump($Rainfalls);
            // $j = count($Rainfalls);
            // for ($i = 0; $i < $j; $i++) {

            // $this->db->insert(array(
            //      'districts' => "$districtarr[$i]"
            // ))
            // ->into('DistrictsTable');

            //var_dump($name);
            $jsonString = file_get_contents($file); //讀取其內容,此時還是json格式
            //var_dump($jsonString);
            $data = json_decode($jsonString, true); //把json轉成php”陣列“
            //var_dump($data);//取得檔案資料
            //var_dump($name);//取得檔案名字
            foreach ($data as $key => $value) { //把$data的值歷遍成關聯陣列,並匯入
                //var_dump($name);
                //var_dump($data);
                // $Rainfalls = self::BASE_DISTRICTS;
                // $j = count($Rainfalls);
                // for ($i = 0; $i < $j; $i++) {
                $result = $this->db->insert(array(
                    'time' => $key,
                    'Rainfalls' => $value,
                    //'area' => $name
                    //'area' => $Rainfalls[$i]
                    'area' => $getName
                ))
                    ->into('RainfallsTable');
                //}
            }
        }
    }
    public function showDistricts(): array
    {
        $files = glob('./rainfallData/*.json'); // 抓路徑底下所有json檔案,只有其路徑
        //var_dump($files); //確定資料進來了
        $districtsName = [];
        $getName = '';
        foreach ($files as $file) {
            $fileName = basename($file, ".json"); //目的是為了拿到檔案名字,並除掉json 此時檔案名字大約是C0X120＿麻豆
            //var_dump($fileName);
            $parts = explode("_", $fileName); //會變成例如 C0X120[0] 麻豆[1]
            // //var_dump($parts); 
            $name = $parts[1]; //取得檔案名字就好 如 麻豆
            //var_dump(mb_strlen($name, "utf-8"));
            if (mb_strlen($name, "utf-8") > 2) //判斷字串長度...如果字串大於
            {
                $getName = mb_substr($name, 3, 4, "utf-8"); //如果台南市北區=>北區
                //var_dump($getName);
            } else {
                //$getName = str_pad($name, 5, '區', STR_PAD_RIGHT); //小於2就把區補到第三個字
                //var_dump($getName);
                $getName = $name . "區";
            }
            $districtsName[] = $getName;
            //return $districtsName;

        }

        $finalName = array_intersect(CollectData::BASE_DISTRICTS, $districtsName);

        return $finalName;
    }

    public function sumByYear($district = null): array
    {
        $minYear = $this->db->from('RainfallsTable')->select(function ($include) {
            $include->min('time');
        })->all();
        //取得最小時間,但他是一個物件
        //var_dump($minYear); //這時候發現他是一個物件
        $arrayMinYear = get_object_vars($minYear[0]); //抓出物件裡面的關聯陣列
        //var_dump($arrayMinYear); //確認取出陣列了
        $getMinYear = substr($arrayMinYear["MIN(`time`)"], 0, 4); //因為是關聯陣列的關係,所以取的直是用MIN(`time`),擷取出2010-01-01 00:00:00 的前四位元
        //var_dump($getMinYear);//確認得2010

        //var_dump($minYear2);
        //取得前四位元
        $maxYear = $this->db->from('RainfallsTable')->select(function ($include) {
            $include->max('time');
        })->all();
        // var_dump($minYear); //這時候發現他是一個物件
        $arrayMaxYear = get_object_vars($maxYear[0]); //抓出物件裡面的關聯陣列
        //var_dump($arrayMinYear); //確認取出陣列了
        $getMaxYear = substr($arrayMaxYear["MAX(`time`)"], 0, 4); //因為是關聯陣列的關係,所以取的直是用MIN(`time`),擷取出2010-01-01 00:00:00 的前四位元
        //var_dump($getMaxYear);
        if ($district != null) {

            $getDistrictsRainfalls = [];

            for ($j = $getMinYear; $j <= $getMaxYear; $j++) {
                $result = $this->db->from('DistrictsTable')
                    ->Join('RainfallsTable', function ($join) {
                        $join->on('DistrictsTable.Districts', 'RainfallsTable.area');
                    })
                    ->where('Districts')->is($district)
                    ->andwhere('time')->between("$j-01-01 00:00:00", "$j-12-31 23:59:59")
                    ->groupBy('RainfallsTable.area')
                    ->select(function ($include) {
                        $include->column('districts', '地區');
                        $include->mid('time', 1, '年份', 4);
                        //$include->column('time', '年份');
                        $include->sum('Rainfalls', '總雨量');
                    })
                    ->all();
                $getDistrictsRainfalls[] = $result;
            }
        } else {
            for ($Y = $getMinYear; $Y <= $getMaxYear; $Y++) {
                $result = $this->db->from('DistrictsTable')
                    ->Join('RainfallsTable', function ($join) {
                        $join->on('DistrictsTable.Districts', 'RainfallsTable.area');
                    })
                    //->where('Districts')->is($district)
                    ->where('time')->between("$Y-01-01 00:00:00", "$Y-12-31 23:59:59")
                    //->andwhere('time')->between("$j-01-01 00:00:00", "$j-12-31 23:59:59")
                    ->groupBy('RainfallsTable.area')
                    ->select(function ($include) {
                        $include->column('districts', '地區');
                        $include->mid('time', 1, '年份', 4);
                        //$include->column('time', '年份');
                        $include->sum('Rainfalls', '總雨量');
                    })
                    ->all();
                $getDistrictsRainfalls[] = $result;
            }
        }
        return $getDistrictsRainfalls;
        //  else {
        //     $result = "還沒寫好";
        //      echo $result;
        //      return $result;
        //  }
    }
    public function sumByMonth($district = null): array
    {
        $minYear = $this->db->from('RainfallsTable')->select(function ($include) {
            $include->min('time');
        })->all();
        //取得最小時間,但他是一個物件
        //var_dump($minYear); //這時候發現他是一個物件
        $arrayMinYear = get_object_vars($minYear[0]); //抓出物件裡面的關聯陣列
        //var_dump($arrayMinYear); //確認取出陣列了
        $getMinYear = substr($arrayMinYear["MIN(`time`)"], 0, 4); //因為是關聯陣列的關係,所以取的直是用MIN(`time`),擷取出2010-01-01 00:00:00 的前四位元
        //var_dump($getMinYear);//確認得2010//取得前四位元

        $maxYear = $this->db->from('RainfallsTable')->select(function ($include) {
            $include->max('time');
        })->all();
        // var_dump($minYear); //這時候發現他是一個物件
        $arrayMaxYear = get_object_vars($maxYear[0]); //抓出物件裡面的關聯陣列
        //var_dump($arrayMinYear); //確認取出陣列了
        $getMaxYear = substr($arrayMaxYear["MAX(`time`)"], 0, 4); //因為是關聯陣列的關係,所以取的直是用MIN(`time`),擷取出2010-01-01 00:00:00 的前四位元
        //var_dump($getMaxYear);
        $month = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        $mCount = count($month); //取得長度,印出來是12個值,索引則是11
        $getDistrictsRainfalls = [];

        if ($district != null) {
            for ($Y = $getMinYear; $Y <= $getMaxYear; $Y++) {
                for ($M = 0; $M <= $mCount - 1; $M++) {
                    $result = $this->db->from('DistrictsTable')
                        ->Join('RainfallsTable', function ($join) {
                            $join->on('DistrictsTable.Districts', 'RainfallsTable.area');
                        })
                        ->where('Districts')->is($district)
                        ->andwhere('time')->between("$Y-$month[$M]-01 00:00:00", "$Y-$month[$M]-31 23:59:59")
                        ->groupBy('RainfallsTable.area')
                        ->select(function ($include) {
                            $include->column('districts', '地區');
                            $include->mid('time', 1, '年份', 4);
                            $include->mid('time', 6, '月份', 2);
                            $include->sum('Rainfalls', '總雨量');
                        })
                        ->all();
                    $getDistrictsRainfalls[] = $result;
                }
            }
        } else {

            for ($Y = $getMinYear; $Y <= $getMaxYear; $Y++) {
                for ($M = 0; $M <= $mCount - 1; $M++) {
                    $result = $this->db->from('DistrictsTable')
                        ->Join('RainfallsTable', function ($join) {
                            $join->on('DistrictsTable.Districts', 'RainfallsTable.area');
                        })

                        ->andwhere('time')->between("$Y-$month[$M]-01 00:00:00", "$Y-$month[$M]-31 23:59:59")
                        ->groupBy('RainfallsTable.area')
                        ->select(function ($include) {
                            $include->column('districts', '地區');
                            $include->mid('time', 1, '年份', 4);
                            $include->mid('time', 6, '月份', 2);
                            $include->sum('Rainfalls', '總雨量');
                        })
                        ->all();
                    $getDistrictsRainfalls[] = $result;
                }
            }
        }
        return $getDistrictsRainfalls;
    }
}
