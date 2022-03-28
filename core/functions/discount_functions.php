<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################





function _calculateGeneralPriceDiscount( $orderPrice, $log )
{
        $customerID = (int)regGetIdByLogin($log);
        $q = db_query("select discount_id, price_range, percent_discount from ".
                        ORDER_PRICE_DISCOUNT_TABLE." order by price_range " );
        $data = array();
        while( $row = db_fetch_row($q) ) $data[] = $row;

        if ( count($data) != 0 )
        {
                for( $i=0; $i<count($data)-1; $i++ )
                {
                        if ( $data[$i][ "price_range" ] < $orderPrice
                                && $orderPrice < $data[$i+1][ "price_range" ]  )
                                return $data[$i][ "percent_discount" ];
                }
                if (  $data[ count($data)-1 ][ "price_range" ] < $orderPrice  )
                        return $data[ count($data)-1 ][ "percent_discount" ];
        }

        return 0;
}


function dscCalculateDiscount( $orderPrice, $log  )
{
        $discount = array(
                        "discount_percent"       => 0,
                        "discount_standart_unit" => 0,
                        "discount_current_unit"  => 0,
                        "rest_standart_unit"     => 0,
                        "rest_current_unit"      => 0,
                        "priceUnit"              => getPriceUnit() );
        $customerID = (int)regGetIdByLogin($log);
        switch( CONF_DISCOUNT_TYPE )
        {

                // discount is switched off
                case 1:
                        return $discount;
                        break;

                // discount is based on customer group
                case 2:
                        if (  !is_bool($customerID=regGetIdByLogin($log))  )
                        {
                                $customer_group                 = GetCustomerGroupByCustomerId( $customerID );
                                if ( $customer_group )
                                        $discount["discount_percent"]         = $customer_group["custgroup_discount"];
                                else
                                        $discount["discount_percent"]        = 0;
                        }
                        else
                                return $discount;
                        break;

                // discount is calculated with help general order price
                case 3:
                        $discount["discount_percent"]                 = _calculateGeneralPriceDiscount( $orderPrice, $log );
                        break;

                // discount equals to discount is based on customer group plus
                //                discount calculated with help general order price
                case 4:
                        if ( !is_bool($customerID) )
                        {
                                $customer_group = GetCustomerGroupByCustomerId( $customerID );
                                if ( !$customer_group )
                                        $customer_group = array( "custgroup_discount" => 0  );
                        }
                        else
                                $customer_group["custgroup_discount"] = 0;
                        $discount["discount_percent"]                 = $customer_group["custgroup_discount"] +
                                                                        _calculateGeneralPriceDiscount(
                                                                                $orderPrice, $log );
                        break;

                // discount is calculated as MAX( discount is based on customer group,
                //                        discount calculated with help general order price  )
                case 5:
                        if ( !is_bool($customerID) )
                                $customer_group = GetCustomerGroupByCustomerId( $customerID );
                        else
                                $customer_group["custgroup_discount"] = 0;
                        if ( $customer_group["custgroup_discount"] >= _calculateGeneralPriceDiscount(
                                                        $orderPrice, $log ) )
                                $discount["discount_percent"] = $customer_group["custgroup_discount"];
                        else
                                $discount["discount_percent"] = _calculateGeneralPriceDiscount( $orderPrice, $log );
                        break;
        }

        $discount["discount_standart_unit"]        = ((float)$orderPrice/100)*(float)$discount["discount_percent"];
        $discount["discount_current_unit"]        = show_priceWithOutUnit( $discount["discount_standart_unit"] );
        $discount["rest_standart_unit"]         = $orderPrice - $discount["discount_standart_unit"];
        $discount["rest_current_unit"]          = show_priceWithOutUnit( $discount["rest_standart_unit"] );
        return $discount;
}



// *****************************************************************************
// Purpose        gets all order price discounts
// Inputs
// Remarks
// Returns
function dscGetAllOrderPriceDiscounts()
{
        $q = db_query( "select discount_id, price_range, percent_discount from ".ORDER_PRICE_DISCOUNT_TABLE.
                        " order by price_range" );
        $data = array();
        while( $row = db_fetch_row($q) ) $data[] = $row;
        return $data;
}

// *****************************************************************************
// Purpose        add order price discount
// Inputs
// Remarks
// Returns        if discount with $price_range already exists this function returns false and does not add new discount
//                        otherwise true
function dscAddOrderPriceDiscount( $price_range, $percent_discount )
{
        $q=db_query( "select price_range, percent_discount from ".ORDER_PRICE_DISCOUNT_TABLE.
                        " where price_range=".xEscSQL($price_range));
        if ( ($row=db_fetch_row($q)) )
                return false;
        else
        {
                db_query("insert into ".ORDER_PRICE_DISCOUNT_TABLE." ( price_range, percent_discount ) ".
                         " values( ".xEscSQL($price_range).", ".xEscSQL($percent_discount)." ) ");
                return true;
        }
}

// *****************************************************************************
// Purpose        delete discount
// Inputs
// Remarks
// Returns
function dscDeleteOrderPriceDiscount( $discount_id )
{
        db_query("delete from ".ORDER_PRICE_DISCOUNT_TABLE." where discount_id=".(int)$discount_id);
}

// *****************************************************************************
// Purpose        update discount
// Inputs
// Remarks
// Returns
function dscUpdateOrderPriceDiscount( $discount_id, $price_range, $percent_discount )
{
        $q=db_query( "select price_range, percent_discount from ".ORDER_PRICE_DISCOUNT_TABLE.
                        " where price_range=".xEscSQL($price_range)." AND discount_id <> ".xEscSQL($discount_id));
        if ( ($row=db_fetch_row($q)) )
                return false;
        else
        {
                db_query("update ".ORDER_PRICE_DISCOUNT_TABLE.
                        " set price_range=".xEscSQL($price_range).", percent_discount=".xEscSQL($percent_discount)." ".
                        " where discount_id=".(int)$discount_id);
                return true;
        }
}

?>