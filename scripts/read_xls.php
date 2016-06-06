<?php
ini_set('memory_limit', '-1');
global $i;

function readXLS($name,$path)
{
echo "Read $name .xlsx to .xml ...".EOL;
require_once dirname(__FILE__) . '\Classes\PHPExcel.php';

$inputFileName = $path;

    require_once dirname(__FILE__) .'/Classes/PHPExcel/IOFactory.php';

    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($inputFileName);
    if ($i==0 && $name=='stalnoy'){

      $i=1;

    }
    if ($name!="stalnoy_cater"){
      $objPHPExcel->setActiveSheetIndex(0);
    } else {
      $objPHPExcel->setActiveSheetIndex(1);
    }

    $aSheet = $objPHPExcel->getActiveSheet();

$xml=new domDocument("1.0", "utf-8");
$xml->formatOutput = true;
$time=date("Y-n-d H:i");
$array = array();

$root = $xml->createElement("root");
$xml->appendChild($root);
$root->setAttribute("date", $time);;
$root->setAttribute("hash", md5_file($path));
$r=0;

foreach($aSheet->getRowIterator() as $row){

  $xrow = $xml->createElement("row_".$r);
  $root->appendChild($xrow);

  $cellIterator = $row->getCellIterator();
  $cellIterator->setIterateOnlyExistingCells(false);
  $item = array();
  $c=0;

  foreach($cellIterator as $cell){
    $scell=($cell->getCalculatedValue()==NULL)?'unset':$cell->getCalculatedValue();
    array_push($item, $scell);
    $col = $xml->createElement("col_".$c,htmlspecialchars($scell, ENT_COMPAT));
    $xrow->appendChild($col);
    $c++;
  }

  // echo "READ_XLS_$name#$r", EOL;
  // var_dump($path);
  $r++;
}

$xml->save(dirname(__DIR__) . "/output/xml/".$name.".xml");

echo 'DONE'." Memory usage ".(memory_get_peak_usage(true) / 1024 / 1024)." MB".EOL;
echo "UPD DATABASE $name\n";
require_once dirname(__DIR__)."/scripts/uploads/upd_".$name.".php";
// sleep(1);
if($i==1){
  echo "Updatind... [stalnoy_cater]".PHP_EOL;
  readXLS("stalnoy_cater",$path);
  echo "UPD DATABASE stalnoy_cater";
  require_once dirname(__DIR__)."/scripts/uploads/upd_stalnoy_cater.php";
  $i=0;
}
}


 ?>
