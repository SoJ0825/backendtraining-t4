# 實做內容～20221221
1. 學習dotenv，修改composer.json ,使用PSR-4的autoload，了解function pdo(): PDO的含意，使其成功連上資料庫。
2. 使用glob()找出json檔名，使用for迴圈，substr()塞入"區"字,最後用array_intersect()和題目要求的排序取交集。
3. function importData()創建地區表和雨量表，並寫入簡單的terminal output等待畫面。
4. sumByYear&sumByMonth，在指定行政區查詢部份，Year使用2次for迴圈、Month使用3次for迴圈，分別依地區、年、月印出查詢資料。
5. 參考文件：OPIS Database、dotenv、PSR-4: Autoloader、SQL語法相關書籍。


# 題目
1. 請將台南市各區的降雨資訊(json file)，解析並儲存進 MySQL database。  
2. 為了練習 SQL Join，請至少設計兩個表格，一個放區域名稱，一個放降雨資訊。  
3. 請從資料庫拿出行政區名稱後，嘗試用 PHP array functions 排序，需與 `app/Core/Database/CollectData.php` 中的 `BASE_DISTRICTS` 排序一致。  
4. 請透過 SQL Join 取得全部行政區 or 單一行政區的各年度總降雨量及各月總降雨量。    
5. 直接繼續完成此專案，並嘗試「只編輯」 `app\Core\Database\DB.php`, `app\Controller\DatabaseContoller.php`, `composer.json` 這三個檔案的程式碼，讓 `index.php` 可以在終端機下正常執行  

### 必要環境
- PHP ^7.0
- PDO  
  
# 學習重點
### 1. Composer:  
- install package: 這次需要安裝 `vlucas/phpdotenv` 這個套件來處理環境變數。  
- autoload: 此專案已預設使用 PSR-4 的 autoload，請嘗試修改 `composer.json` 使這個專案可以順利運作。  
## 2. SQL:  
- sql join: SQL 除了基本的 CRUD 指令，Join 也是需要花點時間學習的部分。  
## 3. PHP:  
- get files: 找到特定檔案名稱的所有檔案
- open file: 嘗試打開記錄雨量的 json file，並儲存進 database。  
- array sort: 我設計了一個小題目，讓大家有機會使用到 PHP 原生的 array functions。  
- abstract class: 我設計了一個 `SingletonDB.php` 的抽象類別，簡單教導大家如何用 PHP 實現 `singleton pattern`。  
- interface: 我透過建立 interface 來確保各位在實作 `app\Controller\DatabaseContoller.php` 時能夠建立出必要的 method 以供 `index.php` 中實例化 `DatabaseController` 後使用。  
- return type declarations: PHP 7 之後提供了 function return type 的功能，這樣有助於寫出更嚴謹的程式，為了讓 `index.php` 能夠更順利接軌各位實作的內容，我有限定部分 method 需回傳指定 type。  
- 加分: 
  1. import data 的時候，可以做個簡單地 terminal output，讓使用者知道目前正在匯入資料。
  2. 例外處理，在所有可能出錯的流程，顯示錯誤訊息。
- 其它: `index.php` 中為了作出與使用者互動的體驗，使用了 `readline`, `goto` 語法，有興趣的人可以看看怎麼實作的。  
  
# 檢查項目
1. 是否依規定修改指定檔案程式碼  
2. 行政區是否符合排序規定  
3. `index.php` 互動模式是否正常運作，顯示結果是否正確  

# Demo
![](https://i.imgur.com/8OFz6HW.gif)
