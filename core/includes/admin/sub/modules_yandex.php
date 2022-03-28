<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        //экспорт товаров в Яндекс.Маркет и другие аналогичные системы

        if (!strcmp($sub, "yandex"))
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(23,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {

                        $cats = catGetCategoryCListMin();
                        $smarty->assign( "cats", $cats );

                        function _exportToYandexMarket( $f, $rate, $export_product_name, $categories_select )
                        {

                                _exportBegin( $f );

                                  $cates = array();
                                  foreach ($categories_select as $ess){
                                  $catess = catCalculatePathToCategory( $ess );
                                  foreach ($catess as $ess_m){
                                  if($ess_m['categoryID']!=1) $cates[] = $ess_m['categoryID'];
                                  }
                                  }
                                  $categories_select_all = array_merge($categories_select, $cates);
                                  $categories_select_all = array_unique($categories_select_all);
                                _exportAllCategories( $f, $categories_select_all );
                                _exportProducts( $f, $rate, $export_product_name, $categories_select );
                                _exportEnd( $f );
                        }


                        function _exportBegin( $f )
                        {
                                fputs( $f, "<?xml version=\"1.0\" encoding=\"windows-1251\"?>\n" );
                                fputs( $f, "        <!DOCTYPE yml_catalog SYSTEM \"shops.dtd\">\n" );
                                fputs( $f, "                <yml_catalog date=\"".date("Y-m-d H:i")."\">\n" );
                                fputs( $f, "                        <shop>\n" );
                                fputs( $f, "                                <name>"._deleteHTML_Elements(CONF_SHOP_NAME)."</name>\n");
                                fputs( $f, "                                <company>"._deleteHTML_Elements(CONF_SHOP_NAME)."</company>\n");
                                fputs( $f, "                                <url>".correct_URL(CONF_FULL_SHOP_URL)."</url>\n");
                                fputs( $f, "                                <currencies>\n");
                                fputs( $f, "                                        <currency id=\"RUR\" rate=\"1\"/>\n");
                                fputs( $f, "                                        <currency id=\"USD\" rate=\"CBRF\"/>\n");
                                fputs( $f, "                                        <currency id=\"EUR\" rate=\"CBRF\"/>\n");
                                fputs( $f, "                                </currencies>\n");
                        }


                        function _exportAllCategories( $f, $categories_select )
                        {
                                if(!count($categories_select))return 0;
                                fputs($f,"                                <categories>\n");


                                        $sql = "
                                                SELECT categoryID, name, parent FROM ".CATEGORIES_TABLE." WHERE categoryID IN (".implode(", ", xEscSQL($categories_select)).")
                                                ";
                                        $q = db_query($sql);
                                        while ($row = db_fetch_row($q))
                                        {
                                                if ($row[2] <= 1) fputs($f,"                                        <category id=\"".$row[0]."\">"._deleteHTML_Elements($row[1])."</category>\n");
                                                else fputs($f,"                                        <category id=\"".$row[0]."\" parentId=\"".$row[2]."\">"._deleteHTML_Elements($row[1])."</category>\n");
                                                
                                        }

                                        fputs($f,"                                </categories>\n");
                        }


                        function _exportProducts($f, $rate, $export_product_name, $categories_select )
                        {
                                if(!count($categories_select))return 0;
                                include_once ("core/classes/class.html2text.php");
                                include_once ("core/classes/class.htmlparser.php");
                                fputs( $f, "                                <offers>\n");

                                //товары с нулевым остатком на складе
                                if (isset($_POST["yandex_dont_export_negative_stock"]))
                                        $clause = " and in_stock>0";
                                else
                                        $clause = "";

                                //какое описание экспортировать
                                if ($_POST["yandex_export_description"] == 1)
                                {
                                        $dsc = "description";
                                        $dsc_q = ", ".$dsc;
                                }
                                else if ($_POST["yandex_export_description"] == 2)
                                {
                                        $dsc = "brief_description";
                                        $dsc_q = ", ".$dsc;
                                }
                                else
                                {
                                        $dsc = "";
                                        $dsc_q = "";
                                }

                                $clause .= " and categoryID IN (".implode(", ", xEscSQL($categories_select)).")";

                                $sql = "select productID, name, Price, categoryID, default_picture".$dsc_q.", in_stock from ".PRODUCTS_TABLE." where enabled=1".$clause;
                                $q = db_query($sql);

                                $store_url = correct_URL(CONF_FULL_SHOP_URL);

                                while ($product = db_fetch_row($q))
                                {

                                        fputs( $f, "                                        <offer available=\"".(($product['in_stock'] || !CONF_CHECKSTOCK)?'true':'false')."\" id=\"".$product["productID"]."\">\n");
                                        fputs( $f, "                                                <url>".$store_url."index.php?productID=".$product["productID"]."&amp;from=ya</url>\n" );
                                        fputs( $f, "                                                <price>".roundf($product["Price"]*$rate)."</price>\n" );
                                        fputs( $f, "                                                <currencyId>RUR</currencyId>\n" );
                                        fputs( $f, "                                                <categoryId>".$product["categoryID"]."</categoryId>\n" );

                                        if ($product["default_picture"] != NULL)
                                        {
                                                $pic_clause = " and photoID=".(int)$product["default_picture"];
                                        }
                                        else
                                                $pic_clause = "";

                                        $q1 = db_query("select filename, thumbnail from ".PRODUCT_PICTURES." where productID=".(int)$product["productID"] . $pic_clause);
                                        $pic_row = db_fetch_row($q1);

                                        if ($pic_row) //экспортировать фотографию
                                        {
                                                if ( strlen($pic_row["filename"]) && file_exists("data/small/".$pic_row["filename"]) )
                                                        fputs( $f, "                                                <picture>".$store_url.
                                                                "data/small/".str_replace(' ', '%20',_deleteHTML_Elements($pic_row["filename"]))."</picture>\n" );
                                                else
                                                        if ( strlen($pic_row["thumbnail"]) && file_exists("data/medium/".$pic_row["thumbnail"]) )
                                                                fputs( $f, "                                                <picture>".$store_url.
                                                                        "data/medium/".str_replace(' ', '%20',_deleteHTML_Elements($pic_row["thumbnail"]))."</picture>\n" );

                                        }


                                        switch ($export_product_name){
                                                default:
                                                case 'only_name':
                                                        $_NameAddi = '';
                                                        break;
                                                case 'path_and_name':
                                                        $_NameAddi = '';
                                                        $_t = catCalculatePathToCategory( $product['categoryID'] );
                                                        foreach ($_t as $__t)
                                                                if($__t['categoryID']!=1)
                                                                        $_NameAddi .= $__t['name'].':';
                                                        break;
                                        }
                                        $product["name"]                = _deleteHTML_Elements($_NameAddi.$product["name"]);

                                        fputs( $f, "                                                <name>".$product["name"]."</name>\n" );

                                        if ( strlen($dsc)>0 )
                                        {
                                                $product_dsc = new Html2Text ($product[$dsc], 10000);   
                                                $product_dsc = $product_dsc->convert();
												fputs( $f, "                                                <description>"._deleteHTML_Elements($product_dsc)."</description>\n" );
                                        }
                                        else
                                        {
                                                fputs( $f, "                                                <description></description>\n" );
                                        }

                                        fputs( $f, "                                        </offer>\n");

                                }


                                fputs( $f, "                                </offers>\n");
                        }

                        function _exportEnd( $f )
                        {
                                fputs( $f, "                        </shop>\n" );
                                fputs( $f, "                </yml_catalog>");
                        }


                if (isset($_GET["yandex_export_successful"])) //show successful save confirmation message
                {
                        if (file_exists("core/temp/yandex.xml"))
                        {
                                $smarty->assign("yandex_export_successful", 1);
                                $smarty->assign("yandex_filesize", (string) round( filesize("core/temp/yandex.xml") / 1024 ) );
                        }
                }

                if (!isset($_POST["yandex_export"]))$_POST["yandex_export"] = '';
                if ($_POST["yandex_export"]) //save payment gateways_settings
                {
                        $_SESSION["yandsess"] = $_POST["categories_select"];

                        $rurrate = (float)$_POST["yandex_rur_rate"];
                        $yandex_export_product_name = isset($_POST['yandex_export_product_name'])?$_POST['yandex_export_product_name']:'only_name';

                        if ($rurrate <= 0)
                        {
                                $smarty->assign( "yandex_errormsg", "Курс рубля указан неверно. Пожалуйста, вводите положительное число" );
                        }
                        else //экспортировать товары
                        {
                                if (file_exists("core/temp/yandex.xml")) unlink("core/temp/yandex.xml");
                                $f = @fopen("core/temp/yandex.xml","w");
                                if ($f)
                                {
                                        _exportToYandexMarket( $f, $rurrate, $yandex_export_product_name, $_POST["categories_select"] );
                                        fclose($f);
                                        Redirect(ADMIN_FILE."?dpt=modules&sub=yandex&yandex_export_successful=yes");
                                }
                                else
                                {

                                        $smarty->assign( "yandex_errormsg", "Ошибка при создании файла yandex.xml" );

                                }
                        }
                }

                $_SESSION["yandsess"] = (isset($_SESSION["yandsess"]) && count($_SESSION["yandsess"]) > 0)?$_SESSION["yandsess"]:array(0);
                $smarty->assign("admin_sub_dpt", "modules_yandex.tpl.html");
                $smarty->assign( "yandsess", $_SESSION["yandsess"] );
        }
        }
?>