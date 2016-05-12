<?php
define('stalnoy',true);

include dirname(dirname(__FILE__)).'/connect_mysql/settings.php';
include dirname(dirname(__FILE__)).'/connect_mysql/safemysql.php';
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
$table='last_export';
$db = new SafeMysql(array('user' => $db_user, 'pass' => $db_pass, 'db' => $db_database, 'charset' => 'utf8'));
// $mysqli = new mysqli("localhost", $db_user, $db_pass, "stalnoy");
// $load=simplexml_load_file(dirname(dirname(dirname(__FILE__))) . "/buckup_stalnoy/yandex_market3.xml",LIBXML_NOCDATA);
$xmlf=dirname(dirname(__FILE__))."/xml/last_export.xml";
$sXml=file_get_contents($xmlf);
// var_dump($sXml);
$data_file=dirname(__FILE__)."\\tmp\data.txt";
    $load = new SimpleXMLElement($sXml);
$data='';
    $quw='';
    $tmp='';
$cols=$db->getCol("SELECT COLUMN_NAME
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = ?s
ORDER BY ORDINAL_POSITION", $table);
// print_r($cols);
$parseCol = '';
        foreach ($cols as $c1 => $c2) {
            $parseCol.=$db->parse("?n,", $c2);
        }
$parseCol=substr($parseCol, 0, -1);
$i=1;
$s=0;
$ii=0;

$del=$db->query("DELETE FROM ?n;", $table);
foreach ($load as $key => $catarray) {
    if ((string)$catarray->col_0=='Код_товара') {
        continue;
    }

 // print_r($catarray);
 // break;
 $string='';
  // $parseVal = '';
    $str=1;
    $itr=1;
    $temparr=array();
    $strarray=array();
    foreach ($catarray as $v1 => $v2) {
      //     var_dump($catarray);
      //     exit();
        if ($str >= 26) {
             if ( $str==26 || $str==27){
                    $str++;
                   continue;
             }
// var_dump($v2);
             //NOTE: Блок считывания характеристик
             if ($itr <= 2) {
                   $itr++;
                  // $tstr=$v2;
                   array_push($temparr,htmlspecialchars((string)$v2,ENT_QUOTES | ENT_DISALLOWED));


             } else {
                   array_push($temparr,htmlspecialchars((string)$v2,ENT_QUOTES | ENT_DISALLOWED));
                   array_push($strarray,$temparr);
                   $temparr=array();
                   $itr=1;

                  //  var_dump($temparr);
                  //  exit();



             }





} else {
        $string.=$v2.',,';

}
      if(count($catarray) == $str){
            $strarray=json_encode($strarray, JSON_HEX_AMP | JSON_HEX_APOS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT);
            $string.=$strarray.',,';
      }

$str++;
    }
    // $parseVal=substr($parseVal, 0, -1);
    // $tmp.="($parseVal),";
    $string=substr($string, 0, -2);
    echo "XML READ_LAST_EXPORT #".$i.EOL;
    $string.="%%";
    $data.=$string;
  // echo $parseVal.PHP_EOL;
  // $sql =$db->query("INSERT INTO ?n (?p) VALUES (?p)",$table,$parseCol,$parseVal);
// echo "#".$i.PHP_EOL;
$i++;
//     $ii++;
//     $s++;
//     if ($ii==100) {
//         $quw.="INSERT INTO `$table` ($parseCol) VALUES ";
//         $tmp=substr($tmp, 0, -1);
//         $quw.=$tmp.";";
//       // echo $quw;
//       $sql=$db->query("?p", $quw);
//         if ($sql) {
//             echo "XLS_STALNOY Load!(#$s)".EOL;
//       // ob_flush();
//       // flush();
//         }
//         $quw='';
//         $tmp='';
//         $ii=0;
// // usleep(100);
//     }

// if ($s==100){
//       exit();
//
// }
}
$start = microtime(true);
$data=substr($data, 0, -2);
file_put_contents($data_file, $data);
echo "Load Data".EOL;
$qu=$db->query("LOAD DATA LOCAL INFILE ?p INTO TABLE ?n FIELDS TERMINATED BY ',,' LINES TERMINATED BY '%%'", '"'.str_replace('\\', '\\\\', $data_file).'"', $table);
if ($qu) {
    echo "Loading complite!".EOL;
}
// $quw.="INSERT INTO `$table` ($parseCol) VALUES ";
// $tmp=substr($tmp, 0, -1);
// $quw.=$tmp.";";
// // echo $quw;
// $sql=$db->query("?p", $quw);
// if ($sql) {
//     echo "Load!(#$s)".EOL;
// // ob_flush();
// // flush();
// }

echo 'DONE'.' Memory usage '.(memory_get_peak_usage(true) / 1024 / 1024).' MB'.EOL;
$time = microtime(true) - $start;
echo 'Время загрузки ['.round($time, 3).'] сек'.EOL;
unlink($data_file);
// unlink($xmlf);
