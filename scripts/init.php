<?php
echo "VERSION 2.3.3.3\n";
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once __DIR__."/path_handler.php";
require_once __DIR__."/read_xls.php";

if (PHP_SAPI != "cli") {
    exit;
}
function gen_yml(){
  // global $state;
  // if ($state==1){
    require_once  __DIR__."/upd_last_export_yml.php";
  // }
  // if ($state==0){
  //   $file_list=glob(dirname(__DIR__)."/output/yml/*.xml");
  //   // var_dump($file_list);
  //   if (count($file_list)==0){
  //       require_once  __DIR__."/upd_last_export_yml.php";
  //   }
  // }
}
if ($argc>1){
  if ($argv[1]=="-nil"){
    echo "Cleaning ...\n";
    @unlink(dirname(__DIR__)."/data/hash.txt");
    @unlink(dirname(__DIR__)."/data/log.txt");
    $file_list=glob(dirname(__DIR__).'/output/xml/*.xml');
    foreach ($file_list as $a => $b) {
      // var_dump($b);
      echo "Remove $b\n";
      unlink($b);

    }
      exit();
  }



 if ($argv[1]=="-force"){
  //  define(FORCE,true);
  $flag=true;
  $type1="force";
  $type="none";
  if ($argv[2]=="-stalnoy"){
    //  define(FORCE,true);
    $flag=true;
    $type="stalnoy";
  }
}  elseif ($argv[1]=="-update"){
  //  define(FORCE,true);
  $flag=true;
  $type1="update";
  $type="none";
  if ($argv[2]=="-stalnoy"){
    //  define(FORCE,true);
    $flag=true;
    $type="stalnoy";
  }
}
elseif ($argv[1]=="-normal"){
  //  define(FORCE,true);
  $flag=true;
  $type1="normal";
  $type="none";
  if ($argv[2]=="-stalnoy"){
    //  define(FORCE,true);
    $flag=true;
    $type="stalnoy";
  }
}
 else {
   $type="none";
   $flag=false;
 }
}
// $options = $argv;
// var_dump($argv);

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
      if ($type1=="force"){
        if ($type=="stalnoy"){
          if ($a =="stalnoy"){
            @unlink($arrayxml[$a]);
            @unlink($arrayxml[$a."_cater"]);
            echo "Updatind... [$a]".PHP_EOL;
            readXLS($a,$b);
          } else {
              continue;
          }



        } elseif ($type=="none") {
          if ($a!="stalnoy" ){
            // echo  "#".__LINE__." ignored $a\n";
            // var_dump($arrayxml[$a]);
            @unlink($arrayxml[$a]);
            echo "Updatind... [$a]".PHP_EOL;
            readXLS($a,$b);
            $state=1;
            continue;
        }
      }
    }
     elseif ($type1=="update"){

        gen_yml();
      }
      elseif ($type1=="normal" && $type=="stalnoy"){
        echo  "#".__LINE__." GO $a\n";

          if ($a =="stalnoy"){
            if ($hash_array[$a."_hash"]==(string)$load->attributes()->hash ){
              echo "Actual_xml [$a]".PHP_EOL;
              continue;} else {
                if ($a=="stalnoy" ){
                  echo  "#".__LINE__." GO $a\n";
                  @unlink($arrayxml[$a]);
                  echo "Updatind... [$a]".PHP_EOL;
                  readXLS($a,$b);
                  // $state=1;
                }
              }

        } else {
          continue;
        }





      } elseif ($type1=="normal" && $type=="none") {
          echo  "#".__LINE__." GO $a\n";
          if ($a!="stalnoy" ){

            if ($hash_array[$a."_hash"]==(string)$load->attributes()->hash ){
              echo "Actual_xml [$a]".PHP_EOL;
              continue;} else {
                if ($a!="stalnoy" ){
                  echo  "#".__LINE__." GO $a\n";
                  @unlink($arrayxml[$a]);
                  echo "Updatind... [$a]".PHP_EOL;
                  readXLS($a,$b);
                  // $state=1;
                }
              }

        }

       }

}
else{
    ECHO "no params \nexit...";
    exit;
    // echo "1111\n";
    // var_dump($hash_array[$a."_hash"]);
    // var_dump((string)$load->attributes()->hash);
    if ($hash_array[$a."_hash"]==(string)$load->attributes()->hash ){
      echo "Actual_xml [$a]".PHP_EOL;
      continue;
      // if(!file_exists($arrayxml[$a."_cater"]) && $a=="stalnoy" ) {
      //   echo "Updatind... [$a]".PHP_EOL;
      //   readXLS($a."_cater",$b);
      //     $state=1;
      // }
    } else {
      if ($a!="stalnoy" ){
        echo "3333\n";
        @unlink($arrayxml[$a]);
        echo "Updatind... [$a]".PHP_EOL;
        readXLS($a,$b);
        $state=1;
      } else {

          echo "22222\n";
      }

    }

  }
}
  else {
    readXLS($a,$b);
    $state=1;
  }
}





 ?>
