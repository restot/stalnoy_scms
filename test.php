<?php

require_once  __DIR__.'\scripts\settings.php';
require_once  __DIR__.'\scripts\safemysql.php';
require_once  __DIR__.'\scripts\upload.php';
echo("1ываsdf"."\n");
$db = new SafeMysql(array('user' => SetUp::db_user, 'pass' => SetUp::db_pass, 'db' => SetUp::db_database, 'charset' => 'utf8'));
// echo "string"; NOTE: neon test
// require_once __DIR__.'/neon.php';
//
// $file=file_get_contents(__DIR__."/test.neon");
// // var_dump($input);
// $neon = new Neon;
// $out=$neon->decode($file);
// var_dump($out);
//
echo("2"."\n");
// NOTE: SetUp Class test
//
// require_once __DIR__.'/settings.php';
//
// NOTE: work
// var_dump(SetUP::db_host);
$test= new UpdBase;
$table="last_export";
$q=$test->getCOls($db,$table,'hash_key');
$cols= array('Код_товара','Цена','Наличие' );
$d=$test->prepareData($db,$cols,$table);
$c=array_key_exists('DT-5001',$d);

var_dump($c);
