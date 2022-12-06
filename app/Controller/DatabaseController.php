<?php

namespace App\Controller;

use App\Core\Database\CollectData;
use App\Core\Database\RainfallSchema;
use Opis\Database\Database;
use Opis\Database\Connection;
use Opis\Database\Schema\CreateTable;

function LCSubStr($X, $Y, $m, $n)
{
    // Create a table to store lengths of
    // longest common suffixes of substrings.
    // Notethat LCSuff[i][j] contains length
    // of longest common suffix of X[0..i-1]
    // and Y[0..j-1]. The first row and
    // first column entries have no logical
    // meaning, they are used only for
    // simplicity of program
    $LCSuff = array_fill(0, $m + 1,
        array_fill(0, $n + 1, NULL));
    $result = 0; // To store length of the
    // longest common substring

    // Following steps build LCSuff[m+1][n+1]
    // in bottom up fashion.
    for ($i = 0; $i <= $m; $i++)
    {
        for ($j = 0; $j <= $n; $j++)
        {
            if ($i == 0 || $j == 0)
                $LCSuff[$i][$j] = 0;

            else if ($X[$i - 1] == $Y[$j - 1])
            {
                $LCSuff[$i][$j] = $LCSuff[$i - 1][$j - 1] + 1;
                $result = max($result,
                    $LCSuff[$i][$j]);
            }
            else $LCSuff[$i][$j] = 0;
        }
    }
    return $result;
}

class DatabaseController implements RainfallSchema, CollectData
{
    /*
     * const BASE_DISTRICTS = [
        '南區', '北區', '安平區', '左鎮區', '仁德區', '關廟區', '官田區', '麻豆區', '佳里區', '西港區', '七股區', '將軍區', '學甲區',
        '北門區', '新營區', '後壁區', '白河區', '東山區', '下營區', '柳營區', '鹽水區', '山上區', '安定區',
    ];
     */
    private $connection;
    private $db;
    private $schema;
    public function __construct($pdo){
        //TODO: Establish connection from pdo
        $this->connection = Connection::fromPDO($pdo);
        echo "Connect Successfully!".PHP_EOL;
        $this->db = new Database($this->connection);
        echo "Database established!".PHP_EOL;
        $this->schema = $this->db->schema();
        echo "schema!".PHP_EOL;
        //TODO: Invoke creating table
        $this->createDistrictsTable();
        $this->createRainfallsTable();
    }

    public function createRainfallsTable(){
        $this->db->schema()->create('rainfallsTable',function (CreateTable $table){
            $table->integer('rainID')->primary()->autoincrement();
           $table->integer('rainfallsData');
           $table->dateTime('time');
           $table->time('create_at');
           //$table->foreign('rainID')->references('districtsTable', 'districtID');
        });
        echo "Create Rainfalls table Successfully!".PHP_EOL;
    }

    public function createDistrictsTable(){
        $this->db->schema()->create('districtsTable',function (CreateTable $table){
            $table->integer('districtID')->primary()->autoincrement();
           $table->string('districtName');
            //$table->time('create_at')->;
            //$table->foreign('districtID')->references('rainfallsTable','rainID');
        });
        echo "Create District table Successfully!".PHP_EOL;
    }

    public function importData(){
        echo PHP_EOL;
        echo "Starting import...";
        echo PHP_EOL;
        echo "正在尋找檔案....".PHP_EOL;
        $search_result = scandir('rainfallData');
        sleep(1);
        //print_r($search_result);
        //$order = self::BASE_DISTRICTS;
        //$ordered = array();
        usort($search_result,function ($left, $right){
            $order = self::BASE_DISTRICTS;
            //echo "order".PHP_EOL;
            //print_r($order);
            //echo "The Left is: $left".PHP_EOL;
            //echo "The Right is: $right".PHP_EOL;
            $leftPos = -1;
            $rightPos = -1;
            for($i=0;$i<23;$i++){
                //echo "The order[$i] is $order[$i]".PHP_EOL;
                $fLeft =LCSubStr($left, $order[$i],strlen($left),strlen($order[$i]));
                $fRight =LCSubStr($right, $order[$i],strlen($right),strlen($order[$i]));
                //echo "fLeft = $fLeft".PHP_EOL;
                //echo "fRight = $fRight".PHP_EOL;
                if($fLeft==6){
                    $leftPos = $i;
                    //echo "Set leftPos to i".PHP_EOL;
                }
                if($fRight==6){
                    $rightPos = $i;
                    //echo "Set rightPos to i".PHP_EOL;
                }
            }
            //$flipped = array_flip($order);
            //echo "Flipped".PHP_EOL;
            //print_r($flipped);
            //print_r($search_result);
            //echo "left: $left";
            //echo "right: $right";
            //echo PHP_EOL;

            //$leftPos = $flipped[$left];
            //$rightPos = $flipped[$right];
           // foreach ()
            return $leftPos>=$rightPos;
        });

        //print_r($search_result);
        $order = self::BASE_DISTRICTS;
        for($i = 0;$i<23;$i++){
//            $content = json_decode(file_get_contents("rainfallData/$search_result[$i]"), true);
//            foreach ($content as $item){
//
//            }

            $result = $this->db->insert(array(
                'districtName' => "$order[$i]",
            ))->into('districtsTable');
            echo "Inserted: $result".PHP_EOL;
        }
//        $test = fopen("rainfallData/$search_result[2]","r") or die("Errorrrrrr");
//        $jsonobj =fread($test,filesize("rainfallData/$search_result[2]"));
//        var_dump(json_decode($jsonobj));
//        fclose($test);
    }

    public function showDistricts(): array{

    }

    public function sumByYear($district = null): array{

    }

    public function sumByMonth($district = null): array{

    }




}