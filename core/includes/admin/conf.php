<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


        //ADMIN :: configuration

        //define admin department
        $admin_dpt = array(
                "id" => "conf", //department ID
                "sort_order" => 30, //sort order (less `sort_order`s appear first)
                "name" => ADMIN_SETTINGS, //department name
                "sub_departments" => array
                 (
                        array("id"=>"setting", "name"=>ADMIN_SETTINGS_GENERAL ),
                        array("id"=>"currencies", "name"=>ADMIN_CURRENCY_TYPES),
                        array("id"=>"shipping", "name"=>STRING_SHIPPING_TYPE),
                        array("id"=>"payment", "name"=>STRING_PAYMENT_TYPE),
                        array("id"=>"blocks_edit", "name"=>ADMIN_CONF_BLOCKS),
                        array("id"=>"admin_edit", "name"=>ADMIN_CONF_ADMINS),
                        array("id"=>"countries", "name" => ADMIN_COUNTRIES),
                        array("id"=>"zones", "name" => ADMIN_MENU_TAXEZ)
                 )
        );
        add_department($admin_dpt);


        //show department if it is being selected
        if ($dpt == "conf")
        {
                //set default sub department if required
                if (!isset($sub)) $sub = "setting";

                if (file_exists("core/includes/admin/sub/".$admin_dpt["id"]."_$sub.php")) //sub-department file exists
                {
                        //assign admin main department template
                        $smarty->assign("admin_main_content_template", $admin_dpt["id"].".tpl.html");
                        //assign subdepts
                        $smarty->assign("admin_sub_departments", $admin_dpt["sub_departments"]);
                        //include selected sub-department
                        include("core/includes/admin/sub/".$admin_dpt["id"]."_$sub.php");
                }
                else //no sub department found
                        $smarty->assign("admin_main_content_template", "notfound.tpl.html");
        }

?>