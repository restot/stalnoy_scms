<?php
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once __DIR__."/path_handler.php";
require_once __DIR__."/read_xls.php";

if (PHP_SAPI != "cli") {
    exit;
}
if ($argc>1){
 if ($argv[1]=="-force"){
  //  define(FORCE,true);
  $flag=true;
  $type="force";
}  elseif ($argv[1]=="-update"){
  //  define(FORCE,true);
  $flag=true;
  $type="update";
}
 else $flag=false;
}
// $options = $argv;
// var_dump($argv[1]);

// exit();

$array=PathHandler::files('/input/xls/*');
$hash_array=PathHandler::hashArray($array);


$file=jsonIO::checkHashFile();
// var_dump($file);

// var_dump($hash_array);
if ($file==NULL || $file=='null'|| $file=='NULL' || $file!=$hash_array){
  $status=jsonIO::writeData($hash_array);
  // var_dump($status);
  if ($status!="No error"){
    // var_dump($status);
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
$state=0;
foreach ($array as $a => $b) {
  if(array_key_exists($a,$arrayxml)){
    // $hash_array[$a]
    $xmlt=file_get_contents($arrayxml[$a]);
    // var_dump($xmlt);
    $load = new SimpleXMLElement($xmlt);
    // var_dump((string)$load->attributes()->hash);

// var_dump($flag);
    if ($flag==true){
      if ($type=="force"){
      if ($a!="stalnoy" ){
        // echo "22222\n";
        // var_dump($arrayxml[$a]);
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
      }}
      if ($type=="update"){
        $state=1;
      }
    }
    // echo "1111\n";
    // var_dump($hash_array[$a."_hash"]);
    // var_dump((string)$load->attributes()->hash);
    if ($hash_array[$a."_hash"]==(string)$load->attributes()->hash ){
      echo "Actual_xml [$a]".PHP_EOL;
      if(!file_exists($arrayxml[$a."_cater"]) && $a=="stalnoy" ) {
        echo "Updatind... [$a]".PHP_EOL;
        readXLS($a."_cater",$b);
          $state=1;
      }
    } else {
      if ($a!="stalnoy" ){
        // echo "3333\n";
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
if ($state==0){
  $file_list=glob(dirname(__DIR__)."/output/yml/*.xml");
  var_dump($file_list);
  if (count($file_list)==0){
      require_once  __DIR__."/upd_last_export_yml.php";
  }
}
// $file=jsonIO::writeData($file);


 ?>
