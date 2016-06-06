<?php

Class PathHandler{
  public function hash_log($aa,$bb)
  {
    $text=date("Y-n-d H:i")." | ".$aa." | ".$bb." | "."\n";
    // echo $text;
    file_put_contents(dirname(__DIR__)."/data/log.txt",$text,FILE_APPEND);
  }

public function files($path){
  $path_array=array();
   $file_list=glob(dirname(__DIR__).$path);
  foreach ($file_list as $a => $b) {
    // var_dump($b);
    if(is_dir($b)){
      $temp_path=glob($b."/*.xls*");
      if(count($temp_path)==1){
        preg_match('/.*xls_(.*)/s',$b,$c);
        $path_array[$c[1]]=$temp_path[0];

      }else if(count($temp_path)>1) {
        echo "2 FILES on FOLDER !!!".PHP_EOL;
        var_dump($temp_path);
        exit();
      }
      unset($temp_path,$c);

    } else {
      preg_match('/.*xml\/(.*).xml/Us',$b,$c);
      $path_array[$c[1]]=$b;
    }
  }
  return $path_array;

}

public function hashArray($array)
{
  $hash_array=array();
  foreach ($array as $key => $value) {
    $hash_array[$key."_hash"]=md5_file($value);
    $hash_array[$key."_size"]=filesize($value);
    PathHandler::hash_log($key,$hash_array[$key."_hash"]);
  }
  // var_dump();
  return $hash_array;
}
}

Class jsonIO  {
  public function checkHashFile()
  {
     $file_list=glob(dirname(__DIR__)."\data\hash.txt");
    //  var_dump($file_list);
     if (count($file_list)==0){
        // $data=NULL;
        // var_dump($file_list);
        file_put_contents(dirname(__DIR__)."/data/hash.txt",NULL);
     } else{
       $data=json_decode(file_get_contents($file_list[0]),true);
      //  var_dump($data);
       if ($data==NULL || $data=='null'|| $data=='NULL'){
         return NULL;
       }
     }
     return $data;
  }

  public function writeData($array)
  {
     $file_list=glob(dirname(__DIR__)."\data\hash.txt");
    $json=json_encode($array,JSON_PRETTY_PRINT);
    // var_dump(json_last_error_msg());
    // echo"123\n";
    file_put_contents($file_list[0],$json);
    return json_last_error_msg();
  }

}
