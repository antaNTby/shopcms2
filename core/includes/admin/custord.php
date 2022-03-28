<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


        //ADMIN :: new orders managment

        //define a new admin department
        $admin_dpt = array(
                "id" => "custord", //department ID
                "sort_order" => 20, //sort order (less `sort_order`s appear first)
                "name" => ADMIN_CUSTOMERS_AND_ORDERS, //department name
                "sub_departments" => array
                 (
                        array("id"=>"new_orders", "name"=>ADMIN_NEW_ORDERS),
                        array("id"=>"subscribers", "name"=>ADMIN_NEWS_SUBSCRIBERS),
                        array("id"=>"order_statuses", "name"=>ADMIN_ORDER_STATUES),
                        array("id"=>"reg_fields", "name"=>ADMIN_CUSTOMER_REG_FIELDS),
                        array("id"=>"discounts", "name"=>ADMIN_DISCOUNTS),
                        array("id"=>"aux_pages", "name"=>ADMIN_AUX_PAGES),
                        array("id"=>"custlist", "name"=>ADMIN_CUSTOMERS),
                        array("id"=>"custgroup", "name"=>ADMIN_CUSTGROUP),
                        array("id"=>"affiliate", "name"=>STRING_AFFILIATE_PROGRAM)
                 )
        );
        add_department($admin_dpt);

        //show department if it is being selected
        if ($dpt == "custord")
        {
                //set default sub department if required
                if (!isset($sub)) $sub = "new_orders";

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