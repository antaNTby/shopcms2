<?php
  #####################################
  # ShopCMS: Скрипт интернет-магазина
  # Copyright (c) by ADGroup
  # http://shopcms.ru
  #####################################
  
  $fileToDownLoad = "core/temp/yandex.xml";
  
  if ( file_exists($fileToDownLoad)) {
    if ( isset ( $_GET["download"] )) {
      header('Content-type: application/force-download');
      header('Content-Transfer-Encoding: Binary');
      header('Content-length: '.filesize($fileToDownLoad));
      header('Content-disposition: attachment; filename='.basename($fileToDownLoad));
      readfile($fileToDownLoad);
    }
    else {
      echo implode("", file($fileToDownLoad));
    }
  }
  
?>