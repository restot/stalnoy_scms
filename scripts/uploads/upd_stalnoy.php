<?php
require_once  dirname(__DIR__).'/settings.php';
require_once  dirname(__DIR__).'/safemysql.php';
require_once  dirname(__DIR__).'/fastsql.php';
$start = microtime(true);
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
$table='last_export';
echo "Load stalnoy to DATABASE ...".EOL;
$db = new SafeMysql(array('user' => SetUp::db_user, 'pass' => SetUp::db_pass, 'db' => SetUp::db_database, 'charset' => 'utf8'));

$xmlf=dirname(dirname(__DIR__))."/output/xml/stalnoy.xml";
$sXml=file_get_contents($xmlf);


// $data_file=dirname(__DIR__)."/output/xml/data.txt";
    $load = new SimpleXMLElement($sXml);
    $test =new fastsql;
    // $data='';
    // $quw='';
    // $tmp='';
$cols=$db->getCol("SELECT COLUMN_NAME
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = ?s
ORDER BY ORDINAL_POSITION", $table);
$del=$db->query("DELETE FROM ?n;", $table);
$hash=(string)$load->attributes()->hash;
$parseCol = '';
        foreach ($cols as $c1 => $c2) {
          // if ($c2=='hash_key'){
          //   continue;
          // }
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
    // $string='';
    $parseVal = '';
    $str=1;
    $itr=1;
    $temparr=array();
    $strarray=array();
    foreach ($catarray as $v1 => $v2) {
        if ($str >= 26) {
             if ( $str==26 || $str==27){
                    $str++;
                   continue;
             }
             //NOTE: Блок считывания характеристик
             if ($itr <= 2) {
                   $itr++;
                   array_push($temparr,htmlspecialchars((string)$v2,ENT_QUOTES | ENT_DISALLOWED));

             } else {
                   array_push($temparr,htmlspecialchars((string)$v2,ENT_QUOTES | ENT_DISALLOWED));
                   array_push($strarray,$temparr);
                   $temparr=array();
                   $itr=1;

             }

} else {
        // $string.=$v2.',,';
        // $test->addCol($v2);
        $parseVal.=$db->parse("?s,", $v2);
}
      if(count($catarray) == $str){
            $strarray=json_encode($strarray, JSON_HEX_AMP | JSON_HEX_APOS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT);
            // $string.=$strarray.',,';
              // $test->addCol($strarray);
              $parseVal.=$db->parse("?s,", $strarray);
      }

$str++;
    }
    $parseVal.=$db->parse("?s,", $hash);
      $parseVal=substr($parseVal, 0, -1);
      $sql =$db->query("INSERT INTO ?n (?p) VALUES (?p)",$table,$parseCol,$parseVal);
      // var_dump($sql);
      // exit();
    // $test->addCol($hash);
    // $test->addRow();
    // $string.=$hash;
    // echo "XML READ_LAST_EXPORT #".$i.EOL;
    // $string.="%%";
    // $data.=$string;

$i++;

}

// $data=substr($data, 0, -2);
// file_put_contents($data_file, $data);
// echo "Load Data".EOL;
// $qu=$db->query("LOAD DATA LOCAL INFILE ?p INTO TABLE ?n FIELDS TERMINATED BY ',,' LINES TERMINATED BY '%%' ($parseCol)", '"'.str_replace('\\', '\\\\', $data_file).'"', $table);
// if ($qu) {
//     echo "Loading complite!".EOL;
// }
// $test->sendData($db,$table,$parseCol);
echo 'DONE'.' Memory usage '.(memory_get_peak_usage(true) / 1024 / 1024).' MB'.EOL;
$time = microtime(true) - $start;
echo 'Upload time ['.round($time, 3).'] сек'.EOL;
// unlink($data_file);
