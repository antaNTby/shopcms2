<?php
  #####################################
  # ShopCMS: Скрипт интернет-магазина
  # Copyright (c) by ADGroup
  # http://shopcms.ru
  #####################################

  //init Smarty
  require ("core/smarty/smarty.class.php");
  $smarty = new Smarty; //core smarty object

  if ((int)CONF_SMARTY_FORCE_COMPILE) //this forces Smarty to recompile design each time

  {
      $smarty->force_compile = true;
  }

  $relaccess = checklogin();

  //set Smarty include files dir
  $smarty->template_dir = "core/tpl/admin";

  $error = "";

  // validate order and user
  if (CONF_BACKEND_SAFEMODE != 1 && !isset($_SESSION["log"]) || !isset($_GET["orderID"]))
  {
      $error = ERROR_FORBIDDEN;
  }
  else
  {

      $orderID = (int)$_GET["orderID"];
      $order = ordGetOrder($orderID);

      $order["discount_value"] = round((float)$order["order_discount"] * $order["clear_total_priceToShow"]) /
          100;

      if (!$order)
      {
          $error = ERROR_CANT_FIND_REQUIRED_PAGE;
      }
      else //order was found in the database

      {
          //administrator is allowed to access all orders invoices
          //and if logged user is not administrator, check if order belongs to this user.

          if (CONF_BACKEND_SAFEMODE != 1 && !in_array(100, $relaccess) && ($order["customerID"] != regGetIdByLogin
              ($_SESSION["log"]))) //attempt to view orders of other customers

          {
              $error = ERROR_FORBIDDEN;
          }
          else // show invoice

          {
              $orderContent = ordGetOrderContent($orderID);
              $smarty->assign("orderContent", $orderContent);
              $smarty->assign("order", $order);
              $smarty->assign( "completed_order_status", ostGetCompletedOrderStatus() );
          }

      }
  }
  $smarty->assign("error", $error);

  //show Smarty output
  $smarty->display("invoice.tpl.html");
?>