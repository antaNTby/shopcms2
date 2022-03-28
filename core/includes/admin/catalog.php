<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


        //ADMIN :: products and categories view

        //define admin department
        $admin_dpt = array(
                "id" => "catalog", //department ID
                "sort_order" => 10, //sort order (less `sort_order`s appear first)
                "name" => ADMIN_CATALOG, //department name
                "sub_departments" => array
                (
                        array("id"=>"products_categories", "name"=>ADMIN_CATEGORIES_PRODUCTS),
						array("id"=>"tpl", "name"=>ADMIN_TPLS_PRODUCT),
						array("id"=>"subtpl", "name"=>ADMIN_TPLS_SUBPRODUCT),
                        array("id"=>"dbsync", "name"=>ADMIN_SYNCHRONIZE_TOOLS),
                        array("id"=>"extra", "name"=>ADMIN_PRODUCT_OPTIONS),
                        array("id"=>"special", "name"=>ADMIN_SPECIAL_OFFERS),
                        array("id"=>"excel_import", "name"=>ADMIN_IMPORT_FROM_EXCEL),
                        array("id"=>"excel_export", "name"=>ADMIN_EXPORT_TO_EXCEL),
                        array("id"=>"discuss", "name" => ADMIN_DISCUSSIONS )
                )
        );
        add_department($admin_dpt);


        //show new orders page if selected
        if ($dpt == "catalog")
        {
                //set default sub department if required
                if (!isset($sub)) $sub = "products_categories";

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