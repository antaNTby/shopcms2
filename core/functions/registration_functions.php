<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

// *****************************************************************************
// Purpose  add administrator login into database and set default address
// Inputs   $admin_login - administrator login, $admin_pass - administrator password
// Remarks        this function is called by installation
// Returns        this function always returns true
function regRegisterAdmin( $admin_login, $admin_pass )
{
        // $q_count = db_query( "select COUNT(*) FROM  ".CUSTOMERS_TABLE." WHERE Login='".$admin_login."'" );
        // $count = db_fetch_row( $q_count );
        // $count = $count[0];
        db_query( "delete from ".CUSTOMERS_TABLE." where Login='".xEscSQL($admin_login)."'" );

        if ( CONF_DEFAULT_CUSTOMER_GROUP=='0' )
                $custgroupID = "NULL";
        else
                $custgroupID = CONF_DEFAULT_CUSTOMER_GROUP;

        $admin_pass = cryptPasswordCrypt( $admin_pass, null );

        $currencyID = CONF_DEFAULT_CURRENCY;
        $actions = 'a:35:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";i:5;s:1:"6";i:6;s:1:"7";i:7;s:1:"8";i:8;s:1:"9";i:9;s:2:"10";i:10;s:2:"11";i:11;s:2:"12";i:12;s:2:"13";i:13;s:2:"14";i:14;s:2:"15";i:15;s:2:"16";i:16;s:2:"17";i:17;s:2:"18";i:18;s:2:"19";i:19;s:2:"20";i:20;s:2:"21";i:21;s:2:"22";i:22;s:2:"23";i:23;s:2:"24";i:24;s:2:"25";i:25;s:2:"26";i:26;s:2:"27";i:27;s:2:"28";i:28;s:2:"29";i:29;s:2:"30";i:30;s:2:"31";i:31;s:2:"32";i:32;s:2:"33";i:33;s:2:"34";i:34;s:3:"100";}';
        
		db_query( "insert into ".CUSTOMERS_TABLE.
                " (Login, cust_password, Email, first_name, last_name, subscribed4news, ".
                "         custgroupID, addressID, reg_datetime, CID, actions ) values ".
                                "('".xToText($admin_login)."','".xEscSQL($admin_pass)."', ".
                                                " '-', '-', '-', 0, ".(int)$custgroupID.", NULL, ".
                                                " '".xEscSQL(get_current_time())."', ".(int)$currencyID.", '".xEscSQL($actions)."' )" );
        $errorCode = 0;
        $zoneID = "50";
        $state        = "";
        $countryID = "1";
        $defaultAddressID = regAddAddress(
                                "-", "-",
                                $countryID,
                                $zoneID,
                                $state,
                                "-",
                                "-",
                                $admin_login,
                                $errorCode );
        regSetDefaultAddressIDByLogin( $admin_login, $defaultAddressID );
        return true;
}

function regRegisterAdminSlave( $admin_login, $admin_pass, $actions=array() )
{
        $actions[] = 100;
        $actions = xEscSQL(serialize($actions));

        // $q_count = db_query( "select COUNT(*) FROM  ".CUSTOMERS_TABLE." WHERE Login='".$admin_login."'" );
        // $count = db_fetch_row( $q_count );
        // $count = $count[0];
        db_query( "delete from ".CUSTOMERS_TABLE." where Login='".xToText($admin_login)."'" );

        if ( CONF_DEFAULT_CUSTOMER_GROUP=='0' ) $custgroupID = "NULL";
        else $custgroupID = CONF_DEFAULT_CUSTOMER_GROUP;

        $admin_pass = cryptPasswordCrypt( $admin_pass, null );

        $currencyID = CONF_DEFAULT_CURRENCY;

        db_query( "insert into ".CUSTOMERS_TABLE.
                " (Login, cust_password, Email, first_name, last_name, subscribed4news, ".
                "         custgroupID, addressID, reg_datetime, CID, actions ) values ".
                                "('".xToText($admin_login)."','".xEscSQL($admin_pass)."', ".
                                                " '-', '-', '-', 0, ".(int)$custgroupID.", NULL, ".
                                                " '".xEscSQL(get_current_time())."', ".(int)$currencyID.", '".$actions."')" );
        $errorCode = 0;
        $zoneID = "50";
        $state        = "";
        $countryID = "1";
        $defaultAddressID = regAddAddress(
                                "-", "-",
                                $countryID,
                                $zoneID,
                                $state,
                                "-",
                                "-",
                                $admin_login,
                                $errorCode );
        regSetDefaultAddressIDByLogin( $admin_login, $defaultAddressID );
        return true;
}


// *****************************************************************************
// Purpose
// Inputs   $login - login
// Remarks
// Returns        true if login exists in database, false otherwise
function regIsRegister( $login )
{
        $q=db_query("select count(*) from ".CUSTOMERS_TABLE." where Login='".xToText($login)."'");
        $r = db_fetch_row($q);
        return  ( $r[0] != 0 );
}


// *****************************************************************************
// Purpose
// Inputs   $customerID - custmer ID
// Remarks
// Returns        false if customer does not exist, login - otherwise
function regGetLoginById( $customerID )
{
        if ($customerID == 0) return false;

        $q = db_query("select Login from ".CUSTOMERS_TABLE." where customerID=".(int)$customerID);
        if ( ($r=db_fetch_row($q)) ) return $r["Login"];
        else return false;
}


// *****************************************************************************
// Purpose
// Inputs   $login - login
// Remarks
// Returns        false if customer does not exist, customer ID - otherwise
function regGetIdByLogin( $login )
{
        $q = db_query("select customerID from ".CUSTOMERS_TABLE." where Login='".xToText($login)."'");
        if (  ($r=db_fetch_row($q)) ) return (int)$r["customerID"];
        else return NULL;
}



// *****************************************************************************
// Purpose  authenticate user
// Inputs   $login - login, $password - password
// Remarks  if user is authenticated successfully then this function sets sessions variables,
//                update statistic, move cart content into DB
// Returns        false if authentication failure, true - otherwise
function regAuthenticate($login, $password, $Redirect = true)
{
        $q = db_query("select cust_password, CID, ActivationCode FROM ".CUSTOMERS_TABLE." WHERE Login='".xToText($login)."'");
        $row = db_fetch_row($q);
//        echo $login." ".$password."<br>";
//var_dump($row);exit;

        if(CONF_ENABLE_REGCONFIRMATION && $row['ActivationCode']){

                if($Redirect)RedirectProtected(set_query('&act_customer=1&notact=1'));
                else return false;
        }

        if ($row && strlen( trim($login) ) > 0)
        {
                if ($row["cust_password"] == cryptPasswordCrypt($password, null) )
                {
                        // set session variables
                        $_SESSION["log"]         = $login;
                        $_SESSION["pass"]         = cryptPasswordCrypt($password, null);


                        $_SESSION["current_currency"] = $row["CID"];

                        // update statistic
                        stAddCustomerLog( $login );

                        // move cart content into DB
                        moveCartFromSession2DB();
                        return true;
                }
                else
                        return false;
        }
        else return false;
}



// *****************************************************************************
// Purpose          sends password to customer email
// Inputs
// Remarks
// Returns        true if success
function regSendPasswordToUser( $login, &$smarty_mail )
{
        $q = db_query("select Login, cust_password, Email FROM ".CUSTOMERS_TABLE." WHERE Login='".xToText($login)."' AND (ActivationCode=\"\" OR ActivationCode IS NULL)");
        if ($row = db_fetch_row($q)) //send password
        {
                $password = cryptPasswordDeCrypt( $row["cust_password"], null );
                $smarty_mail->assign( "user_pass", $password );
                $smarty_mail->assign( "user_login", $row['Login'] );
                $html = $smarty_mail->fetch("remind_password.tpl.html");
                xMailTxtHTMLDATA($row["Email"], EMAIL_FORGOT_PASSWORD_SUBJECT, $html);
                return true;
        }
        else
                return false;
}


// *****************************************************************************
// Purpose  determine administrator user
// Inputs   $login - login
// Remarks  if user is authenticated successfully then this function sets sessions variables,
//                update statistic, move cart content into DB
// Returns        false if authentication failure, true - otherwise
function regIsAdminiatrator( $login )
{
        $relaccess = false;
        if (isset($_SESSION["log"])){
        $q = db_query("select actions from ".CUSTOMERS_TABLE." WHERE Login='".xToText($login)."'");
        $n = db_fetch_row($q);
        $n[0] = unserialize( $n[0] );
        if(in_array(100,$n[0]))$relaccess = true;
        }
        return $relaccess;
}



// *****************************************************************************
// Purpose        register new customer
// Inputs
//                                $login                                - login
//                                $cust_password                - password
//                                $Email                                - email
//                                $first_name                        - customer first name
//                                $last_name                        - customer last name
//                                $subscribed4news        - if 1 customer is subscribed to news
//                                $additional_field_values - additional field values is array of item
//                                                                        "additional_field" is value of this field
//                                                                        key is reg_field_ID
// Remarks
// Returns
function regRegisterCustomer( $login, $cust_password, $Email, $first_name,
                $last_name, $subscribed4news, $additional_field_values, $affiliateLogin = '')
{
        $affiliateID = 0;

        if ($affiliateLogin){

                $sql = "select customerID  FROM ".CUSTOMERS_TABLE."
                        WHERE Login='".xToText(trim($affiliateLogin))."'";
                list($affiliateID) = db_fetch_row(db_query($sql));
        }

        foreach( $additional_field_values as $key => $val)
                $additional_field_values[$key] = $val;


        $currencyID = CONF_DEFAULT_CURRENCY;


        $cust_password = cryptPasswordCrypt( $cust_password, null );
        // add customer to CUSTOMERS_TABLE

        $custgroupID = CONF_DEFAULT_CUSTOMER_GROUP;
        if ( $custgroupID == 0 )
                $custgroupID = "NULL";
        /**
         * Activation code
         */
        $ActivationCode = '';
        if(CONF_ENABLE_REGCONFIRMATION){

                $CodeExists = true;
                while ($CodeExists) {

                        $ActivationCode = generateRndCode(16);
                        $sql = 'SELECT 1 FROM '.CUSTOMERS_TABLE.'
                                WHERE ActivationCode="'.xEscapeSQLstring($ActivationCode).'"';
                        @list($CodeExists) = db_fetch_row(db_query($sql));
                }
        }
        db_query("insert into ".CUSTOMERS_TABLE.
                "( Login, cust_password, Email, first_name, last_name, subscribed4news, reg_datetime, CID, custgroupID, affiliateID, ActivationCode )".
                "values( '".xToText(trim($login))."', '".xEscSQL(trim($cust_password))."', '".xToText(trim($Email))."', ".
                " '".xToText(trim($first_name))."', '".xToText(trim($last_name))."', '".(int)$subscribed4news."', '".xEscSQL(get_current_time())."', ".
                        (int)$currencyID.", ".(int)$custgroupID.", ".xEscSQL(trim($affiliateID)).", '".xEscSQL(trim($ActivationCode))."' )" );

        // add additional values to CUSTOMER_REG_FIELDS_TABLE
        foreach( $additional_field_values as $key => $val )
                SetRegField($key, $login, $val["additional_field"]);

        $customerID = regGetIdByLogin($login);
        //db_query("update ".CUSTOMERS_TABLE." set addressID='".$addressID.
        //        "' where Login='".$login."'" );

        if ( $subscribed4news )
                subscrAddRegisteredCustomerEmail( $customerID );

        return true;
}


// *****************************************************************************
// Purpose        send notification message to email
// Inputs
//                                $login                                - login
//                                $cust_password                - password
//                                $Email                                - email
//                                $first_name                        - customer first name
//                                $last_name                        - customer last name
//                                $subscribed4news        - if 1 customer is subscribed to news
//                                $additional_field_values - additional field values is array of item
//                                                                        "additional_field" is value of this field
//                                                                        key is reg_field_ID
//                                $updateOperation        - 1 if customer info is updated, 0
//                                                                otherwise
// Remarks
// Returns
function regEmailNotification($smarty_mail, $login, $cust_password, $Email, $first_name,
                $last_name, $subscribed4news, $additional_field_values,
                $countryID, $zoneID, $state, $city, $address, $updateOperation )
{
        $user = array();
        $smarty_mail->assign( "login", $login );
        $smarty_mail->assign( "cust_password", $cust_password );
        $smarty_mail->assign( "first_name", $first_name );
        $smarty_mail->assign( "last_name", $last_name );
        $smarty_mail->assign( "Email", $Email );
        $additional_field_values = GetRegFieldsValues( $login );
        $smarty_mail->assign( "additional_field_values", $additional_field_values );

        $addresses = regGetAllAddressesByLogin( $login );
        for( $i=0; $i<count($addresses); $i++ )
                $addresses[$i]["addressStr"] = regGetAddressStr( (int)$addresses[$i]["addressID"] );
        $smarty_mail->assign( "addresses", $addresses );

        if(CONF_ENABLE_REGCONFIRMATION){

                $sql = 'SELECT ActivationCode FROM '.CUSTOMERS_TABLE.'
                        WHERE Login="'.xEscapeSQLstring($login).'" AND cust_password="'.xEscapeSQLstring(cryptPasswordCrypt($cust_password, null)).'"';
                @list($ActivationCode) = db_fetch_row(db_query($sql));

                $smarty_mail->assign('ActURL', CONF_FULL_SHOP_URL.(substr(CONF_FULL_SHOP_URL, strlen(CONF_FULL_SHOP_URL)-1,1)=='/'?'':'/').'index.php?act_customer=1&act_code='.$ActivationCode);
                $smarty_mail->assign('ActCode', $ActivationCode);
        }

        $html = $smarty_mail->fetch( "register_successful.tpl.html" );
        xMailTxtHTMLDATA($Email, EMAIL_REGISTRATION, $html);
}

// *****************************************************************************
// Purpose        get customer info
// Inputs
//                                $login                                - login
//                                $cust_password                - password
//                                $Email                                - email
//                                $first_name                        - customer first name
//                                $last_name                        - customer last name
//                                $subscribed4news        - if 1 customer is subscribed to news
//                                $additional_field_values - additional field values is array of item
//                                                                        "additional_field" is value of this field
//                                                                        key is reg_field_ID
//                                $updateOperation        - 1 if customer info is updated, 0
//                                                                otherwise
// Remarks
// Returns
function regGetCustomerInfo($login, & $cust_password, & $Email, & $first_name,
                & $last_name, & $subscribed4news, & $additional_field_values,
                & $countryID, & $zoneID, & $state, & $city, & $address )
{
        $q=db_query("select customerID, cust_password, Email, first_name, last_name, ".
                " subscribed4news, custgroupID, addressID  from ".CUSTOMERS_TABLE.
                " where Login='".xToText($login)."'");
        $r = db_fetch_row($q);
        $cust_password = cryptPasswordDeCrypt( $r["cust_password"], null );
        if (CONF_BACKEND_SAFEMODE)
                $r["Email"] = ADMIN_SAFEMODE_BLOCKED;
        else
        $Email = $r["Email"];
        $first_name=$r["first_name"];
        $last_name= $r["last_name"];
        $subscribed4news        = (int)$r["subscribed4news"];
        $addressID                        = (int)$r["addressID"];
        $customerID                        = (int)$r["customerID"];
        $q=db_query("select countryID, zoneID, state, city, address from ".
                CUSTOMER_ADDRESSES_TABLE." where customerID=".(int)$customerID);
        $r=db_fetch_row($q);
        $countryID  = $r["countryID"];
        $zoneID                = $r["zoneID"];
        $state                =  $r["state"];
        $city                =  $r["city"];
        $address        =  $r["address"];
        $additional_field_values = GetRegFieldsValues( $login );
        foreach( $additional_field_values as $key => $value )
                $additional_field_values[$key] =  $additional_field_values[$key];
}




// *****************************************************************************
// Purpose        get customer info
// Inputs
// Remarks
// Returns
function regGetCustomerInfo2( $login )
{
        $q = db_query("select customerID, cust_password, Email, first_name, last_name, ".
                " subscribed4news, custgroupID, addressID, Login, ActivationCode from ".CUSTOMERS_TABLE.
                " where Login='".xToText($login)."'");
        if ( $row=db_fetch_row($q) )
        {
                if ( $row["custgroupID"] != null )
                {
                        $q = db_query("select custgroupID, custgroup_name, custgroup_discount, sort_order from ".
                                CUSTGROUPS_TABLE." where custgroupID=".(int)$row["custgroupID"] );
                        $custGroup = db_fetch_row($q);
                        $row["custgroup_name"] = $custGroup["custgroup_name"];
                }
                else
                $row["custgroup_name"] = "";
                $row["cust_password"] = cryptPasswordDeCrypt( $row["cust_password"], null );

                if (CONF_BACKEND_SAFEMODE) $row["Email"] = ADMIN_SAFEMODE_BLOCKED;
                $row["allowToDelete"]  = regVerifyToDelete( $row["customerID"] );
        }
        return $row;
}



// -----------------------------------------------

function regAddAddress(
                                $first_name, $last_name, $countryID,
                                $zoneID, $state, $city,
                                $address, $log, &$errorCode )
{
        $customerID = regGetIdByLogin( $log );

        if ( $zoneID == 0 ) $zoneID = "NULL";
        db_query("insert into ".CUSTOMER_ADDRESSES_TABLE.
                " ( first_name, last_name, countryID, zoneID, state, city, ".
                                " address, customerID ) ".
                " values( '".xToText(trim($first_name))."', '".xToText(trim($last_name))."', ".(int)$countryID.", ".(int)$zoneID.", '".xToText(trim($state))."', ".
                        " '".xToText(trim($city))."', '".xToText(trim($address))."', ".(int)$customerID." )");
        return db_insert_id();
}

function regUpdateAddress( $addressID,
                                $first_name, $last_name, $countryID,
                                $zoneID, $state, $city,
                                $address, &$errorCode )
{
        if ( $zoneID == 0 ) $zoneID = "NULL";
        db_query("update ".CUSTOMER_ADDRESSES_TABLE.
                " set ".
                " first_name='".xToText(trim($first_name))."', last_name='".xToText(trim($last_name))."', countryID=".(int)$countryID.", ".
                " zoneID=".(int)$zoneID.", state='".xToText(trim($state))."', ".
                " city='".xToText(trim($city))."', address='".xToText(trim($address))."' where addressID=".(int)$addressID);
        return true;
}

function redDeleteAddress( $addressID )
{
        db_query("update ".CUSTOMERS_TABLE." set addressID=NULL where addressID=".(int)$addressID);
        db_query("delete from ".CUSTOMER_ADDRESSES_TABLE." where addressID=".(int)$addressID);
}


function regGetAddress( $addressID )
{
        if ( $addressID != null )
        {
                // $customerID
                $q = db_query(        "select first_name, last_name, countryID, zoneID, ".
                                                " state, city, address, customerID from ".
                                                CUSTOMER_ADDRESSES_TABLE." where addressID=".(int)$addressID);
               $row=db_fetch_row($q);
               return $row;
        }
        else
                return false;
}


function regGetAddressByLogin( $addressID, $login )
{
        $customerID = regGetIdByLogin( $login );
        $address = regGetAddress( $addressID );
        if ( (int)$address["customerID"] == (int)$customerID )
                return $address;
        else
                return false;
}


function regGetAllAddressesByLogin( $log )
{
        $customerID = regGetIdByLogin( $log );

        $customerID = (int) $customerID;
        if ($customerID == 0) return NULL;

        $q = db_query( "select addressID, first_name, last_name, countryID, zoneID, state, city, address ".
                                        " from ".CUSTOMER_ADDRESSES_TABLE." where customerID=".(int)$customerID);
        $data = array();
        while( $row = db_fetch_row($q) )
        {

                if ( $row["countryID"] != null )
                {
                        $q1=db_query("select country_name from ".COUNTRIES_TABLE.
                                " where countryID=".(int)$row["countryID"] );
                        $country = db_fetch_row($q1);
                        $row["country"] = $country[0];
                }
                else
                        $row["country"] = "-";

                if ( $row["zoneID"] != null )
                {
                        $q1 = db_query("select zone_name from ".ZONES_TABLE.
                                        " where zoneID=".(int)$row["zoneID"] );
                         $zone = db_fetch_row( $q1 );
                        $row["state"] = $zone[0];
                }

                $data[] = $row;
        }
        return $data;
}

function regGetDefaultAddressIDByLogin( $log )
{
        $q = db_query("select addressID from ".CUSTOMERS_TABLE." where Login='".xToText($log)."'");
        if ( $row = db_fetch_row( $q ) )
                return (int)$row[0];
        else
                return null;
}

function regSetDefaultAddressIDByLogin( $log, $defaultAddressID )
{
        db_query( "update ".CUSTOMERS_TABLE." set addressID=".(int)$defaultAddressID." where Login='".xToText($log)."'" );
}


function _testStrInvalidSymbol( $str )
{
        $res = strstr( $str, "'" );
        if ( is_string($res) )
                return false;

        $res = strstr( $str, "\\" );
        if ( is_string($res) )
                return false;

        $res = strstr( $str, '"' );
        if ( is_string($res) )
                return false;

        $res = strstr( $str, "<" );
        if ( is_string($res) )
                return false;

        $res = strstr( $str, ">" );
        if ( is_string($res) )
                return false;

        return true;
}

function _testStrArrayInvalidSymbol( $array )
{
        foreach( $array as $str )
        {
                $res = _testStrInvalidSymbol( $str );
                if ( !$res )
                        return false;
        }
        return true;
}



// *****************************************************************************
// Purpose        verify address input data
// Inputs
//                                $first_name                        - customer first name
//                                $last_name                        - customer last name
//                                $countryID                        - country ID
//                                $zoneID
//                                $state
//                                $city
//                                $address
// Remarks
// Returns        empty string if success, error message otherwise
function regVerifyAddress(        $first_name, $last_name,
                                                        $countryID, $zoneID, $state,
                                                        $city, $address )
{
        $error = "";
        if ( trim($first_name) == "" ) $error = ERROR_INPUT_NAME;
        else
        if ( trim($last_name) == "" ) $error = ERROR_INPUT_NAME;
        else
        if ( CONF_ADDRESSFORM_STATE == 0 && trim($state) == "" && $zoneID == 0 )        $error = ERROR_INPUT_STATE;
        else
        if ( CONF_ADDRESSFORM_CITY == 0 && trim($city) == "" )        $error = ERROR_INPUT_CITY;
        else
        if ( CONF_ADDRESSFORM_ADDRESS == 0 && trim($address)=="")        $error = ERROR_INPUT_ADDRESS;

        $q = db_query("select count(*) from ".ZONES_TABLE." where countryID=".(int)$countryID);
        $r = db_fetch_row( $q );
        $countZone = $r[0];

        if ( $countZone != 0 )
        {
                $q = db_query("select count(*) from ".ZONES_TABLE." where zoneID=".(int)$zoneID.
                        "  AND countryID=".(int)$countryID);
                $r = db_fetch_row( $q );
                if ( $r[0] == 0 && CONF_ADDRESSFORM_STATE != 2 )
                        $error = ERROR_ZONE_DOES_NOT_CONTAIN_TO_COUNTRY;
        }
        else if ($zoneID!=0) $error = ERROR_INPUT_STATE;

        return $error;
}

function regGetContactInfo( $login, &$cust_password, &$Email, &$first_name,
                                &$last_name, &$subscribed4news, &$additional_field_values )
{
        $q=db_query("select customerID, cust_password, Email, first_name, last_name, ".
                " subscribed4news, custgroupID, addressID  from ".CUSTOMERS_TABLE.
                " where Login='".xToText($login)."'");
        $row = db_fetch_row( $q );
        $cust_password                                = cryptPasswordDeCrypt( $row["cust_password"], null );
        $Email    = $row["Email"];
        $first_name  =$row["first_name"];
        $last_name  =$row["last_name"];
        $subscribed4news                        = $row["subscribed4news"];
        $additional_field_values        = GetRegFieldsValues($login);
}

function regVerifyContactInfo( $login, $cust_password1, $cust_password2,
                                                $Email, $first_name, $last_name, $subscribed4news,
                                                $additional_field_values )
{
        $error = "";
        if (
                        !_testStrArrayInvalidSymbol(
                                                                                array( $login, $cust_password1, $cust_password2 )
                                                                        )
                )
                $error = ERROR_INVALID_SYMBOL_LOGIN_INFO;
        else
        if ( trim($login) == "" ) $error = ERROR_INPUT_LOGIN;
        else
        if (!(((ord($login)>=ord("a")) && (ord($login)<=ord("z"))) ||
                        ((ord($login)>=ord("A")) && (ord($login)<=ord("Z")))))
                                $error = ERROR_LOGIN_SHOULD_START_WITH_LATIN_SYMBOL;
        else
        if ( $cust_password1 == "" ||  $cust_password2 == "" || $cust_password1 != $cust_password2 )
                $error = ERROR_WRONG_PASSWORD_CONFIRMATION;
        else
        if ( trim($first_name) == "" ) $error = ERROR_INPUT_NAME;
        else
        if ( trim($last_name) == "" ) $error = ERROR_INPUT_NAME;
        else
        if ( trim($Email) == "" ) $error = ERROR_INPUT_EMAIL;
        else if (!preg_match("/^[_\.a-z0-9-]{1,20}@(([a-z0-9-]+\.)+(com|net|org|mil|edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/is",$Email))
        { //e-mail validation
                $error = ERROR_INPUT_EMAIL;
        }

        if (isset($_POST['affiliationLogin']))
        if ( !regIsRegister($_POST['affiliationLogin']) && $_POST['affiliationLogin'])
                        $error = ERROR_WRONG_AFFILIATION;

        foreach( $additional_field_values as $key => $val )
        {
                if ( !_testStrInvalidSymbol($val["additional_field"]) )
                        return ERROR_INVALID_SYMBOL;
                if ( trim($val["additional_field"]) == "" && GetIsRequiredRegField($key) == 1 )
                {
                        $error = ERROR_INPUT_ADDITION_FIELD;
                        break;
                }
        }
        return $error;
}


function regUpdateContactInfo( $old_login, $login, $cust_password,
                                                $Email, $first_name, $last_name, $subscribed4news,
                                                $additional_field_values )
{
        db_query("update ".CUSTOMERS_TABLE."  set ".
                        " Login = '".xToText(trim($login))."', ".
                        " cust_password = '".cryptPasswordCrypt( $cust_password, null )."', ".
                        " Email = '".xToText($Email)."', ".
                        " first_name = '".xToText(trim($first_name))."', ".
                        " last_name = '".xToText(trim($last_name))."', ".
                        " subscribed4news = ".(int)$subscribed4news." ".
                        " where Login='".xToText(trim($old_login))."'");
        foreach( $additional_field_values as $key => $val )
                SetRegField($key, $login, $val["additional_field"]);


        if (!strcmp($old_login, $login)) //update administrator login (core/config/connect.inc.php)
        {
        db_query("update ".CUSTOMERS_TABLE." set Login='".xToText(trim($login))."' where Login='".xToText(trim($old_login))."'");
        }


        $customerID = regGetIdByLogin( $login );



        if ( $subscribed4news )
                subscrAddRegisteredCustomerEmail( $customerID );
        else
                subscrUnsubscribeSubscriberByEmail( base64_encode($Email) );
}


// *****************************************************************************
// Purpose        get address string by address ID
// Inputs
// Remarks
// Returns
function regGetAddressStr( $addressID, $NoTransform = false  )
{
        $address = regGetAddress( $addressID );

        // countryID, zoneID, state
        $country = cnGetCountryById( $address["countryID"] );
        $country = $country["country_name"];
        if ( trim($address["state"]) == "" )
        {
                $zone = znGetSingleZoneById( $address["zoneID"] );
                $zone = $zone["zone_name"];
        }
        else
                $zone = trim($address["state"]);

        if ( $country != "" )
        {
                $strAddress = $address["first_name"]."  ".$address["last_name"];
                if (strlen($address["address"])>0) $strAddress .= "<br>".$address["address"];
                if (strlen($address["city"])>0) $strAddress .= "<br>".$address["city"];
                if (strlen($zone)>0) $strAddress .= "  ".$zone;
                if (strlen($country)>0) $strAddress .= "<br>".$country;
        }
        else
        {
                $strAddress = $address["first_name"]."  ".$address["last_name"];
                if (strlen($address["address"])>0) $strAddress .= "<br>".$address["address"];
                if (strlen($address["city"])>0) $strAddress .= "<br>".$address["city"];
                if (strlen($zone)>0) $strAddress .= " ".$zone;
        }

        return $strAddress;
}


// *****************************************************************************
// Purpose        gets all customers
// Inputs
// Remarks
// Returns
function regGetCustomers( $callBackParam, &$count_row, $navigatorParams = null )
{
        if ( $navigatorParams != null )
        {
                $offset                = $navigatorParams["offset"];
                $CountRowOnPage        = $navigatorParams["CountRowOnPage"];
        }
        else
        {
                $offset = 0;
                $CountRowOnPage = 0;
        }

        $where_clause = "";
        if ( isset($callBackParam["Login"]) )
        {
                $callBackParam["Login"] = xEscSQL( $callBackParam["Login"] );
                $where_clause .= " Login LIKE '%".$callBackParam["Login"]."%' ";
        }

        if ( isset($callBackParam["first_name"]) )
        {
                $callBackParam["first_name"] = xEscSQL( $callBackParam["first_name"] );
                if ( $where_clause != "" ) $where_clause .= " AND ";
                $where_clause .= " first_name LIKE '%".$callBackParam["first_name"]."%' ";
        }

        if ( isset($callBackParam["last_name"]) )
        {
                $callBackParam["last_name"] = xEscSQL( $callBackParam["last_name"] );
                if ( $where_clause != "" ) $where_clause .= " AND ";
                $where_clause .= " last_name LIKE '%".$callBackParam["last_name"]."%' ";
        }

        if ( isset($callBackParam["email"]) )
        {
                $callBackParam["email"] = xEscSQL( $callBackParam["email"] );
                if ( $where_clause != "" ) $where_clause .= " AND ";
                $where_clause .= " Email LIKE '%".$callBackParam["email"]."%' ";
        }

        if ( isset($callBackParam["groupID"]) )
        {
                if ( $callBackParam["groupID"] != 0 )
                {
                        if ( $where_clause != "" ) $where_clause .= " AND ";
                        $where_clause .= " custgroupID = ".(int)$callBackParam["groupID"]." ";
                }
        }

        if ( isset($callBackParam["ActState"]) )
        {
                switch ($callBackParam["ActState"]){

                        #activated
                        case 1:
                                if ( $where_clause != "" ) $where_clause .= " AND ";
                                $where_clause .= " (ActivationCode='' OR ActivationCode IS NULL)";
                                break;
                        #not activated
                        case 0:
                                if ( $where_clause != "" ) $where_clause .= " AND ";
                                $where_clause .= " ActivationCode!=''";
                                break;
                }
        }


        if ( $where_clause != "" )
                $where_clause = " where ".$where_clause;


        $order_clause = "";
        if ( isset($callBackParam["sort"]) )
        {
                $order_clause .= " order by ".xEscSQL($callBackParam["sort"])." ";
                if ( isset($callBackParam["direction"]) )
                {
                        if ( $callBackParam["direction"] == "ASC" )
                                $order_clause .=  " ASC ";
                        else
                                $order_clause .=  " DESC ";
                }
        }




        $q=db_query("select customerID, Login, cust_password, Email, first_name, last_name, subscribed4news, ".
                 " custgroupID, addressID, reg_datetime, ActivationCode ".
                 " from ".CUSTOMERS_TABLE." ".$where_clause." ".$order_clause );
        $data = array();
        $i=0;//var_dump ($navigatorParams);
        while( $row=db_fetch_row($q) )
        {

                if ( ($i >= $offset && $i < $offset + $CountRowOnPage) ||
                                $navigatorParams == null  )
                {
                        $group = GetCustomerGroupByCustomerId( $row["customerID"] );
                        $row["custgroup_name"] = $group["custgroup_name"];
                        $row["allowToDelete"]  = regVerifyToDelete( $row["customerID"] );
                        $row["reg_datetime"]  = format_datetime( $row["reg_datetime"] );
                        $data[] = $row;
                }
                $i++;
        }
        $count_row = $i;
        return $data;
}


function regSetSubscribed4news( $customerID, $value )
{
        db_query( "update ".CUSTOMERS_TABLE." set subscribed4news = ".(int)$value.
                        " where customerID=".(int)$customerID );
        if ($value > 0)
        {
                subscrAddRegisteredCustomerEmail($customerID);
        }
        else
        {
                subscrUnsubscribeSubscriberByCustomerId($customerID);
        }
}

function regSetCustgroupID( $customerID, $custgroupID )
{
        db_query( "update ".CUSTOMERS_TABLE." set custgroupID=".(int)$custgroupID.
                        " where customerID=".(int)$customerID );
}



function regAddressBelongToCustomer( $customerID, $addressID )
{

        if (!$customerID) return false;

        if (!$addressID) return false;

        $q_count = db_query( "select count(*) from ".CUSTOMER_ADDRESSES_TABLE.
                " where customerID=".(int)$customerID." AND addressID=".(int)$addressID );
        $count = db_fetch_row( $q_count );
        $count = $count[0];
        return ( $count != 0 );
}




function regVerifyToDelete( $customerID )
{

        if (!$customerID) return 0;

        $q = db_query( "select count(*) from ".CUSTOMERS_TABLE." where customerID=".(int)$customerID );
        $row = db_fetch_row($q);

        if ( regIsAdminiatrator(regGetLoginById($customerID))  )
                return false;

        return ($row[0] == 1);
}



function regDeleteCustomer( $customerID )
{
        if ( $customerID == null || trim($customerID) == ""  )
                return false;

        if (!$customerID) return 0;

        if ( regVerifyToDelete( $customerID ) )
        {
                db_query( "delete from ".SHOPPING_CARTS_TABLE." where customerID=".(int)$customerID );
                db_query( "delete from ".MAILING_LIST_TABLE." where customerID=".(int)$customerID );
                db_query( "delete from ".CUSTOMER_ADDRESSES_TABLE." where customerID=".(int)$customerID );
                db_query( "delete from ".CUSTOMER_REG_FIELDS_VALUES_TABLE." where customerID=".(int)$customerID );
                db_query( "delete from ".CUSTOMERS_TABLE." where customerID=".(int)$customerID );
                db_query( "update ".ORDERS_TABLE." set customerID=NULL where customerID=".(int)$customerID );
                return true;
        }
        else
                return false;
}

function regActivateCustomer($_CustomerID){

        $sql = 'UPDATE '.CUSTOMERS_TABLE.'
                SET ActivationCode = ""
                WHERE customerID='.(int)$_CustomerID;
        db_query($sql);
}

?>