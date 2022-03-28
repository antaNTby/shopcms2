<?php
  #####################################
  # ShopCMS: Скрипт интернет-магазина
  # Copyright (c) by ADGroup
  # http://shopcms.ru
  #####################################
  
  if ( isset ( $_GET["xcart"] ) || isset ( $_POST["xcart"] )) {
     
	  $JsHttpRequest = & new JsHttpRequest(DEFAULT_CHARSET);
      //# of selected currency
      $current_currency = isset ( $_SESSION["current_currency"] ) ? $_SESSION["current_currency"] : CONF_DEFAULT_CURRENCY;
      $q = db_query("select code, currency_value, where2show, currency_iso_3, Name, roundval from ".CURRENCY_TYPES_TABLE." where CID=".( int ) $current_currency);
      if ( $row = db_fetch_row($q)) {
          $selected_currency_details = $row;
          //for show_price() function
      }
      else
      //no currency found. In this case check is there any currency type in the database
          {
          $q = db_query("select code, currency_value, where2show, roundval from ".CURRENCY_TYPES_TABLE);
          if ( $row = db_fetch_row($q)) {
              $selected_currency_details = $row;
              //for show_price() function
          }
      }
      
	  //should we add products to cart?
      if ( isset ( $_GET["addproduct"] )) {
          $variants = array( );
          foreach ( $_GET as $key => $val ) {
              if ( strstr($key, "option_select_hidden_"))
                  $variants[] = $val;
          }
          unset ( $_SESSION["variants"] );
          $_SESSION["variants"] = $variants;
          //add product to cart with productID=$add
          if ( isset ( $_GET["addproduct"] ) && ( int ) $_GET["addproduct"] > 0

              /* && isset($_SESSION["variants"]) */
              ) {
              if ( isset ( $_SESSION["variants"] )) {
                  $variants = $_SESSION["variants"];
                  unset ( $_SESSION["variants"] );
                  session_unregister("variants");
                  //calling session_unregister() is required since unset() may not work on some systems
              }
              else {
                  $variants = array( );
              }
              for ( $mcn = 0; $mcn < $_GET["multyaddcount"]; $mcn++ )
                  cartAddToCart(( int ) $_GET["addproduct"], $variants);
          }
          
		  $resCart = cartGetCartContent();
          $resDiscount = dscCalculateDiscount($resCart["total_price"], isset ( $_SESSION["log"] ) ? $_SESSION["log"] : "");
          $discount_value = addUnitToPrice($resDiscount["discount_current_unit"]);
          $discount_percent = $resDiscount["discount_percent"];
          $k = 0;
          $cnt = 0;
          
		  if ( isset ( $_SESSION["log"] ))
              //taking products from database
              {
              $q = db_query("select itemID, Quantity FROM ".SHOPPING_CARTS_TABLE." WHERE customerID=".( int ) regGetIdByLogin($_SESSION["log"]));
              while ( $row = db_fetch_row($q)) {
                  $q1 = db_query("select productID from ".SHOPPING_CART_ITEMS_TABLE." where itemID=".( int ) $row["itemID"]);
                  $r1 = db_fetch_row($q1);
                  $variants = GetConfigurationByItemId($row["itemID"]);
                  $k += GetPriceProductWithOption($variants, $r1["productID"]) * $row["Quantity"];
                  $cnt += $row["Quantity"];
              }
          }
          else
              if ( isset ( $_SESSION["gids"] ))
                  //...session vars
                  {
                  for ( $i = 0; $i < count($_SESSION["gids"]); $i++ ) {
                      if ( $_SESSION["gids"][$i] ) {
                          $t = db_query("select Price FROM ".PRODUCTS_TABLE." WHERE productID=".( int ) $_SESSION["gids"][$i]);
                          $rr = db_fetch_row($t);
                          $sum = $rr["Price"];
                          // $rr["Price"]
                          foreach ( $_SESSION["configurations"][$i] as $varconf ) {
                              $q1 = db_query("select price_surplus from ".PRODUCTS_OPTIONS_SET_TABLE." where variantID=".( int ) $varconf." AND productID=".( int ) $_SESSION["gids"][$i]);
                              $r1 = db_fetch_row($q1);
                              $sum += $r1["price_surplus"];
                          }
                          $k += $_SESSION["counts"][$i] * $sum;
                          $cnt += $_SESSION["counts"][$i];
                      }
                  }
              }
              $GLOBALS['_RESULT'] = array( "shopping_cart_value" => $k, "shopping_cart_value_shown" => show_price($k), "shopping_cart_items" => $cnt );
      }
  }
  else {
      
	  //init Smarty
      require ( "core/smarty/smarty.class.php" );
      $smarty = new Smarty;
      //core smarty object
      $smarty_mail = new Smarty;
      
	  //for e-mails
      if ( ( int ) CONF_SMARTY_FORCE_COMPILE ) {
          $smarty->force_compile = true;
          $smarty_mail->force_compile = true;
      }
      
	  //authorized access check
      $relaccess = checklogin();
      //# of selected currency
      $current_currency = isset ( $_SESSION["current_currency"] ) ? $_SESSION["current_currency"] : CONF_DEFAULT_CURRENCY;
      $smarty->assign("current_currency", $current_currency);
      $q = db_query("select code, currency_value, where2show, currency_iso_3, Name, roundval from ".CURRENCY_TYPES_TABLE." where CID=".( int ) $current_currency);
      
	  if ( $row = db_fetch_row($q)) {
          $smarty->assign("currency_name", $row[0]);
          $selected_currency_details = $row;
          //for show_price() function
      }
      else
      //no currency found. In this case check is there any currency type in the database
          {
          $q = db_query("select code, currency_value, where2show, roundval from ".CURRENCY_TYPES_TABLE);
          if ( $row = db_fetch_row($q)) {
              $smarty->assign("currency_name", $row[0]);
              $selected_currency_details = $row;
              //for show_price() function
          }
      }
      
	  //set Smarty include files dir
      if ( isset ( $_SESSION["CUSTOM_DESIGN"] )) {
          define('TPL', $_SESSION["CUSTOM_DESIGN"]);
          $smarty->template_dir = "core/tpl/user/".$_SESSION["CUSTOM_DESIGN"];
      }
      else {
          define('TPL', CONF_DEFAULT_TEMPLATE);
          $smarty->template_dir = "core/tpl/user/".CONF_DEFAULT_TEMPLATE;
      }
      $smarty_mail->template_dir = "core/tpl/email";
      
	  if ( isset ( $_SESSION["log"] ))
          $smarty->assign("log", $_SESSION["log"]);
      
	  //should we add products to cart?
      if ( isset ( $_GET["addproduct"] )) {
          $variants = array( );
          foreach ( $_GET as $key => $val ) {
              if ( strstr($key, "option_select_hidden_"))
                  $variants[] = $val;
          }
          unset ( $_SESSION["variants"] );
          $_SESSION["variants"] = $variants;
          Redirect("index.php?do=cart&shopping_cart=yes&add2cart=".( int ) $_GET["addproduct"]."&multyaddcount=".( int ) $_GET['multyaddcount']);
      }
      
	  //specify that this is a popup window
      $this_is_a_popup_cart_window = true;
      $smarty->assign("this_is_a_popup_cart_window", 1);
      //include core shopping cart routine
      include ( "core/includes/shopping_cart.php" );
      //show Smarty output
      $smarty->display("shopping_cart.tpl.html");
  }
?>