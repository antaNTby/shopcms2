<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        if (!strcmp($sub, "custgroup")) //show registered customers list
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(32,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {
                        
                if ( isset($_GET["delete"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=custord&sub=custgroup&safemode=yes");
                        }

                        DeleteCustGroup( $_GET["delete"] );
                        Redirect(ADMIN_FILE."?dpt=custord&sub=custgroup");
                }

                if ( isset($_POST["save_custgroups"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=custord&sub=custgroup&safemode=yes");
                        }

                        // add new group
                        if ( trim($_POST["new_custgroup_name"]) != "" )
                        {
                                AddCustGroup(
                                        $_POST["new_custgroup_name"], $_POST["new_custgroup_discount"],
                                        $_POST["new_sort_order"] );
                        }

                        // update groups
                        $data = array();
                        foreach( $_POST as $key => $val )
                        {
                                if ( strstr($key, "custgroup_name_") )
                                {
                                        $key = str_replace("custgroup_name_","",$key);
                                        $data[$key]["custgroup_name"]=$val;
                                }
                                if ( strstr($key, "custgroup_discount_") )
                                {
                                        $key = str_replace("custgroup_discount_","",$key);
                                        $data[$key]["custgroup_discount"]=$val;
                                }
                                if ( strstr($key, "sort_order_") )
                                {
                                        $key = str_replace("sort_order_","",$key);
                                        $data[$key]["sort_order"]=$val;
                                }
                        }

                        foreach( $data as $key => $val )
                        {
                                UpdateCustGroup(
                                        $key,
                                        $val["custgroup_name"],
                                        $val["custgroup_discount"],
                                        $val["sort_order"] );
                        }

                        Redirect(ADMIN_FILE."?dpt=custord&sub=custgroup");

                }


                // get all groups
                $custgroups = GetAllCustGroups();

                $smarty->assign("custgroups", $custgroups);

                //set sub template
                $smarty->assign("admin_sub_dpt", "custord_custgroup.tpl.html");
        }
        }
?>