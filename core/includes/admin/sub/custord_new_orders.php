<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        //orders list
        if (  !strcmp($sub, "new_orders") )
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(7,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {

                $order_detailes = (  isset($_POST["orders_detailed"]) || isset($_GET["orders_detailed"])  );

                if ( !$order_detailes )
                {

                        $order_statuses = ostGetOrderStatues();

                        function _setCallBackParamsToSearchOrders( &$callBackParam )
                        {
                                if ( isset($_GET["sort"]) )
                                        $callBackParam["sort"] = $_GET["sort"];
                                if ( isset($_GET["direction"]) )
                                        $callBackParam["direction"] = $_GET["direction"];

                                if ( $_GET["order_search_type"] == "SearchByOrderID" )
                                        $callBackParam["orderID"] = (int)$_GET["orderID_textbox"];
                                else if ( $_GET["order_search_type"] == "SearchByStatusID" )
                                {
                                        $orderStatuses = array();
                                        $data = ScanGetVariableWithId( array("checkbox_order_status") );
                                        foreach( $data as $key => $val )
                                                if ( $val["checkbox_order_status"] == "1" )
                                                        $orderStatuses[] = $key;
                                        $callBackParam["orderStatuses"] = $orderStatuses;
                                }
                        }

                        function _copyDataFromGetToPage( &$smarty, &$order_statuses )
                        {
                                if ( isset($_GET["order_search_type"])  )
                                        $smarty->assign( "order_search_type", $_GET["order_search_type"] );
                                if ( isset($_GET["orderID_textbox"]) )
                                        $smarty->assign( "orderID", (int)$_GET["orderID_textbox"] );
                                $data = ScanGetVariableWithId( array("checkbox_order_status") );
                                for( $i=0; $i<count($order_statuses); $i++ )
                                        $order_statuses[$i]["selected"] = 0;
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

                        function _getReturnUrl()
                        {
                                $url = ADMIN_FILE."?dpt=custord&sub=new_orders";
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
                                        $url .= "&show_all=".$_GET["show_all"];
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

                        function _getUrlToNavigate()
                        {
                                $url = ADMIN_FILE."?dpt=custord&sub=new_orders";
                                if ( isset($_GET["order_search_type"]) )
                                        $url .= "&order_search_type=".$_GET["order_search_type"];
                                if ( isset($_GET["orderID_textbox"]) )
                                        $url .= "&orderID_textbox=".$_GET["orderID_textbox"];
                                $data = ScanGetVariableWithId( array("checkbox_order_status") );
                                foreach( $data as $key => $val )
                                        $url .= "&checkbox_order_status_".$key."=".$val["checkbox_order_status"];

                                $data = ScanGetVariableWithId( array("set_order_status") );
                                $changeStatusIsPressed = (count($data)!=0);

                                if ( isset($_GET["search"]) || $changeStatusIsPressed )
                                        $url .= "&search=1";

                                if ( isset($_GET["sort"]) )
                                        $url .= "&sort=".$_GET["sort"];
                                if ( isset($_GET["direction"]) )
                                        $url .= "&direction=".$_GET["direction"];

                                return $url;
                        }


                        function _getUrlToSort()
                        {
                                $url = ADMIN_FILE."?dpt=custord&sub=new_orders";
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
                                        $url .= "&show_all=".$_GET["show_all"];

                                $data = ScanGetVariableWithId( array("set_order_status") );
                                $changeStatusIsPressed = (count($data)!=0);

                                if ( isset($_GET["search"]) || $changeStatusIsPressed )
                                        $url .= "&search=1";
                                return $url;
                        }

                        if(isset($_POST["status_cpast"])){
                        $dataup = ScanPostVariableWithId( array( "ordsel" ) );
                        foreach( $dataup as $key => $val )
                        {
                        ostSetOrderStatusToOrder( (int)$key, $_POST["status_cpast"], '', '' );
                        }
                        $smarty->assign( "status_cpast_ok", 1 );
                        }else{
                        $smarty->assign( "status_cpast_ok", 0 );
                        }

                        if(isset($_POST["orders_delete"])){
                        $dataup2 = ScanPostVariableWithId( array( "ordsel" ) );
                        foreach( $dataup2 as $key => $val )
                        {
                        ordDeleteOrder( (int)$key );
                        }
                        $smarty->assign( "orders_delete_ok", 1 );
                        }else{
                        $smarty->assign( "orders_delete_ok", 0 );
                        }


                        $data = ScanGetVariableWithId( array("set_order_status") );
                        $changeStatusIsPressed = (count($data)!=0);

                        if ( isset($_GET["search"]) || $changeStatusIsPressed )
                        {
                                _copyDataFromGetToPage( $smarty, $order_statuses );

                                $callBackParam = array();
                                _setCallBackParamsToSearchOrders( $callBackParam );
                                $orders = array();
                                $count = 0;
                                $navigatorHtml = GetNavigatorHtml( _getUrlToNavigate(), 20,
                                        'ordGetOrders', $callBackParam, $orders, $offset, $count );
                                $smarty->assign( "orders", $orders );
                                $smarty->assign( "navigator", $navigatorHtml );
                        }

                        if ( isset($_GET["offset"]) )
                                $smarty->assign( "offset", $_GET["offset"] );
                        if ( isset($_GET["show_all"]) )
                                $smarty->assign( "show_all", $_GET["show_all"] );
                        if ( isset($_GET["status_del"]) ){
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                        Redirect(ADMIN_FILE."?dpt=custord&sub=new_orders&safemode=yes" );
                        }
                        DelOrdersBySDL((int)$_GET["status_del"]);
                        $smarty->assign( "status_del_ok", 1 );
                        }else{
                        $smarty->assign( "status_del_ok", 0 );
                        }

                        $smarty->hassign( "urlToSort", _getUrlToSort() );
                        $smarty->hassign( "urlToReturn", _getReturnUrl() );
                        $smarty->assign( "order_statuses", $order_statuses );
                }
                else
                {
                        if ( isset($_GET["delete"]) )
                        {
                                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(ADMIN_FILE."?dpt=custord&sub=new_orders&orders_detailed=yes&orderID=".(int)$_GET["orderID"]."&urlToReturn=".$_GET["urlToReturn"]."&safemode=yes" );
                                }

                                ordDeleteOrder( (int)$_GET["orderID"] );
                                Redirect( base64_decode($_GET["urlToReturn"]) );
                        }

                        if ( isset($_POST["set_status"]) )
                        {
                                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(ADMIN_FILE."?dpt=custord&sub=new_orders&orders_detailed=yes&orderID=".$_GET["orderID"]."&urlToReturn=".$_GET["urlToReturn"]."&safemode=yes" );
                                }

                                if ( (int)$_POST["status"] != -1 )
                                        ostSetOrderStatusToOrder( (int)$_GET["orderID"],
                                                $_POST["status"],
                                                isset($_POST['status_comment'])?$_POST['status_comment']:'',
                                                isset($_POST['notify_customer'])?$_POST['notify_customer']:'' );

                                Redirect(ADMIN_FILE."?dpt=custord&sub=new_orders&orders_detailed=yes&orderID=".(int)$_GET["orderID"]."&urlToReturn=".$_GET["urlToReturn"] );
                        }

                        if ( isset($_GET["urlToReturn"]) )
                                $smarty->assign( "encodedUrlToReturn", $_GET["urlToReturn"] );
                        if ( isset($_GET["urlToReturn"]) )
                                $smarty->hassign( "urlToReturn", base64_decode($_GET["urlToReturn"]) );

                        $order = ordGetOrder( (int)$_GET["orderID"] );
                        $orderContent = ordGetOrderContent( (int)$_GET["orderID"]);

                        $order_status_report = xNl2Br(stGetOrderStatusReport( (int)$_GET["orderID"] ));
                        $order_statuses = ostGetOrderStatues();

                        $smarty->assign( "cancledOrderStatus", ostGetCanceledStatusId() );
                        $smarty->assign( "orderContent", $orderContent );
                        $smarty->assign( "order", $order );
                        $smarty->assign( "https_connection_flag", 1 );
                        $smarty->assign( "order_status_report", $order_status_report );
                        $smarty->assign( "order_statuses", $order_statuses );
                        $smarty->assign( "order_detailed", 1 );
                }
                $smarty->assign( "admin_sub_dpt", "custord_new_orders.tpl.html" );
        }
        }
?>