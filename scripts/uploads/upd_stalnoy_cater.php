<?php
require_once  dirname(__DIR__).'/settings.php';
require_once  dirname(__DIR__).'/safemysql.php';
$array=PathHandler::files('/input/xls/*');
$hash_array=PathHandler::hashArray($array);
$qarray['hash_key']=$hash_array['stalnoy_hash'];

$table='last_export_categories';
$db = new SafeMysql(array('user' => SetUp::db_user, 'pass' => SetUp::db_pass, 'db' => SetUp::db_database, 'charset' => 'utf8'));

$xmlf=dirname(dirname(__DIR__))."/output/xml/stalnoy_cater.xml";
$sXml=file_get_contents($xmlf);
$start = microtime(true);
echo "Load stalnoy_cater to DATABASE ...".PHP_EOL;
    $load = new SimpleXMLElement($sXml);
    $data='';
    $quw='';
    $tmp='';
$cols=$db->getCol("SELECT COLUMN_NAME
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = ?s
ORDER BY ORDINAL_POSITION",$table);

$data_file=dirname(dirname(__DIR__))."/output/xml/data.txt";
$del=$db->query("DELETE FROM ?n;", $table);
$hash=(string)$load->attributes()->hash;

$parseCol = '';
        foreach ($cols as $c1 => $c2) {
          if($c2!='parentId' &&
          $c2!='idProm' &&
          // $c2!='uId' &&
          $c2!='Категория1' &&
          $c2!='Категория2' &&
          $c2!='Категория3' &&
          $c2!='Категория4'  ) {
            $parseCol.=$db->parse("?n,",$c2);
          }

        }
$parseCol=substr($parseCol, 0, -1);
// var_dump($parseCol);
// exit();
$i=1;
$ii=0;

$del=$db->query("DELETE FROM ?n;",$table);

foreach ($load as $key => $catarray) {
  if ((string)$catarray->col_0=='Номер_группы'){
    continue;
  }
$string='';

  $parseVal = '';
  foreach ($catarray as $v1 => $v2) {
      $parseVal.=$db->parse("?s,",$v2);

    // $string.=$v2.',,';

  }
  $parseVal.=$db->parse("?s,",$hash);
  $parseVal=substr($parseVal, 0, -1);
$sql =$db->query("INSERT INTO ?n (?p) VALUES (?p)",$table,$parseCol,$parseVal);

    // $string.=$hash;
    // $string.="%%";
    // echo "XML READ_LAST_EXPORT #".$i.PHP_EOL;
    //
    // $data.=$string;

  $i++;
  $ii++;


}



// $data=substr($data, 0, -2);
// file_put_contents($data_file, $data);
// echo "Load Data".PHP_EOL;
// $qu=$db->query("LOAD DATA LOCAL INFILE ?p INTO TABLE ?n  FIELDS TERMINATED BY ',,' LINES TERMINATED BY '%%' ($parseCol)", '"'.str_replace('\\', '\\\\', $data_file).'"', $table);
// if ($qu) {
//     echo "Loading complite!".PHP_EOL;
// }

echo 'DONE'.' Memory usage '.(memory_get_peak_usage(true) / 1024 / 1024).' MB'.PHP_EOL;
$time = microtime(true) - $start;
echo 'Upload time ['.round($time, 3).'] сек'.PHP_EOL;
// unlink($data_file);

echo "Load Done!".PHP_EOL;
$updprom=$db->query("UPDATE last_export_categories INNER JOIN categories_template ON last_export_categories.Номер_группы=categories_template.Номер_группы
SET last_export_categories.idProm=categories_template.idProm
WHERE last_export_categories.Номер_группы=categories_template.Номер_группы;");
$t=($updprom)?' Done!':' Fail...';
echo "Update Prom_cat".$t.PHP_EOL;
 ?>
