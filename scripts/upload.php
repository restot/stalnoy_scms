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
      $fdata=array();
      foreach ($data as $key => $value) {
        // $fdata[$value[0]];
        // var_dump($value);
        $fdata[$value['Код_товара']]=array();
          $tarr=array();
      foreach ($value as $a => $b) {

        if($a=='Код_товара'){
          continue;
        }
        $fdata[$value['Код_товара']][$a]=$b;
        // array_push(  $fdata[$value['Код_товара'][$a]],$b);
      }
    // array_push($fdata,$tarr);
    // unset($tarr);
      }
      // $darr=
      // var_dump($data);
      return $fdata;
    }

    public function prepareData2($db,array $cols,$table)
        {
          $parseCol = '';
            foreach ($cols as $c1 => $c2) {
                $parseCol.=$db->parse("?n,", $c2);
            }
          $parseCol=substr($parseCol, 0, -1);
          $data=$db->getAll("SELECT ?p FROM ?n ",$parseCol,$table);
          $fdata=array();
          foreach ($data as $key => $value) {
            // $fdata[$value[0]];
            // var_dump($value);
            // $fdata[$value['Код_товара']]=array();
              $tarr=array();
          foreach ($value as $a => $b) {

            // if($a=='Код_товара'){
            //   continue;
            // }
            // array_push($fdata[$value['Код_товара']],$b);
            array_push($tarr,$b);
          }
        array_push($fdata,$tarr);
        unset($tarr);
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

    // public

    public function updateData($extdata,$intdata,$data) # NOTE: $intdata => internal data;
    {

    $this->fulldata=$intdata;

     foreach ($extdata as $key => $value) {
    // var_dump($extdata);
      if(array_key_exists($value[0],$intdata)){
          // var_dump($value);
        $intdata[$value[0]]['Цена']=$value['Цена'];
        $intdata[$value[0]]['Наличие']=$value['Наличие'];
      }
     }
    //  var_dump($intdata);
    //  exit();
  $this->intdata=$intdata;


    //  var_dump($this->fulldata);



    //  return $out;
         $out=array_map(array('UpdBase', 'diff'),$data);
         $out=array_unique($out);
         var_dump($out);
    }
         public function diff()
         {
          //  var_dump(func_get_args());
           $a=func_get_args();
          //  var_dump($a);
           $b=$a[0];
          //  var_dump($fulldata[$a[0]]);
            // GLOBAL  $fulldata;
          //  var_dump($this->intdata[$b[0]]);
          //  var_dump($this->fulldata[$b[0]]);

          //  exit();
           $res[$b[0]]=array_diff_assoc($this->intdata[$b[0]],$this->fulldata[$b[0]]);
            // var_dump($res);
            // exit();

           return $res;
         }
}
