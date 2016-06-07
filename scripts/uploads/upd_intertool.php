<?php
// define('stalnoy',true);
require_once  dirname(__DIR__).'/settings.php';
require_once  dirname(__DIR__).'/safemysql.php';
// define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
$table='last_export';
$db = new SafeMysql(array('user' => SetUp::db_user, 'pass' => SetUp::db_pass, 'db' => SetUp::db_database, 'charset' => 'utf8'));
// $mysqli = new mysqli("localhost", $db_user, $db_pass, "stalnoy");
// $load=simplexml_load_file(dirname(dirname(dirname(__FILE__))) . "/buckup_stalnoy/yandex_market3.xml",LIBXML_NOCDATA);
$sXml=file_get_contents("http://intertool.ua/xml_output/yandex_market.xml");
$load = new SimpleXMLElement($sXml, LIBXML_NOCDATA);

$cols=$db->getCol("SELECT COLUMN_NAME
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = ?s
ORDER BY ORDINAL_POSITION",$table);
// print_r($cols);
$parseCol = '';
        foreach ($cols as $c1 => $c2) {
          if($c2!='uID') {
            $parseCol.=$db->parse("?n,",$c2);
            $narray[$c1]=$c2;
          }
        }
$parseCol=substr($parseCol, 0, -1);
// print_r($narray);

$items=0;
$iter=0;
foreach ($load->shop->offers->offer as $row => $tag) {
  $catarray=(array)$tag->attributes();
  // var_dump($tag);
  // // exit();
  // if (1){
  //       continue;
  // }
  $item=array();
  $parray=array();
  foreach ($catarray['@attributes'] as $key => $value) {
    $item[$key]=$value;
    unset($key);
    unset($value);
  }


   foreach ($tag as $key => $value) {

     if (count((array)$value)>1){
       $barray=(array)$value;
       foreach ($barray['@attributes'] as $k => $v) {
         $tarray[$k]=$v;
       }
      //  print_r($tarray);
       $tarray[0]=$barray[0];
       array_push($parray,$tarray);
       unset($tarray,$barray,$k,$v);


     } else {
       $item[$key]=(string)$value;
     }

   }
   // $item['params']=json_encode($parray);
  //  print_r($parray);

// var_dump($item);
// var_dump($narray);
// foreach ($narray as $ka1=> $ka2) {
//   if(!isset($item[$ka2])){
//     $qarray[$ka2]='unset';
//   } else {
//     $qarray[$ka2]=$item[$ka2];
//   }
//
// }
// $qarray['date']=date("Y-n-d H:i:s");
$qarray['Цена']=round($item['price'],6);
$qarray['Идентификатор_товара']=$item['id'];
$qarray['Наличие']=($item['available']!='true')?'0':'+';
  // $qarray['name']=iconv('uft8','cp1251',$qarray['name']);
  // $item['description']=$mysqli->real_escape_string($item['description']);
  //  print_r($item);
  $parseValue='';
  foreach ($qarray as $i1 => $i2) {

        $parseValue.=$db->parse("?s,",$i2);

  }
$parseValue=substr($parseValue, 0, -1);

// var_dump($qarray);
  $check=$db->getOne("SELECT ?n FROM ?n WHERE ?n=?s","Идентификатор_товара",$table,"Идентификатор_товара",$qarray['Идентификатор_товара']);
  // var_dump($check);
  // exit();
  if ($check==$qarray['Идентификатор_товара']){
        $sql = $db->query("UPDATE ?n SET ?u WHERE ?n=?s",$table,$qarray,"Идентификатор_товара",$qarray['Идентификатор_товара']);
        $stat=($sql===true)?'UPD':'ERR_UPD';
        $items++;
        echo "LOAD_INTER#".$iter." Load [".$qarray['Идентификатор_товара']."]"." Status=[".$qarray['Наличие']."]".EOL;
        // file_put_contents(dirname(__FILE__)."\log.txt","#".$iter." Load [".$qarray['Идентификатор_товара']."]"." Status=[".$qarray['Наличие']."]".PHP_EOL,FILE_APPEND);
  } else {
        echo "LOAD_INTER#".$iter." Check [".$qarray['Идентификатор_товара']."]".EOL;
        // file_put_contents(dirname(__FILE__)."\log.txt","#".$iter." Check [".$qarray['Идентификатор_товара']."]".PHP_EOL,FILE_APPEND);
  }
  // usleep(300000);
  unset($item);
    $iter++;

}

$res=$iter-$items;
echo "Updated items: ".$items.EOL;
echo "New items: ".$res.EOL;
// file_put_contents(dirname(__FILE__)."\log.txt","Обновленно принудительно: ".$items.PHP_EOL,FILE_APPEND);
// file_put_contents(dirname(__FILE__)."\log.txt","Новых товаров: ".$iter-$items.PHP_EOL,FILE_APPEND);
// file_put_contents(dirname(__FILE__)."\log.txt",'DONE'." Memory usage ".(memory_get_peak_usage(true) / 1024 / 1024)." MB".PHP_EOL,FILE_APPEND);
 ?>
