<?php

$strJsonFileContents = file_get_contents("/home/lj/PhpstormProjects/backendtraining-t4/rainfallData/test.json");
$array = json_decode($strJsonFileContents, true);
var_dump($array);
//echo gettype($array);

?>