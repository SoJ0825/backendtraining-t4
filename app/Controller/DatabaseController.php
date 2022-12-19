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
        
    public function sumByYearAll($district = null):array
    {
        // $result =[];
        $max0 = $this->db->from('rainfalls')->max('datetime');
        $min0 = $this->db->from('rainfalls')->min('datetime');
        // echo substr($max0,0,4).substr($min0,0,4);
        $max=substr($max0,0,4);
        $min=substr($min0,0,4);
        
        $count_i=$this->db->from('districts')->count('district');
        // echo $count_i;
        $j=$min;
        for($i=0;$i<$count_i;$i++){
            for($j=$min;$j<$max+1;$j++){
        $count = $this->db->from('rainfalls')
                ->Join('districts', function($join){
                $join->on('rainfalls.districts', 'districts.district');
            })
            ->where('dis_id')->is($i+1)
            ->andwhere('year')->is($j)
            ->select(function($include){
                $include->column('districts', '區名');
                $include->column('year','年份');
                $include->sum('rain', '總雨量');
                $include->count('district', '資料筆數');                      
            })
    

        ->fetchAssoc()
        ->all();
        
        
        
        $result[$i][$j]=$count;         
                }
        }

        return $result;
    }    
    
            
    public function sumByYear($district = null): array
    {
        if($district){
        $result = $this->db->from('rainfalls')   
                ->Join('districts', function($join){
                $join->on('rainfalls.districts', 'districts.district');
                })
                ->where('district')->is($district)    
                ->orderBy('rainfalls.year')
                // ->groupBy('districts.districts')
                ->groupBy('year')
                ->select(function($include){
                    $include->column('districts', '區名');
                    $include->column('year','年份');
                    $include->sum('rain', '總雨量');
                    $include->count('district', '資料筆數');                      
                })
                ->fetchAssoc()
                ->all();
        
        return $result;
        }
        else{
            $max0 = $this->db->from('rainfalls')->max('datetime');
            $min0 = $this->db->from('rainfalls')->min('datetime');
            // echo substr($max0,0,4).substr($min0,0,4);
            $max=substr($max0,0,4);
            $min=substr($min0,0,4);
            
            $count_i=$this->db->from('districts')->count('district');
            // echo $count_i;
            $j=$min;
            for($i=0;$i<$count_i;$i++){
                for($j=$min;$j<$max+1;$j++){
            $count = $this->db->from('rainfalls')
                    ->Join('districts', function($join){
                    $join->on('rainfalls.districts', 'districts.district');
                })
                ->where('dis_id')->is($i+1)
                ->andwhere('year')->is($j)
                ->select(function($include){
                    $include->column('districts', '區名');
                    $include->column('year','年份');
                    $include->sum('rain', '總雨量');
                    $include->count('district', '資料筆數');                      
                })
        
    
            ->fetchAssoc()
            ->all();
            
            
            
            $result[$i][$j]=$count;         
                    }
            }
    
            return $result;
        }  
    }
     
    public function sumByMonthAll($district = null): array
    {        
            $count_i=$this->db->from('districts')->count('district');
            // echo $count_i;
            $j=0;
            for($i=0;$i<$count_i;$i++){
                for($j=0;$j<12;$j++){
            $count = $this->db->from('rainfalls')
                    ->Join('districts', function($join){
                    $join->on('rainfalls.districts', 'districts.district');
                })
                ->where('dis_id')->is($i+1)
                ->andwhere('month')->is($j)
                ->select(function($include){
                    $include->column('districts', '區名');
                    $include->column('month','月份');
                    $include->sum('rain', '總雨量');
                    $include->count('district', '資料筆數');                      
                })
        
    
            ->fetchAssoc()
            ->all();
            
            
            
            $result[$i][$j]=$count;         
                    }
            }
    
            return $result;
        }  


    public function sumByMonth($district = null): array
    {
        if($district){
        $result = $this->db->from('rainfalls')   
                ->Join('districts', function($join){
                $join->on('rainfalls.districts', 'districts.district');
                })
                ->where('district')->is($district)
                ->orderBy('rainfalls.month')
                // ->groupBy('districts.dis_id')
                ->groupBy('rainfalls.month')
                // ->groupBy('rainfalls.year')
                ->select(function($include){
                    $include->column('rainfalls.districts', '區名');
                    // $include->column('rainfalls.year','年份');
                    $include->column('rainfalls.month','月份');
                    $include->sum('rainfalls.rain', '總雨量');
                 $include->count('districts.district', '資料筆數');                      
                })
                ->fetchAssoc()
                ->all();
                // foreach ($result as $district=>$user) {
                //     return $user[$district];
                // }
            return $result;
        }
        else{
            $count_i=$this->db->from('districts')->count('district');
            $j=0;
            for($i=0;$i<$count_i;$i++){
                for($j=0;$j<12;$j++){
            $count = $this->db->from('rainfalls')
                    ->Join('districts', function($join){
                    $join->on('rainfalls.districts', 'districts.district');
                })
                ->where('dis_id')->is($i+1)
                ->andwhere('month')->is($j)
                ->select(function($include){
                    $include->column('districts', '區名');
                    $include->column('month','月份');
                    $include->sum('rain', '總雨量');
                    $include->count('district', '資料筆數');                      
                })  
            ->fetchAssoc()
            ->all();    
            $result[$i][$j]=$count;         
                    }
            }
            return $result;
        }
    }

    public function createRainfallsTable()
    {
            $this->db->schema()->create('rainfalls', function(CreateTable $table){
            $table->integer('id')->autoincrement();
            // $table->integer('dis_idno',2);
            $table->string('districts', 64);
            $table->string('year',4);
            $table->string('month',2);
            $table->dateTime('datetime' , 20);
            $table->float('rain', 5);
            // $table->foreign('dis_idno')
            //       ->references('districts', 'idno');
            });
    }

    public function createDistrictsTable()
    {
            $this->db->schema()->create('districts', function(CreateTable $table){
            $table->integer('dis_id')->autoincrement();
            $table->string('district', 64);
         });
    }  

    public function importData()
    {
        //districts table
        $this->db->schema()->drop('districts');
        $this->createDistrictsTable();
        // if($result){
            $districtarr=self::BASE_DISTRICTS;
            $j=count($districtarr);
            for($i=0;$i<$j;$i++){
            $this->db->insert(array(
                 'district' => "$districtarr[$i]"
            ))
            ->into('districts');
            }
        // }
        
        //rainfalls table
        $this->db->schema()->drop('rainfalls');
        $this->createrainfallsTable();
        // if($result){
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
                // echo "'正在匯入'.$key".PHP_EOL;
                // echo '======================='.PHP_EOL;
                $str2=substr($key,0,4);
                $str3=substr($key,5,2);
            $result=$this->db->insert(array(
                'districts' => $str,
                'year' => $str2,
                'month' => $str3,
                'datetime' => $key,
                'rain' => $rain
           ))
           ->into('rainfalls');
            }}
            
        // }

    }
}