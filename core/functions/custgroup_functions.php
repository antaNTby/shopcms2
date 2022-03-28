<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################



        function GetCustomerGroupByCustomerId( $customerID )
        {
                $q = db_query( "select custgroupID from ".CUSTOMERS_TABLE.
                                " where customerID=".(int)$customerID);
                $customer = db_fetch_row($q);

                if ( is_null($customer["custgroupID"]) || trim($customer["custgroupID"])=="" )
                        return false;

                $q = db_query("select custgroupID, custgroup_name, custgroup_discount, sort_order from ".
                                CUSTGROUPS_TABLE." where custgroupID=".$customer["custgroupID"] );
                $row = db_fetch_row($q);
                return $row;
        }


        function GetAllCustGroups()
        {
                $q=db_query("select custgroupID, custgroup_name, custgroup_discount, sort_order from ".
                                CUSTGROUPS_TABLE." order by sort_order, custgroup_name ");
                $data=array();
                while( $r=db_fetch_row($q) ) $data[]=$r;
                return $data;
        }

        function DeleteCustGroup($custgroupID)
        {
                db_query("update ".CUSTOMERS_TABLE." set custgroupID=NULL where custgroupID=".(int)$custgroupID);
                db_query("delete from ".CUSTGROUPS_TABLE." where custgroupID=".(int)$custgroupID);
        }

        function UpdateCustGroup($custgroupID, $custgroup_name, $custgroup_discount, $sort_order )
        {
                db_query(
                                "update ".CUSTGROUPS_TABLE." set  ".
                                "custgroup_name='".xToText($custgroup_name)."', ".
                                "custgroup_discount='".(float)$custgroup_discount."', ".
                                "sort_order=".(int)$sort_order." ".
                                "where custgroupID=".(int)$custgroupID
                        );
        }


        function AddCustGroup( $custgroup_name, $custgroup_discount, $sort_order)
        {
                db_query("insert into ".CUSTGROUPS_TABLE.
                        "( custgroup_name, custgroup_discount, sort_order ) ".
                        "values( '".xToText($custgroup_name)."', '".(float)$custgroup_discount."', '".(int)$sort_order."' )");
        }

?>