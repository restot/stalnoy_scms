<?php
require_once __DIR__."/path_handler.php";




$array=PathHandler::files();
$hash_array=PathHandler::hashArray($array);
// var_dump($array);
// var_dump(PathHandler::hashArray($array));

$file=jsonIO::checkHashFile();
var_dump($file);
if ($file==NULL){
  $status=jsonIO::writeData($hash_array);
  if ($status!="No error"){
    var_dump($status);
    exit();
  }
}

 ?>
