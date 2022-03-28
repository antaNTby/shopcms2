<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

    if ( isset($order_history) && isset($_SESSION["log"]) )
        {

                function _setCallBackParamsToSearchOrders( &$callBackParam )
                {
                        $callBackParam = array( "customerID" => regGetIdByLogin($_SESSION["log"]) );
                        if ( isset($_GET["sort"]) )
                        {
                                $callBackParam["sort"] = xEscSQL($_GET["sort"]);
                                if ( isset($_GET["direction"]) )
                                        $callBackParam["direction"] = xEscSQL($_GET["direction"]);
                        }
                        else
                        {
                                $callBackParam["sort"] = "order_time";
                                $callBackParam["direction"] = "DESC";
                        }

                        if ( $_GET["order_search_type"] == "SearchByOrderID" )
                                $callBackParam["orderID"] = (int)$_GET["orderID_textbox"];
                        else if ( $_GET["order_search_type"] == "SearchByStatusID" )
                        {
                                $orderStatuses = array();
                                $data = ScanGetVariableWithId( array("checkbox_order_status") );
                                foreach( $data as $key => $val )
                                        if ( $val["checkbox_order_status"] == "1" )
                                                $orderStatuses[] = (int)$key;
                                $callBackParam["orderStatuses"] = $orderStatuses;
                        }
                }

                function _getReturnUrl()
                {
                        $url = "index.php?order_history=yes";
                        if ( isset($_GET["order_search_type"]) )
                                $url .= "&order_search_type=".$_GET["order_search_type"];
                        if ( isset($_GET["orderID_textbox"]) )
                                $url .= "&orderID_textbox=".$_GET["orderID_textbox"];
                        $data = ScanGetVariableWithId( array("checkbox_order_status") );
                        foreach( $data as $key => $val )
                                $url .= "&checkbox_order_status_".$key."=".$val["checkbox_order_status"];
                        if ( isset($_GET["offset"]) )
                                $url .= "&offset=".$_GET["offset"];
                        if ( isset($_GET["show_all"]) )
                                $url .= "&show_all=yes";
                        $data = ScanGetVariableWithId( array("set_order_status") );
                        $changeStatusIsPressed = (count($data)!=0);
                        if ( isset($_GET["search"]) || $changeStatusIsPressed )
                                $url .= "&search=1";
                        if ( isset($_GET["sort"]) )
                                $url .= "&sort=".$_GET["sort"];
                        if ( isset($_GET["direction"]) )
                                $url .= "&direction=".$_GET["direction"];
                        return base64_encode( $url );
                }

                function _copyDataFromGetToPage( &$smarty, &$order_statuses )
                {
                        if ( isset($_GET["order_search_type"])  )
                                $smarty->assign( "order_search_type", $_GET["order_search_type"] );
                        if ( isset($_GET["orderID_textbox"]) )
                                $smarty->assign( "orderID", (int)$_GET["orderID_textbox"] );
                        $data = ScanGetVariableWithId( array("checkbox_order_status") );
                        for( $i=0; $i<count($order_statuses); $i++ ) $order_statuses[$i]["selected"] = 0;
                        foreach( $data as $key => $val )
                        {
                                if ( $val["checkbox_order_status"] == "1" )
                                {
                                        for( $i=0; $i<count($order_statuses); $i++ )
                                                if ( (int)$order_statuses[$i]["statusID"] == (int)$key )
                                                        $order_statuses[$i]["selected"] = 1;
                                }
                        }
                }

                function _getUrlToSort()
                {
                        $url = "index.php?order_history=yes";
                        if ( isset($_GET["order_search_type"]) )
                                $url .= "&order_search_type=".$_GET["order_search_type"];
                        if ( isset($_GET["orderID_textbox"]) )
                                $url .= "&orderID_textbox=".$_GET["orderID_textbox"];
                        $data = ScanGetVariableWithId( array("checkbox_order_status") );
                        foreach( $data as $key => $val )
                                $url .= "&checkbox_order_status_".$key."=".$val["checkbox_order_status"];
                        if ( isset($_GET["offset"]) )
                                $url .= "&offset=".$_GET["offset"];
                        if ( isset($_GET["show_all"]) )
                                $url .= "&show_all=yes";

                        if ( isset($_GET["search"]) )
                                $url .= "&search=1";
                        return $url;
                }

                function _getUrlToNavigate()
                {
                        $url = "index.php?order_history=yes";
                        if ( isset($_GET["order_search_type"]) )
                                $url .= "&order_search_type=".$_GET["order_search_type"];
                        if ( isset($_GET["orderID_textbox"]) )
                                $url .= "&orderID_textbox=".$_GET["orderID_textbox"];
                        $data = ScanGetVariableWithId( array("checkbox_order_status") );
                        foreach( $data as $key => $val )
                                $url .= "&checkbox_order_status_".$key."=".$val["checkbox_order_status"];

                        if ( isset($_GET["search"]) )
                                $url .= "&search=1";

                        if ( isset($_GET["sort"]) )
                                $url .= "&sort=".$_GET["sort"];
                        if ( isset($_GET["direction"]) )
                                $url .= "&direction=".$_GET["direction"];
                        return $url;
                }



                $order_statuses = ostGetOrderStatues();
                $smarty->assign( "completed_order_status", ostGetCompletedOrderStatus() );

                if ( isset($_GET["search"]) )
                {
                        $callBackParam = array();
                        _setCallBackParamsToSearchOrders( $callBackParam );
                        _copyDataFromGetToPage( $smarty, $order_statuses );

                        $orders = array();
                        $offset = 0;
                        $count = 0;
                        $navigatorHtml = GetNavigatorHtml( _getUrlToNavigate(), 20,
                                'ordGetOrders', $callBackParam, $orders, $offset, $count );

                        $smarty->assign( "orders_navigator", $navigatorHtml );
                        $smarty->assign( "user_orders", $orders );
                        $smarty->assign( "urlToSort", _getUrlToSort() );
                }else{
                        $callBackParam = array();
                        _setCallBackParamsToSearchOrders( $callBackParam );
                        _copyDataFromGetToPage( $smarty, $order_statuses );

                        $orders = array();
                        $offset = 0;
                        $count = 0;
                        $navigatorHtml = GetNavigatorHtml( _getUrlToNavigate(), 10,
                                'ordGetOrders', $callBackParam, $orders, $offset, $count );

                        $smarty->assign( "orders_navigator", $navigatorHtml );
                        $smarty->assign( "user_orders", $orders );
                        $smarty->assign( "urlToSort", _getUrlToSort() );
                }

                $smarty->assign( "urlToReturn", html_amp(_getReturnUrl()) );
                $smarty->assign( "order_statuses", $order_statuses);
                $smarty->assign( "main_content_template", "order_history.tpl.html" );
        }



        if ( isset($order_detailed))
        {
                $orderID = (int) $order_detailed;

                $smarty->assign( "urlToReturn", html_amp(base64_decode($_GET["urlToReturn"])) );

                $order = ordGetOrder( $orderID );

                if (!$order || ($order["customerID"] != regGetIdByLogin($_SESSION["log"]))) //attempt to view orders of other customers
                {
                        unset($order);
                }
                else
                {
                        $orderContent = ordGetOrderContent( $orderID );
                        $order_status_report = xNl2Br(stGetOrderStatusReport( $orderID ));
                        $order_statuses = ostGetOrderStatues();

                        $smarty->assign( "completed_order_status", ostGetCompletedOrderStatus() );
                        $smarty->assign( "orderContent", $orderContent );
                        $smarty->assign( "order", $order );
                        $smarty->assign( "https_connection_flag", 1 );
                        $smarty->assign( "order_status_report", $order_status_report );
                        $smarty->assign( "order_statuses", $order_statuses );
                        $smarty->assign( "order_detailed", 1 );
                        $smarty->assign( "main_content_template", "order_history.tpl.html");
                }
        }

        if (isset($p_order_detailed) )
        {
                $orderID = (int)$p_order_detailed;
                $order = ordGetOrder( $orderID );

                if (!$order)
                {
                header("HTTP/1.0 404 Not Found");
                header("HTTP/1.1 404 Not Found");
                header("Status: 404 Not Found");
                die(ERROR_404_HTML);
                }


                if ($order["customerID"] != regGetIdByLogin($_SESSION["log"])) //attempt to view orders of other customers
                {
                        unset($order);
                        Redirect( "index.php?register_authorization=yes" );
                }
                else
                {
                        $orderContent = ordGetOrderContent( $orderID );
                        $order_status_report = xNl2Br(stGetOrderStatusReport( $orderID ));
                        $order_statuses = ostGetOrderStatues();

                        $smarty->assign( "completed_order_status", ostGetCompletedOrderStatus() );
                        $smarty->assign( "orderContent", $orderContent );
                        $smarty->assign( "order", $order );
                        $smarty->assign( "https_connection_flag", 1 );
                        $smarty->assign( "order_status_report", $order_status_report );
                        $smarty->assign( "order_statuses", $order_statuses );
                        $smarty->assign( "order_detailed", 1 );
                        $smarty->assign( "main_content_template", "order_history.tpl.html");
                }
        }

?>