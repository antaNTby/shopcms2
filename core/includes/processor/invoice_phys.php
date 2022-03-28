<?php
  #####################################
  # ShopCMS: Скрипт интернет-магазина
  # Copyright (c) by ADGroup
  # http://shopcms.ru
  #####################################

  //init Smarty
  require ("core/smarty/smarty.class.php");
  $smarty = new Smarty; //core smarty object
  $smarty_mail = new Smarty; //for e-mails

  if ((int)CONF_SMARTY_FORCE_COMPILE) 

  {
      $smarty->force_compile = true;
      $smarty_mail->force_compile = true;
  }
  
  //set Smarty include files dir
  $smarty->template_dir = "core/modules/tpl/";

  //assign core Smarty variables
        if (!isset($_GET["orderID"]) || !isset($_GET["order_time"]) || !isset($_GET["customer_email"]) || !isset($_GET["moduleID"]))
        {
                die ("Заказ не найден в базе данных");
        }
  $InvoiceModule = modGetModuleObj((int)$_GET['moduleID'], PAYMENT_MODULE);
  $_GET["orderID"] = (int)$_GET["orderID"];

  $q = db_query("select count(*) from ".ORDERS_TABLE." where orderID=".$_GET["orderID"]." and order_time='".xEscSQL(
      base64_decode($_GET["order_time"]))."' and customer_email='".xEscSQL(base64_decode($_GET["customer_email"])).
      "'");
  $row = db_fetch_row($q);

  if ($row[0] == 1) //заказ найден в базе данных

  {

      $order = ordGetOrder($_GET["orderID"]);

      //define smarty vars
      $smarty->assign("billing_lastname", $order["customer_lastname"]);
      $smarty->assign("billing_firstname", $order["customer_firstname"]);
      $smarty->assign("billing_city", $order["billing_city"]);
      $smarty->assign("billing_address", $order["billing_address"]);

                if ($InvoiceModule->is_installed())
                {
                        $smarty->assign('InvoiceModule', $InvoiceModule);
                        $smarty->assign( "invoice_description", str_replace("[orderID]", (string)$_GET["orderID"], $InvoiceModule->_getSettingValue('CONF_PAYMENTMODULE_INVOICE_PHYS_DESCRIPTION')) );
                }
                else //описание не опред
                {
                        die ("Модуль оплаты по квитанциям не установлен");
                }

      //сумма квитанции
      $q = db_query("select order_amount_string from ".DB_PRFX."_module_payment_invoice_phys where orderID=".$_GET["orderID"]);
      $row = db_fetch_row($q);
      if ($row) //сумма найдена в файле с описанием квитанции

      {
          $smarty->assign("invoice_amount", $row[0]);
      }
      else //сумма не найдена - показываем в текущей валюте

      {
          $smarty->assign("invoice_amount", show_price($order["order_amount"]));
      }


  }
  else
  {
      die("Заказ не найден в базе данных");
  }

  //show Smarty output
  $smarty->display("invoice_phys.tpl.html");
?>