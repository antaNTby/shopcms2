<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################




// *****************************************************************************
// Purpose        gets all options
// Inputs
// Remarks
// Returns
function configGetOptions()
{
        $options = db_query("select optionID, name FROM ".PRODUCT_OPTIONS_TABLE);
        $data = array();
        while( $option_row = db_fetch_row($options) ) $data[] = $option_row;
        return $data;
}



function configGetProductOptionValue( $productID )
{
        $data = array();
        $options = db_query("select optionID, name FROM ".PRODUCT_OPTIONS_TABLE." order by sort_order, name");
        while( $option_row = db_fetch_row($options) )
        {
                $item = array();
                $item["option_row"] = $option_row;
                $item["option_value"] = null;
                $value = db_query("select option_value, option_type, option_show_times FROM ".
                                PRODUCT_OPTIONS_VALUES_TABLE." WHERE optionID=".(int)$option_row["optionID"].
                                " AND productID=".(int)$productID);
                if (   !($value_row=db_fetch_row($value))   )
                {
                        $value_row["option_value"] = null;
                        $value_row["option_type"] = 0;
                        $value_row["option_show_times"] = 1;
                }
                $item["option_value"] = $value_row;

                $q=db_query("select COUNT(*) from ".PRODUCTS_OPTIONS_SET_TABLE.
                                " where optionID=".(int)$option_row["optionID"].
                                " AND productID=".(int)$productID);
                $r=db_fetch_row($q);
                $item["value_count"]=$r[0];
                $data[] = $item;
        }
        return $data;
}

function configSet_N_VALUES_OptionType( $productID, $optionID )
{
        $q = db_query( "select count(*) from ".PRODUCT_OPTIONS_VALUES_TABLE.
                        " where optionID=".(int)$optionID." AND productID=".(int)$productID);
        $count = db_fetch_row($q);
        $count = $count[0];

        if ( $count == 0 )
        {
                db_query( "insert into ".PRODUCT_OPTIONS_VALUES_TABLE.
                        " ( optionID, productID, option_value, option_type, option_show_times ) ".
                        " values( ".(int)$optionID.", ".(int)$productID.", '', 2, 1 ) ");
        }
        else
        {
                db_query( "update ".PRODUCT_OPTIONS_VALUES_TABLE.
                        " set option_type=1 ".
                        " where productID=".(int)$productID." AND optionID=".(int)$optionID);
        }
}




function configUpdateOptionValue( $productID, $updatedValues )
{
        foreach( $updatedValues as $key => $value )
        {
                if ( $updatedValues[$key]["option_radio_type"] == "UN_DEFINED" ||
                                $updatedValues[$key]["option_radio_type"] == "ANY_VALUE" )
                        $option_type=0;
                else
                        $option_type=1;
                if ( $updatedValues[$key]["option_radio_type"] == "UN_DEFINED" )
                        $option_value=null;
                else
                {
                        if ( isset($updatedValues[$key]["option_value"]) )
                                $option_value=$updatedValues[$key]["option_value"];
                        else
                                $option_value=null;
                }

                $where_clause = " where optionID=".(int)$key." AND productID=".(int)$productID;

                $q=db_query("select count(*) from ".PRODUCT_OPTIONS_VALUES_TABLE." ".$where_clause );

                $r = db_fetch_row($q);

                if ( $r[0]==1 ) // if row exists
                {
                        db_query("update ".PRODUCT_OPTIONS_VALUES_TABLE." set option_value='".
                                xEscSQL($option_value)."', option_type=".(int)$option_type." ".
                                $where_clause );
                }
                else // insert query
                {
                        db_query("insert into ".
                                PRODUCT_OPTIONS_VALUES_TABLE.
                                "(optionID, productID, option_value, option_type)".
                                "values ('".(int)$key."', '".(int)$productID."', '".xEscSQL($option_value).
                                        "', '".(int)$option_type."')");
                }
        }
}


// *****************************************************************************
// Purpose        this function updates product option that can be configurated by customer
// Inputs                     $option_show_times - how many times do show in user part
//                        $variantID_default - option id (FK) refers to
//                                PRODUCTS_OPTIONS_VALUES_VARIANTS_TABLE (PK)
//                        $setting - structure
//                                $setting[ <optionID> ]["switchOn"] - if true show this
//                                                value in user part
//                                $setting[ <optionID> ]["price_surplus"] - price surplus when
//                                                this option is selected by user
// Remarks
// Returns                nothing
function UpdateConfiguriableProductOption($optionID, $productID,
                $option_show_times, $variantID_default, $setting )
{
        $where_clause=" where optionID=".(int)$optionID." AND productID=".(int)$productID;
        $q=db_query( "select count(*) from ".PRODUCT_OPTIONS_VALUES_TABLE.$where_clause );
        $r=db_fetch_row($q);
        if ( $r[0]!=0 )
        {
                 db_query("update ".PRODUCT_OPTIONS_VALUES_TABLE.
                         " set option_value='', ".
                         " option_show_times='".(int)$option_show_times."', ".
                         " variantID=".(int)$variantID_default." ".
                         $where_clause );
        }
        else
        {
                 db_query("insert into ".PRODUCT_OPTIONS_VALUES_TABLE.
                         "(optionID, productID, option_type, option_value, ".
                         "option_show_times, variantID) ".
                         "values('".(int)$optionID."', '".(int)$productID."', 0, '', '".
                         (int)$option_show_times."',  ".
                         (int)$variantID_default."  )");
        }

        $q1=db_query("select variantID from ".PRODUCTS_OPTIONS_VALUES_VARIANTS_TABLE.
                         " where optionID=".(int)$optionID);
        $if_only = false;
        while( $r1=db_fetch_row($q1) )
        {
                $key = $r1["variantID"];
                $where_clause=" where productID=".(int)$productID." AND optionID=".(int)$optionID.
                                 " AND variantID=".(int)$key;
                if ( !isset($setting[$key]["switchOn"]) )
                {
                        db_query( "delete from ".PRODUCTS_OPTIONS_SET_TABLE.$where_clause );
                }
                else
                {
                        $q=db_query("select count(*) from ".PRODUCTS_OPTIONS_SET_TABLE.
                                        $where_clause);
                        $r=db_fetch_row($q);
                        if ( $r[0]!=0 )
                        {
                                db_query("update ".PRODUCTS_OPTIONS_SET_TABLE." set price_surplus='".
                                        (float)$setting[$key]["price_surplus"]."'".$where_clause );
                                $if_only = true;
                        }
                        else
                        {
                                db_query("insert into ".PRODUCTS_OPTIONS_SET_TABLE.
                                         "(productID, optionID, variantID, price_surplus)".
                                         "values( '".(int)$productID."', '".
                                                (int)$optionID."', '".(int)$key."', '".
                                                (float)$setting[$key]["price_surplus"]."' )"
                                 );
                                $if_only = true;
                        }
                }
        }
        if ( !$if_only )
        {
                db_query("update ".PRODUCT_OPTIONS_VALUES_TABLE.
                         " set option_show_times=0 where optionID=".(int)$optionID." AND ".
                                " productID=".(int)$productID);
        }
}

?>