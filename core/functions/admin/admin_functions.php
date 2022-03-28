<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


function GetAllAdminAttributes()
{
        $q = db_query("select customerID, Login, actions from ".CUSTOMERS_TABLE." where actions!='' ORDER BY Login ASC");
        $data = array();
        while( $row = db_fetch_row( $q ) )
        {
                $row[2] = unserialize( $row[2] );
                if(in_array(100,$row[2]))$data[] = $row;
        }
        return $data;
}

function CheckLoginAdminNew($login)
{
        $q = db_query("select count(*) from ".CUSTOMERS_TABLE." where Login='".xEscSQL($login)."'");
                      $n = db_fetch_row($q);
                      $data = $n[0];
        return $data;
}

function adminpgGetadminPage( $admin_ID )
{
        $q = db_query("select Login, actions from ".CUSTOMERS_TABLE." where customerID=".(int)$admin_ID);
        $row = db_fetch_row($q);
        $row[1] = unserialize( $row[1] );
        return $row;
}



function UpdateAdminRights( $edit_num, $actions)
{
        $actions[] = 100;
        $actions = xEscSQL(serialize ($actions));
        db_query("update ".CUSTOMERS_TABLE." set actions='".$actions."' where customerID=".(int)$edit_num);
}


function adminpgDeleteadmin( $admin_page_ID )
{
        db_query("delete from ".CUSTOMERS_TABLE." where customerID=".(int)$admin_page_ID);
}


?>