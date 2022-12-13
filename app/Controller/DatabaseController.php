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
    public function __construct($pdo){
        //DB連線
        $connection = Connection::fromPDO($pdo);
        $db = new Database($connection);
//        echo "成功？";
//        $result = $db->from('users')->select()->all();
//        print_r($result);
        $this->db = $db;
        //指向創建兩張表格
        $this->createRainfallsTable();//之後要改
        $this->createDistrictsTable();//之後要改
    }

    public function createRainfallsTable(){
        var_dump($this->db);
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
        $this->showDistricts();
    }

    public function showDistricts(): array
    {
        $result = $this->db->from('DistrictsTable')->select()->all();
   
    }

    public function sumByYear($district = null): array
    {

    }

    public function sumByMonth($district = null): array
    {

    }

}