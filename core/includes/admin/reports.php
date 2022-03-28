<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


        //ADMIN :: new orders managment

        //define a new admin department
        $admin_dpt = array(
                "id" => "reports", //department ID
                "sort_order" => 50, //sort order (less `sort_order`s appear first)
                "name" => ADMIN_REPORTS, //department name
                "sub_departments" => array
                 (
                        array( "id" => "category_viewed_times", "name" => ADMIN_CATEGORY_VIEWED_TIMES ),
                        array( "id" => "customer_log", "name" => ADMIN_CUSTOMER_LOG ),
                        array( "id" => "information", "name" => ADMIN_INFORMATION ),
                        array( "id" => "coming", "name" => ADMIN_COMING ),
                        array( "id" => "security", "name" => ADMIN_SECURITY ),
                        /*array( "id" => "site_map", "name" => ADMIN_SITE_MAP ),*/
                        array( "id" => "search_stat", "name" => ADMIN_SEARCH_STAT )
                 )
        );
        add_department($admin_dpt);

        //show department if it is being selected
        if ($dpt == "reports")
        {
                //set default sub department if required
                if (!isset($sub)) $sub = "category_viewed_times";

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