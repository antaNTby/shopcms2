<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

                if (!strcmp($sub, "custlist")) //show registered customers list
                {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(33,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {
                        if ( isset($_GET["groupID"]) ) $_GET["groupID"] =  (int)$_GET["groupID"];
                        if ( isset($_GET["customerID"]) ) $_GET["customerID"] = (int)$_GET["customerID"];
                        if ( isset($_GET["deleteCustomerID"]) ) $_GET["deleteCustomerID"] = (int)$_GET["deleteCustomerID"];
                        if ( isset($_POST["custgroupID"])) $_POST["custgroupID"] = (int)$_POST["custgroupID"];
                        function _getUrlToSort()
                        {
                                // customer  search URL section
                                $res = ADMIN_FILE."?dpt=custord&sub=custlist";

                                // parametr search
                                if ( isset($_GET["login"]) )
                                        $res .= "&login=".$_GET["login"];
                                if ( isset($_GET["first_name"]) )
                                        $res .= "&first_name=".$_GET["first_name"];
                                if ( isset($_GET["last_name"]) )
                                        $res .= "&last_name=".$_GET["last_name"];
                                if ( isset($_GET["email"]) )
                                        $res .= "&email=".$_GET["email"];
                                if ( isset($_GET["groupID"]) )
                                        $res .= "&groupID=".$_GET["groupID"];

                                if ( isset($_GET["search"]) )
                                        $res .= "&search=".$_GET["search"];

                                // navigator params
                                if ( isset($_GET["offset"]) )
                                        $res .= $_GET["offset"];
                                if ( isset($_GET["show_all"]) )
                                        $res .= "&show_all=".$_GET["show_all"];

                                return $res;
                        }


                        function _getUrlToNavigate()
                        {
                                // customer  search URL section
                                $res = ADMIN_FILE."?dpt=custord&sub=custlist";

                                // parametr search
                                if ( isset($_GET["login"]) )
                                        $res .= "&login=".$_GET["login"];
                                if ( isset($_GET["first_name"]) )
                                        $res .= "&first_name=".$_GET["first_name"];
                                if ( isset($_GET["last_name"]) )
                                        $res .= "&last_name=".$_GET["last_name"];
                                if ( isset($_GET["email"]) )
                                        $res .= "&email=".$_GET["email"];
                                if ( isset($_GET["groupID"]) )
                                        $res .= "&groupID=".$_GET["groupID"];

                                if ( isset($_GET["search"]) )
                                        $res .= "&search=".$_GET["search"];

                                if ( isset($_GET["sort"]) )
                                        $res .= "&sort=".$_GET["sort"];
                                if ( isset($_GET["direction"]) )
                                        $res .= "&direction=".$_GET["direction"];
                                if ( isset($_GET["fActState"]) )
                                        $res .= "&fActState=".$_GET["fActState"];

                                return $res;
                        }

                        function _getUrlToReturn()
                        {
                                // customer  search URL section
                                $res = ADMIN_FILE."?dpt=custord&sub=custlist";

                                // parametr search
                                if ( isset($_GET["login"]) )
                                        $res .= "&login=".$_GET["login"];
                                if ( isset($_GET["first_name"]) )
                                        $res .= "&first_name=".$_GET["first_name"];
                                if ( isset($_GET["last_name"]) )
                                        $res .= "&last_name=".$_GET["last_name"];
                                if ( isset($_GET["email"]) )
                                        $res .= "&email=".$_GET["email"];
                                if ( isset($_GET["groupID"]) )
                                        $res .= "&groupID=".$_GET["groupID"];
                                if ( isset($_GET["fActState"]) )
                                        $res .= "&fActState=".$_GET["fActState"];

                                // search
                                if ( isset($_GET["search"]) )
                                        $res .= "&search=".$_GET["search"];

                                // sort params
                                if ( isset($_GET["sort"]) )
                                        $res .= "&sort=".$_GET["sort"];
                                if ( isset($_GET["direction"]) )
                                        $res .= "&direction=".$_GET["direction"];

                                // navigator params
                                if ( isset($_GET["offset"]) )
                                        $res .= $_GET["offset"];
                                if ( isset($_GET["show_all"]) )
                                        $res .= "&show_all=".$_GET["show_all"];

                                return $res;
                        }

                        function _copyFromGetVarsToPage( &$smarty )
                        {
                                if ( isset($_GET["login"]) )
                                        $smarty->hassign( "login",  $_GET["login"] );
                                if ( isset($_GET["first_name"]) )
                                        $smarty->hassign( "first_name", $_GET["first_name"] );
                                if ( isset($_GET["last_name"]) )
                                        $smarty->hassign( "last_name", $_GET["last_name"] );
                                if ( isset($_GET["email"]) )
                                        $smarty->hassign( "email", $_GET["email"] );
                                if ( isset($_GET["groupID"]) )
                                        $smarty->hassign( "groupID", $_GET["groupID"] );
                                if ( isset($_GET["fActState"]) )
                                        $smarty->hassign( "ActState", $_GET["fActState"] );
                        }



                        function _getUrlToNavigate_ORDER_HISTORY()
                        {
                                // customer  search URL section
                                $res = ADMIN_FILE."?dpt=custord&sub=custlist&customer_details=order_history";
                                if ( isset($_GET["encodedReturnUrl"]) )
                                        $res .= "&encodedReturnUrl=".$_GET["encodedReturnUrl"];
                                if ( isset($_GET["customerID"]) )
                                        $res .= "&customerID=".$_GET["customerID"];
                                if ( isset($_GET["sort"]) )
                                        $res .= "&sort=".$_GET["sort"];
                                if ( isset($_GET["direction"]) )
                                        $res .= "&direction=".$_GET["direction"];
                                return $res;
                        }

                        function _getUrlToSort_ORDER_HISTORY()
                        {
                                $res = ADMIN_FILE."?dpt=custord&sub=custlist&customer_details=order_history";
                                if ( isset($_GET["encodedReturnUrl"]) )
                                        $res .= "&encodedReturnUrl=".$_GET["encodedReturnUrl"];
                                if ( isset($_GET["customerID"]) )
                                        $res .= "&customerID=".$_GET["customerID"];
                                if ( isset($_GET["offset"]) )
                                        $res .= "&offset=".$_GET["offset"];
                                if ( isset($_GET["show_all"]) )
                                        $res .= "&show_all=".$_GET["show_all"];
                                return $res;
                        }

                        function _getUrlToSubmit_ORDER_HISTORY()
                        {
                                $res = ADMIN_FILE."?dpt=custord&sub=custlist&customer_details=order_history";

                                // return URL
                                if ( isset($_GET["encodedReturnUrl"]) )
                                        $res .= "&encodedReturnUrl=".$_GET["encodedReturnUrl"];

                                // customer ID
                                if ( isset($_GET["customerID"]) )
                                        $res .= "&customerID=".$_GET["customerID"];

                                // navigator URL
                                if ( isset($_GET["offset"]) )
                                        $res .= "&offset=".$_GET["offset"];
                                if ( isset($_GET["show_all"]) )
                                        $res .= "&show_all=".$_GET["show_all"];

                                // sort
                                if ( isset($_GET["sort"]) )
                                        $res .= "&sort=".$_GET["sort"];
                                if ( isset($_GET["direction"]) )
                                        $res .= "&direction=".$_GET["direction"];

                                return $res;
                        }


                        function _getUrlToNavigate_VISIT_LOG()
                        {
                                $res = ADMIN_FILE."?dpt=custord&sub=custlist&customer_details=visit_log";
                                if ( isset($_GET["encodedReturnUrl"]) )
                                        $res .= "&encodedReturnUrl=".$_GET["encodedReturnUrl"];
                                if ( isset($_GET["customerID"]) )
                                        $res .= "&customerID=".$_GET["customerID"];
                                return $res;
                        }


                        if(isset($_GET['activateID'])){

                                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(set_query('&activateID=&safemode=yes'));
                                }
                                regActivateCustomer($_GET['activateID']);
                                Redirect(set_query('activateID='));
                        }

                        if ( isset($_GET["deleteCustomerID"]) )
                        {
                                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect( set_query("&safemode=yes&deleteCustomerID=") );
                                }

                                regDeleteCustomer( $_GET["deleteCustomerID"] );
                                if(isset($_GET['encodedReturnUrl']))
                                        Redirect( base64_decode($_GET["encodedReturnUrl"]) );
                                else
                                        Redirect( ADMIN_FILE.'?dpt=custord&sub=custlist');
                        }

                        if ( !isset($_GET["customer_details"]) )
                        {

                                if ( isset($_GET["search"]) || isset($_GET["export_to_excel"]) )
                                {
                                                                       if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                        {
                                                Redirect(ADMIN_FILE."?dpt=custord&sub=custlist&safemode=yes" );
                                        }
                                        $_GET["search"] = 1;
                                        _copyFromGetVarsToPage( $smarty );

                                        $encodedReturnUrl = base64_encode( _getUrlToReturn() );

                                        $callBackParam = array();
                                        $customers = array();

                                        if ( trim($_GET["login"]) != "" )
                                                $callBackParam["Login"]                = trim($_GET["login"]);
                                        if ( trim($_GET["first_name"]) != "" )
                                                $callBackParam["first_name"]= trim($_GET["first_name"]);
                                        if ( trim($_GET["last_name"]) != "" )
                                                $callBackParam["last_name"]        = trim($_GET["last_name"]);
                                        if ( trim($_GET["email"]) != "" )
                                                $callBackParam["email"]                = trim($_GET["email"]);
                                        if ( trim($_GET["groupID"]) != "" )
                                                $callBackParam["groupID"]   = $_GET["groupID"];
                                        if ( isset($_GET["fActState"]) && trim($_GET["fActState"]) != "" )
                                                $callBackParam["ActState"]   = $_GET["fActState"];
                                        if ( isset($_GET["sort"]) )
                                        {
                                                $callBackParam["sort"] = trim($_GET["sort"]);
                                                if ( isset($_GET["direction"]) )
                                                        $callBackParam["direction"] = trim($_GET["direction"]);
                                        }

                                        $count = 0;

                                        if (isset($_GET["export_to_excel"]))
                                        {
                                                $number2showNexport = (int) $_GET["count_to_export"];
                                        }
                                        else
                                        {
                                                $number2showNexport = 20;
                                        }

                                        $navigatorHtml = GetNavigatorHtml( _getUrlToNavigate(), $number2showNexport,
                                                        'regGetCustomers', $callBackParam,
                                                        $customers, $offset, $count );

                                        if ( isset($_GET["export_to_excel"]) )
                                        {
                                                serExportCustomersToExcel( $customers );
                                                $smarty->assign( "customers_has_been_exported_succefully", 1 );
                                                $smarty->assign( "urlToDownloadResult",
                                                                ADMIN_FILE."?do=get_file&getFileParam=".cryptFileParamCrypt( "GetCustomerExcelSqlScript", null ) );
                                                $smarty->assign( "file_size", filesize( "core/temp/customers.csv" ) );
                                        }

                                        $smarty->hassign( "encodedReturnUrl", $encodedReturnUrl );
                                        $smarty->assign( "search_result_string", str_replace( "{N}",  $count,
                                                                        ADMIN_N_RECORD_IS_SEARCHED) );
                                        $smarty->assign( "count_to_export", count ($customers) );
                                        $smarty->assign( "customers", $customers);
                                        $smarty->assign( "navigator", $navigatorHtml);
                                }



                                $smarty->hassign( "urlToSort", _getUrlToSort() );

                                $customer_groups = GetAllCustGroups();
                                $smarty->assign( "customer_groups", $customer_groups );
                        }
                        else
                        {

                                $c_login = regGetLoginById($_GET["customerID"]);
                                if (!$c_login)
                                {
                                        $smarty->assign( "customerID", NULL);
                                        $smarty->assign( "customer_details", $_GET["customer_details"] );
                                }
                                else
                                {

                                        if ( isset($_GET["encodedReturnUrl"]) )
                                        {
                                                $smarty->hassign( "encodedReturnUrl", $_GET["encodedReturnUrl"] );
                                                $returnUrl = base64_decode( $_GET["encodedReturnUrl"] );
                                                $smarty->hassign( "returnUrl", $returnUrl );
                                        }

                                        if ( $_GET["customer_details"] == "contact_info" )
                                        {
                                            if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                            {
                                                Redirect(ADMIN_FILE."?dpt=custord&sub=custlist&safemode=yes" );
                                            }
                                                if ( isset($_POST["subscribed4news_submit"]) )
                                                {
                                                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                                        {
                                                                Redirect(ADMIN_FILE."?dpt=custord&sub=custlist&customer_details=contact_info&customerID=".$_GET["customerID"]."&encodedReturnUrl=".$_GET["encodedReturnUrl"]."&safemode=yes" );
                                                        }
                                                        regSetSubscribed4news( $_GET["customerID"], isset($_POST["subscribed4news"])?1:0 );
                                                }

                                                if ( isset($_POST["custgroupID"]) )
                                                {
                                                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                                        {
                                                                Redirect(ADMIN_FILE."?dpt=custord&sub=custlist&customer_details=contact_info&customerID=".$_GET["customerID"]."&encodedReturnUrl=".$_GET["encodedReturnUrl"]."&safemode=yes" );
                                                        }
                                                        regSetCustgroupID( $_GET["customerID"], $_POST["custgroupID"] );
                                                }


                                                $log1 = regGetLoginById($_GET["customerID"]);
                                                $customerInfo = regGetCustomerInfo2( $log1 );
                                                $reg_fields_values = GetRegFieldsValues( $log1 );

                                                $customer_groups = GetAllCustGroups();
                                                $smarty->assign( "customer_groups", $customer_groups );
                                                $smarty->assign( "reg_fields_values", $reg_fields_values );
                                                $smarty->assign( "customerInfo", $customerInfo );
                                        }

                                        if (  $_GET["customer_details"] == "address_book"  )
                                        {
                                                $log1 = regGetLoginById($_GET["customerID"]);
                                                $addresses = regGetAllAddressesByLogin( $log1 );
                                                for($i=0; $i<count($addresses); $i++)
                                                        $addresses[$i]["addressStr"] =
                                                                        regGetAddressStr( $addresses[$i]["addressID"] );

                                                $defaultAddressID = regGetDefaultAddressIDByLogin( $log1 );
                                                $smarty->assign( "addresses", $addresses );
                                                $smarty->assign( "defaultAddressID", $defaultAddressID );
                                        }

                                        if ( $_GET["customer_details"] == "order_history" )
                                        {
                                                $data = ScanPostVariableWithId( array("set_order_status") );
                                                foreach( $data as $orderID => $value )
                                                        ostSetOrderStatusToOrder( $orderID,
                                                                        $_POST[ "order_status_in_table_".$orderID ] );

                                                $orders = array();
                                                $callBackParam = array();
                                                $callBackParam["customerID"] = $_GET["customerID"];

                                                if ( isset($_GET["sort"]) )
                                                        $callBackParam["sort"] = $_GET["sort"];
                                                if ( isset($_GET["direction"]) )
                                                        $callBackParam["direction"] = $_GET["direction"];
                                                $count = 0;
                                                $navigatorHtml = GetNavigatorHtml( _getUrlToNavigate_ORDER_HISTORY(), 20,
                                                        'ordGetOrders', $callBackParam,
                                                        $orders, $offset, $count );

                                                $smarty->hassign( "urlToSubmit", _getUrlToSubmit_ORDER_HISTORY() );
                                                $smarty->hassign( "urlToSort", _getUrlToSort_ORDER_HISTORY() );
                                                $smarty->assign( "navigator", $navigatorHtml );
                                                $smarty->assign( "order_statuses",  ostGetOrderStatues() );
                                                $smarty->assign( "orders", $orders );
                                        }

                                        if ( $_GET["customer_details"] == "visit_log" )
                                        {
                                                $callBackParam = array();
                                                $visits        = array();
                                                $callBackParam["log"] = regGetLoginById( $_GET["customerID"] );
                                                $count = 0;
                                                $navigatorHtml = GetNavigatorHtml( _getUrlToNavigate_VISIT_LOG(), 20,
                                                        'stGetVisitsByLogin', $callBackParam,
                                                        $visits, $offset, $count );

                                                $smarty->assign( "navigator", $navigatorHtml );
                                                $smarty->assign( "visits", $visits );
                                        }

                                        if ( $_GET['customer_details'] == 'affiliate'){

                                                $customerID = $_GET["customerID"];
                                                require('core/includes/admin/sub/custord_custlist_affiliate.php');
                                        }

                                        $smarty->assign( "customerID", $_GET["customerID"] );
                                        $smarty->assign( "customer_details", $_GET["customer_details"] );

                                }
                        }
                        $smarty->assign( "admin_sub_dpt", "custord_custlist.tpl.html");
                }
                }
?>