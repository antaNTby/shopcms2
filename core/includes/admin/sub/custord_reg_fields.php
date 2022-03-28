<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        if ( !strcmp($sub, "reg_fields") )
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(10,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {

          if ( isset($_GET["delete"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=custord&sub=reg_fields&safemode=yes" );
                        }

                        DeleteRegField( $_GET["delete"] );
                        Redirect(ADMIN_FILE."?dpt=custord&sub=reg_fields");
                }

                if ( isset($_POST["save_fields"]) ) //save registration form custom fields
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=custord&sub=reg_fields&safemode=yes" );
                        }

                        // add new field
                        if ( trim($_POST["new_reg_field_name"]) != "" )
                        {
                                $new_reg_field_required=0;
                                if (  isset($_POST["new_reg_field_required"]) )
                                        $new_reg_field_required=1;
                                AddRegField(
                                        $_POST["new_reg_field_name"],
                                        $new_reg_field_required,
                                        $_POST["new_sort_order"] );
                        }

                        // update fields
                        $data = ScanPostVariableWithId( array( "reg_field_name",
                                "reg_field_required", "sort_order" ) );


                        foreach($data as $key => $val)
                        {
                                if ( !isset($val["reg_field_required"]) )
                                        $val["reg_field_required"]=0;
                                UpdateRegField(
                                        $key,
                                        $val["reg_field_name"],
                                        $val["reg_field_required"],
                                        $val["sort_order"] );
                        }

                        Redirect(ADMIN_FILE."?dpt=custord&sub=reg_fields");

                }



                if ( isset($_POST["save_address_form"]) ) //save address form configration
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=custord&sub=reg_fields&safemode=yes" );
                        }

                        db_query("update ".SETTINGS_TABLE." set settings_value = '".(int)$_POST["addr_state"]."' where settings_constant_name = 'CONF_ADDRESSFORM_STATE'");
                        db_query("update ".SETTINGS_TABLE." set settings_value = '".(int)$_POST["addr_city"]."' where settings_constant_name = 'CONF_ADDRESSFORM_CITY'");
                        db_query("update ".SETTINGS_TABLE." set settings_value = '".(int)$_POST["addr_address"]."' where settings_constant_name = 'CONF_ADDRESSFORM_ADDRESS'");

                        Redirect(ADMIN_FILE."?dpt=custord&sub=reg_fields");
                }


                $fields=GetRegFields();
                $smarty->assign("fields", $fields );

                //set sub template
                $smarty->assign("admin_sub_dpt", "custord_reg_fields.tpl.html");
        }
        }


?>