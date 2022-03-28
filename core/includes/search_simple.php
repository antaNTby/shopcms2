<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        // simple search
        if (isset($_GET["inside"]))
                $smarty->assign("search_in_results", $_GET["inside"]);

        if (isset($_GET["searchstring"])) //make a simple search
        {

                function _getUrlToNavigate()
                {
                        $url = "index.php?searchstring=".$_GET["searchstring"];
                        if ( isset($_GET["x"]) )
                                $url .= "&x=".$_GET["x"];
                        if ( isset($_GET["y"]) )
                                $url .= "&y=".$_GET["y"];
                        if ( isset($_GET["sort"]) )
                                $url .= "&sort=".$_GET["sort"];
                        if ( isset($_GET["direction"]) )
                                $url .= "&direction=".$_GET["direction"];
                        return $url;
                }

                function _getUrlToSort()
                {
                        $url = "index.php?searchstring=".$_GET["searchstring"];
                        if ( isset($_GET["x"]) )
                                $url .= "&x=".$_GET["x"];
                        if ( isset($_GET["y"]) )
                                $url .= "&y=".$_GET["y"];
                        if ( isset($_GET["offset"]) )
                                $url .= "&offset=".$_GET["offset"];
                        if ( isset($_GET["show_all"]) )
                                $url .= "&show_all=yes";
                        return $url;
                }

                function _sortSetting( &$smarty, $urlToSort )
                {
                        $sort_string = STRING_PRODUCT_SORT;
                        $sort_string = str_replace( "{ASC_NAME}",   "<a href='".$urlToSort."&sort=name&direction=ASC'>".STRING_ASC."</a>",        $sort_string );
                        $sort_string = str_replace( "{DESC_NAME}",  "<a href='".$urlToSort."&sort=name&direction=DESC'>".STRING_DESC."</a>",        $sort_string );
                        $sort_string = str_replace( "{ASC_PRICE}",   "<a href='".$urlToSort."&sort=Price&direction=ASC'>".STRING_ASC."</a>",        $sort_string );
                        $sort_string = str_replace( "{DESC_PRICE}",  "<a href='".$urlToSort."&sort=Price&direction=DESC'>".STRING_DESC."</a>",        $sort_string );
                        $sort_string = str_replace( "{ASC_RATING}",   "<a href='".$urlToSort."&sort=customers_rating&direction=ASC'>".STRING_ASC."</a>",        $sort_string );
                        $sort_string = str_replace( "{DESC_RATING}",  "<a href='".$urlToSort."&sort=customers_rating&direction=DESC'>".STRING_DESC."</a>",        $sort_string );
                        $smarty->assign( "string_product_sort", html_amp($sort_string) );
                }

                $searchstrings = array();
                $tmp = explode(" ", $_GET["searchstring"]);
                foreach( $tmp as $key=> $val )
                {
                        if ( strlen( trim($val) ) > 0 ) $searchstrings[] = $val;
                }

                if ( isset($_GET["inside"]) )
                {
                        $data = ScanGetVariableWithId( array("search_string") );
                        foreach( $data as $key => $value ) $searchstrings[] = $value["search_string"];
                }
                $smarty->hassign( "searchstrings", $searchstrings );

                $callBackParam = array();
                $products      = array();
                $callBackParam["search_simple"] = $searchstrings;

                if ( isset($_GET["sort"]) ) $callBackParam["sort"] = $_GET["sort"];
                if ( isset($_GET["direction"]) ) $callBackParam["direction"] = $_GET["direction"];

                $countTotal = 0;
                $navigatorHtml = GetNavigatorHtml(
                                        _getUrlToNavigate(), CONF_PRODUCTS_PER_PAGE,
                                        'prdSearchProductByTemplate', $callBackParam,
                                        $products, $offset, $countTotal );

                if ( CONF_PRODUCT_SORT == '1' )
                        _sortSetting( $smarty, _getUrlToSort() );
                  if(CONF_ALLOW_COMPARISON_FOR_SIMPLE_SEARCH == 1){

                     $show_comparison = 0;
                        foreach ($products as $_Key=>$_Product){

                                $products[$_Key]['allow_products_comparison'] = 1;
                                $show_comparison++;
                        }
                        $smarty->assign( "show_comparison", $show_comparison );
                }
                $smarty->assign( "products_to_show",  $products );
                $smarty->assign( "products_to_show_counter", count($products));
                $smarty->assign( "products_found", $countTotal );
                $smarty->assign( "products_to_show_count", $countTotal );
                $smarty->assign( "search_navigator", $navigatorHtml );
                $smarty->assign( "main_content_template", "search_simple.tpl.html" );
        }
?>