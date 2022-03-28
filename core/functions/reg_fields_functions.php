<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

// *****************************************************************************
// Purpose        gets all additional fields (see registry form)
// Inputs   nothing
// Remarks
// Returns        array of item
//                                each item
//                                        "reg_field_ID"                        - field id
//                                        "reg_field_name"                - field name
//                                        "reg_field_required"        - 1, if field is required to set
//                                        "sort_order"                        - sort order
function GetRegFields()
{
        $q=db_query("select reg_field_ID, reg_field_name, reg_field_required, sort_order from ".
                CUSTOMER_REG_FIELDS_TABLE." order by sort_order, reg_field_name " );
        $data=array();
        while( $row=db_fetch_row($q) ) $data[]=$row;
        return $data;
}


// *****************************************************************************
// Purpose        add additional field
// Inputs                $reg_field_name                        - field name
//                                $reg_field_required                - 1, if field is required to set
//                                $sort_order                                - sort order
// Remarks
// Returns        nothing
function AddRegField($reg_field_name, $reg_field_required, $sort_order)
{
        db_query("insert into ".CUSTOMER_REG_FIELDS_TABLE.
                "(reg_field_name, reg_field_required, sort_order) ".
                "values( '".xToText(trim($reg_field_name))."', ".(int)$reg_field_required.", ".(int)$sort_order." ) ");
}


// *****************************************************************************
// Purpose        delete additional field
// Inputs                $reg_field_ID                        - field id
// Remarks
// Returns        nothing
function DeleteRegField($reg_field_ID)
{
        db_query("delete from ".CUSTOMER_REG_FIELDS_VALUES_TABLE.
                " where reg_field_ID=".(int)$reg_field_ID);
        db_query("delete from ".CUSTOMER_REG_FIELDS_TABLE.
                " where reg_field_ID=".(int)$reg_field_ID);
}


// *****************************************************************************
// Purpose        update additional field
// Inputs
//                                $reg_field_ID                        - field id
//                                $reg_field_name                        - field name
//                                $reg_field_required                - 1, if field is required to set
//                                $sort_order                                - sort order
// Remarks
// Returns        nothing
function UpdateRegField($reg_field_ID, $reg_field_name,
        $reg_field_required, $sort_order)
{
        db_query(
                        "update ".CUSTOMER_REG_FIELDS_TABLE." set ".
                        "reg_field_name='".xToText(trim($reg_field_name))."', ".
                        "reg_field_required=".(int)$reg_field_required.", ".
                        "sort_order=".(int)$sort_order." ".
                        "where reg_field_ID=".(int)$reg_field_ID);
}


// *****************************************************************************
// Purpose        set additional field value to customer
// Inputs
//                                $reg_field_ID                - field id
//                                $customer_login                - login
//                                $reg_field_value        - value (string)
// Remarks
// Returns        nothing
function SetRegField($reg_field_ID, $customer_login, $reg_field_value)
{

        $customerID = regGetIdByLogin( $customer_login );
        $q=db_query("select count(*) from ".CUSTOMER_REG_FIELDS_VALUES_TABLE.
                " where reg_field_ID=".(int)$reg_field_ID." AND customerID=".(int)$customerID);
        $r=db_fetch_row($q);
        if ( $r[0] == 0 )
        {
                if ( trim($reg_field_value) == "" ) return;
                db_query("insert into ".CUSTOMER_REG_FIELDS_VALUES_TABLE.
                        "(reg_field_ID, customerID, reg_field_value) ".
                        "values( '".(int)$reg_field_ID."', '".(int)$customerID."', '".xToText(trim($reg_field_value))."' )");
        }
        else
        {
                if ( trim($reg_field_value) == "" )
                        db_query( "delete from ".CUSTOMER_REG_FIELDS_VALUES_TABLE.
                                " where reg_field_ID=".(int)$reg_field_ID." AND  ".
                                "         customerID=".(int)$customerID);
                else
                        db_query("update ".CUSTOMER_REG_FIELDS_VALUES_TABLE." set ".
                                " reg_field_value='".xToText(trim($reg_field_value))."' ".
                                " where reg_field_ID=".(int)$reg_field_ID." AND customerID=".(int)$customerID);
        }
}


// *****************************************************************************
// Purpose
// Inputs
//                                $reg_field_ID                - field id
//                                $customer_login                - login
//                                $reg_field_value        - value (string)
// Remarks
// Returns        1 if field requred to set, 0 otherwise
function GetIsRequiredRegField($reg_field_ID)
{
        $q=db_query("select reg_field_required from ".CUSTOMER_REG_FIELDS_TABLE.
                " where reg_field_ID=".(int)$reg_field_ID);
        $r=db_fetch_row($q);
        return $r["reg_field_required"];
}


// *****************************************************************************
// Purpose        gets additional reg fields values of a registered customer
// Inputs        customerID
// Remarks
// Returns        array of item
//                                each item
//                                        "reg_field_ID"                        - field id
//                                        "reg_field_name"                - field name
//                                        "reg_field_value"                - value
function GetRegFieldsValuesByCustomerID( $customerID )
{
        //get customer
        if (!$customerID) return array();

        $q = db_query("select reg_field_ID, reg_field_name from ".
                CUSTOMER_REG_FIELDS_TABLE." order by sort_order, reg_field_name ");
        $data=array();
        while( $r=db_fetch_row($q) )
        {
                $q1=db_query("select reg_field_value from ".
                        CUSTOMER_REG_FIELDS_VALUES_TABLE." where reg_field_ID=".(int)$r["reg_field_ID"].
                                " AND customerID=".(int)$customerID);
                $reg_field_value="";
                if ( $r1=db_fetch_row($q1) ) $reg_field_value = $r1["reg_field_value"];
                if ( strlen( trim($reg_field_value) ) > 0 )
                {
                        $row=array();
                        $row["reg_field_ID"]   = $r["reg_field_ID"];
                        $row["reg_field_name"] = $r["reg_field_name"];
                        $row["reg_field_value"]= $reg_field_value;
                        $data[]=$row;
                }
        }
        return $data;
}


// *****************************************************************************
// Purpose        gets additional reg fields values of a registered customer
// Inputs        customer login
// Remarks
// Returns        array of item
//                                each item
//                                        "reg_field_ID"                        - field id
//                                        "reg_field_name"                - field name
//                                        "reg_field_value"                - value
function GetRegFieldsValues( $customer_login )
{
        //get customer
        $customerID = regGetIdByLogin( $customer_login );
        if (!$customerID) return array();

        return GetRegFieldsValuesByCustomerID( $customerID );
}

// *****************************************************************************
// Purpose        gets additional field values of a customer by orderID
// Inputs
// Remarks
// Returns        array of item
//                                each item
//                                        "reg_field_ID"                        - field id
//                                        "reg_field_name"                - field name
//                                        "reg_field_value"                - value
function GetRegFieldsValuesByOrderID( $orderID )
{
        if (!$orderID) return array();

        //check if this order has been made by a registered customer or not (quick checkout)
        $q=db_query("select customerID from ".
                ORDERS_TABLE." where orderID = ".(int)$orderID);
        $row = db_fetch_row($q);
        if ($row[0] > 0)
                return GetRegFieldsValuesByCustomerID( $row[0] ); //made by a registered customer

        //quick checkout
        $q=db_query("select reg_field_ID, reg_field_name from ".
                CUSTOMER_REG_FIELDS_TABLE." order by sort_order, reg_field_name ");
        $data = array();
        while( $r=db_fetch_row($q) )
        {
                $q1=db_query("select reg_field_value from ".
                        CUSTOMER_REG_FIELDS_VALUES_TABLE_QUICKREG." where reg_field_ID=".(int)$r["reg_field_ID"].
                                " AND orderID=".(int)$orderID);
                $reg_field_value="";
                if ( $r1=db_fetch_row($q1) ) $reg_field_value = $r1["reg_field_value"];
                if ( strlen( trim($reg_field_value) ) > 0 )
                {
                        $row=array();
                        $row["reg_field_ID"]    = $r["reg_field_ID"];
                        $row["reg_field_name"]  = $r["reg_field_name"];
                        $row["reg_field_value"] = $reg_field_value;
                        $data[]=$row;
                }
        }
        return $data;
}

?>