<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


if ( isset($order2_shipping_quick) )
{
        if(!cartCheckMinTotalOrderAmount())
                Redirect('index.php?shopping_cart=yes&min_order=error');

        if ( !cartCheckMinOrderAmount() )
                Redirect( "index.php?shopping_cart=yes" );

        $moduleFiles = GetFilesInDirectory( "core/modules/shipping", "php" );
        foreach( $moduleFiles as $fileName ) include( $fileName );

        function _getOrder()
        {
                if (!isset($_SESSION["first_name"]) || !isset($_SESSION["last_name"]) || !isset($_SESSION["email"])) return NULL;

                $order["first_name"] = $_SESSION["first_name"];
                $order["last_name"]  = $_SESSION["last_name"];
                $order["email"]      = $_SESSION["email"];

                $res = cartGetCartContent();
                $order["orderContent"] = $res["cart_content"];

                $d = oaGetDiscountPercent( $res, "" );
                $order["order_amount"] = $res["total_price"] - ($res["total_price"]/100)*$d;

                return $order;
        }


        function _getShippingCosts( $shipping_methods, $order, $moduleFiles )
        {
                if (!isset($_SESSION["receiver_countryID"]) || !isset($_SESSION["receiver_zoneID"]))
                        return NULL;

                $shipping_modules        = modGetModules( $moduleFiles );
                $shippingAddressID = 0;
                $shipping_costs = array();

                $res = cartGetCartContent();

                $sh_address = array(
                        "countryID" => $_SESSION["receiver_countryID"],
                        "zoneID" => $_SESSION["receiver_zoneID"]
                );
                $addresses = array( $sh_address, $sh_address );

                $j = 0;
                foreach( $shipping_methods as $shipping_method )
                {
                        $_ShippingModule = modGetModuleObj($shipping_method["module_id"], SHIPPING_RATE_MODULE);
                        if($_ShippingModule){

                                if ( $_ShippingModule->allow_shipping_to_address( $sh_address ) )
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

                return $shipping_costs;
        }

        $order            = _getOrder();
        $strAddress       = quickOrderGetReceiverAddressStr(); 
        $shipping_methods = shGetAllShippingMethods( true );

        if ( isset($_POST["continue_button"]) ){

                $_POST['shServiceID'] = isset($_POST['shServiceID'][$_POST['select_shipping_method']]) ? $_POST['shServiceID'][$_POST['select_shipping_method']]:0;
                RedirectProtected( "index.php?order3_billing_quick=yes&shippingMethodID=".
                                $_POST["select_shipping_method"].
                                "&shServiceID=".$_POST['shServiceID']
                                );
        }

        if ( count($shipping_methods) == 0 )
                RedirectProtected( "index.php?order3_billing_quick=yes&shippingMethodID=0" );

        $shipping_costs = _getShippingCosts( $shipping_methods, $order, $moduleFiles );
               
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
				
        $smarty->assign( "strAddress",                        $strAddress );
        $smarty->assign( "shipping_costs",                $shipping_costs );
        $smarty->assign( "shipping_methods",        $shipping_methods );
        $smarty->assign( "shipping_methods_count",  count($shipping_methods) );
        $smarty->assign( "main_content_template", "order2_shipping_quick.tpl.html" );
}

?>