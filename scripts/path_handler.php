<?php

Class PathHandler{

public function files(){
  $path_array=array();
   $file_list=glob(dirname(__DIR__)."/input/xls/*");
  foreach ($file_list as $a => $b) {
    // var_dump($b);
    if(is_dir($b)){
      $temp_path=glob($b."/*");
      if(count($temp_path)==1){
        preg_match('/.*xls_(.*)/s',$b,$c);
        $path_array[$c[1]]=$temp_path[0];
      }
      unset($temp_path,$c);

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
  }
  // var_dump();
  return $hash_array;
}
}

Class jsonIO  {
  public function checkHashFile()
  {
     $file_list=glob(dirname(__DIR__)."/data/*");
    //  var_dump($file_list);
     if (count($file_list)==0){
        $data=NULL;
        var_dump($file_list);
        file_put_contents(dirname(__DIR__)."/data/hash.txt",$data);
     } else{
       $data=json_decode(file_get_contents($file_list[0]));
     }
     return $data;
  }

  public function writeData($array)
  {
    $json=json_encode($array);
    // var_dump(json_last_error_msg());
    file_put_contents(dirname(__DIR__)."/data/hash.txt",$json);
    return json_last_error_msg();
  }

}
