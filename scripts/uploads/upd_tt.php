<?php
// define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
// define('stalnoy',true);
require_once  dirname(__DIR__).'/settings.php';
require_once  dirname(__DIR__).'/safemysql.php';
$array=PathHandler::files('/input/xls/*');
$hash_array=PathHandler::hashArray($array);
$qarray['hash_key']=$hash_array['tt_hash'];
$table='last_export';
$db = new SafeMysql(array('user' => SetUp::db_user, 'pass' => SetUp::db_pass, 'db' => SetUp::db_database, 'charset' => 'utf8'));

$sXml=file_get_contents(dirname(dirname(__DIR__))."/output/xml/tt.xml");
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
  if ((string)$tag->col_1=='Артикул' ||
       (string)$tag->col_1=='unset' ||
       (string)$tag->col_2=='unset' ||
       (string)$tag->col_3=='unset' ||
       (string)$tag->col_4=='unset' ){
    continue;
  }
  // print_r($load);
  $pos=strpos((string)$tag->col_6,'*');
  if ($pos === false) {
    $itemcount='0';
  } else{
    $itemcount='+';
  }
// $qarray['Идентификатор_товара']=(string)$tag->col_1;
$qarray['Цена']=round((float)$tag->col_5,6);
$uuid=(string)$tag->col_1;
$qarray['Наличие']=$itemcount;
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
    echo "LOAD_TT#".$iter." Load [".$uuid."]"." available=[".$qarray['Наличие']."]".EOL;
    // file_put_contents(dirname(__FILE__)."\log.txt","#".$iter." Load [".$uuid."]"." available=[".$qarray['Наличие']."]".PHP_EOL,FILE_APPEND);
  } else {
    echo "LOAD_TT#".$iter." Check [".$uuid."]".EOL;
    //  file_put_contents(dirname(__FILE__)."\log.txt","#".$iter." Check [".$uuid."]".PHP_EOL,FILE_APPEND);
  }
// usleep(300000);
  // unset($item);
    $iter++;


}

echo "Updated items: ".$items.EOL;
// file_put_contents(dirname(__FILE__)."\log.txt","Обновленно принудительно: ".$items.PHP_EOL,FILE_APPEND);
// file_put_contents(dirname(__FILE__)."\log.txt",'DONE'." Memory usage ".(memory_get_peak_usage(true) / 1024 / 1024)." MB".PHP_EOL,FILE_APPEND);






 ?>
