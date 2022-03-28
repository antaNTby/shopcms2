<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        if ( isset($order3_billing) )
        {

                if(!cartCheckMinTotalOrderAmount()) Redirect('index.php?shopping_cart=yes&min_order=error');

                if (  !isset($_GET["order3_billing"])        ||
                          !isset($_GET["shippingAddressID"]) ||
                          !isset($_GET["shippingMethodID"])  ||
                          !isset($_GET["billingAddressID"])  )

                Redirect( "index.php?page_not_found=yes" );


                $_GET["shippingAddressID"] = (int)$_GET["shippingAddressID"];
                $_GET["billingAddressID"]  = (int)$_GET["billingAddressID"];
                $_GET["shippingMethodID"]  = (int)$_GET["shippingMethodID"];

                if ( $_GET["shippingAddressID"]!=0 && !regAddressBelongToCustomer(regGetIdByLogin($_SESSION["log"]), $_GET["shippingAddressID"]) ){
                        Redirect( "index.php?page_not_found=yes" );
                }
                if ( $_GET["billingAddressID"]!=0 && !regAddressBelongToCustomer(regGetIdByLogin($_SESSION["log"]), $_GET["billingAddressID"]) ){
                        Redirect( "index.php?page_not_found=yes" );
                }
                if ( $_GET["shippingMethodID"] != 0 ){
                        if ( !shShippingMethodIsExist($_GET["shippingMethodID"]) ){
                                Redirect( "index.php?page_not_found=yes" );
                        }
                }


                if ( !cartCheckMinOrderAmount() ) Redirect( "index.php?shopping_cart=yes" );

                if ( isset($_POST["continue_button"]) )
                {
                        RedirectProtected("index.php?order4_confirmation=yes&".
                                "shippingAddressID=".$_GET["shippingAddressID"]."&".
                                "shippingMethodID=".$_GET["shippingMethodID"]."&".
                                "billingAddressID=".$_GET["billingAddressID"]."&".
                                "paymentMethodID=".$_POST["select_payment_method"].
                                (isset($_GET['shServiceID'])?"&shServiceID=".$_GET['shServiceID']:'') );
                }

                if ( isset($_GET["selectedNewAddressID"]) )
                {
                        RedirectProtected("index.php?order3_billing=yes&".
                                "shippingAddressID=".$_GET["shippingAddressID"]."&".
                                "shippingMethodID=".$_GET["shippingMethodID"]."&".
                                "billingAddressID=".$_GET["selectedNewAddressID"].
                                (isset($_GET['shServiceID'])?"&shServiceID=".$_GET['shServiceID']:'') );
                }

                $moduleFiles = GetFilesInDirectory( "core/modules/payment", "php" );
                foreach( $moduleFiles as $fileName ) include( $fileName );

                $payment_methods = payGetAllPaymentMethods(true);
                $payment_methodsToShow = array();
                foreach( $payment_methods as $payment_method )
                {
                        if ($_GET["shippingMethodID"] == 0) //no shipping methods available => show all available payment types
                        {
                                $shippingMethodsToAllow = true;
                        }
                        else // list of payment options depends on selected shipping method
                        {
                                $shippingMethodsToAllow = false;
                                foreach( $payment_method["ShippingMethodsToAllow"] as $ShippingMethod )
                                        if ( ((int)$_GET["shippingMethodID"] == (int)$ShippingMethod["SID"]) &&
                                                                         $ShippingMethod["allow"] )
                                        {
                                                $shippingMethodsToAllow = true;
                                                break;
                                        }
                        }

                        if ( $shippingMethodsToAllow ) $payment_methodsToShow[] = $payment_method;
                }

                if ( count($payment_methodsToShow) == 0 )
                        RedirectProtected( "index.php?order4_confirmation=yes&".
                                                "shippingAddressID=".$_GET["shippingAddressID"]."&".
                                                "shippingMethodID=".$_GET["shippingMethodID"]."&".
                                                "billingAddressID=".regGetDefaultAddressIDByLogin($_SESSION["log"])."&".
                                                "paymentMethodID=0".
                                (isset($_GET['shServiceID'])?"&shServiceID=".$_GET['shServiceID']:'') );

                $smarty->assign( "shippingAddressID",        $_GET["shippingAddressID"] );
                $smarty->assign( "billingAddressID",        $_GET["billingAddressID"] );
                $smarty->assign( "shippingMethodID",        $_GET["shippingMethodID"] );
                $smarty->assign( "strAddress", regGetAddressStr($_GET["billingAddressID"]) );
                $smarty->assign( "payment_methods", $payment_methodsToShow );
                $smarty->assign( "payment_methods_count",  count($payment_methodsToShow) );
                $smarty->assign( "main_content_template", "order3_billing.tpl.html" );
        }
?>