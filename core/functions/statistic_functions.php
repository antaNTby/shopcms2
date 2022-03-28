<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

// *****************************************************************************
// Purpose        get remote customer computer IP address
// Inputs           $log - login
// Remarks
// Returns        nothing
function stGetCustomerIP_Address()
{
        $ip = ($_SERVER["REMOTE_ADDR"]!="") ? $_SERVER["REMOTE_ADDR"] : 0;
		$ip = (preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/is", $ip))? $ip : 0;
        return $ip;
}



// *****************************************************************************
// Purpose        adds record to customer log
// Inputs   $log - login
// Remarks
// Returns        nothing
function stAddCustomerLog( $log )
{
        $customerID =  regGetIdByLogin( $log );
        if ( $customerID != null )
        {
                $ipAddress = stGetCustomerIP_Address();
                db_query( " insert into ".CUSTOMER_LOG_TABLE.
                          "  (customerID, customer_ip, customer_logtime) ".
                          "  values( ".(int)$customerID.", '".xEscSQL($ipAddress)."', '".xEscSQL(get_current_time())."' ) " );
        }
}


// *****************************************************************************
// Purpose        gets customer log report
// Inputs   nothing
// Remarks
// Returns        array of items
//                                customerID                        - customer ID
//                                customer_ip                        - IP address of customer client PC
//                                customer_logtime                - time of loging
//                                login                                - customer login
function stGetCustomerLogReport()
{
        $q = db_query("select customerID, customer_ip, customer_logtime from ".CUSTOMER_LOG_TABLE );
        $data = array();
        while( $row = db_fetch_row($q) )
        {
                $row["customer_logtime"] = dtConvertToStandartForm( $row["customer_logtime"], 1);
                $row["login"] = regGetLoginById( $row["customerID"] );
                $data[] = $row;
        }
        return array_reverse($data);
}




function stGetLastVists( $log )
{
        $customerID =  regGetIdByLogin( $log );
        $q = db_query( "select customer_logtime from ".CUSTOMER_LOG_TABLE.
                " where customerID=".(int)$customerID." order by customer_logtime DESC");
        $data = array();
        $i = 1;
         while( $row = db_fetch_row($q) )
        {
                if ( $i <= 20 )
                        $data[] = $row;
                else
                        break;
        }
        return array_reverse( $data );
}


// *****************************************************************************
// Purpose
// Inputs   $navigatorParams - item
//                                "offset"                - count row from begin to place being shown
//                                "CountRowOnPage"        - count row on page to show on page
//            $callBackParam - item
//                                "log"                        - customer login
// Remarks
// Returns
//                                returns array of customer visit row
//                                $count_row is set to count rows
function stGetVisitsByLogin( $callBackParam, &$count_row, $navigatorParams = null )
{
        if ( $navigatorParams != null )
        {
                $offset                        = $navigatorParams["offset"];
                $CountRowOnPage        = $navigatorParams["CountRowOnPage"];
        }
        else
        {
                $offset = 0;
                $CountRowOnPage = 0;
        }

        $customerID =  regGetIdByLogin( $callBackParam["log"] );
        $q = db_query( "select customer_logtime, customer_ip from ".CUSTOMER_LOG_TABLE.
                " where customerID=".(int)$customerID." order by customer_logtime DESC" );
        $data = array();
        $i=0;
        while( $row = db_fetch_row($q)  )
        {
                if ( ($i >= $offset && $i < $offset + $CountRowOnPage) ||
                                $navigatorParams == null  )
                {
                        $row["customer_logtime"] = format_datetime( $row["customer_logtime"] );
                        $data[] = $row;
                }
                $i++;
        }
        $count_row = $i;
        return $data;
}


// *****************************************************************************
// Purpose        gets visit count
// Inputs
// Remarks
// Returns
function stGetVisitsCount( $log )
{
        $customerID =  regGetIdByLogin( $log );
        $q = db_query( "select count(*) customer_logtime from ".CUSTOMER_LOG_TABLE.
                " where customerID=".(int)$customerID);
        $row = db_fetch_row( $q );
        return $row[0];
}




// *****************************************************************************
// Purpose        delete all records in customer log
// Inputs   nothing
// Remarks
// Returns        array of items
function stClearCustomerLogReport()
{
        db_query( "delete from ".CUSTOMER_LOG_TABLE );
}




function stChangeOrderStatus( $orderID, $statusID, $comment = '', $notify = 0 )
{
        $q_status_name = db_query( "select status_name from ".ORDER_STATUES_TABLE.
                        " where statusID=".(int)$statusID);
        list($status_name) = db_fetch_row($q_status_name);
        $sql =  "insert into ".ORDER_STATUS_CHANGE_LOG_TABLE.
                " ( orderID, status_name, status_change_time, status_comment ) ".
                " values( ".(int)$orderID.", '".xToText($status_name)."', '".
                        xEscSQL(get_current_time())."', '".xToText(trim($comment))."' ) ";
        db_query($sql);

        if($notify){

                $Order                 = ordGetOrder( $orderID );
                $t                         = '';
                $Email                 = '';
                $FirstName         = '';
                regGetContactInfo(regGetLoginById($Order['customerID']), $t, $Email, $FirstName, $t, $t, $t);

                if(!$Email)
                        $Email = $Order['customer_email'];
                if(!$FirstName)
                        $FirstName = $Order['customer_firstname'];

                xMailTxt($Email, STRING_CHANGE_ORDER_STATUS, 'customer.order.change_status.tpl.html',
                        array(
                                'customer_firstname' => $FirstName,
                                '_MSG_CHANGE_ORDER_STATUS' => str_replace(
                                        array('{STATUS}','{ORDERID}'),
                                        array(($status_name=='STRING_CANCELED_ORDER_STATUS'?STRING_CANCELED_ORDER_STATUS:$status_name), $orderID), MSG_CHANGE_ORDER_STATUS),
                                '_ADMIN_COMMENT' => $comment
                                ));
        }

}



function stGetOrderStatusReport( $orderID )
{
        $q = db_query( "select orderID, status_name, status_change_time, status_comment from ".
                ORDER_STATUS_CHANGE_LOG_TABLE." where orderID=".(int)$orderID);
        $data = array();
        while( $row = db_fetch_row($q) )
        {
                $row["status_change_time"] = format_datetime( $row["status_change_time"] );

                $data[] = $row;
        }
        return $data;
}


function IncrementProductViewedTimes($productID)
{
        db_query("update ".PRODUCTS_TABLE." set viewed_times=viewed_times+1 ".
                " where productID=".(int)$productID);
}

function GetProductViewedTimes($productID)
{
        $q=db_query("select viewed_times from ".PRODUCTS_TABLE." where productID=".(int)$productID);
        $r=db_fetch_query($q);
        return $r["viewed_times"];
}

function GetProductViewedTimesReport($categoryID)
{
        if ( $categoryID != 0 )
        {
                $q=db_query("select name, viewed_times from ".
                        PRODUCTS_TABLE." where categoryID=".(int)$categoryID.
                                " order by viewed_times DESC ");
        }
        else
        {
                $q=db_query("select name, viewed_times from ".
                        PRODUCTS_TABLE." order by viewed_times DESC ");
        }
        $data=array();
        while( $r=db_fetch_row($q) )
        {
                $row=array();
                $row["name"]=$r["name"];
                $row["viewed_times"]=$r["viewed_times"];
                $data[]=$row;
        }
        return $data;
}


function IncrementCategoryViewedTimes($categoryID)
{
        db_query("update ".CATEGORIES_TABLE." set viewed_times=viewed_times+1 ".
                " where categoryID=".(int)$categoryID);
}

function GetCategoryViewedTimes($categoryID)
{
        $q=db_query("select viewed_times from ".
                CATEGORIES_TABLE." where categoryID=".(int)$categoryID);
        $r=db_fetch_query($q);
        return $r["viewed_times"];
}

function GetCategortyViewedTimesReport()
{
        $q=db_query("select categoryID, name, viewed_times from ".CATEGORIES_TABLE." where categoryID!=1 order by viewed_times DESC");
        $data=array();
        while( $r=db_fetch_row($q) )
        {
                $wayadd = '';
                $way = catCalculatePathToCategoryA($r["categoryID"]);
                foreach ($way as $rail) {
                if($rail['categoryID']!=1) $wayadd .= $rail['name'].' / ';
                }
                $row=array();
                $row["name"]=$wayadd."<b>".$r["name"]."</b>";
                $row["viewed_times"]=$r["viewed_times"];
                $data[]=$row;
        }
        return $data;
}

?>