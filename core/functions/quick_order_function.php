<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


function quickOrderContactInfoVerify()
{
        $first_name                = $_POST["first_name"];
        if ( trim($first_name) == "" )
                return ERROR_INPUT_NAME;

        $last_name                = $_POST["last_name"];
        if ( trim($last_name) == "" )
                return ERROR_INPUT_NAME;

        $Email                        = $_POST["email"];
        if ( trim($Email) == "" )
                return ERROR_INPUT_EMAIL;

        if (!preg_match("/^[_\.a-z0-9-]{1,20}@(([a-z0-9-]+\.)+(com|net|org|mil|edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/is",$Email)){
                if ($Email != "-"){
                return ERROR_INPUT_EMAIL;
                }
        }

        if (isset($_POST['affiliationLogin']))
                if ( !regIsRegister($_POST['affiliationLogin']) && $_POST['affiliationLogin'])
                        return ERROR_WRONG_AFFILIATION;
        //aux fields
        foreach($_POST as $key => $val)
        {
                if (strstr($key,"additional_field_"))
                {
                        $id = (int) str_replace("additional_field_","",$key);
                        if (GetIsRequiredRegField($id) && strlen(trim($val))==0)
                                return FEEDBACK_ERROR_FILL_IN_FORM;
                }
        }

        return "";
}


function quickOrderReceiverAddressVerify()
{
        $receiver_first_name        = $_POST["receiver_first_name"];
        $receiver_last_name        = $_POST["receiver_last_name"];
        $countryID                = $_POST["countryID"];
        if ( isset($_POST["state"])  )
                $state = $_POST["state"];
        else
                $state = "";
        $city                        = $_POST["city"];
        $address                = $_POST["address"];
        if ( isset($_POST["zoneID"]) )
                $zoneID = $_POST["zoneID"];
        else
                $zoneID = 0;
        $error = regVerifyAddress( $receiver_first_name, $receiver_last_name, $countryID, $zoneID, $state,
                                                         $city, $address );
        return $error;
}


function quickOrderBillingAddressVerify()
{
        if ( isset($_POST["billing_address_check"]) )
                return quickOrderReceiverAddressVerify();
        $payer_first_name                = $_POST["payer_first_name"];
        $payer_last_name                = $_POST["payer_last_name"];
        $billingCountryID                = $_POST["billingCountryID"];
        if ( isset($_POST["billingState"]) )
                $billingState = $_POST["billingState"];
        else
                $billingState = "";

        $billingCity                        = $_POST["billingCity"];
        $billingAddress                        = $_POST["billingAddress"];
        if ( isset($_POST["billingZoneID"]) )
                $billingZoneID = $_POST["billingZoneID"];
        else
                $billingZoneID = 0;
        $error = regVerifyAddress( $payer_first_name, $payer_last_name, $billingCountryID,
                                                        $billingZoneID, $billingState, $billingCity, $billingAddress );
        return $error;
}


function quikOrderSetCustomerInfo()
{
        $_SESSION["first_name"]        = $_POST["first_name"];
        $_SESSION["last_name"]        = $_POST["last_name"];
        $_SESSION["email"]        = $_POST["email"];
        $_SESSION['affiliationLogin'] = $_POST['affiliationLogin'];

        //save aux fields to session
        foreach($_POST as $key => $val)
        {
                if (strstr($key,"additional_field_") && strlen(trim($val)) > 0) //save information into sessions
                {
                        $_SESSION[$key] = $val;
                }
        }
}


function quickOrderSetReceiverAddress()
{
        $_SESSION["receiver_first_name"] = $_POST["first_name"];
        $_SESSION["receiver_last_name"]  = $_POST["last_name"];
        $_SESSION["receiver_countryID"]         = $_POST["countryID"];
        $_SESSION["receiver_state"]         = $_POST["state"];
        $_SESSION["receiver_zoneID"]         = $_POST["zoneID"];
        $_SESSION["receiver_city"]         = $_POST["city"];
        $_SESSION["receiver_address"]         = $_POST["address"];
}


function quickOrderSetBillingAddress()
{
        if ( !isset($_POST["billing_address_check"]) )
        {
                $_SESSION["billing_first_name"]  = $_POST["first_name"];
                $_SESSION["billing_last_name"]         = $_POST["last_name"];
                $_SESSION["billing_countryID"]         = $_POST["billingCountryID"];
                $_SESSION["billing_state"]         = $_POST["billingState"];
                $_SESSION["billing_city"]          = $_POST["billingCity"];
                $_SESSION["billing_zoneID"]          = $_POST["billingZoneID"];
                $_SESSION["billing_address"]         = $_POST["billingAddress"];
        }
        else
        {
                $_SESSION["billing_first_name"]  = $_POST["first_name"];
                $_SESSION["billing_last_name"]   = $_POST["last_name"];
                $_SESSION["billing_countryID"]         = $_POST["countryID"];
                $_SESSION["billing_state"]         = $_POST["state"];
                $_SESSION["billing_zoneID"]         = $_POST["zoneID"];
                $_SESSION["billing_city"]         = $_POST["city"];
                $_SESSION["billing_address"]         = $_POST["address"];
        }
}

function quickOrderGetReceiverAddressStr()
{
        if (!isset($_SESSION["receiver_countryID"]) || !isset($_SESSION["receiver_first_name"])) return "";

        // countryID, zoneID, state
        $country = cnGetCountryById( $_SESSION["receiver_countryID"] );
        $country = $country["country_name"];
        if ( trim($_SESSION["receiver_state"]) == "" )
        {
                $zone = znGetSingleZoneById( $_SESSION["receiver_zoneID"] );
                $zone = $zone["zone_name"];
        }
        else
                $zone = trim( $_SESSION["receiver_state"] );

        if (strlen($_SESSION["receiver_address"])>0){
        $strAddress = xHtmlSpecialChars($_SESSION["receiver_first_name"] );

     $strAddress .= " ".xHtmlSpecialChars($_SESSION["receiver_last_name"] );


        if (strlen($_SESSION["receiver_address"])>0)
                $strAddress .= "<br>".xHtmlSpecialChars( $_SESSION["receiver_address"] );
        if (strlen($_SESSION["receiver_city"])>0)
                $strAddress .= "<br>".xHtmlSpecialChars( $_SESSION["receiver_city"] );
        if (strlen($zone)>0)
                $strAddress .= "<br>".xHtmlSpecialChars($zone);

        if (strlen($country)>0)
                $strAddress .= "<br>".$country;
                }
        return $strAddress;
}

function quickOrderGetBillingAddressStr()
{
        if (!isset($_SESSION["billing_countryID"]) || !isset($_SESSION["billing_first_name"]))
                return "";

        // countryID, zoneID, state
        $country = cnGetCountryById( $_SESSION["billing_countryID"] );
        $country = $country["country_name"];
        if ( trim($_SESSION["billing_state"]) == "" )
        {
                $zone = znGetSingleZoneById( $_SESSION["billing_zoneID"] );
                $zone = $zone["zone_name"];
        }
        else
                $zone = trim( $_SESSION["billing_state"] );

         $strAddress = xHtmlSpecialChars( $_SESSION["billing_first_name"] );
        if (strlen($_SESSION["billing_address"])>0)
                $strAddress .= "<br>".xHtmlSpecialChars( $_SESSION["billing_address"] );
        if (strlen($_SESSION["billing_city"])>0)
                $strAddress .= "<br>".xHtmlSpecialChars( $_SESSION["billing_city"] );
        if (strlen($zone)>0)
                $strAddress .= "<br>".xHtmlSpecialChars($zone);

        if (strlen($country)>0)
                $strAddress .= "<br>".$country;

        return $strAddress;
}

?>