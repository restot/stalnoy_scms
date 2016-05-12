<?php
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once __DIR__."/path_handler.php";
require_once __DIR__."/read_xls.php";



$array=PathHandler::files('/input/xls/*');
$hash_array=PathHandler::hashArray($array);


$file=jsonIO::checkHashFile();

if ($file==NULL){
  $status=jsonIO::writeData($hash_array);
  if ($status!="No error"){
    var_dump($status);
    exit();
  }
}
// var_dump($array);

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
    if ($hash_array[$a."_hash"]==(string)$load->attributes()->hash){
      echo "Actual_xml [$a]".PHP_EOL;
      if(!file_exists($arrayxml[$a."_cater"]) && $a=="stalnoy") {
        readXLS($a."_cater",$b);
      }
    } else {
      if ($a!="stalnoy"){
        @unlink($arrayxml[$a]);
        readXLS($a,$b);
      } else {
        @unlink($arrayxml[$a]);
        @unlink($arrayxml[$a."_cater"]);
        readXLS($a,$b);
      }

    }
  } else {
    readXLS($a,$b);
  }
}

 ?>
