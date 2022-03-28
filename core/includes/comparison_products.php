<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        if(!isset($_SESSION["comparison"]) || !is_array($_SESSION["comparison"])) $_SESSION["comparison"] = array();
        if ( isset($comparison_products) && count($_SESSION["comparison"]) > 0)
        {
                $_SESSION["comparison"] = array_unique($_SESSION["comparison"]);
                $products = array();
                foreach( $_SESSION["comparison"] as $_productID )
                {
                        $product = GetProduct($_productID);
                        if ( $product )
                        {
                                $product["picture"]                = GetThumbnail( $_productID );
                                $product["saveWithUnit"]           = show_price($product["list_price"] - $product["Price"]);
                                if ( $product["list_price"] != 0 )
                                $product["savePercent"]            = ceil( ( ($product["list_price"] - $product["Price"])/$product["list_price"] )*100  );
                                $product["list_priceWithUnit"]     = show_price($product["list_price"]);
                                $product["PriceWithUnit"]          = show_price($product["Price"]);

                                $products[] = $product;
                        }
                }

                $options = configGetOptions();
                $definedOptions = array();
                foreach( $options as $option )
                {
                        $optionIsDefined = false;
                        foreach( $products as $product )
                        {
                                foreach( $product["option_values"] as $optionValue )
                                {
                                        if ( $optionValue["optionID"]==$option["optionID"] )
                                        {
                                                if ( $optionValue["option_type"] == 0 && $optionValue["value"]!=""
                                                        ||
                                                         $optionValue["option_type"] == 1 )
                                                {
                                                        $optionIsDefined = true;
                                                        break;
                                                }
                                        }
                                }
                        }
                        if ( $optionIsDefined )
                                $definedOptions[] = $option;
                }

                $optionIndex = 0;
                foreach( $definedOptions as $option )
                {
                        $productIndex = 0;
                        foreach( $products as $product )
                        {
                                $existFlag = false;

                                foreach( $product["option_values"] as $optionValue )
                                {
                                        if ( $optionValue["optionID"]==$option["optionID"] )
                                        {
                                                if ( $optionValue["option_type"] == 0 && $optionValue["value"]!="" )
                                                        $value = $optionValue["value"];
                                                 else if ( $optionValue["option_type"] == 1 )
                                                {
                                                        $value = "";
                                                        $extra = GetExtraParametrs( $product["productID"] );

                                                        foreach( $extra as $item )
                                                        {
                                                                if ( $item["option_type"] == 1 && $item["optionID"] == $optionValue["optionID"] && isset($item["values_to_select"]) && count( $item["values_to_select"] ) > 0 )
                                                                        //if option is defined
                                                                {
                                                                        foreach( $item["values_to_select"] as $value_to_select )
                                                                        {
                                                                                if ( $value != "" )
                                                                                        $value .= " / ".$value_to_select["option_valueWithOutPrice"];
                                                                                else
                                                                                        $value .= $value_to_select["option_valueWithOutPrice"];
                                                                        }
                                                                }
                                                        }
                                                }
                                                else
                                                        $value = STRING_VALUE_IS_UNDEFINED;

                                                // $item = array( "name" => $option["name"], "value" => $value );
                                                $products[ $productIndex ][ $optionIndex ] = $value;
                                                $existFlag = true;
                                                break;
                                        }
                                }
                                if ( !$existFlag ) $products[ $productIndex ][ $optionIndex ] =  STRING_VALUE_IS_UNDEFINED;

                                $productIndex++;
                        }
                        $optionIndex++;
                }


                $counta = count($products);
                if ( $counta > 0 )
                {
                        $smarty->assign("product_category_path",
                                catCalculatePathToCategory( $products[0]["categoryID"] ) );
                        $category = catGetCategoryById( $products[0]["categoryID"] );
                        if ( $category )
                                $smarty->assign("category_description", $category["description"]);
                }

                $smarty->assign("definedOptions", $definedOptions );
                $smarty->assign("products", $products );
                $smarty->assign("products_count", $counta );
                $smarty->assign("main_content_template", "comparison_products.tpl.html" );
        }
        $smarty->assign("compare_value", count($_SESSION["comparison"]) );

?>