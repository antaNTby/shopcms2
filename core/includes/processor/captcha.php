<?php
  #####################################
  # ShopCMS: Скрипт интернет-магазина
  # Copyright (c) by ADGroup
  # http://shopcms.ru
  #####################################

  header ("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
  header ("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
  header ("Pragma: no-cache");
  $captcha = new KCAPTCHA();
  $_SESSION['captcha_keystring'] = $captcha->getKeyString();
  
?>