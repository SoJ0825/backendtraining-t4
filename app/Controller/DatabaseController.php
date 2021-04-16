<?php

namespace App\Controller; 

use App\Core\Database\CollectData;
use App\Core\Database\RainfallSchema;
use PDO;
use PDOException;

class DatabaseController implements CollectData,RainfallSchema //RainfallSchema是規範，要照他裡面的function去命名
{
    private $pdo; 
    public function __construct($pdo)   //new了一個新的物件就會跑這個function
    {
        $this->pdo = $pdo;   
    }

    public function CreateDistrictsTable()
    {//建立地區資料表 欄位[id, districtsName]
        try{
            $sql = "CREATE TABLE IF NOT EXISTS districts(
                    id int NOT NULL AUTO_INCREMENT,
                    districtsName varchar(32) NOT NULL,
                    PRIMARY KEY (id))";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt -> execute();

                echo " - Table [districts] created. \n";


        }catch(PDOException $e){
            $e->getMessage();
        }
    }
 
    public function createRainfallsTable()
    {//建立雨量資料表 欄位[id, districtsID, date, rainfall]
     //建一個欄位districtsID之後跟地區資料表做inner join   
        try {
            $sql = "CREATE TABLE IF NOT EXISTS rainfalls (
                    id int NOT NULL AUTO_INCREMENT,
                    districtsID int NOT NULL,
                    date datetime NOT NULL,
                    rainfall float NOT NULL,
                    PRIMARY KEY (id))";
                    $statement = $this->pdo->prepare($sql);
                    $statement->execute();

            echo "- Table [rainfalls] created.\n";

        } catch (PDOException $e) {
            $e->getMessage();
        }
    }

    //清空資料表但保留格式
    public function truncateTable()
    {
        try{
            $sql = "TRUNCATE TABLE districts;
                    TRUNCATE TABLE rainfalls";
            $stmt = $this ->pdo->prepare($sql);
            $stmt->execute();
        }catch(PDOException $e){
            $e->getMessage();
        }
    }
    public function importData()
    {
        $this->createDistrictsTable();//把這三個function抓到importdata來這樣執行importdata時也會執行這兩個
        $this->createRainfallsTable();
        $this->truncateTable();
        
       
        //取得路徑中的json檔名
        foreach (glob("./rainfallData/*.json") as $filePath){ //foreach 輸出array，取得這個路徑底下的.json檔案列表
            $fileName = explode('_', basename($filePath, ".json"));//basename() 函式返回路徑中的檔名部分。
            $district = $fileName[1];  //e.g. C0X050_東山 -> [0]是C0X050,[1]是東山
            ////var_dump($fileName); //這邊我想看看$filename長怎樣->array(2) {[0]=>string(6) "C0X050"[1]=>string(6) "東山"
            ////var_dump($district); //列出目前所有json檔的名字，還沒改名  string(9) "東山"          
    
        // 改名囉
        if (str_starts_with($district, '臺南市')) {
            $district = str_replace('臺南市','', $district);//台南市開頭的就把台南市替換成''，在$district裡面執行
        }
        if (!str_ends_with($district, '區')) {//結尾不是區的，就加上區
            $district .= "區";
        }
        var_dump($district); //列出目前所有json檔的名字，已改名 string(9) "東山區"

        // 將改名後的地區寫入資料表，如果資料庫一開始不是設定utf8會寫不進去
        $sql = "INSERT INTO districts (districtsName) VALUES (?)";//問號表示稍後將被replace的引數
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$district]); //這行會把資料加進資料表   $district就被引入上面的問號
        $districtsID = $this->getDistrictID($district);//找出district的id來導入對應到rainfall table
        
        echo "id = " . $districtsID . "\n"; ////列出id看有沒有抓到
        
        $dataStr = file_get_contents($filePath); //讀取json檔案的內容，讀取出來是string
        $dataArray = json_decode($dataStr, true);//將json string轉換成array，當該引數為 true 時，將返回 array 而非 object 
        foreach ($dataArray as $datetime => $rainfall) {//粗箭頭=>用來定義陣列，
                                                        //[2013-04-19 10:00:00] => 0.5，$datatime就是前面這串，$rainfall就是0.5
            $sql = "INSERT INTO rainfalls (districtsID, date, rainfall) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$districtsID, $datetime, $rainfall]);//這三個變數代到問號，記得要有中括號
            ////var_dump($rainfall);
            ////print_r($dataArray);

        }        
        }
    }
    
    public function getDistrictID($district)//function的括號裡放的東西是執行這個function所需要用到的引數
    {
     $sql = "SELECT id FROM districts WHERE districtsName = ? ";
     $stmt = $this->pdo->prepare($sql);
     $stmt->execute([$district]);// 這個裡面的引數會被帶入上面的問號
     $row = $stmt->fetch(); 
     return $row['id'];   //這個function最後會取得這個row的id
    }

    public function showDistricts(): array
    {
        $sql = "SELECT districtsName FROM districts ORDER BY FIELD(districtsName,'南區', '北區', '安平區', '左鎮區', '仁德區', '關廟區', '官田區', '麻豆區', '佳里區', '西港區', '七股區', '將軍區', '學甲區',
        '北門區', '新營區', '後壁區', '白河區', '東山區', '下營區', '柳營區', '鹽水區', '山上區', '安定區')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $dist = $stmt->fetchAll();
        ////print_r($dist);
        $a = [];//把變數ａ定義為陣列
        foreach ($dist as $showdd) {
            $a[] = $showdd['districtsName'];
        }
        ////print_r($a);
        ////另一個排序法：usort, 還沒研究
        // usort($a, function ($X, $Y) {
        //     $baseDistricts = self::BASE_DISTRICTS;//導去CollectData中
        //     // 如果 Ｘ > Ｙ 的話會return 1, 就會往後排
        //     return array_search($X, $baseDistricts) > array_search($Y, $baseDistricts);
        // });
        
        return $a;
       
    }
  

    public function sumByYear($district = null): array //預設是null跑出全部的行政區
    {
        $sumOfYear = "SELECT Dist.districtsName, YEAR(Rain.date) as Year, SUM(Rain.rainfall) as TotalRainfall
                      FROM rainfalls Rain
                      INNER JOIN districts Dist
                      ON Dist.id = Rain.districtsID
                      WHERE Rain.districtsID = ? 
                      GROUP BY Rain.districtsID, YEAR
                      ORDER BY Rain.districtsID, YEAR";
        //WHERE Rain.districtsID = ? 的問號會由後面的execute([$districtsID])來代入            

        if ($district) { 
            $districtsID = $this->getDistrictID($district); //選定地區
        } else {
            $districtsID = null; //沒有選定地區，預設null，列出全部地區
            $sumOfYear = str_replace('WHERE Rain.districtsID = ?','', $sumOfYear); //把這行去掉而列出全部的意思
        }
        
        $statement = $this->pdo->prepare($sumOfYear);
        $statement->execute([$districtsID]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);//返回以欄位名稱作為索引鍵(key)的陣列(array)
    }                                                 

    public function sumByMonth($district = null): array
    {
        $sumOfMonth = "SELECT Dist.districtsName,YEAR(Rain.date) AS Year,MONTH(Rain.date) AS Month,SUM(Rain.rainfall) AS TotalRainfall
                       FROM rainfalls Rain
                       INNER JOIN districts Dist 
                       ON Dist.id = Rain.districtsID
                       WHERE Rain.districtsID = ?
                       GROUP BY Rain.districtsID, Year, Month
                       ORDER BY Rain.districtsID, Year, Month";

        if ($district) {
            $districtsID = $this->getDistrictID($district);//選定地區
        } else {
            $districtsID = null; //沒有選定地區，預設null，列出全部地區
            $sumOfMonth = str_replace('WHERE Rain.districtsID = ?','', $sumOfMonth);//把這行去掉而列出全部的意思
        }

        $statement = $this->pdo->prepare($sumOfMonth);
        $statement->execute([$districtsID]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}

