<?php
// define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
// define('stalnoy',true);
echo "Please enter KURS BP...\n";
define('KURS',trim(fgets(STDIN)));
// exec("color e");
require_once  dirname(__DIR__)."/path_handler.php";
$array=PathHandler::files('/input/xls/*');
$hash_array=PathHandler::hashArray($array);
$qarray['hash_key']=$hash_array['bp_hash'];
require_once  dirname(__DIR__).'/settings.php';
require_once  dirname(__DIR__).'/safemysql.php';
require_once  dirname(__DIR__).'/fastsql.php';
$table='last_export';
$db = new SafeMysql(array('user' => SetUp::db_user, 'pass' => SetUp::db_pass, 'db' => SetUp::db_database, 'charset' => 'utf8'));

$sXml=file_get_contents(dirname(dirname(__DIR__))."/output/xml/bp.xml");
$load = new SimpleXMLElement($sXml, LIBXML_NOCDATA);
// echo dirname(__FILE__)."/xml/converted.xml";
$cols=$db->getCol("SELECT COLUMN_NAME
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = ?s
ORDER BY ORDINAL_POSITION",$table);
// print_r($cols);
// $parseCol = '';
//         foreach ($cols as $c1 => $c2) {
//           if(
//             $c2=='Цена' ||
//             $c2=='Наличие') {
//             $parseCol.=$db->parse("?n,",$c2);
//             $qarray[$c2]=$c1;
//           }
//         }
// $parseCol=substr($parseCol, 0, -1);
// print_r($cols);

$items=0;
$iter=1;
foreach ($load as $row => $tag) {
  if (
       (string)$tag->col_0=='unset' ||
       (string)$tag->col_0=='Код' ||
       (string)$tag->col_6=='unset' ||
       (string)$tag->col_3=='unset'
       ){
    continue;
  }
  // print_r($load);
  // $pos=strpos((string)$tag->c(string)$tag->col_6ol_6,);
  if ((int)$tag->col_8 != 0) {
    $itemcount='+';
  } else{
    $itemcount='-';
  }
  $uuid=(string)$tag->col_0;
  // var_dump($tag->col_6);
// $qarray['Идентификатор_товара']=(string)$tag->col_1;
// if((int)$tag->col_5!=0){
$qarray['Цена']=round((float)$tag->col_6/KURS,6);
// } else{

      // $qarray['Цена']=0;
// }


$qarray['Наличие']=$itemcount;



// var_dump($hash_array);
// exit();

// $qarray['Валюта']="USD";
// $qarray['date']=date("Y-n-d H:i:s");
// print_r($tag);
// if ((int)$tag->col_0==0){
//   continue;
// }
// break;


// $qarray['name']=iconv('uft8','cp1251',$qarray['name']);
 // $item['description']=$mysqli->real_escape_string($item['description']);
  //  print_r($item);
//   $parseValue='';
//   foreach ($qarray as $i1 => $i2) {
//
//         $parseValue.=$db->parse("?s,",$i2);
//
//   }
// $parseValue=substr($parseValue, 0, -1);

// print_r($qarray);
  $check=$db->getOne("SELECT ?n FROM ?n WHERE ?n=?s","Идентификатор_товара",$table,"Идентификатор_товара",$uuid);
  // var_dump($check);
  if ($check==$uuid){
    $sql = $db->query("UPDATE ?n SET ?u WHERE ?n=?s",$table,$qarray,"Идентификатор_товара",$uuid);

        $items++;
    echo "LOAD_BP#".$iter." Load [".$uuid."]"." available=[".$qarray['Наличие']."]".'['.$qarray['Цена'].']'.EOL;
    // file_put_contents(dirname(__FILE__)."\log.txt","#".$iter." Load [".$uuid."]"." available=[".$qarray['Наличие']."]".'['.$qarray['Цена'].']'.PHP_EOL,FILE_APPEND);
  } else {
    echo "LOAD_BP#".$iter." Check [".$uuid."]".EOL;
    // file_put_contents(dirname(__FILE__)."\log.txt","#".$iter." Check [".$uuid."]".PHP_EOL,FILE_APPEND);
  }
// usleep(300000);
 // unset($item);
    $iter++;


}

echo "Updated items: ".$items.EOL;
// file_put_contents(dirname(__FILE__)."\log.txt","Обновленно принудительно: ".$items.PHP_EOL,FILE_APPEND);
// file_put_contents(dirname(__FILE__)."\log.txt",'DONE'." Memory usage ".(memory_get_peak_usage(true) / 1024 / 1024)." MB".PHP_EOL,FILE_APPEND);



 ?>
