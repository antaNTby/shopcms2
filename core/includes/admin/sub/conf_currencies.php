<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        //currency types list

        if (!strcmp($sub, "currencies"))
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(13,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {


                if (isset($_GET["save_successful"])) //show successful save confirmation message
                {
                        $smarty->assign("configuration_saved", 1);
                }

                if (isset($_GET["delete"]))
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=conf&sub=currencies&safemode=yes" );
                        }
                        // delete currency
                        currDeleteCurrency( $_GET["delete"] );
                        Redirect(ADMIN_FILE."?dpt=conf&sub=currencies" );
                }

                if (isset($_POST["save_currencies"]))
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=conf&sub=currencies&safemode=yes" );
                        }

                        // scan data
                        $data = ScanPostVariableWithId( array( "curr_name", "curr_value",
                                                        "curr_where", "curr_code", "curr_sort", "curr_currencyIso3", "curr_roundval" ) );

                        // update existing currencies
                        foreach( $data as $key => $val )
                        {
                                if ( $val["curr_name"] == "" || $val["curr_currencyIso3"] == "" || $val["curr_code"] == "" )
                                        continue;
                                $val["curr_value"] = (float)$val["curr_value"];
                                $val["curr_sort"]  = (int)$val["curr_sort"];
                                $val["curr_where"] = (int)$val["curr_where"];
                                $val["curr_where"] = (int)$val["curr_where"];
                                $val["curr_roundval"] = (int)$val["curr_roundval"];
                                if ( $val["curr_value"] < 0 )
                                        continue;
                                currUpdateCurrency( $key, $val["curr_name"], $val["curr_code"], $val["curr_currencyIso3"],
                                                                        $val["curr_value"], $val["curr_where"], $val["curr_sort"], $val["curr_roundval"] );
                        }

                        //add a new currency type
                        if ( $_POST["curr_new_name"]!="" &&
                                 $_POST["curr_new_code"]!="" &&
                                 $_POST["curr_new_value"]!="" &&
                                 $_POST["curr_new_currencyIso3"] )
                        {
                                currAddCurrency( $_POST["curr_new_name"], $_POST["curr_new_code"],
                                                $_POST["curr_new_currencyIso3"],
                                                $_POST["curr_new_value"],
                                                $_POST["curr_new_where"], $_POST["curr_new_sort"], $_POST["curr_new_roundval"] );
                        }

                        Redirect(ADMIN_FILE."?dpt=conf&sub=currencies&save_successful=yes");
                }

                // get all currencies
                $currencies = currGetAllCurrencies();
                $smarty->assign("currencies", $currencies);

                //set sub-department template
                $smarty->assign("admin_sub_dpt", "conf_currencies.tpl.html");
        }
        }
?>