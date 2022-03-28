<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################



if ( isset($quick_register) && !isset($_SESSION["log"]) )
{

        // *****************************************************************************
        // Purpose        copies data from $_POST variable to HTML page
        // Inputs                     $smarty - smarty object
        // Remarks
        // Returns        nothing
        function _copyDataFromPostToPage( &$smarty )
        {
                $smarty->hassign("first_name", trim($_POST["first_name"]));
                $smarty->hassign("last_name", trim($_POST["last_name"]));
                $smarty->hassign("email", trim($_POST["email"]) );
                $smarty->hassign("affiliationLogin", trim($_POST["affiliationLogin"]));
                $smarty->assign("subscribed4news", (isset($_POST["subscribed4news"])?1:0) );

                $zones = znGetZonesById( (int)$_POST["countryID"] );
                $smarty->assign("zones",$zones);
                $smarty->assign("countryID", (int)$_POST["countryID"] );
                if ( isset($_POST["state"]) ) $smarty->hassign("state", trim($_POST["state"]) );
                if ( isset($_POST["zoneID"]) ) $smarty->assign("zoneID", (int)$_POST["zoneID"] );
                $smarty->hassign("city", trim($_POST["city"]));
                $smarty->hassign("address", trim($_POST["address"]));
                $smarty->hassign( "receiver_first_name", trim($_POST["receiver_first_name"]) );
                $smarty->hassign( "receiver_last_name", trim($_POST["receiver_last_name"]) );

                //aux registration fields
                $additional_field_values = array();
                $data = ScanPostVariableWithId( array( "additional_field" ) );
                foreach( $data as $key => $val )
                {
                        $item = array( "reg_field_ID" => $key, "reg_field_name" => "",
                                "reg_field_value" => $val["additional_field"] );
                        $additional_field_values[] = $item;
                }
                $smarty->hassign("additional_field_values", $additional_field_values );

                if ( CONF_ORDERING_REQUEST_BILLING_ADDRESS == '1' )
                {
                        if (  isset($_POST["billing_address_check"]) ) $smarty->assign( "billing_address_check", "1" );

                        if ( !isset($_POST["billing_address_check"]) )
                        {
                                $smarty->hassign( "payer_first_name", trim($_POST["payer_first_name"]));
                                $smarty->hassign( "payer_last_name", trim($_POST["payer_last_name"]));
                                $smarty->assign( "billingCountryID", (int)$_POST["billingCountryID"] );
                                if ( isset($_POST["billingState"]) )  $smarty->hassign( "billingState", trim($_POST["billingState"]));
                                if ( isset($_POST["billingZoneID"]) ) $smarty->assign( "billingZoneID", (int)$_POST["billingZoneID"] );
                                $smarty->hassign( "billingCity", trim($_POST["billingCity"]));
                                $smarty->hassign( "billingAddress", trim($_POST["billingAddress"]));

                                $billingZones = znGetZonesById( $_POST["billingCountryID"] );
                                $smarty->assign( "billingZones", $billingZones );
                        }
                        else
                        {
                                $smarty->hassign( "payer_first_name", trim($_POST["receiver_first_name"]));
                                $smarty->hassign( "payer_last_name", trim($_POST["receiver_last_name"]));
                                $smarty->assign( "billingCountryID", (int)$_POST["countryID"] );
                                if ( isset($_POST["state"]) ) $smarty->hassign( "billingState", trim($_POST["state"]) );
                                if ( isset($_POST["zoneId"]) ) $smarty->assign( "billingZoneID", (int)$_POST["zoneId"] );
                                $smarty->hassign( "billingCity", trim($_POST["city"]));
                                $smarty->hassign( "billingAddress", trim($_POST["address"]) );
                                $smarty->assign( "billingZones", $zones);
                        }
                }
        }



        $isPost = isset($_POST["first_name"]) && isset($_POST["last_name"]);

        if ( $isPost )
        {
                _copyDataFromPostToPage( $smarty );
        }
        else
        {
                $zones = znGetZonesById(CONF_DEFAULT_COUNTRY);
                $smarty->assign("zones",$zones);
                $smarty->assign("billingZones",$zones);
        }

        if ( isset($_POST["save"]) )
        {
                $_POST["affiliationLogin"] = isset($_POST["affiliationLogin"])?$_POST["affiliationLogin"]:'';
                $affiliationLogin = $_POST["affiliationLogin"];
                if ( !isset($_POST["state"]) ) $_POST["state"] = "";
                if ( !isset($_POST["zoneID"]) ) $_POST["zoneID"] = 0;
                if ( !isset($_POST["billingState"]) )$_POST["billingState"] = "";
                if ( !isset($_POST["billingZoneID"]) ) $_POST["billingZoneID"] = 0;


                $error = "";
                $error = quickOrderContactInfoVerify();

                // receiver address
                if ( $error == "" ) $error = quickOrderReceiverAddressVerify();

                // payer address
                if ( CONF_ORDERING_REQUEST_BILLING_ADDRESS == '1' && $error == "" ) $error = quickOrderBillingAddressVerify();

                if(CONF_ENABLE_CONFIRMATION_CODE){
                        if(!$_POST['fConfirmationCode'] || !isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] !==  $_POST['fConfirmationCode'])  $error = ERR_WRONG_CCODE;
                        unset($_SESSION['captcha_keystring']);
                }

                if ( $error == "" )
                {
                        quikOrderSetCustomerInfo();
                        quickOrderSetReceiverAddress();
                        if (  CONF_ORDERING_REQUEST_BILLING_ADDRESS == '1' ) quickOrderSetBillingAddress();

                        RedirectJavaScript("index.php?order2_shipping_quick=yes");
                }
                else
                        $smarty->assign( "reg_error", $error );

        }

        // additional fields
        $additional_fields = GetRegFields();
        $smarty->assign("additional_fields", $additional_fields );


        $callBackParam = array();
        $count_row = 0;
        $countries = cnGetCountries( $callBackParam, $count_row );
        $smarty->assign("countries", $countries );

        $smarty->assign( "quick_register", 1 );
        $smarty->assign( "main_content_template", "register_quick.tpl.html" );
        if(isset($_SESSION['refid']))$smarty->assign('SessionRefererLogin', $_SESSION['refid']);
}

?>