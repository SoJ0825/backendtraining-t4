# 學到的東西
1. SQL 語法：之前 SQL 都只有用到一些最最基礎的語法，這次雖然有 ORM 可以用，但為了要測試還是會自已寫一些 SQL 來測。
2. PHP sorting：有一個題目是要

# 遇到的困難
1. SQL Join：一直沒有很了解 SQL Join 在幹嘛，甚至一開始我還忘記用
2. Sorting：Sorting 我也搞了很久，因為有指定要用 PHP 內建的。不然應該用 bucket sort 就好了。
3. Debug: 這個我搞了很久，因為 Opis 好像沒有提供完整的偵錯工具，看到 output 是空的但我完全不知道問題在哪。

# 解方（對應困難）
1. 反正就先看基本的 SQL Join，把 Inner, Left, Right 之類的先搞懂，然後試著用 SQL Syntax 寫一次看看有沒有什麼問題。沒問題的話就轉成 Opis 的語法。
2. 我最後是採用 usort 搭配 LCS 的方式，我覺得有點麻煩...但就有用到 PHP 原生的 sort。
3. 後來去翻文件，發現 opis 有些 function 會回傳 false，解決了一個大麻煩。

# 題目
1. 請將台南市各區的降雨資訊(json file)，解析並儲存進 MySQL database。  
2. 為了練習 SQL Join，請至少設計兩個表格，一個放區域名稱，一個放降雨資訊。  
3. 請從資料庫拿出行政區名稱後，嘗試用 PHP array functions 排序，需與 `app/Core/Database/CollectData.php` 中的 `BASE_DISTRICTS` 排序一致。  
4. 請透過 SQL Join 取得全部行政區 or 單一行政區的各年度總降雨量及各月總降雨量。    
5. 直接繼續完成此專案，並嘗試「只編輯」 `app\Core\Database\DB.php`, `app\Controller\DatabaseContoller.php`, `composer.json` 這三個檔案的程式碼，讓 `index.php` 可以在終端機下正常執行  

---

其他應該沒什麼太大的問題，都有順利解決(吧)，基本上也有符合檢查項目。



