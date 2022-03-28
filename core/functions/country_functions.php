<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


function cnGetCountryById( $countryID )
{
        if ( is_null($countryID) || $countryID == "" ) $countryID = "NULL";
        else $countryID = (int)$countryID;
        $q = db_query("select countryID, country_name, country_iso_2, country_iso_3 from ".
                COUNTRIES_TABLE." where countryID=".$countryID);
        $row=db_fetch_row($q);
        return $row;
}



// *****************************************************************************
// Purpose        gets all manufacturers
// Inputs                     nothing
// Remarks
// Returns                array of maunfactirer, each item of this array
//                                have next struture
//                                        "countryID"        - id
//                                        "country_name"        - name
//                                        "country_iso_2"        - ISO abbreviation ( 2 chars )
//                                        "country_iso_3"        - ISO abbreviation ( 3 chars )
function cnGetCountries( $callBackParam, &$count_row, $navigatorParams = null )
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

        $q=db_query("select countryID, country_name, ".
                " country_iso_2, country_iso_3 from ".COUNTRIES_TABLE." ".
                " order by country_name" );
        $data=array();
        $i=0;
        while( $r=db_fetch_row($q) )
        {
                if ( ($i >= $offset && $i < $offset + $CountRowOnPage) ||
                                $navigatorParams == null  )
                {
                        $data[] = $r;
                }
                $i++;
        }
        $count_row = $i;
        return $data;
}



// *****************************************************************************
// Purpose        deletes country
// Inputs                     id
// Remarks
// Returns                nothing
function cnDeleteCountry($countryID)
{

        $tax_classes = taxGetTaxClasses();
        foreach( $tax_classes as $class ) taxDeleteRate( $class["classID"], $countryID );

        db_query("update ".CUSTOMER_ADDRESSES_TABLE.
                " set countryID=NULL where countryID=".(int)$countryID);
        $q = db_query("select zoneID from ".ZONES_TABLE." where countryID=".(int)$countryID);
        while( $r = db_fetch_row( $q ) )
        {
                db_query( "update ".CUSTOMER_ADDRESSES_TABLE.
                        " set zoneID=NULL where zoneID=".(int)$r["zoneID"]);
        }
        db_query("delete from ".ZONES_TABLE." where countryID=".(int)$countryID);
        db_query("delete from ".COUNTRIES_TABLE." where countryID=".(int)$countryID);
}


// *****************************************************************************
// Purpose        updates manufacturers
// Inputs                     $countryID        - id
//                        $country_name        - name
//                        $country_iso_2        - ISO abbreviation ( 2 chars )
//                        $country_iso_3        - ISO abbreviation ( 3 chars )
// Remarks
// Returns                nothing
function cnUpdateCountry( $countryID, $country_name, $country_iso_2, $country_iso_3 )
{
        db_query("update ".COUNTRIES_TABLE." set ".
                "  country_name='".xToText(trim($country_name))."', ".
                "  country_iso_2='".xToText(trim($country_iso_2))."', ".
                "  country_iso_3='".xToText(trim($country_iso_3))."' ".
                "  where countryID=".(int)$countryID);
}


// *****************************************************************************
// Purpose        adds manufacturers
// Inputs
//                        $country_name        - name
//                        $country_iso_2        - ISO abbreviation ( 2 chars )
//                        $country_iso_3        - ISO abbreviation ( 3 chars )
// Remarks
// Returns                nothing
function cnAddCountry($country_name, $country_iso_2, $country_iso_3  )
{
        db_query("insert into ".COUNTRIES_TABLE."( country_name, country_iso_2, country_iso_3 )".
                "values( '".xToText(trim($country_name))."', '".xToText(trim($country_iso_2))."', '".
                xToText(trim($country_iso_3))."' )" );
        return db_insert_id();
}

?>