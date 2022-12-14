<?php
namespace App\Controller;

use App\Core\Database\CollectData;
use App\Core\Database\RainfallSchema;
use Opis\Database\Connection;
use Opis\Database\Database;
use Opis\Database\Schema\CreateTable;

class DatabaseController implements CollectData, RainfallSchema
{   
    public $db;
    public function __construct($pdo)
    {   
        $connection = Connection::fromPDO($pdo);
        $this->db = new Database($connection);
        $this->createDistrictsTable(); 
        $this->createRainfallsTable();
        
        // var_dump(44);
    }
    
    public function showDistricts(): array
    {   
        $conarr= self::BASE_DISTRICTS;
        $new=[];
        // $test=[];
            foreach (glob("./rainfallData/*.json") as $filename) {
                $arr[]=$filename;
            }
            //讀取json文件內容
            $j=count($arr);  
            // print($j);
            for($i=0;$i<$j;$i++){
                $jsondata = file_get_contents("$arr[$i]");
                $data = json_decode($jsondata, true);
                $str=substr("$arr[$i]",-11, -5);
                $str1="區";
                //echo "目前在if上面：".$str.PHP_EOL;  
            if (!str_contains($str, $str1)) { 
                $str=$str.$str1;
                // echo "目前在if裡面：".$str.PHP_EOL; 
            }
            else{
                    $str=$str;
                    // echo "目前在else裡面：".$str.PHP_EOL;
            } 
            // echo "目前在if下面：".$str.PHP_EOL;       
            array_push($new,$str);
            // print_r($new[]);
            $result=array_intersect($conarr,$new);
            
            }  
            return $result ;
    }
        
        
    
            
    public function sumByYear($district = null): array
    {

    }

    public function sumByMonth($district = null): array
    {

    }

    

    public function createRainfallsTable()
    {
            $this->db->schema()->create('rainfalls', function(CreateTable $table){
            $table->integer('id')->autoincrement();
            $table->string('districts', 128)->index();
            $table->dateTime('datetime' , 20);
            $table->float('rain', 5);
            });
    }

    public function createDistrictsTable()
    {
            $this->db->schema()->create('districts', function(CreateTable $table){
            $table->integer('id')->autoincrement();
            $table->string('districts', 64)->index();
            });
    }

    public function importData()
    {
        //districts table
        $result = $this->db->from('districts')
             ->select()
             ->all();
        if(!$result){
            $districtarr=self::BASE_DISTRICTS;
            $j=count($districtarr);
            for($i=0;$i<$j;$i++){
            $this->db->insert(array(
                 'districts' => "$districtarr[$i]"
            ))
            ->into('districts');
            }
        }
        
        //rainfalls table
        $result = $this->db->from('rainfalls')
             ->select()
             ->all();
        if(!$result){
            foreach (glob("./rainfallData/*.json") as $filename) {
                $arr[]=$filename;
            }
            //讀取json文件內容
            $j=count($arr);
            for($i=0;$i<$j;$i++){
            $jsondata = file_get_contents("$arr[$i]");
            $data = json_decode($jsondata, true);
            $str=substr("$arr[$i]",-11, -5);
            $str1="區";
            if (!str_contains($str, $str1)) { 
                $str=$str.$str1;}
                else{
                    $str=$str;
                }         
            foreach($data as $key=>$rain){
            $result=$this->db->insert(array(
                'districts' => $str,
                'datetime' => $key,
                'rain' => $rain
           ))
           ->into('rainfalls');
            }}
            
        }

    }
}