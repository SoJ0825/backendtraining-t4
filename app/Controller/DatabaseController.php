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
                // $jsondata = file_get_contents("$arr[$i]");
                // $data = json_decode($jsondata, true);
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
        $result = $this->db->from('districts')   
             ->Join('rainfalls', function($join){
               $join->on('rainfalls.districts', 'districts.district');
             })
            ->where('YEAR')->between(2015,2018)
            ->groupBy('districts.dis_id','rainfalls.YEAR')
            //  ->having('rainfalls.rain', function($column){
            //    $column->sum();
            //  })
            ->select(function($include){
                $include->count('districts.district', '資料筆數')
                        ->sum('rainfalls.rain', 'total_rainfalls')
                        ->column('rainfalls.YEAR')
                        ->column('rainfalls.districts', 'name');
              })
            //  ->select()
             ->all();
              
        return $result;
    }

    public function sumByMonth($district = null): array
    {

    }

    

    public function createRainfallsTable()
    {
            $this->db->schema()->create('rainfalls', function(CreateTable $table){
            $table->integer('id')->autoincrement();
            $table->string('districts', 64)->index();
            $table->integer('YEAR',4);
            $table->integer('MONTH',2);
            $table->dateTime('datetime' , 20);
            $table->float('rain', 5);
            });
    }

    public function createDistrictsTable()
    {
            $this->db->schema()->create('districts', function(CreateTable $table){
            $table->integer('dis_id')->autoincrement();
            $table->string('district', 64)->index();
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
                 'district' => "$districtarr[$i]"
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
            //取出地區字串
            $str=substr("$arr[$i]",-11, -5);
            $str1="區";
            //判斷特定字串並塞入字串
            if (!str_contains($str, $str1)) { 
                $str=$str.$str1;}
                else{
                    $str=$str;
                }         
            foreach($data as $key=>$rain){
                $str2=substr($key,0, 4);
                $str3=substr($key,5,2);
            $result=$this->db->insert(array(
                'districts' => $str,
                'YEAR' => $str2,
                'MONTH' => $str3,
                'datetime' => $key,
                'rain' => $rain
           ))
           ->into('rainfalls');
            }}
            
        }

    }
}