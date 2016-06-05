<?php
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once __DIR__."/path_handler.php";
require_once __DIR__."/read_xls.php";

if (PHP_SAPI != "cli") {
    exit;
}
if ($argc>1){
 if ($argv[1]=="-force"){
   define(FORCE,true);
 }
}
// $options = $argv;
// var_dump(FORCE);

// exit();

$array=PathHandler::files('/input/xls/*');
$hash_array=PathHandler::hashArray($array);


$file=jsonIO::checkHashFile();

if ($file==NULL || $file=='null'|| $file=='NULL'){
  $status=jsonIO::writeData($hash_array);
  // var_dump($status);
  if ($status!="No error"){
    var_dump($status);
    exit();
  }
}
// var_dump($file);
// 123
// foreach ($hash_array as $key => $value) {   //NOTE: кусок кода для проверки по ключам 1 берется из имеющихся файлов, 2 из файла с хешеми и записями
//   if (array_key_exists($key,$file)){
//     var_dump($file);
//   } else {
//     echo $key."_nope".PHP_EOL;
//   }
// }

$arrayxml=PathHandler::files('/output/xml/*.xml');
// var_dump($arrayxml);
// exit();
foreach ($array as $a => $b) {
  if(array_key_exists($a,$arrayxml)){
    // $hash_array[$a]
    $xmlt=file_get_contents($arrayxml[$a]);
    // var_dump($xmlt);
    $load = new SimpleXMLElement($xmlt);
    // var_dump((string)$load->attributes()->hash);
    // var_dump($hash_array[$a_]);
    $state=0;
    if (FORCE){
      if ($a!="stalnoy" ){
        @unlink($arrayxml[$a]);
        echo "Updatind... [$a]".PHP_EOL;
        readXLS($a,$b);
        $state=1;
      } else {
        @unlink($arrayxml[$a]);
        @unlink($arrayxml[$a."_cater"]);
        echo "Updatind... [$a]".PHP_EOL;
        readXLS($a,$b);
        $state=1;
      }
    }
    if ($hash_array[$a."_hash"]==(string)$load->attributes()->hash ){
      echo "Actual_xml [$a]".PHP_EOL;
      if(!file_exists($arrayxml[$a."_cater"]) && $a=="stalnoy" ) {
        echo "Updatind... [$a]".PHP_EOL;
        readXLS($a."_cater",$b);
          $state=1;
      }
    } else {
      if ($a!="stalnoy" ){
        @unlink($arrayxml[$a]);
        echo "Updatind... [$a]".PHP_EOL;
        readXLS($a,$b);
        $state=1;
      } else {
        @unlink($arrayxml[$a]);
        @unlink($arrayxml[$a."_cater"]);
        echo "Updatind... [$a]".PHP_EOL;
        readXLS($a,$b);
        $state=1;
      }

    }
  } else {
    readXLS($a,$b);
    $state=1;
  }
}
if ($state==1){
  require_once  __DIR__."/upd_last_export_yml.php";
}
// $file=jsonIO::writeData($file);


 ?>
