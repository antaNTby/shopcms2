<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        //shipping types list
        if (!strcmp($sub, "shipping"))
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(14,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {

        $moduleFiles = GetFilesInDirectory( "core/modules/shipping", "php" );

        foreach( $moduleFiles as $fileName )
                include( $fileName );

        if (isset($_GET["save_successful"])) //show successful save confirmation message
                $smarty->assign("configuration_saved", 1);

        if (isset($_GET["delete"])) //delete shipping type
        {
                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                {
                        Redirect(ADMIN_FILE."?dpt=conf&sub=shipping&safemode=yes" );
                }
                shDeleteShippingMethod($_GET["delete"]);
                Redirect(ADMIN_FILE."?dpt=conf&sub=shipping" );
        }

        if (isset($_POST["save_shipping"])) //save shipping and payment types
        {
                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                {
                        Redirect(ADMIN_FILE."?dpt=conf&sub=shipping&safemode=yes" );
                }

                $values = ScanPostVariableWithId( array( "Enabled", "name", "description",
                                                        "email_comments_text", "module", "sort_order" ) );
                foreach( $values as $key => $value )
                {
                        shUpdateShippingMethod(
                                        $key, $value["name"], $value["description"], isset($value["Enabled"])?1:0,
                                        (int)$value["sort_order"], $value["module"], $value["email_comments_text"] );
                }

                if (  trim($_POST["new_name"]) != "" )
                {
                        shAddShippingMethod( $_POST["new_name"], $_POST["new_description"],
                                                isset($_POST["new_Enabled"])?1:0, (int)$_POST["new_sort_order"],
                                                $_POST["new_module"], $_POST["new_email_comments_text"] );
                }

                Redirect(ADMIN_FILE."?dpt=conf&sub=shipping&save_successful=yes" );

        }


        /**
         * get all installed module objects
         */
        $smarty->assign( "shipping_types", shGetAllShippingMethods() );
        $smarty->assign( "shipping_modules", modGetAllInstalledModuleObjs(SHIPPING_RATE_MODULE) );


        //set sub-department template
        $smarty->assign("admin_sub_dpt", "conf_shipping.tpl.html");
        }
        }
?>