<?php
// require_once  __DIR__.'/settings.php';
// require_once  __DIR__.'/safemysql.php';

// $db = new SafeMysql(array('user' => SetUp::db_user, 'pass' => SetUp::db_pass, 'db' => SetUp::db_database, 'charset' => 'utf8'));

class UpdBase
{
    public function prepareData($db,array $cols,$table)
    {
      $parseCol = '';
        foreach ($cols as $c1 => $c2) {
            $parseCol.=$db->parse("?n,", $c2);
        }
      $parseCol=substr($parseCol, 0, -1);
      $data=$db->getAll("SELECT ?p FROM ?n ",$parseCol,$table);

      foreach ($data as $key => $value) {
        // $fdata[$value[0]];
        // var_dump($value);
        $fdata[$value['Код_товара']]=array();
      foreach ($value as $a => $b) {
        if($a=='Код_товара'){
          continue;
        }
        array_push($fdata[$value['Код_товара']],$b);
      }

      }
      // $darr=
      // var_dump($data);
      return $fdata;
    }

    public function getCOls( $db, string $table,$filter=NULL)
    {
      var_dump($filter);
        $cols = $db->getCol('SELECT COLUMN_NAME
	FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE()
	AND TABLE_NAME = ?s
	ORDER BY ORDINAL_POSITION', $table);
    $parseCol = '';
        foreach ($cols as $c1 => $c2) {
          if ( $filter!=NULL && in_array($c2,(is_array($filter)?$filter:(array)$filter))){
            echo $c2." col ignored\n";
            continue;
          }
            $parseCol.=$db->parse("?n,", $c2);
        }
$parseCol=substr($parseCol, 0, -1);
return $parseCol;
    }

    public function updateData($extdata,$intdata)
    {
     
    }
}
