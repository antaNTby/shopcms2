<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


        //payment types list

        if (!strcmp($sub, "payment"))
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(15,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {


        $shipping_methods = shGetAllShippingMethods();

        $moduleFiles = GetFilesInDirectory( "core/modules/payment", "php" );

        foreach( $moduleFiles as $fileName )
                include( $fileName );


        if (isset($_GET["save_successful"])) //show successful save confirmation message
                $smarty->assign("configuration_saved", 1);

        if (isset($_GET["delete"])) //delete payment type
        {
                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                {
                        Redirect(ADMIN_FILE."?dpt=conf&sub=payment&safemode=yes" );
                }
                payDeletePaymentMethod( $_GET["delete"] );
                Redirect(ADMIN_FILE."?dpt=conf&sub=payment" );
        }

        if (isset($_POST["save_payment"])) //save payment and payment types
        {
                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                {
                        Redirect(ADMIN_FILE."?dpt=conf&sub=payment&safemode=yes" );
                }

                $values = ScanPostVariableWithId( array( "Enabled", "name", "description",
                                                        "email_comments_text", "module", "sort_order", "calculate_tax" ) );

                foreach( $values as $PID => $value )
                {
                        payUpdatePaymentMethod(
                                        $PID, $value["name"], $value["description"], isset($value["Enabled"])?1:0,
                                        (int)$value["sort_order"],
                                        $value["module"], $value["email_comments_text"],
                                        isset($value["calculate_tax"])?1:0 );

                        payResetPaymentShippingMethods( $PID );
                        foreach( $shipping_methods as $shipping_method )
                        {
                                if ( isset($_POST["ShippingMethodsToAllow_".$PID."_".$shipping_method["SID"]]) )
                                        paySetPaymentShippingMethod( $PID, $shipping_method["SID"] );
                        }
                }


                if ( trim($_POST["new_name"]) != "" )
                {
                         $PID = payAddPaymentMethod( $_POST["new_name"], $_POST["new_description"],
                                isset($_POST["new_Enabled"])?1:0, (int)$_POST["new_sort_order"],
                                $_POST["new_email_comments_text"], $_POST["new_module"],
                                isset($_POST["new_calculate_tax"])?1:0 );
                        foreach( $shipping_methods as $shipping_method )
                        {
                                if ( isset($_POST["new_ShippingMethodsToAllow_".$shipping_method["SID"]]) )
                                        paySetPaymentShippingMethod( $PID, $shipping_method["SID"] );
                        }
                }

                Redirect(ADMIN_FILE."?dpt=conf&sub=payment&save_successful=yes" );
        }

        $smarty->assign("payment_types", payGetAllPaymentMethods() );
        $smarty->assign("payment_modules", modGetAllInstalledModuleObjs(PAYMENT_MODULE) );
        $smarty->assign("shipping_methods", $shipping_methods );


        //set sub-department template
        $smarty->assign("admin_sub_dpt", "conf_payment.tpl.html");
        }
        }
?>