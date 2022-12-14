<?php
// $array = [
//     '南區', '北區', '安平區', '左鎮區', '仁德區', '關廟區', '官田區', '麻豆區', '佳里區', '西港區', '七股區', '將軍區', '學甲區',
//     '北門區', '新營區', '後壁區', '白河區', '東山區', '下營區', '柳營區', '鹽水區', '山上區', '安定區',
// ];
const BASE_DISTRICTS = [
    '南區', '北區', '安平區', '左鎮區', '仁德區', '關廟區', '官田區', '麻豆區', '佳里區', '西港區', '七股區', '將軍區', '學甲區',
    '北門區', '新營區', '後壁區', '白河區', '東山區', '下營區', '柳營區', '鹽水區', '山上區', '安定區',
];
$conarr=BASE_DISTRICTS;

// $serialize_data=serialize($array);
// echo $serialize_data;
// $rearr = unserialize($serialize_data);
// var_dump($rearr);
// file_put_contents('districts.json',$rearr);

// $fp = fopen('districts.json', 'w');
// fwrite($fp, print_r($array, true));

// $districts_json=json_encode($conarr);
//  print_r($districts_json);
// $fp = fopen('districts.json', 'w');
// fwrite($fp, print_r($districts_json, true));
// json_decode("districts.json");

// echo file_get_contents("districts.json");
// $districts_json_read = json_decode(file_get_contents(“districts.json"));
// print_r($client_json_read);

//rainfalls table
// $result = $this->db->from('rainfalls')
// ->select()
// ->all();
// if(!$result){
foreach (glob("./rainfallData/*.json") as $filename) {
   $arr[]=$filename;
}
//讀取json文件內容
$j=count($arr);
var_dump($arr);
$str0=[];
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
     $str0=  $str; 
foreach($str0 as $key=>$value){
    echo $key.$value;
}
// foreach($data as $key=>$rain){
// $result=$this->db->insert(array(
//    'districts' => $str,
//    'datetime' => $key,
//    'rain' => $rain
// ))
// ->into('rainfalls');
// }}

// <?php
// $a1=array("a"=>"red","b"=>"green","c"=>"blue","d"=>"yellow");
// $a2=array("e"=>"red","f"=>"green","g"=>"blue");

// $result=array_intersect($a1,$a2);
// print_r($result);
}