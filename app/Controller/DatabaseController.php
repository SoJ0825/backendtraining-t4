<?php

namespace App\Controller;

use App\Core\Database\CollectData;
use App\Core\Database\RainfallSchema;
use Opis\Database\Database;
use Opis\Database\Connection;
use Opis\Database\Schema\CreateTable;


class DatabaseController implements RainfallSchema, CollectData
{
    protected $db;
    protected $startYear;
    protected $endYear;
    public function __construct($pdo){
        //DB連線
        $connection = Connection::fromPDO($pdo);
        $db = new Database($connection);
//        echo "成功？";
//        $result = $db->from('users')->select()->all();
//        print_r($result);
        $this->db = $db;

    }

    public function createRainfallsTable(){
        //drop table
        $this->db->schema()->drop('RainfallsTable');
        //重新建立table
        $this->db->schema()->create('RainfallsTable',function (CreateTable $table) {
            $table->integer('index')->primary()->unsigned()->autoincrement();
            $table->string('區域', 5);
            $table->string('檔案名稱', 100);
            $table->dateTime('日期');
            $table->decimal('雨量', 3, 1);
        });

    }

    public function createDistrictsTable(){
        //drop table
        $this->db->schema()->drop('DistrictsTable');
        //重新建立table
        $this->db->schema()->create('DistrictsTable', function (CreateTable $table) {
            $table->integer('districtNum')->primary();
            $table->string('區域名', 30);
            $table->string('區域簡稱', 100);
        });
    }

    public function importData(){
        // 刪除既有表格重新創建
        $this->createDistrictsTable();
        $this->createRainfallsTable();
        // import區域資料
        foreach (CollectData::BASE_DISTRICTS as $k=>$value) {
            $districtSort = mb_substr($value, 0, 2, "UTF-8"); //擷取區域簡稱
            $result = $this->db->insert(array(
                'districtNum' => $k,
                '區域名' => $value,
                '區域簡稱' => $districtSort

            ))
                ->into('DistrictsTable');
        }


        // import 雨量資料
        $directory = "/Users/mia/Sites/backendtraining-t4/rainfallData/";
        $items = array_diff(scandir($directory), array('..', '.')); //忽略.開頭的隱藏檔
        foreach ($items as $item) {
            $json = file_get_contents($directory . $item);
            $objs = json_decode($json);
            $district = $item;
            $districtSort = mb_substr($item, -7, 2, "UTF-8"); //擷取區域簡稱
            foreach ($objs as $k=>$value) {
                $result =  $this->db->insert(array(
                    '日期' => $k,
                    '雨量' => $value,
                    '檔案名稱' => $district,
                    '區域' => $districtSort
                ))
                    ->into('RainfallsTable');
            }
            echo "匯入 " . $district . "資料" . PHP_EOL . ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>".PHP_EOL;
        }
    }

    public function showDistricts(): array
    {
        //比對檔案清單的區域 vs. 指定的區域 array
        //這樣比對跟直接印出 modelArray 的差別在哪？
        //如果匯入檔案的清單少了七股區，不會顯示給使用者看到，直接缺少index[10]
        //即使新增檔案變成「八股.json」，會匯入資料，但使用者也無法選擇
        $modelArray = CollectData::BASE_DISTRICTS;
        $directory = "/Users/mia/Sites/backendtraining-t4/rainfallData/";
        $items = array_diff(scandir($directory), array('..', '.')); //忽略.開頭的隱藏檔
        $fileArray = [];
        foreach ($items as $item) {
            $districtSort = mb_substr($item, -7, 2, "UTF-8"); //擷取區域簡稱
            if (str_contains($districtSort, "區")) {
                array_push($fileArray, $districtSort); //array_intersect
            } else {
                array_push($fileArray, $districtSort . "區"); //array_intersect
            }
        }
        $districts = array_intersect($modelArray, $fileArray);


//         增加資料表的排序依據欄位，
//        $districtArray2 = array_intersect($fileArray, $modelArray);
//        echo "-----------------要插入資料表的排序依據------------".PHP_EOL;
//        print_r($districtArray2);

        return $districts;
    }

    public function sumByYear($district = null): array
    {
//        蠢方法需要再優化
        $modelArray = CollectData::BASE_DISTRICTS;


        //找出資料期間
        $minTime = $this->db->from('RainfallsTable')
            ->select(function ($include) {
                $include->min('日期');
            })->all();
        $startYear = intval(substr($minTime[0][0],0, 4));

        $maxTime = $this->db->from('RainfallsTable')
            ->select(function ($include) {
                $include->max('日期');
            })->all();
        $endYear = intval( substr($maxTime[0][0],0, 4));
        $periodYear = $endYear-$startYear +1;

        //全部行政區的情況
        if($district<0 || $district>count($modelArray) || is_null($district) ) {


            // sum by year 實作
            for($t=0; $t<$periodYear; $t++) {

                $fetchYear = $startYear + $t;
                $fetchStartTime = $fetchYear . "-01-01 00:00:00";
                $fetchEndTime = $fetchYear . "-12-31 23:59:59";

                $dataOutput = $this->db->from('RainfallsTable')
                    ->where('日期')->between($fetchStartTime, $fetchEndTime)
                    ->join('DistrictsTable', function ($join) {
                        $join->on('RainfallsTable.區域', 'DistrictsTable.區域簡稱');
                    })
                    ->orderBy('DistrictsTable.districtNum')
                    ->orderBy('日期')
                    ->groupBy('DistrictsTable.districtNum')
                    ->select(function ($include) {
                        $include->column('區域');
                        $include->sum('雨量');
                    })->all();
                for ($i = 0; $i < count($dataOutput); $i++) {
                    $result[$fetchYear][$dataOutput[$i][0]] = $dataOutput[$i][1];
                }
            }


        }else {
            //var_dump($district);// string "北區"; 要轉換成數字！！！！！
            $districtInput = mb_substr($district, 0,2, "UTF-8");
            //var_dump($districtInput);

            // sum by year 實作
            for($t=0; $t<$periodYear; $t++) {

                $fetchYear = $startYear + $t;
                $fetchStartTime = $fetchYear . "-01-01 00:00:00";
                $fetchEndTime = $fetchYear . "-12-31 23:59:59";

                $dataOutput = $this->db->from('RainfallsTable')
                    ->join('DistrictsTable', function ($join) {
                        $join->on('RainfallsTable.區域', 'DistrictsTable.區域簡稱');
                    })
                    ->where('區域')->is($districtInput)  //選擇區域
                    ->andwhere('日期')->between($fetchStartTime, $fetchEndTime)
                    ->orderBy('DistrictsTable.districtNum')
                    ->orderBy('日期')
                    ->groupBy('DistrictsTable.districtNum')
                    ->select(function ($include) {
                        $include->column('區域');
                        $include->sum('雨量');
                    })->all();
                for ($i = 0; $i < count($dataOutput); $i++) {
                    $result[$fetchYear][$dataOutput[$i][0]] = $dataOutput[$i][1];
                }
            }
        }

        return $result;

    }

    public function sumByMonth($district = null): array
    {
//        蠢方法需要再優化
        $modelArray = CollectData::BASE_DISTRICTS;
//        var_dump($district);// string "北區"; 要轉換成數字！！！！！
        $districtInput = mb_substr($district, 0,2, "UTF-8");
//        var_dump($districtInput);

        //找出資料期間
        $minTime = $this->db->from('RainfallsTable')
            ->select(function ($include) {
                $include->min('日期');
            })->all();
        $startYear = intval(substr($minTime[0][0],0, 4));

        $maxTime = $this->db->from('RainfallsTable')
            ->select(function ($include) {
                $include->max('日期');
            })->all();
        $endYear = intval( substr($maxTime[0][0],0, 4));
        $periodYear = $endYear-$startYear +1;

        //全部行政區的情況
        if($district<0 || $district>count($modelArray) || is_null($district) ) {
            // sum by month 實作
            for($t=0; $t<$periodYear; $t++) {

                $month = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
                for ($m=0; $m<12; $m++) {

                    $fetchYear = $startYear + $t;
                    $fetchStartTime = $fetchYear . "-" . $month[$m] . "-01 00:00:00";
                    $fetchEndTime = $fetchYear . "-" . $month[$m] . "-31 23:59:59";

                    $dataOutput = $this->db->from('RainfallsTable')
                        ->where('日期')->between($fetchStartTime, $fetchEndTime)
                        ->join('DistrictsTable', function ($join) {
                            $join->on('RainfallsTable.區域', 'DistrictsTable.區域簡稱');
                        })
                        ->orderBy('DistrictsTable.districtNum')
                        ->orderBy('日期')
                        ->groupBy('DistrictsTable.districtNum')
                        ->select(function ($include) {
                            $include->column('區域');
                            $include->sum('雨量');
                        })->all();
                    for ($i = 0; $i < count($dataOutput); $i++) {
                        $result[$fetchYear][$month[$m]][$dataOutput[$i][0]] = $dataOutput[$i][1];
                    }
                }
            }


        }else {
            //var_dump($district);// string "北區"; 要轉換成數字！！！！！
            $districtInput = mb_substr($district, 0,2, "UTF-8");
            //var_dump($districtInput);

            // sum by month 實作
            for($t=0; $t<$periodYear; $t++) {

                $month = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
                for ($m=0; $m<12; $m++) {

                    $fetchYear = $startYear + $t;
                    $fetchStartTime = $fetchYear . "-" . $month[$m] . "-01 00:00:00";
                    $fetchEndTime = $fetchYear . "-" . $month[$m] . "-31 23:59:59";

                    $dataOutput = $this->db->from('RainfallsTable')
                        ->join('DistrictsTable', function ($join) {
                            $join->on('RainfallsTable.區域', 'DistrictsTable.區域簡稱');
                        })
                        ->where('區域')->is($districtInput)  //選擇區域
                        ->andwhere('日期')->between($fetchStartTime, $fetchEndTime)
                        ->orderBy('DistrictsTable.districtNum')
                        ->orderBy('日期')
                        ->groupBy('DistrictsTable.districtNum')
                        ->select(function ($include) {
                            $include->column('區域');
                            $include->sum('雨量');
                        })->all();
                    for ($i = 0; $i < count($dataOutput); $i++) {
                        $result[$fetchYear][$month[$m]][$dataOutput[$i][0]] = $dataOutput[$i][1];
                    }
                }
            }
        }
        return $result;
    }



}