<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        //catalog: products extra parameters list

        if (!strcmp($sub, "extra"))
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(3,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {

                if (isset($_GET["save_successful"])) //update was successful
                        $smarty->assign("save_successful",ADMIN_UPDATE_SUCCESSFUL);

                if ( isset($_POST["save_values"] ) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=catalog&sub=extra&optionID=".$_POST["optionID"]."&safemode=yes");
                        }

                        // update existing values
                        $updateOptions = ScanPostVariableWithId( array( "sort_order",
                                                "option_value" ) );
                        optUpdateOptionValues($updateOptions);

                        // add new value
                        if ( isset($_POST["add_value"]) && trim($_POST["add_value"]) != "" )
                                optAddOptionValue($_POST["optionID"], $_POST["add_value"],
                                        (int)$_POST["add_sort"] );

                        Redirect(ADMIN_FILE."?dpt=catalog&sub=extra&optionID=".$_POST["optionID"] );
                }
                if (isset($_POST["save_options"])) //save extra product options
                {

                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=catalog&sub=extra&safemode=yes");
                        }

                        //save existing
                        $updateOptions = ScanPostVariableWithId( array( "extra_option", "extra_sort" ) );

                        //now update database
                        optUpdateOptions($updateOptions);

                        //add a new option
                        if ( isset($_POST["add_option"]) )
                                optAddOption( $_POST["add_option"], $_POST["add_sort"] );

                        Redirect(ADMIN_FILE."?dpt=catalog&sub=extra&save_successful=yes");
                }

                // delete value
                if ( isset($_GET["kill_value"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=catalog&sub=extra&optionID=".$_GET["optionID"]."&safemode=yes");
                        }

                        $variantid = (int)$_GET["kill_value"];

                        db_query("delete from ".PRODUCTS_OPTIONS_VALUES_VARIANTS_TABLE." where variantID=".$variantid);
                        db_query("delete from ".PRODUCTS_OPTIONS_SET_TABLE." where variantID=".$variantid);
                        db_query("delete from ".CATEGORY_PRODUCT_OPTIONS_TABLE." where variantID=".$variantid);
                        db_query("update ".PRODUCT_OPTIONS_VALUES_TABLE." set variantID=NULL where variantID=".$variantid);

                        Redirect(ADMIN_FILE."?dpt=catalog&sub=extra&optionID=".$_GET["optionID"] );
                }

                //delete extra option?
                if (isset($_GET["kill_option"]))
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=catalog&sub=extra&safemode=yes");
                        }

                        $optionid = (int)$_GET["kill_option"];

                        db_query("delete from ".PRODUCT_OPTIONS_TABLE." where optionID=".$optionid);
                        db_query("delete from ".PRODUCTS_OPTIONS_VALUES_VARIANTS_TABLE." where optionID=".$optionid);
                        db_query("delete from ".PRODUCT_OPTIONS_VALUES_TABLE." where optionID=".$optionid);
                        db_query("delete from ".PRODUCTS_OPTIONS_SET_TABLE." where optionID=".$optionid);
                        db_query("delete from ".CATEGORY_PRODUCT_OPTIONS_TABLE." where optionID=".$optionid);

                        Redirect(ADMIN_FILE."?dpt=catalog&sub=extra");
                }

                if ( !isset($_GET["optionID"]) )
                {

                        //now select all available product options
                        $options = optGetOptions();
                        $smarty->assign("options", $options);
                }
                else
                {
                        $option = optGetOptionById( (int)$_GET["optionID"] );
                        $values = optGetOptionValues( (int)$_GET["optionID"] );

                        $smarty->assign("optionID", (int)$_GET["optionID"] );
                        $smarty->assign("values", $values);
                        $smarty->assign("option_name",$option["name"]);
                        $smarty->assign("value_count", count($values) );
                }

                //set sub-department template
                $smarty->assign("admin_sub_dpt", "catalog_extra.tpl.html");
        }
        }
?>