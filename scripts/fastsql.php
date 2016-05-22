<?php
#Fast SQL

class fastsql
{
    // var $data_file=__DIR__."/data.txt";
  // var $data='';
    private $data_file = __DIR__."\data.txt";
    private $data;
    // private $string;
// var $string;
  // public function init() {
  //   global $data;
  //   global $data_file;
  // $data_file=dirname(__FILE__)."/data.txt";
  // $data='';
  // }
  public function addCol(string $value)
  {
      // global $data_file;
      // global $string;
      // var_dump($this->string);
      // var_dump($string);
    $this->string .= $value.',,';
      // var_dump($this->string);
  }
    public function addRow()
    {
        // global $string;
    // global $data_file;
    // global $data;
        $this->string = substr($this->string, 0, -2);
        $this->string .= '%%';
        $this->data .= $this->string;
    // file_put_contents($data_file, $string, FILE_APPEND );
    // var_dump($data_file);
    unset($this->string);
    }
    public function sendData($db,$table,$cols)
    {
        // global $data;
    // global $data_file;
        $this->data = substr($this->data, 0, -2);
        // var_dump($this->data_file);
        file_put_contents($this->data_file, $this->data);
        $qu = $db->query("LOAD DATA LOCAL INFILE ?p INTO TABLE ?n FIELDS TERMINATED BY ',,' LINES TERMINATED BY '%%' ($cols)", '"'.str_replace('\\', '\\\\', $this->data_file).'"', $table);
        if ($qu) {
            echo 'Loading complite!'.EOL;
            // unlink($this->data_file);
            unset($this->data);
            unset($qu);
        }
    }
}
