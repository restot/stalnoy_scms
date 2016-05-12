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

}
