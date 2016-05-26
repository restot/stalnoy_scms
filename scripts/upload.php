<?php
// require_once  __DIR__.'/settings.php';
// require_once  __DIR__.'/safemysql.php';

// $db = new SafeMysql(array('user' => SetUp::db_user, 'pass' => SetUp::db_pass, 'db' => SetUp::db_database, 'charset' => 'utf8'));

class UpdBase
{
    public function prepareData($db)
    {
    }

    public function getCOls(obj $db, string $table,array $filter=NULL)
    {
        $cols = $db->getCol('SELECT COLUMN_NAME
	FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE()
	AND TABLE_NAME = ?s
	ORDER BY ORDINAL_POSITION', $table);
    $parseCol = '';
        foreach ($cols as $c1 => $c2) {
          if (in_array($filter,$c2)){
            echo $c2." col ignored";
            continue;
          }
            $parseCol.=$db->parse("?n,", $c2);
        }
$parseCol=substr($parseCol, 0, -1);
return $parsCol;
    }
}
