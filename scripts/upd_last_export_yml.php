<?php
require_once  __DIR__.'/settings.php';
require_once  __DIR__.'/safemysql.php';
// require_once  dirname(__DIR__).'/fastsql.php';
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
$db = new SafeMysql(array('user' => SetUp::db_user, 'pass' => SetUp::db_pass, 'db' => SetUp::db_database, 'charset' => 'utf8'));
// TODO: all it work
// TODO: 123

$dbcategories=$db->getAll("SELECT * FROM ?n ORDER BY ?n ASC","last_export_categories","Название_группы");

$dboffers=$db->getAll("SELECT * FROM ?n ","last_export");

$xml=new domDocument("1.0", "utf-8"); // Создаём XML-документ версии 1.0 с кодировкой utf-8
$xml->formatOutput = true;

$time=date("Y-n-d H:i");
$yml_catalog = $xml->createElement("yml_catalog");
$xml->appendChild($yml_catalog);
$yml_catalog->setAttribute("date", $time);
$shop = $xml->createElement("shop");
$yml_catalog->appendChild($shop);
  $name = $xml->createElement("name", 'Stalnoy');
  $shop->appendChild($name);
  $company = $xml->createElement("company", 'Stalnoy');
  $shop->appendChild($company);
  $url = $xml->createElement("url", 'http://stalnoy.org/');
  $shop->appendChild($url);
  $email = $xml->createElement("email", 'vapst@yandex.ru');
  $shop->appendChild($email);
  $currencies=$xml->createElement("currencies");
  $shop->appendChild($currencies);
  $currency = $xml->createElement("currency");
  $currency->setAttribute("id","EUR");
  $currency->setAttribute("rate","1");
  $currencies->appendChild($currency);
    $currency = $xml->createElement("currency");
    $currency->setAttribute("id","UAH");
    $currency->setAttribute("rate","1");
    $currencies->appendChild($currency);
      $currency = $xml->createElement("currency");
      $currency->setAttribute("id","USD");
      $currency->setAttribute("rate","23.1");
      $currencies->appendChild($currency);
  $categories = $xml->createElement("categories");
  $shop->appendChild($categories);

foreach ($dbcategories as $c1 => $c2) {
  $category = $xml->createElement("category",$c2['Название_группы']);
  $categories->appendChild($category);


   if ($c2['Идентификатор_группы']!=='unset'){
       $category->setAttribute("id",$c2['Идентификатор_группы']);
   } else{
       $category->setAttribute("id",$c2['Номер_группы']);
     }




  if ($c2['Номер_родителя']!=='unset' && $c2['Идентификатор_родителя']!=='unset'){
    if ($c2['Идентификатор_родителя']!=='unset'){
      $category->setAttribute("parentId",$c2['Идентификатор_родителя']);
    } else{
      $category->setAttribute("parentId",$c2['Номер_родителя']);
    }

  }

  $category->setAttribute("portal_id",$c2['idProm']);
}

$offers = $xml->createElement("offers");
$shop->appendChild($offers);
$i=1;
foreach ($dboffers as $dbk => $dbv) {

  if (!1){
    continue;
  } else {



  $offer = $xml->createElement("offer");
  $offers->appendChild($offer);
    if ($dbv['Идентификатор_товара']!='unset') {
      $uuid=$dbv['Идентификатор_товара'];
    } else {
      $uuid=$dbv['Код_товара'];
    }
  $offer->setAttribute("id",$uuid);

  if ($dbv['Наличие']=='+' || $dbv['Наличие']=='true'){
    $offer->setAttribute("available","true");
  } elseif (($dbv['Наличие']=="false" || $dbv['Наличие']=='-' || $dbv['Наличие']==' ')) {
    $offer->setAttribute("available","");
  } else {
    $offer->setAttribute("available","false");
  }

  $offer->setAttribute("selling_type",$dbv['Тип_товара']);

  $name = $xml->createElement("name", htmlspecialchars($dbv['Название_позиции']));
  $offer->appendChild($name);
  if($dbv['Идентификатор_группы']!=='unset'){
    $category_offer = $xml->createElement("categoryId",$dbv['Идентификатор_группы']);
  } else{
    $category_offer = $xml->createElement("categoryId",$dbv['Номер_группы']);
  }

  $offer->appendChild($category_offer);

  foreach ($dbcategories as $k1 => $k2) {
    if ($dbv['Идентификатор_группы']==$k2['Идентификатор_группы']){
      $portal_category_id_query=$k2['idProm'];
      break;
    }
     elseif ($dbv['Номер_группы']==$k2['Номер_группы']) {
      $portal_category_id_query=$k2['idProm'];
     }else {
      $portal_category_id_query='';
    }
  }

  $portal_category_id = $xml->createElement("portal_category_id",$portal_category_id_query);
  $offer->appendChild($portal_category_id);

  $price = $xml->createElement("price", round($dbv['Цена'],6));
  $offer->appendChild($price);

  if ($dbv['Ссылка_изображения']!='unset'){
  $pictarr=explode(',',$dbv['Ссылка_изображения']);
  foreach ($pictarr as $p1 => $p2) {
    $picture= $xml->createElement("picture",trim($p2));
    $offer->appendChild($picture);
  }

  }

  $currencyId=$xml->createElement("currencyId",$dbv['Валюта']);
  $offer->appendChild($currencyId);

  $vendor = $xml->createElement("vendor",$dbv['Производитель']);
  $offer->appendChild($vendor);

  $vendorCode = $xml->createElement("vendorCode",$dbv['Код_товара']);
  $offer->appendChild($vendorCode);

if ($dbv['Страна_производитель']!='unset'){
  $country = $xml->createElement("country",$dbv['Страна_производитель']);
  $offer->appendChild($country);
}

if ($dbv['Описание']!='Код_товара'){
  $description = $xml->createElement("description",htmlspecialchars($dbv['Описание'], ENT_COMPAT));
  $offer->appendChild($description);
}

if($dbv['Характеристики']!='[]'){

      // $paramsarry=str_replace('u0022',"''",$dbv['Характеристики']);
      // var_dump($paramsarry);
      // exit();
  $paramsarry=json_decode($dbv['Характеристики']);

  //
  if(!is_array($paramsarry)){
        var_dump($dbv['Характеристики']);
        // var_dump($dbv['Характеристики']);
        var_dump($paramsarry);


        switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - Ошибок нет';
        break;
        case JSON_ERROR_DEPTH:
            echo ' - Достигнута максимальная глубина стека';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Некорректные разряды или не совпадение режимов';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Некорректный управляющий символ';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Синтаксическая ошибка, не корректный JSON';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Некорректные символы UTF-8, возможно неверная кодировка';
        break;
        default:
            echo ' - Неизвестная ошибка';
        break;
    }
 exit();


 }
  foreach ($paramsarry as $g1 => $g2) {


     if ($g2[0]!="unset"){
      //      var_dump($g2);
      //      exit();
      if ($g2[1]="unset"){
            // var_dump($g2[0]);

            if (strlen(htmlentities($g2[2], ENT_XML1 | ENT_COMPAT))>180){
              $g2[2]=preg_replace('/(?!\s.*\s)\s.*$/s'," ",substr(htmlentities($g2[2], ENT_XML1 | ENT_COMPAT),0,180),1);
            }
        $param = $xml->createElement("param",htmlentities($g2[2], ENT_XML1 | ENT_COMPAT));
        $param->setAttribute("name",htmlentities($g2[0], ENT_XML1 | ENT_COMPAT));
        $offer->appendChild($param);
      } else {
        if (strlen(htmlentities($g2[2], ENT_XML1 | ENT_COMPAT))>180){
          $g2[2]=preg_replace('/(?!\s.*\s)\s.*$/s'," ",substr(htmlentities($g2[2], ENT_XML1 | ENT_COMPAT),0,180),1);
        }
        $param = $xml->createElement("param",htmlentities($g2[2], ENT_XML1 | ENT_COMPAT));
        $param->setAttribute("name",htmlentities($g2[0], ENT_XML1 | ENT_COMPAT));
        $param->setAttribute("unit",htmlentities($g2[1], ENT_XML1 | ENT_COMPAT));
        $offer->appendChild($param);
      }
      unset($str);
    } else {
      continue;
    }
    unset($g2);
  }

// var_dump($paramsarry);
}
// if($dbv['Код_товара']=="HT-1001"){
//       var_dump($paramsarry);
//            exit();
// }


echo "GEN_YML#$i "."[".$dbv['Код_товара']."]".EOL;
$i++;

  // break;
// usleep(10000);

}
}








$xml->save(dirname(__DIR__)."/output/yml/stalnoy_yml.xml");




 ?>
