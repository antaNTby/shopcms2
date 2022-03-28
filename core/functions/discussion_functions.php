<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


// *****************************************************************************
// Purpose        gets all discussion
// Inputs   $navigatorParams - item
//                                        "offset"                        - count row from begin to place being shown
//                                        "CountRowOnPage"        - count row on page to show on page
// Remarks
// Returns
//                                returns array of discussion
//                                $count_row is set to count(discussion)
function discGetAllDiscussion( $callBackParam, &$count_row, $navigatorParams = null )
{
        $data = array();

        $orderClause = "";
        if ( isset($callBackParam["sort"]) )
        {
                $orderClause = " order by ".xEscSQL($callBackParam["sort"]);
                if ( isset($callBackParam["direction"]) )
                {
                        if ( $callBackParam["direction"] == "ASC" )
                                $orderClause .= " ASC ";
                        else
                                $orderClause .= " DESC ";
                }
        }

        $filter = "";
        if ( isset($callBackParam["productID"]) )
        {
                if ( $callBackParam["productID"] != 0 )
                        $filter = " AND ".PRODUCTS_TABLE.".productID=".(int)$callBackParam["productID"];
        }

        $q = db_query("select DID, Author, Body, add_time, Topic, name AS product_name from ".
                DISCUSSIONS_TABLE.", ".PRODUCTS_TABLE.
                " where ".DISCUSSIONS_TABLE.".productID=".PRODUCTS_TABLE.".productID ".$filter." ".
                $orderClause );

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
        $i=0;
        while( $row = db_fetch_row($q) )
        {
                if ( ($i >= $offset && $i < $offset + $CountRowOnPage) ||
                                $navigatorParams == null  )
                {
                        $row["add_time"]        = format_datetime( $row["add_time"] );
                        $data[] = $row;
                }
                $i ++;
        }
        $count_row = $i;
        return $data;
}

function discGetAllDiscussedProducts()
{
        $q = db_query(
                "select name AS product_name, ".PRODUCTS_TABLE.".productID AS productID from ".
                        DISCUSSIONS_TABLE.", ".PRODUCTS_TABLE.
                        " where ".DISCUSSIONS_TABLE.".productID=".PRODUCTS_TABLE.".productID ".
                        " group by ".PRODUCTS_TABLE.".productID, ".PRODUCTS_TABLE.".name order by product_name" );
        $data = array();
        while( $row = db_fetch_row($q) ) $data[] = $row;
        return $data;
}

function discGetDiscussion( $DID )
{
        $q = db_query("select DID, Author, Body, add_time, Topic, name AS product_name, ".
                " ".PRODUCTS_TABLE.".productID AS productID from ".
                DISCUSSIONS_TABLE.", ".PRODUCTS_TABLE.
                " where ".DISCUSSIONS_TABLE.".productID=".PRODUCTS_TABLE.".productID AND DID=".(int)$DID);
        $row = db_fetch_row( $q );
        $row["add_time"] = format_datetime( $row["add_time"] );
        return $row;
}


function discAddDiscussion( $productID, $Author, $Topic, $Body )
{
        db_query("insert into ".DISCUSSIONS_TABLE.
                "(productID, Author, Body, add_time, Topic)  ".
                "values( ".(int)$productID.", '".xToText($Author)."', '".xToText($Body)."', '".get_current_time()."', '".xToText($Topic)."' )");
}

function discDeleteDiscusion( $DID )
{
        db_query( "delete from ".DISCUSSIONS_TABLE." where DID=".(int)$DID );
}

?>