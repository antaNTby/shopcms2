<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


        if ( isset($categoryID) && !isset($_GET["search_with_change_category_ability"]) && !isset($productID))
        {

                if ( isset($_GET["prdID"]) )
                        $_GET["prdID"] = (int)$_GET["prdID"];
                if ( isset($_GET["search_price_from"]) )
                        if ( trim($_GET["search_price_from"]) != "" )
                                $_GET["search_price_from"] = (int)$_GET["search_price_from"];
                if ( isset($_GET["search_price_to"]) )
                        if (  trim($_GET["search_price_to"])!="" )
                                $_GET["search_price_to"] = (int)$_GET["search_price_to"];
                if ( isset($_GET["categoryID"]) )
                        $_GET["categoryID"] = (int)$_GET["categoryID"];
                if ( isset($_GET["offset"]) )
                        $_GET["offset"] = (int)$_GET["offset"];

                if  (  !catGetCategoryById($_GET["categoryID"])  )  {
                header("HTTP/1.0 404 Not Found");
                header("HTTP/1.1 404 Not Found");
                header("Status: 404 Not Found");
                die(ERROR_404_HTML);
                }

                function _getUrlToNavigate( $categoryID )
                {
                        $url = "index.php?categoryID=".$categoryID;
                        $data = ScanGetVariableWithId( array("param") );
                        if ( isset($_GET["search_name"]) )
                                $url .= "&search_name=".$_GET["search_name"];
                        if ( isset($_GET["search_price_from"]) )
                                $url .= "&search_price_from=".$_GET["search_price_from"];
                        if ( isset($_GET["search_price_to"]) )
                                $url .= "&search_price_to=".$_GET["search_price_to"];
                        foreach( $data as $key => $val )
                        {
                                $url .= "&param_".$key;
                                $url .= "=".$val["param"];
                        }
                        if ( isset($_GET["search_in_subcategory"]) )
                                $url .= "&search_in_subcategory=1";
                        if ( isset($_GET["sort"]) )
                                $url .= "&sort=".$_GET["sort"];
                        if ( isset($_GET["direction"]) )
                                $url .= "&direction=".$_GET["direction"];
                        if ( isset($_GET["advanced_search_in_category"]) )
                                $url .= "&advanced_search_in_category=".$_GET["advanced_search_in_category"];
                        if (CONF_MOD_REWRITE && $url == "index.php?categoryID=".$categoryID)
                                $url = "category_".$categoryID;
                        return $url;
                }

                function _getUrlToSort( $categoryID )
                {
                        $url = "index.php?categoryID=$categoryID";
                        $data = ScanGetVariableWithId( array("param") );
                        if ( isset($_GET["search_name"]) )
                                $url .= "&search_name=".$_GET["search_name"];
                        if ( isset($_GET["search_price_from"]) )
                                $url .= "&search_price_from=".$_GET["search_price_from"];
                        if ( isset($_GET["search_price_to"]) )
                                $url .= "&search_price_to=".$_GET["search_price_to"];
                        foreach( $data as $key => $val )
                        {
                                $url .= "&param_".$key;
                                $url .= "=".$val["param"];
                        }
                        if ( isset($_GET["offset"]) )
                                $url .= "&offset=".$_GET["offset"];
                        if ( isset($_GET["show_all"]) )
                                $url .= "&show_all=yes";
                        if ( isset($_GET["search_in_subcategory"]) )
                                $url .= "&search_in_subcategory=1";
                        if ( isset($_GET["advanced_search_in_category"]) )
                                $url .= "&advanced_search_in_category=".$_GET["advanced_search_in_category"];
                        return $url;
                }

                function _sortSetting( &$smarty, $urlToSort )
                {
                        if(CONF_USE_RATING == 1){
                        $sort_string = STRING_PRODUCT_SORTN;
                        }else{
                        $sort_string = STRING_PRODUCT_SORT;
                        }
                        $sort_string = str_replace( "{ASC_NAME}",   "<a href='".$urlToSort."&sort=name&direction=ASC'>".STRING_ASC."</a>",        $sort_string );
                        $sort_string = str_replace( "{DESC_NAME}",  "<a href='".$urlToSort."&sort=name&direction=DESC'>".STRING_DESC."</a>",        $sort_string );
                        $sort_string = str_replace( "{ASC_PRICE}",   "<a href='".$urlToSort."&sort=Price&direction=ASC'>".STRING_ASC."</a>",        $sort_string );
                        $sort_string = str_replace( "{DESC_PRICE}",  "<a href='".$urlToSort."&sort=Price&direction=DESC'>".STRING_DESC."</a>",        $sort_string );
                        $sort_string = str_replace( "{ASC_RATING}",   "<a href='".$urlToSort."&sort=customers_rating&direction=ASC'>".STRING_ASC."</a>",        $sort_string );
                        $sort_string = str_replace( "{DESC_RATING}",  "<a href='".$urlToSort."&sort=customers_rating&direction=DESC'>".STRING_DESC."</a>",        $sort_string );
                        $smarty->assign( "string_product_sort", html_amp($sort_string));

                }

                //get selected category info
                $category = catGetCategoryById( $categoryID );
                if ( !$category )
                {
                                header("HTTP/1.0 404 Not Found");
                                header("HTTP/1.1 404 Not Found");
                                header("Status: 404 Not Found");
                                die(ERROR_404_HTML);
                }
                else
                {
                       if(!$adminislog) IncrementCategoryViewedTimes($categoryID);

                        if ( isset($_GET["prdID"]) )
                        {
                                if (  isset($_POST["cart_".$_GET["prdID"]."_x"])  )
                                {
                                        $variants=array();
                                        foreach( $_POST as $key => $val )
                                        {
                                                if ( strstr($key, "option_select_hidden") )
                                                {
                                                        $arr=explode( "_", str_replace("option_select_hidden_","",$key) );
                                                        if ( (string)$arr[1] == (string)$_GET["prdID"] )
                                                                $variants[]=$val;
                                                }
                                        }
                                        unset($_SESSION["variants"]);
                                        $_SESSION["variants"]=$variants;
                                        Redirect( "index.php?shopping_cart=yes&add2cart=".$_GET["prdID"]."&multyaddcount=".(int)$_POST['multyaddcount'] );
                                }
                        }

                        //category thumbnail
                        if (!file_exists("data/category/".$category["picture"])) $category["picture"] = "";
                        $smarty->assign("selected_category", $category );


                        if ( $category["show_subcategories_products"] == 1 )
                                $smarty->assign( "show_subcategories_products", 1 );

                        if ( $category["allow_products_search"] )
                                $smarty->assign( "allow_products_search", 1 );

                        $callBackParam               = array();
                        $products                    = array();
                        $callBackParam["categoryID"] = (int)$categoryID;
                        $callBackParam["enabled"]    = 1;

                        if (  isset($_GET["search_in_subcategory"]) )
                                if ( $_GET["search_in_subcategory"] == 1 )
                                {
                                        $callBackParam["searchInSubcategories"] = true;
                                        $callBackParam["searchInEnabledSubcategories"] = true;
                                }

                        if ( isset($_GET["sort"]) )
                                $callBackParam["sort"] = $_GET["sort"];
                        if ( isset($_GET["direction"]) )
                                $callBackParam["direction"] = $_GET["direction"];

                        // search parametrs to advanced search
                        if ( $extraParametrsTemplate != null )
                                        $callBackParam["extraParametrsTemplate"] = $extraParametrsTemplate;
                        if ( $searchParamName != null )
                                        $callBackParam["name"] = $searchParamName;
                        if ( $rangePrice != null )
                                        $callBackParam["price"] = $rangePrice;

                        if ( $category["show_subcategories_products"] ) $callBackParam["searchInSubcategories"] = true;

                        $count = 0;
                        if (CONF_MOD_REWRITE){
                        $urlfarse = _getUrlToNavigate( $categoryID );
                        if($urlfarse == "category_".$categoryID) $urlflag = 1; else $urlflag = 0;
                        $navigatorHtml = GetNavigatorHtmlmd(
                                                $urlfarse, CONF_PRODUCTS_PER_PAGE,
                                                'prdSearchProductByTemplate', $callBackParam,
                                                $products, $offset, $count, $urlflag );
												$navigatorHtml = strtr($navigatorHtml,array("_offset_0"=>""));
                        }else{
                        $navigatorHtml = GetNavigatorHtml(
                                                _getUrlToNavigate( $categoryID ), CONF_PRODUCTS_PER_PAGE,
                                                'prdSearchProductByTemplate', $callBackParam,
                                                $products, $offset, $count );
												$navigatorHtml = strtr($navigatorHtml,array("&offset=0"=>"","&amp;offset=0"=>""));
                        }
                        $show_comparison = $category["allow_products_comparison"];
                        $cc_products = count($products);
                        for($i=0; $i<$cc_products; $i++) $products[$i]["allow_products_comparison"] = $show_comparison;


                        if (CONF_PRODUCT_SORT) _sortSetting( $smarty, _getUrlToSort($categoryID) );

                        if(CONF_SHOW_PARENCAT){
                        $smarty->assign( "catrescur", getcontentcatresc($categoryID));
                        }
                        $smarty->assign( "subcategories_to_be_shown", catGetSubCategoriesSingleLayer($categoryID) );
                        $smarty->assign( "categorylinkscat", getcontentcat($categoryID));
                        //calculate a path to the category
                        $smarty->assign( "product_category_path",
                                                catCalculatePathToCategory($categoryID) );
                        $smarty->assign( "show_comparison", $show_comparison );
                        $smarty->assign( "catalog_navigator", $navigatorHtml );
                        $smarty->assign( "products_to_show", $products);
                        $smarty->assign( "products_to_show_counter", count($products));
                        if(isset($_GET["advanced_search_in_category"])){
                        $smarty->assign( "products_to_showc", count($products));
                        }else{
                        if ( $category["show_subcategories_products"] )
                        $smarty->assign( "products_to_showc", $category["products_count"]);
                        else $smarty->assign( "products_to_showc", catGetCategoryProductCount( $categoryID, true));
                        }
                        $smarty->assign( "categoryID", $categoryID);
                        $smarty->assign( "categoryName", $category["name"]);
                        $smarty->assign( "main_content_template", "category.tpl.html");
                }
        }
?>