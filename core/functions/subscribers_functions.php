<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

function subscrVerifyEmailAddress( $email )
{
        if ( trim($email) == "" )
                return ERROR_INPUT_EMAIL;

        if ( !_testStrInvalidSymbol($email) )
                return ERROR_INPUT_EMAIL;
        if (!preg_match("/^[_\.a-z0-9-]{1,20}@(([a-z0-9-]+\.)+(com|net|org|mil|edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/is",$email))
                return ERROR_INPUT_EMAIL;

        return "";
}


// *****************************************************************************
// Purpose        get all subscribers
// Inputs
// Remarks
// Returns
function subscrGetAllSubscriber( $callBackParam, &$count_row, $navigatorParams = null )
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

        $sql = 'SELECT mtbl.Email, mtbl.customerID, ctbl.ActivationCode FROM '.MAILING_LIST_TABLE.' as mtbl
                LEFT JOIN '.CUSTOMERS_TABLE.' as ctbl ON ctbl.customerID = mtbl.customerID
                WHERE ctbl.ActivationCode="" OR ctbl.ActivationCode IS NULL
                ORDER BY mtbl.Email';
        $q = db_query( $sql );

        $data = array();
        $i=0;
        while( $row = db_fetch_row($q) )
        {
                if ( ($i >= $offset && $i < $offset + $CountRowOnPage) ||
                        $navigatorParams == null  )
                        $data[] = $row;
                $i++;
        }
        $count_row = $i;
        return $data;
}



function _subscriberIsSubscribed( $email )
{
        $q = db_query( "select count(*) from ".MAILING_LIST_TABLE." where Email='".xToText($email)."'" );
        $countSubscribers = db_fetch_row($q);
        $countSubscribers = $countSubscribers[0];

        return ($countSubscribers != 0);
}


// *****************************************************************************
// Purpose        subscribe unregistered customer
// Inputs
// Remarks
// Returns
function subscrAddUnRegisteredCustomerEmail( $email )
{
        if ( !_subscriberIsSubscribed($email) )
        {
                $q = db_query( "select customerID from ".CUSTOMERS_TABLE." where Email='".xToText($email)."'" );
                if ( $row = db_fetch_row($q) )
                {
                        db_query( "update ".CUSTOMERS_TABLE." set subscribed4news=1 ".
                                " where customerID=".(int)$row["customerID"] );
                        db_query( "insert into ".MAILING_LIST_TABLE." ( Email, customerID ) ".
                                " values ( '".xToText($email)."', ".(int)$row["customerID"]." )" );
                }
                else
                        db_query( "insert into ".MAILING_LIST_TABLE." ( Email ) values ( '".xToText($email)."' )" );
        }
}


// *****************************************************************************
// Purpose        subscribe registered customer
// Inputs
// Remarks
// Returns
function subscrAddRegisteredCustomerEmail( $customerID )
{
        $q = db_query( "select Email from ".CUSTOMERS_TABLE." where customerID=".(int)$customerID );
        $customer = db_fetch_row( $q );
        if ( $customer )
        {
                db_query( "update ".CUSTOMERS_TABLE." set subscribed4news=1 where customerID=".(int)$customerID );

                if (  _subscriberIsSubscribed($customer["Email"])  )
                {
                        db_query( "update ".MAILING_LIST_TABLE.
                                " set customerID=".(int)$customerID.
                                " where Email='".xToText($customer["Email"])."'" );

                }
                else
                        db_query( "insert into ".MAILING_LIST_TABLE.
                                " ( Email, customerID ) ".
                                " values( '".xToText($customer["Email"])."', ".(int)$customerID."  ) " );
        }
}


function subscrUnsubscribeSubscriberByCustomerId( $customerID )
{
        db_query( "delete from ".MAILING_LIST_TABLE." where customerID=".(int)$customerID);
        db_query( "update ".CUSTOMERS_TABLE." set subscribed4news=0 where customerID=".(int)$customerID );
}



function subscrUnsubscribeSubscriberByEmail( $email )
{
        $email = base64_decode($email);
        db_query( "update ".CUSTOMERS_TABLE." set subscribed4news=0  where Email='".xToText($email)."'" );
        db_query( "delete from ".MAILING_LIST_TABLE." where Email='".xToText($email)."'" );
}

function subscrUnsubscribeSubscriberByEmail2( $email )
{
        db_query( "update ".CUSTOMERS_TABLE." set subscribed4news=0  where Email='".xToText($email)."'" );
        db_query( "delete from ".MAILING_LIST_TABLE." where Email='".xToText($email)."'" );
}

function SendNewsMessage( $title, $message )
{
        $q = db_query( "select Email from ".MAILING_LIST_TABLE );
        while( $subscriber = db_fetch_row($q) ) xMailTxtHTMLDATA($subscriber["Email"], $title, $message);
}

?>