<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################
        
		$smarty->assign("product_removed", 0);
		
		// 	product has been removed from shopping cart
		
		if (isset($_GET["product_removed"]))
        {
		   $smarty->assign("product_removed", 1);
		   $smarty->assign("main_content_template", "shopping_cart.tpl.html");		
		}
		// shopping cart

        if (isset($this_is_a_popup_cart_window) || isset($_GET["shopping_cart"]) || isset($_POST["shopping_cart"]))
        {

                if (isset($this_is_a_popup_cart_window)) $cart_reguest = "index.php?do=cart";
                else $cart_reguest = "index.php?popup=no";

                $smarty->assign("cart_reguest", $cart_reguest);

                if ( isset($_GET["make_more_exact_cart_content"]) ) $smarty->assign( "make_more_exact_cart_content", 1 );

                //add product to cart with productID=$add
                if ( isset($_GET["add2cart"]) && $_GET["add2cart"]>0 /*&& isset($_SESSION["variants"]) */)
                {
                        if (isset($_SESSION["variants"]))
                        {
                                $variants=$_SESSION["variants"];
                                unset($_SESSION["variants"]);
                                session_unregister("variants"); //calling session_unregister() is required since unset() may not work on some systems
                        }
                        else
                        {
                                $variants = array();
                        }
                        for ($mcn=0; $mcn<$_GET["multyaddcount"]; $mcn++) cartAddToCart( $_GET["add2cart"], $variants );
                        Redirect( $cart_reguest."&shopping_cart=yes" );
                }


                if (isset($_GET["remove"])) //remove from cart product with productID == $remove
                {

                        if (isset($_SESSION["log"]))
                        {
                                db_query("DELETE FROM ".SHOPPING_CARTS_TABLE.
                                        " WHERE customerID=".regGetIdByLogin($_SESSION["log"]).
                                        " AND itemID=".(int)$_GET["remove"]);
                                db_query("DELETE FROM ".SHOPPING_CART_ITEMS_TABLE." where itemID=".(int)$_GET["remove"]);
                                db_query("DELETE FROM ".SHOPPING_CART_ITEMS_CONTENT_TABLE." where itemID=".(int)$_GET["remove"]);
                                db_query("DELETE FROM ".ORDERED_CARTS_TABLE." where itemID=".(int)$_GET["remove"]);
                        }
                        else //remove from session vars
                        {
                                $res = DeCodeItemInClient( $_GET["remove"] );
                                $i = SearchConfigurationInSessionVariable($res["variants"], $res["productID"] );
                                if ( $i!=-1 ) $_SESSION["gids"][$i] = 0;
                        }

                        Redirect( $cart_reguest."&shopping_cart=yes" );
                }


                if (isset($_POST["update"])) //update shopping cart content
                {
                        foreach ($_POST as $key => $val)
                        {
                                if (strstr($key, "count_"))
                                {
                                        if (isset($_SESSION["log"])) //authorized user
                                        {
                                                $productID = GetProductIdByItemId( str_replace("count_","",$key) );
                                                $is=GetProductInStockCount( $productID );
                                                if ($val > 0) //$val is a new items count in the shopping cart
                                                {
                                                        if (CONF_CHECKSTOCK==1)
                                                                $val = min($val, $is); //check stock level
                                                                $q = db_query("UPDATE ".SHOPPING_CARTS_TABLE.
                                                                " SET Quantity=".floor($val).
                                                                " WHERE customerID=".
                                                                regGetIdByLogin($_SESSION["log"]).
                                                                " AND itemID=".
                                                                (int)str_replace("count_","",$key));
                                                }
                                                else //$val<=0 => delete item from cart
                                                        $q = db_query("DELETE FROM ".SHOPPING_CARTS_TABLE." WHERE customerID=".regGetIdByLogin($_SESSION["log"])." AND itemID=".(int)str_replace("count_","",$key));
                                        }
                                        else //session vars
                                        {
                                                $res=DeCodeItemInClient( str_replace("count_","", $key) );

                                                $is=GetProductInStockCount( $res["productID"] );
                                                if ($val > 0)
                                                {
                                                        $i=SearchConfigurationInSessionVariable($res["variants"], $res["productID"] );
                                                        //check stock level
                                                        if (CONF_CHECKSTOCK==1) $val = min($val, $is);
                                                        $_SESSION["counts"][$i] = floor($val);
                                                }
                                                else //remove
                                                {
                                                        $i=SearchConfigurationInSessionVariable($res["variants"], $res["productID"] );
                                                        $_SESSION["gids"][$i] = 0;
                                                }
                                        }
                                }
                        }

                        Redirect( $cart_reguest."&shopping_cart=yes" );

                }

                if (isset($_GET["clear_cart"])) //completely clear shopping cart
                {
                        cartClearCartContet();
                        Redirect( $cart_reguest."&shopping_cart=yes" );
                }


                $resCart = cartGetCartContent();

                $resDiscount = dscCalculateDiscount( $resCart["total_price"], isset($_SESSION["log"])?$_SESSION["log"]:"" );
                $discount_value = addUnitToPrice( $resDiscount["discount_current_unit"] );
                $discount_percent = $resDiscount["discount_percent"];

                $smarty->assign("cart_content", $resCart["cart_content"] );
                $smarty->assign("cart_amount",  $resCart["total_price"] - $resDiscount["discount_standart_unit"]);
                $smarty->assign('cart_min',     show_price(CONF_MINIMAL_ORDER_AMOUNT));
                $smarty->assign("cart_total",   show_price( $resCart["total_price"] - $resDiscount["discount_standart_unit"] ) );

                // discount_prompt = 0 ( discount information is not shown )
                // discount_prompt = 1 ( discount information is showed simply without prompt )
                // discount_prompt = 2 ( discount information is showed with
                //                        STRING_UNREGISTERED_CUSTOMER_DISCOUNT_PROMPT )
                // discount_prompt = 3 ( discount information is showed with
                //                        STRING_UNREGISTERED_CUSTOMER_COMBINED_DISCOUNT_PROMPT )
                switch( CONF_DISCOUNT_TYPE )
                {
                        // discount is switched off
                        case 1:
                                $smarty->assign( "discount_prompt", 0 );
                                break;

                        // discount is based on customer group
                        case 2:
                                if ( isset($_SESSION["log"]) )
                                {
                                        $smarty->assign( "discount_value", $discount_value );
                                        $smarty->assign( "discount_percent", $discount_percent );
                                        $smarty->assign( "discount_prompt", 1 );
                                }
                                else
                                {
                                        $smarty->assign( "discount_value", $discount_value );
                                        $smarty->assign( "discount_percent", $discount_percent );
                                        $smarty->assign( "discount_prompt", 2 );
                                }
                                break;

                        // discount is calculated with help general order price
                        case 3:
                                $smarty->assign( "discount_prompt", 1 );
                                $smarty->assign( "discount_value", $discount_value );
                                $smarty->assign( "discount_percent", $discount_percent );
                                break;

                        // discount equals to discount is based on customer group plus
                        //                discount calculated with help general order price
                        case 4:
                                if ( isset($_SESSION["log"]) )
                                {
                                        $smarty->assign("discount_prompt", 1 );
                                        $smarty->assign("discount_value", $discount_value );
                                        $smarty->assign("discount_percent", $discount_percent );
                                }
                                else
                                {
                                        $smarty->assign("discount_prompt", 3 );
                                        $smarty->assign("discount_value", $discount_value );
                                        $smarty->assign("discount_percent", $discount_percent );
                                }
                                break;

                        // discount is calculated as MAX( discount is based on customer group,
                        //                        discount calculated with help general order price  )
                        case 5:
                                if ( isset($_SESSION["log"]) )
                                {
                                        $smarty->assign("discount_prompt", 1 );
                                        $smarty->assign("discount_value", $discount_value );
                                        $smarty->assign("discount_percent", $discount_percent );
                                }
                                else
                                {
                                        $smarty->assign("discount_prompt", 3 );
                                        $smarty->assign("discount_value", $discount_value );
                                        $smarty->assign("discount_percent", $discount_percent );
                                }
                                break;
                }


                if ( isset($_SESSION["log"]) ) $smarty->assign( "shippingAddressID", regGetDefaultAddressIDByLogin($_SESSION["log"]) );

                $smarty->assign("main_content_template", "shopping_cart.tpl.html");
        }
?>