<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


        //ADMIN :: products and categories view

        //define admin department
        $admin_dpt = array(
                "id" => "modules", //department ID
                "sort_order" => 40, //sort order (less `sort_order`s appear first)
                "name" => ADMIN_MODULES, //department name
                "sub_departments" => array
                (
                        array("id"=>"news", "name"=>ADMIN_NEWS),
                        array("id"=>"survey", "name"=>ADMIN_VOTING),
                        array("id"=>"shipping", "name"=>ADMIN_STRING_SHIPPING_MODULES),
                        array("id"=>"payment", "name"=>ADMIN_STRING_PAYMENT_MODULES),
                        array("id"=>"linkexchange", "name"=>ADMIN_STRING_MODULES_LINKEXCHANGE),
                        array("id"=>"yandex", "name"=>"Яндекс.Маркет" )
                )
        );
        add_department($admin_dpt);


        //show new orders page if selected
        if ($dpt == "modules")
        {
                //set default sub department if required
                if (!isset($sub)) $sub = "news";

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