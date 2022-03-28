<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


// *****************************************************************************
// Purpose                        registration form
// Call condition
//                                        index.php?register=yes OR isset($register)
//                                                                        - register new customer
//                                        ( index.php?register=yes OR isset($register) ) AND isset($order)
//                                                                        - register new customer and proceed order process
//                                        index.php?register=yes&r_successful=yes
//                                                                        - register new customer success notification
// Include PHP                index.php -> [register.php]
// Uses TPL                        register.tpl, reg_successful.tpl
// Remarks

if ( isset($_GET["r_successful"]) )                        // successful registration notification
        $smarty->assign("main_content_template", "reg_successful.tpl.html");

if ( isset($register) && !isset($_SESSION["log"]) )
{



        // *****************************************************************************
        // Purpose        copies data from $_POST variable to HTML page
        // Inputs                     $smarty - smarty object
        // Remarks
        // Returns        nothing
        function _copyDataFromPostToPage( & $smarty )
        {
                $smarty->hassign("login",  trim($_POST["login"]) );
                $smarty->hassign("cust_password1", trim($_POST["cust_password1"]) );
                $smarty->hassign("cust_password2", trim($_POST["cust_password2"]) );
                $smarty->hassign("first_name", trim($_POST["first_name"]));
                $smarty->hassign("affiliationLogin", trim($_POST["affiliationLogin"]));
                $smarty->hassign("last_name", trim($_POST["last_name"]));
                $smarty->hassign("email", trim($_POST["email"]));
                $smarty->assign("subscribed4news", (isset($_POST["subscribed4news"])?1:0) );

                $zones = znGetZonesById( (int)$_POST["countryID"] );
                $smarty->hassign("zones",$zones);

                $additional_field_values = array();
                $data = ScanPostVariableWithId( array( "additional_field" ) );
                foreach( $data as $key => $val )
                {
                        $item = array( "reg_field_ID" => $key, "reg_field_name" => "",
                                "reg_field_value" => $val["additional_field"] );
                        $additional_field_values[] = $item;
                }
                $smarty->hassign("additional_field_values", $additional_field_values );

                $smarty->assign("countryID", (int)$_POST["countryID"] );
                if ( isset($_POST["state"]) ) $smarty->hassign("state", trim($_POST["state"]) );
                if ( isset($_POST["zoneID"]) ) $smarty->assign("zoneID", (int)$_POST["zoneID"] );
                $smarty->hassign("city", trim($_POST["city"]));
                $smarty->hassign("address", trim($_POST["address"]));
                if ( isset($_POST["order"]) || isset($_GET["order"]) )
                {
                        if (  isset($_POST["billing_address_check"]) ) $smarty->assign( "billing_address_check", "1" );

                        $smarty->hassign( "receiver_first_name", trim($_POST["receiver_first_name"]) );
                        $smarty->hassign( "receiver_last_name", trim($_POST["receiver_last_name"]) );

                        if ( !isset($_POST["billing_address_check"]) )
                        {
                                $smarty->hassign( "payer_first_name", trim($_POST["payer_first_name"]));
                                $smarty->hassign( "payer_last_name", trim($_POST["payer_last_name"]));
                                $smarty->assign( "billingCountryID", (int)$_POST["billingCountryID"]);
                                if ( isset($_POST["billingState"]) )  $smarty->hassign( "billingState", trim($_POST["billingState"]));
                                if ( isset($_POST["billingZoneId"]) ) $smarty->assign( "billingZoneId", (int)$_POST["billingZoneId"] );
                                $smarty->hassign( "billingCity", trim($_POST["billingCity"]));
                                $smarty->hassign( "billingAddress", trim($_POST["billingAddress"]));
                                $billingZones = znGetZonesById( (int)$_POST["billingCountryID"] );
                                $smarty->assign( "billingZones", $billingZones );
                        }
                        else
                        {
                                $smarty->hassign( "payer_first_name", trim($_POST["receiver_first_name"]));
                                $smarty->hassign( "payer_last_name", trim($_POST["receiver_last_name"]));
                                $smarty->assign( "billingCountryID", (int)$_POST["countryID"] );
                                if ( isset($_POST["state"]) ) $smarty->hassign( "billingState", trim($_POST["state"]) );
                                if ( isset($_POST["zoneId"]) ) $smarty->assign( "billingZoneId", (int)$_POST["zoneId"] );
                                $smarty->hassign( "billingCity", trim($_POST["city"]));
                                $smarty->hassign( "billingAddress", trim($_POST["address"]));
                                $smarty->assign( "billingZones", $zones);
                        }
                }
        }


        if ( !isset($_POST["state"]) ) $_POST["state"] = "";
        if ( !isset($_POST["countryID"]) ) $_POST["countryID"] = CONF_DEFAULT_COUNTRY;

        $isPost = isset($_POST["login"]) && isset($_POST["cust_password1"]);

        if ( $isPost )  _copyDataFromPostToPage( $smarty );

        if ( isset($_POST["save"]) ) //save user to the database
        {
                $login            = trim($_POST["login"]);
                $cust_password1   = trim($_POST["cust_password1"]);
                $cust_password2   = trim($_POST["cust_password2"]);
                $first_name       = trim($_POST["first_name"]);
                $affiliationLogin = trim($_POST["affiliationLogin"]);
                $last_name        = trim($_POST["last_name"]);
                $Email            = trim($_POST["email"]);
                $subscribed4news  = ( isset($_POST["subscribed4news"]) ? 1 : 0 );
                $additional_field_values = ScanPostVariableWithId( array( "additional_field" ) );

                if ( isset($order) )
                {
                        $receiver_first_name = trim($_POST["receiver_first_name"]);
                        $receiver_last_name  = trim($_POST["receiver_last_name"]);
                }

                $countryID = (int)$_POST["countryID"];
                $state     = trim($_POST["state"]);
                $city      = trim($_POST["city"]);
                $address   = trim($_POST["address"]);
                if ( isset($_POST["zoneID"]) ) $zoneID = (int)$_POST["zoneID"];
                else $zoneID = 0;

                if ( isset($order) && isset($_POST["billing_address_check"]) )
                {
                        $payer_first_name = $receiver_first_name;
                        $payer_last_name  = $receiver_last_name;
                        $billingCountryID = $countryID;
                        $billingState     = $state;
                        $billingCity      = $city;
                        $billingAddress   = $address;
                        $billingZoneID    = $zoneID;
                }
                else if ( isset($order) )
                {
                        $payer_first_name = trim($_POST["payer_first_name"]);
                        $payer_last_name  = trim($_POST["payer_last_name"]);
                        $billingCountryID = (int)$_POST["billingCountryID"];
                        if ( isset($_POST["billingState"]) ) $billingState = trim($_POST["billingState"]);
                        else $billingState = "";
                        $billingCity      = trim($_POST["billingCity"]);
                        $billingAddress   = trim($_POST["billingAddress"]);
                        if ( isset($_POST["billingZoneID"]) ) $billingZoneID = (int)$_POST["billingZoneID"];
                        else $billingZoneID = 0;
                }

                $error = regVerifyContactInfo( $login, $cust_password1, $cust_password2,
                                                $Email, $first_name, $last_name, $subscribed4news,
                                                $additional_field_values );

                if(CONF_ENABLE_CONFIRMATION_CODE){
                        if(!$_POST['fConfirmationCode'] || !isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] !==  $_POST['fConfirmationCode'])  $error = ERR_WRONG_CCODE;
                        unset($_SESSION['captcha_keystring']);
                }

                if ( $error == "" ) unset( $error );

                if (!isset($error) && isset($affiliationLogin))
                        if ( !regIsRegister($affiliationLogin) && $affiliationLogin)
                                $error = ERROR_WRONG_AFFILIATION;

                if ( !isset($error) )
                        if ( regIsRegister($login) )
                                $error = ERROR_USER_ALREADY_EXISTS;

                if ( !isset($error) )
                {
                        if ( !isset($order) )
                                $error = regVerifyAddress(        $first_name, $last_name, $countryID, $zoneID, $state,
                                                                        $city, $address );
                        else
                                $error = regVerifyAddress(        $receiver_first_name, $receiver_last_name, $countryID,
                                                                        $zoneID, $state, $city, $address );
                        if ( $error == "" ) unset( $error );
                }

                if ( !isset($error) && isset($order) )
                {
                        $error = regVerifyAddress( $payer_first_name, $payer_last_name, $billingCountryID,
                                                                        $billingZoneID, $billingState, $billingCity, $billingAddress );
                        if ( $error == "" ) unset( $error );
                }

                if ( !isset($error) )
                {
                        $cust_password = $cust_password1;

                        $registerResult =
                                regRegisterCustomer(
                                        $login, $cust_password, $Email, $first_name,
                                        $last_name, $subscribed4news, $additional_field_values, $affiliationLogin );

                        if ( $registerResult )
                        {

                                if ( isset($order) )
                                {
                                        $addressID = regAddAddress(
                                                $receiver_first_name, $receiver_last_name, $countryID,
                                                $zoneID, $state, $city,
                                                $address, $login, $errorCode );
                                        $billingAddressID = $addressID;

                                        if ( !isset($_POST["billing_address_check"]) )
                                        {
                                                $billingAddressID = regAddAddress(
                                                        $payer_first_name, $payer_last_name, $billingCountryID,
                                                        $billingZoneID, $billingState, $billingCity,
                                                        $billingAddress, $login, $errorCode );
                                        }

                                        regSetDefaultAddressIDByLogin( $login, $addressID );
                                }
                                else
                                {
                                        $addressID = regAddAddress(
                                                $first_name, $last_name, $countryID,
                                                $zoneID, $state, $city,
                                                $address, $login, $errorCode );
                                        regSetDefaultAddressIDByLogin( $login, $addressID );
                                }

                                regEmailNotification( $smarty_mail,
                                        $login, $cust_password, $Email, $first_name,
                                        $last_name, $subscribed4news, $additional_field_values,
                                        $countryID, $zoneID, $state, $city, $address, 0 );

                                if(!CONF_ENABLE_REGCONFIRMATION){
                                        regAuthenticate( $login, $cust_password );
                                }

                                $RedirectURL = '';
                                if ( isset($order) )
                                {
                                        if ( isset($billingAddressID)  )
                                                $RedirectURL = ( "index.php?order2_shipping=yes&shippingAddressID=".
                                                                        regGetDefaultAddressIDByLogin($login).
                                                                        "&defaultBillingAddressID=".$billingAddressID );
                                        else
                                                $RedirectURL = ( "index.php?order2_shipping=yes&shippingAddressID=".
                                                                        regGetDefaultAddressIDByLogin($login) );
                                }elseif ( isset($order_without_billing_address) ){
                                        $RedirectURL = ( "index.php?order2_shipping=yes&shippingAddressID=".
                                                                        regGetDefaultAddressIDByLogin($login) );
                                }else{
                                        $RedirectURL = ( "index.php?r_successful=yes" );
                                }
                                if(CONF_ENABLE_REGCONFIRMATION && (isset($order)||isset($order_without_billing_address))){

                                        xSaveData('xREGMAILCONF_URLORDER2', $RedirectURL);
                                        $RedirectURL = ( "index.php?act_customer=1&order2=yes" );
                                }

                                RedirectJavaScript($RedirectURL);


                        }
                        else
                                $smarty->assign( "reg_error", ERROR_INPUT_STATE );
                }
                else
                        $smarty->assign( "reg_error", $error );
        }

        // countries
        $callBackParam = array();
        $count_row = 0;
        $countries = cnGetCountries( $callBackParam, $count_row );
        $smarty->assign("countries", $countries );

        if ( !$isPost )
        {
                if ( count($countries) != 0 )
                {
                        $zones = znGetZonesById(CONF_DEFAULT_COUNTRY);
                        $smarty->assign("zones", $zones);//var_dump($zones);
                        $smarty->assign("billingZones", $zones);
                }
        }else{
           if ( count($countries) != 0 )
                {
                        $zones = znGetZonesById((int)$_POST["countryID"]);
                        $smarty->assign("zones", $zones);//var_dump($zones);
                        $smarty->assign("billingZones", $zones);
                }
        }

        // additional fields
        $additional_fields=GetRegFields();
        $smarty->assign("additional_fields", $additional_fields );

        if ( isset($register) ) $smarty->assign("return_url", "index.php?register=yes" );

        // proceeding to checkout mode
        if ( isset($order) ) $smarty->assign("order", 1);

        // proceeding to checkout mode without billing address
        if ( isset($order_without_billing_address) ) $smarty->assign("order_without_billing_address", 1);

        if(isset($_SESSION['s_RefererLogin'])) $smarty->assign('SessionRefererLogin', $_SESSION['s_RefererLogin']);

        $smarty->assign("main_content_template", "register.tpl.html");
}
?>