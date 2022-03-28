<?php
  #####################################
  # ShopCMS: Скрипт интернет-магазина
  # Copyright (c) by ADGroup
  # http://shopcms.ru
  #####################################

  $JsHttpRequest = & new JsHttpRequest(DEFAULT_CHARSET);
  
  if ( isset ( $_GET["cpradd"] )) {
    if ( !isset ( $_SESSION["comparison"] ) || !is_array($_SESSION["comparison"]))
    $_SESSION["comparison"] = array( );
    $_SESSION["comparison"][] = ( int ) $_GET["cpradd"];
    $_SESSION["comparison"] = array_unique($_SESSION["comparison"]);
    $GLOBALS['_RESULT'] = array( "cpr_value" => count($_SESSION["comparison"]));
  }
  elseif ( isset ( $_GET["clear"] )) {
    $_SESSION["comparison"] = array( );
    $GLOBALS['_RESULT'] = array( "cpr_value" => 'none' );
  }
  
?>