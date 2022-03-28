<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


        if ( isset($order2_shipping) )
        {//var_dump($_GET);

                if(!cartCheckMinTotalOrderAmount()){

                        Redirect('index.php?shopping_cart=yes&min_order=error');
                }
                if ( !isset($_GET["order2_shipping"]) || !isset($_GET["shippingAddressID"]) )
                        Redirect( "index.php?page_not_found=yes" );

                $_GET["shippingAddressID"] = (int)$_GET["shippingAddressID"];

                if ($_GET["shippingAddressID"] == 0) //no default address specified
                {
                        $addrs = regGetAllAddressesByLogin($_SESSION["log"]);
                }
                else
                {
                        if ( !regAddressBelongToCustomer(regGetIdByLogin($_SESSION["log"]), $_GET["shippingAddressID"]) )
                                Redirect( "index.php?page_not_found=yes" );
                }

                if ( !cartCheckMinOrderAmount() )  Redirect( "index.php?shopping_cart=yes" );

                function _getOrder()
                {
                        $cust_password           = "";
                        $Email                   = "";
                        $first_name              = "";
                        $last_name               = "";
                        $subscribed4news         = "";
                        $additional_field_values = "";
                        $countryID               = "";
                        $zoneID                  = "";
                        $state                   = "";
                        $city                    = "";
                        $address                 = "";

                        regGetCustomerInfo($_SESSION["log"],
                                        $cust_password, $Email, $first_name,
                                        $last_name, $subscribed4news, $additional_field_values,
                                        $countryID, $zoneID, $state, $city, $address );


                        $order["first_name"] = $first_name;
                        $order["last_name"]  = $last_name;
                        $order["email"]      = $Email;

                        $res = cartGetCartContent();
                        $order["orderContent"]        = $res["cart_content"];

                        $d = oaGetDiscountPercent( $res, $_SESSION["log"] );
                        $order["order_amount"] = $res["total_price"] - ($res["total_price"]/100)*$d;

                        return $order;
                }

                if ( isset($_GET["selectedNewAddressID"]) )
                {
                        if ( !isset($_GET["defaultBillingAddressID"]) )
                                RedirectProtected( "index.php?order2_shipping=yes".
                                                        "&shippingAddressID=".$_GET["selectedNewAddressID"] );
                        else
                                RedirectProtected( "index.php?order2_shipping=yes".
                                                        "&shippingAddressID=".$_GET["selectedNewAddressID"].
                                                        "&defaultBillingAddressID=".$_GET["defaultBillingAddressID"] );
                }

                $shippingAddressID  = $_GET["shippingAddressID"];
                $order              = _getOrder();

                $strAddress = regGetAddressStr( $shippingAddressID );

                $moduleFiles = GetFilesInDirectory( "core/modules/shipping", "php" );
                foreach( $moduleFiles as $fileName ) include( $fileName );

                $shipping_methods = shGetAllShippingMethods( true );
                $shipping_costs   = array();
                $res              = cartGetCartContent();

                $sh_address = regGetAddress( $shippingAddressID );
                $addresses = array( $sh_address, $sh_address );

                $j = 0;
                foreach( $shipping_methods as $key => $shipping_method )
                {

                        $_ShippingModule = modGetModuleObj($shipping_method["module_id"], SHIPPING_RATE_MODULE);
                        if($_ShippingModule){

                                if ( $_ShippingModule->allow_shipping_to_address( regGetAddress($shippingAddressID) ) )
                                {
                                        $shipping_costs[$j] = oaGetShippingCostTakingIntoTax( $res, $shipping_method["SID"], $addresses, $order );
                                }
                                else
                                {

                                        $shipping_costs[$j] = array(array('rate'=>-1));
                                }
                        }else //rate = freight charge
                        {
                                $shipping_costs[$j] = oaGetShippingCostTakingIntoTax( $res, $shipping_method["SID"], $addresses, $order );
                        }
                        $j++;
                }

                $_i = count($shipping_costs)-1;
                for ( ; $_i>=0; $_i-- ){

                        $_t = count($shipping_costs[$_i])-1;
                        for ( ; $_t>=0; $_t-- ){

                                if($shipping_costs[$_i][$_t]['rate']>0){
                                        $shipping_costs[$_i][$_t]['rate'] = show_price($shipping_costs[$_i][$_t]['rate']);
                                }else {

                                        if(count($shipping_costs[$_i]) == 1 && $shipping_costs[$_i][$_t]['rate']<0){

                                                $shipping_costs[$_i] = 'n/a';
                                        }else{

                                                $shipping_costs[$_i][$_t]['rate'] = '';
                                        }
                                }
                        }
                }
				
                $result_methods = array();
				$result_costs = array();
                foreach( $shipping_methods as $key => $shipping_method ){
                if ($shipping_costs[$key]!='n/a'){
				$result_methods[] = $shipping_method;
				$result_costs[] = $shipping_costs[$key];
				}
				}
                $shipping_methods = $result_methods;
                $shipping_costs = $result_costs;
				
                if ( isset($_POST["continue_button"]) )
                {
                        $_POST['shServiceID'] = isset($_POST['shServiceID'][$_POST['select_shipping_method']]) ? $_POST['shServiceID'][$_POST['select_shipping_method']]:0;
                        if ( !isset($_GET["defaultBillingAddressID"]) )
                                RedirectProtected( "index.php?order3_billing=yes&".
                                                        "shippingAddressID=".$_GET["shippingAddressID"]."&".
                                                        "shippingMethodID=".$_POST["select_shipping_method"]."&".
                                                        "billingAddressID=".regGetDefaultAddressIDByLogin($_SESSION["log"]).
                                                        "&shServiceID=".$_POST['shServiceID']
                                                         );
                        else
                                RedirectProtected( "index.php?order3_billing=yes&".
                                                        "shippingAddressID=".$_GET["shippingAddressID"]."&".
                                                        "shippingMethodID=".$_POST["select_shipping_method"]."&".
                                                        "billingAddressID=".$_GET["defaultBillingAddressID"].
                                                        "&shServiceID=".$_POST['shServiceID']
                                                        );
                }

                if ( count($shipping_methods) == 0 )
                                RedirectProtected( "index.php?order3_billing=yes&".
                                                        "shippingAddressID=".regGetDefaultAddressIDByLogin($_SESSION["log"])."&".
                                                        "shippingMethodID=0&".
                                                        "billingAddressID=".regGetDefaultAddressIDByLogin($_SESSION["log"]) );


                if ( isset($_GET["defaultBillingAddressID"]) )
                $smarty->assign( "defaultBillingAddressID", $_GET["defaultBillingAddressID"] );
                $smarty->assign( "shippingAddressID",     $_GET["shippingAddressID"] );
                $smarty->assign( "strAddress",                           $strAddress );
                $smarty->assign( "shipping_costs",                   $shipping_costs );
                $smarty->assign( "shipping_methods",               $shipping_methods );
                $smarty->assign( "shipping_methods_count",  count($shipping_methods) );
                $smarty->assign( "main_content_template", "order2_shipping.tpl.html" );
        }
?>