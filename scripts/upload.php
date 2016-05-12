<?php
require_once  __DIR__.'/settings.php';
require_once  __DIR__.'/safemysql.php';

$db = new SafeMysql(array('user' => SetUp::db_user, 'pass' => SetUp::db_pass, 'db' => SetUp::db_database, 'charset' => 'utf8'));

Class UpdBase {

  public function prepareData()
  {
    # code...
  }

}
