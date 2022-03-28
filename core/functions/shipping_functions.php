<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################




// *****************************************************************************
// Purpose  delete shipping method
// Inputs
// Remarks
// Returns  nothing
function shDeleteShippingMethod( $SID )
{
        db_query("delete from ".SHIPPING_METHODS_TABLE." where SID=".(int)$SID);
}



// *****************************************************************************
// Purpose  get payment methods by module
// Inputs
// Remarks
// Returns
function shGetShippingMethodsByModule( $shippingModule )
{
        $moduleID = $shippingModule->get_id();

        if ( strlen($moduleID) == 0 )
                return array();

        $moduleID = (int)$moduleID;

        $q = db_query("select SID, Name, description, Enabled, sort_order, ".
                        " email_comments_text, module_id ".
                        " from ".SHIPPING_METHODS_TABLE." where module_id=".(int)$moduleID );
        $data = array();
        while( $row = db_fetch_row($q) ) $data[] = $row;
        return $data;
}




// *****************************************************************************
// Purpose  get shipping method by ID
// Inputs
// Remarks
// Returns
function shGetShippingMethodById( $shippingMethodID )
{
        $q = db_query( "select SID, Name, description, Enabled, sort_order, email_comments_text, module_id from ".
                SHIPPING_METHODS_TABLE." where SID=".(int)$shippingMethodID);
        $row=db_fetch_row($q);
        return $row;
}


// *****************************************************************************
// Purpose  get all shipping methods
// Inputs
// Remarks
// Returns  nothing
function shGetAllShippingMethods( $enabledOnly = false )
{
        $whereClause = "";
        if ( $enabledOnly ) $whereClause = " where Enabled=1 ";
        $q = db_query("select SID, Name, description, Enabled, sort_order, email_comments_text, module_id from ".
                                SHIPPING_METHODS_TABLE." ".$whereClause." order by sort_order");
        $data = array();
        while( $row = db_fetch_row($q) ) $data[] = $row;
        return $data;
}


// *****************************************************************************
// Purpose  get all installed shipping modules
// Inputs
// Remarks
// Returns  nothing
function shGetInstalledShippingModules()
{
        $moduleFiles = GetFilesInDirectory( "core/modules/shipping", "php" );
        $shipping_modules = array();
        foreach( $moduleFiles as $fileName )
        {
                $className = GetClassName( $fileName );
                if(!$className)continue;
                eval( "\$shipping_module = new ".$className."();" );
                if ( $shipping_module->is_installed() )
                        $shipping_modules[] = $shipping_module;
        }
        return $shipping_modules;
}


// *****************************************************************************
// Purpose  add shipping method
// Inputs
// Remarks
// Returns  nothing
function shAddShippingMethod( $Name, $description, $Enabled, $sort_order,
                                $module_id, $email_comments_text )
{
        db_query("insert into ".SHIPPING_METHODS_TABLE.
                        " ( Name, description, email_comments_text, Enabled, module_id, sort_order  ) values".
                        " ( '".xToText(trim($Name))."', '".xEscSQL($description)."', '".xEscSQL($email_comments_text)."', ".(int)$Enabled.", ".(int)$module_id.", ".(int)$sort_order." )" );
        return db_insert_id();
}


// *****************************************************************************
// Purpose  update shipping method
// Inputs
// Remarks
// Returns  nothing
function shUpdateShippingMethod($SID, $Name, $description, $Enabled, $sort_order,
                                $module_id, $email_comments_text )
{
        db_query("update ".SHIPPING_METHODS_TABLE.
                " set Name='".xToText(trim($Name))."', description='".xEscSQL($description)."', email_comments_text='".xEscSQL($email_comments_text)."', ".
                " Enabled=".(int)$Enabled.", module_id=".(int)$module_id.", sort_order=".(int)$sort_order." where SID=".(int)$SID);
}


// *****************************************************************************
// Purpose
// Inputs   $shippingMethodID - shipping exists
// Remarks
// Returns  true if shipping method is exists
function shShippingMethodIsExist( $shippingMethodID )
{
        $q_count = db_query( "select count(*) from ".SHIPPING_METHODS_TABLE.
                        " where SID=".(int)$shippingMethodID." AND Enabled=1" );
        $counts = db_fetch_row( $q_count );
        return ( $counts[0] != 0 );
}

?>