<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

if ( isset ( $order4_confirmation_quick )) {
    if ( !cartCheckMinTotalOrderAmount() && !isset ( $_GET["order_success"] )) Redirect('index.php?shopping_cart=yes&min_order=error');
    
	$shServiceID = isset ( $_GET['shServiceID'] ) ? $_GET['shServiceID'] : 0;
    
	if ( !isset ( $_POST["submitgo"] ) && !isset ( $_GET["order_success"] )) {
        if ( !isset ( $_GET["shippingMethodID"] )) Redirect("index.php?page_not_found=yes");
        $_GET["shippingMethodID"] = ( int ) $_GET["shippingMethodID"];
        if ( !isset ( $_GET["paymentMethodID"] )) Redirect("index.php?page_not_found=yes");
        $_GET["paymentMethodID"] = ( int ) $_GET["paymentMethodID"];
        if ( $_GET["shippingMethodID"] != 0 )
            if ( !shShippingMethodIsExist($_GET["shippingMethodID"])) Redirect("index.php?page_not_found=yes");
        if ( $_GET["paymentMethodID"] != 0 )
            if ( !payPaymentMethodIsExist($_GET["paymentMethodID"])) Redirect("index.php?page_not_found=yes");
    }
    
	if ( !cartCheckMinOrderAmount()) Redirect("index.php?shopping_cart=yes");
    
	$shippingModuleFiles = GetFilesInDirectory("core/modules/shipping", "php");
    
	foreach ( $shippingModuleFiles as $fileName ) include ( $fileName );
    
	$paymentModuleFiles = GetFilesInDirectory("core/modules/payment", "php");
    
	foreach ( $paymentModuleFiles as $fileName ) include ( $fileName );
    
	if ( isset ( $_POST["submitgo"] )) {
        $cc_number = "";
        $cc_holdername = "";
        $cc_expires = "";
        $cc_cvv = "";
        
		if ( CONF_CHECKSTOCK ) {
            $cartContent = cartGetCartContent();
            $rediractflag = false;
            foreach ( $cartContent["cart_content"] as $cartItem ) {
                // if conventional ordering
                if ( isset ( $_SESSION["log"] )) {
                    $productID = GetProductIdByItemId($cartItem["id"]);
                    $q = db_query("select name, in_stock FROM ".PRODUCTS_TABLE." WHERE productID=".( int ) $productID);
                    $left = db_fetch_row($q);
                    if ( $left["in_stock"] < 1 ) {
                        $rediractflag = true;
                        db_query("DELETE FROM ".SHOPPING_CARTS_TABLE." WHERE customerID=".regGetIdByLogin($_SESSION["log"])." AND itemID=".( int ) $cartItem["id"]);
                        db_query("DELETE FROM ".SHOPPING_CART_ITEMS_TABLE." where itemID=".( int ) $cartItem["id"]);
                        db_query("DELETE FROM ".SHOPPING_CART_ITEMS_CONTENT_TABLE." where itemID=".( int ) $cartItem["id"]);
                        db_query("DELETE FROM ".ORDERED_CARTS_TABLE." where itemID=".( int ) $cartItem["id"]);
                    }
                }
                else
                // if quick ordering
                    {
                    $productID = $cartItem["id"];
                    $q = db_query("select name, in_stock FROM ".PRODUCTS_TABLE." WHERE productID=".( int ) $productID);
                    $left = db_fetch_row($q);
                    if ( $left["in_stock"] < 1 ) {
                        $rediractflag = true;
                        $res = DeCodeItemInClient($productID);
                        $i = SearchConfigurationInSessionVariable($res["variants"], $res["productID"]);
                        if ( $i != - 1 )
                            $_SESSION["gids"][$i] = 0;
                    }
                }
            }
            if ( $rediractflag ) Redirect("index.php?product_removed=yes");
        }
        
		$orderID = ordOrderProcessing($_GET["shippingMethodID"], $_GET["paymentMethodID"], 0, 0, $shippingModuleFiles, $paymentModuleFiles, $_POST["order_comment"], 
		$cc_number, $cc_holdername, $cc_expires, $cc_cvv, null, $smarty_mail, $shServiceID);
        
		$_SESSION["newoid"] = $orderID;
        
		if ( is_bool($orderID))
            RedirectProtected("index.php?order4_confirmation_quick=yes&"."&shippingMethodID=".$_GET["shippingMethodID"]."&paymentMethodID=".$_GET["paymentMethodID"]."&payment_error=1");
        else
            RedirectProtected("index.php?order4_confirmation_quick=yes&"."order_success=yes&paymentMethodID=".$_GET["paymentMethodID"]."&orderID=".$orderID);
    }
    
	if ( isset ( $_GET["order_success"] )) {
        if ( isset ( $_GET["orderID"] ) && isset ( $_SESSION["newoid"] ) && ( int ) $_SESSION["newoid"] == ( int ) $_GET["orderID"] ) {
            $paymentMethod = payGetPaymentMethodById($_GET["paymentMethodID"]);
            $currentPaymentModule = modGetModuleObj($paymentMethod["module_id"], PAYMENT_MODULE);
            if ( $currentPaymentModule != null )
                $after_processing_html = $currentPaymentModule->after_processing_html($_GET["orderID"]);
            else
                $after_processing_html = "";
            $smarty->assign("after_processing_html", $after_processing_html);
        }
        $smarty->assign("order_success", 1);
    }
    else {
        if ( isset ( $_GET["payment_error"] )) {
            if ( $_GET["payment_error"] == 1 )
                $smarty->assign("payment_error", 1);
            else
                $smarty->assign("payment_error", base64_decode(str_replace(" ", "+", $_GET["payment_error"])));
        }
        elseif ( xDataExists('PaymentError')) {
            $smarty->assign("payment_error", xPopData('PaymentError'));
        }
        $orderSum = getOrderSummarize($_GET["shippingMethodID"], $_GET["paymentMethodID"], 0, 0, $shippingModuleFiles, $paymentModuleFiles, $shServiceID);
        $smarty->assign("orderSum", $orderSum);
        $smarty->assign("totalUC", $orderSum["totalUC"]);
    }
    if ( isset ( $_GET["orderID"] )) {
        $smarty->assign("orderidd", ( int ) $_GET["orderID"]);
    }
    $smarty->assign("main_content_template", "order4_confirmation_quick.tpl.html");
}
?>