<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        // *****************************************************************************
        // Purpose        get tax class by class ID
        // Inputs   $classID
        // Remarks
        // Returns
        //                                "classID"                        tax class ID
        //                                "name"                                tax class name
        //                                "address_type"
        //                                                                        0 - shipping address
        //                                                                        1 - billing address
        function taxGetTaxClassById( $classID )
        {
                $q = db_query("select classID, name, address_type from ".TAX_CLASSES_TABLE.
                        " where classID=".(int)$classID);
                if ( $row = db_fetch_row($q) ) return $row;
        }


        function taxGetTaxClasses()
        {
                $q = db_query("select classID, name, address_type from ".TAX_CLASSES_TABLE );
                $res = array();
                while( $row = db_fetch_row($q) ) $res[]=$row;
                return $res;
        }


        function taxAddTaxClass( $name, $address_type )
        {
                if ( trim($name) == "" ) return;
                db_query("insert into ".TAX_CLASSES_TABLE.
                        "( name, address_type ) ".
                        " values( '".xToText($name),"', ".(int)$address_type."  ) " );
        }

        function taxUpdateTaxClass( $classID, $name, $address_type )
        {
                db_query( "update ".TAX_CLASSES_TABLE.
                        " set name='".xToText($name)."', address_type=".(int)$address_type.
                        " where classID=".(int)$classID);
        }

        function taxDeleteTaxClass( $classID )
        {
                db_query("update ".PRODUCTS_TABLE." set classID=NULL where classID=".(int)$classID);
                db_query("delete from ".TAX_CLASSES_TABLE." where classID=".(int)$classID);
        }


        function taxGetRates( $classID )
        {
                $q=db_query("select classID, countryID, value, isByZone from ".
                                TAX_RATES_TABLE." where classID=".(int)$classID." AND isGrouped=0" );
                $res = array();
                while( $row=db_fetch_row($q) )
                {
                        $q1 = db_query("select country_name from ".COUNTRIES_TABLE.
                                " where countryID=".(int)$row["countryID"] );
                        $country = db_fetch_row($q1);
                        $row["country"] = $country["country_name"];
                        $res[]=$row;
                }

                $q=db_query("select classID, countryID, value, isByZone from ".
                                TAX_RATES_TABLE." where classID=".(int)$classID." AND isGrouped=1" );
                if ( $row=db_fetch_row($q) )
                {
                        $row["countryID"]        = 0;
                        $row["isByZone"]        = 0;
                        $res[]                         = $row;
                }
                return $res;
        }

        function taxGetCountriesByClassID_ToSetRate( $classID )
        {
                $res = array();
                $q = db_query("select countryID, country_name from ".COUNTRIES_TABLE.
                                " order by country_name " );
                while( $country=db_fetch_row($q) )
                {
                        $q1 = db_query("select * from ".TAX_RATES_TABLE.
                                " where countryID=".(int)$country["countryID"].
                                " AND classID=".(int)$classID);
                        if ( !($row=db_fetch_row($q1)) )
                                $res[] = $country;
                }
                return $res;
        }

        function taxAddRate( $classID, $countryID, $isByZone, $value )
        {
                if ( $countryID == 0 )
                {
                        $q = db_query("select countryID from ".COUNTRIES_TABLE );
                        while( $country=db_fetch_row($q) )
                        {
                                $q1 = db_query("select * from ".TAX_RATES_TABLE.
                                        " where countryID=".(int)$country["countryID"].
                                        " AND classID=".(int)$classID);
                                 if ( !$row=db_fetch_row($q1) )
                                {
                                        db_query("insert into ".TAX_RATES_TABLE.
                                                " ( classID, countryID, value, ".
                                                "         isByZone, isGrouped ) ".
                                                " values( ".(int)$classID.", ".(int)$country["countryID"].
                                                        ", ".(float)$value.", 0, 1 )" );
                                }
                        }
                }
                else
                        db_query("insert into ".TAX_RATES_TABLE.
                                " ( classID, countryID, value, isByZone, isGrouped ) ".
                                " values( ".(int)$classID.", ".(int)$countryID.", ".(float)$value.", ".(int)$isByZone.", 0 )" );
        }

        function taxUpdateRate( $classID, $countryID, $isByZone, $value )
        {
                 if ( $countryID == 0 )
                {
                        db_query("update ".TAX_RATES_TABLE.
                                " set isByZone=0, value=".(float)$value.
                                " where classID=".(int)$classID." AND isGrouped=1" );
                }
                else
                {
                        db_query("update ".TAX_RATES_TABLE.
                                " set isByZone=".(int)$isByZone.", value=".(float)$value.
                                " where classID=".(int)$classID." AND countryID=".(int)$countryID.
                                " AND isGrouped=0" );
                }
        }

        function taxSetIsByZoneAttribute( $classID, $countryID, $isByZone )
        {
                if ( $countryID != 0 )
                {
                        db_query( "update ".TAX_RATES_TABLE.
                                          " set isByZone=".(int)$isByZone.
                                          " where classID=".(int)$classID." AND countryID=".(int)$countryID);
                }
        }


        function _deleteRate( $classID, $countryID )
        {
                $q = db_query("select zoneID from ".ZONES_TABLE.
                                " where countryID=".(int)$countryID);
                while( $zone=db_fetch_row($q) )
                        db_query("delete from ".TAX_RATES_ZONES_TABLE.
                                " where classID=".(int)$classID." AND zoneID=".(int)$zone["zoneID"]);
                db_query("delete from ".TAX_ZIP_TABLE.
                                " where classID=".(int)$classID." AND countryID=".(int)$countryID);
                db_query("delete from ".TAX_RATES_TABLE.
                                " where classID=".(int)$classID." AND countryID=".(int)$countryID);
        }


        function taxDeleteRate( $classID, $countryID )
        {
                $res = array();
                if ( $countryID==0 )
                {
                        $q=db_query("select countryID from ".TAX_RATES_TABLE.
                                " where classID=".(int)$classID." AND isGrouped=1");
                        while($row=db_fetch_row($q))
                                $res[] = $row["countryID"];
                }
                else
                        $res[]=$countryID;

                $q_count = db_query("select count(*) from ".TAX_RATES_TABLE.
                                " where classID=".(int)$classID." AND isGrouped=1");
                $count = db_fetch_row( $q_count );
                $count = $count[0];

                if ( $count!=0 && count($res)==1 )
                {
                        db_query("update ".TAX_RATES_TABLE.
                                " set isGrouped=1 ".
                                " where classID=".(int)$classID." AND isGrouped=0 AND ".
                                                "countryID=".(int)$res[0] );
                }
                else
                {
                        foreach( $res as $key => $val )
                                _deleteRate($classID, $val);
                }
        }


        function taxGetCountSetZone( $classID, $countryID )
        {
                $res = array();
                $zones = array();
                 $q = db_query("select zoneID, zone_name from ".ZONES_TABLE.
                                " where countryID=".(int)$countryID);
                while( $row=db_fetch_row($q) ) $zones[] = $row;
                $count = 0;

                foreach( $zones as $zone )
                {
                        $q1=db_query("select classID, zoneID, value from ".
                                TAX_RATES_ZONES_TABLE.
                                " where classID=".(int)$classID." AND zoneID=".(int)$zone["zoneID"] );
                        if ( $resItem=db_fetch_row($q1) ) $count ++;
                }
                return $count;
        }


        function taxGetCountZones( $countryID )
        {
                $q = db_query("select count(*) from ".ZONES_TABLE.
                        " where countryID=".(int)$countryID );
                $row = db_fetch_row($q);
                return $row[0];
        }


        function taxGetZoneRates( $classID, $countryID )
        {
                $res = array();
                $zones = array();
                 $q = db_query("select zoneID, zone_name from ".ZONES_TABLE.
                                " where countryID=".(int)$countryID);
                while( $row=db_fetch_row($q) )
                        $zones[] = $row;

                foreach( $zones as $zone )
                {
                        $q1=db_query("select classID, zoneID, value from ".
                                TAX_RATES_ZONES_TABLE.
                                " where classID=".(int)$classID." AND zoneID=".(int)$zone["zoneID"].
                                " AND isGrouped=0"  );
                        if ( $resItem=db_fetch_row($q1) )
                        {
                                $resItem["zone_name"] = $zone["zone_name"];
                                $resItem["countryID"] = $countryID;
                                $res[] = $resItem;
                        }
                }


                $q1=db_query("select classID, zoneID, value from ".
                        TAX_RATES_ZONES_TABLE." where classID=".(int)$classID." AND isGrouped=1" );
                if ( $resItem=db_fetch_row($q1) )
                {
                        $resItem["zone_name"]         = "";
                        $resItem["zoneID"]                 = 0;
                        $resItem["countryID"]        = $countryID;
                        $res[] = $resItem;
                }

                return $res;
        }

        function taxGetZoneByClassIDCountryID_ToSetRate( $classID, $countryID )
        {
                $res = array();
                $q = db_query("select zoneID, zone_name from ".ZONES_TABLE.
                                " where countryID=".(int)$countryID);
                while( $zone=db_fetch_row($q) )
                {
                        $q1 = db_query("select * from ".TAX_RATES_ZONES_TABLE.
                                " where zoneID=".(int)$zone["zoneID"].
                                " AND classID=".(int)$classID);
                        if ( !($row=db_fetch_row($q1)) ) $res[] = $zone;
                }
                return $res;
        }

        function taxAddZoneRate( $classID, $countryID, $zoneID, $value )
        {
                if ( $zoneID == 0 )
                {
                        $q = db_query("select zoneID, zone_name from ".ZONES_TABLE.
                                " where countryID=".(int)$countryID);
                        while( $zone=db_fetch_row($q) )
                        {
                                $q1 = db_query("select * from ".TAX_RATES_ZONES_TABLE.
                                                " where zoneID=".(int)$zone["zoneID"].
                                                " AND classID=".(int)$classID);
                                if ( !($row=db_fetch_row($q1)) )
                                {
                                        db_query("insert into ".TAX_RATES_ZONES_TABLE.
                                                 "( classID, zoneID, value, isGrouped ) ".
                                                 "values( ".(int)$classID.", ".(int)$zone["zoneID"].", ".(float)$value.", 1 ) " );
                                }
                        }
                }
                else
                        db_query( "insert into ".TAX_RATES_ZONES_TABLE.
                                  " ( classID, zoneID, value, isGrouped ) ".
                                  " values( ".(int)$classID.", ".(int)$zoneID.", ".(float)$value.", 0 ) " );
        }


        function taxUpdateZoneRate( $classID, $zoneID, $value )
        {
                if ( $zoneID == 0 )
                        db_query( "update ".TAX_RATES_ZONES_TABLE.
                                " set value=".(float)$value.
                                " where classID=".(int)$classID." AND isGrouped=1" );
                else
                        db_query( "update ".TAX_RATES_ZONES_TABLE.
                                " set value=".(float)$value.
                                " where classID=".(int)$classID." AND zoneID=".(int)$zoneID.
                                " AND isGrouped=0" );
        }

        function taxDeleteZoneRate( $classID, $zoneID )
        {
                if ( $zoneID==0 )
                        db_query("delete from ".TAX_RATES_ZONES_TABLE.
                                " where classID=".(int)$classID." AND isGrouped=1");
                else
                {
                        $q_count = db_query("select count(*) from ".TAX_RATES_ZONES_TABLE.
                                " where classID=".(int)$classID." AND isGrouped=1");
                        $count = db_fetch_row( $q_count );
                        $count = $count[0];

                        if ( $count == 0 )
                                db_query("delete from ".TAX_RATES_ZONES_TABLE.
                                        " where classID=".(int)$classID." AND zoneID=".(int)$zoneID);
                        else
                                db_query( "update ".TAX_RATES_ZONES_TABLE.
                                        " set isGrouped=1 ".
                                        " where classID=".(int)$classID." AND zoneID=".(int)$zoneID);
                }
        }


        function taxGetZipRates( $classID, $countryID )
        {
                $q = db_query( "select tax_zipID, classID, countryID, zip_template, value from ".TAX_ZIP_TABLE.
                        " where classID=".(int)$classID." AND countryID=".(int)$countryID);
                $data = array();
                while( $row=db_fetch_row($q) ) $data[] = $row;
                return $data;
        }

        function taxAddZipRate( $classID, $countryID, $zip_template, $rate )
        {
                $rate = (float)$rate;
                db_query(
                                "insert into ".TAX_ZIP_TABLE.
                                " ( classID, countryID, zip_template, value ) ".
                                " values( ".(int)$classID.", ".(int)$countryID.", '".xEscSQL($zip_template)."', ".$rate." ) ");
        }

        function taxUpdateZipRate( $tax_zipID, $zip_template, $rate )
        {

                $rate = (float)$rate;
                db_query(
                        "update ".TAX_ZIP_TABLE.
                        " set ".
                        " zip_template='".xEscSQL($zip_template)."', ".
                        " value=".$rate.
                        " where tax_zipID=".(int)$tax_zipID);
        }

        function taxDeleteZipRate( $tax_zipID )
        {
                db_query( "delete from ".TAX_ZIP_TABLE.
                        " where tax_zipID=".(int)$tax_zipID);
        }


        function _testTemplateZip( $zip_template, $zip )
        {
                if ( strlen($zip_template)==strlen($zip) )
                {
                        $testResult = true;
                        $starCounter=0;
                        for( $i=0; $i<strlen($zip); $i++ )
                        {
                                if ( ($zip[$i]==$zip_template[$i]) ||
                                                        $zip_template[$i]=='*' )
                                {
                                        if ( $zip_template[$i]=='*' )
                                                $starCounter++;
                                        continue;
                                }
                                else
                                {
                                        $testResult = false;
                                        break;
                                }
                        }
                        if ( $testResult )
                                return $starCounter;
                        else
                                return false;
                }
                else
                        return false;
        }


        function _getBestZipRate( $classID, $countryID, $zip )
        {
                $q=db_query( "select tax_zipID, zip_template, value from ".
                                TAX_ZIP_TABLE." where classID=".(int)$classID." AND countryID=".(int)$countryID);
                $testZipTemplateArray = array();
                while( $row=db_fetch_row($q) )
                {
                        $res = _testTemplateZip( $row["zip_template"], $zip );
                        if ( !is_bool($res) )
                                $testZipTemplateArray[] = array(
                                                        "starCounter" => $res,
                                                        "rate" => $row["value"] );
                }

                if ( count($testZipTemplateArray) == 0 )
                        return null;

                // define "starCounter" minimum
                $starCounterMinIndex = 0;
                for( $i=0; $i < count($testZipTemplateArray); $i++ )
                        if ( $testZipTemplateArray[$starCounterMinIndex]["starCounter"] >
                                        $testZipTemplateArray[$i]["starCounter"] )
                                $starCounterMinIndex = $i;

                return (float)$testZipTemplateArray[$starCounterMinIndex]["rate"];
        }



        // *****************************************************************************
        // Purpose   calculate tax by addresses and productID
        // Inputs    $productID - product ID
        //                         $shippingAddressID - shipping address ID
        //                         $billingAddress        - billing address ID
        // Remarks
        // Returns
        function taxCalculateTax( $productID, $shippingAddressID, $billingAddressID )
        {
                $shippingAddress        = regGetAddress( $shippingAddressID );
                $billingAddress                = regGetAddress( $billingAddressID );
                return taxCalculateTax2( $productID, $shippingAddress, $billingAddress );
        }



        // *****************************************************************************
        // Purpose   calculate tax by addresses and productID
        // Inputs    $productID - product ID
        //                        $shippingAddress - array of
        //                                "countryID"
        //                                "zoneID"
        //                                "zip"
        //                        $billingAddress - array of
        //                                "countryID"
        //                                "zoneID"
        //                                "zip"
        // Remarks
        // Returns
        function taxCalculateTax2( $productID, $shippingAddress, $billingAddress )
        {
                $productID = (int) $productID;

                if ( trim($productID) == "" || $productID == null )
                        return 0;

                // get tax class
                $q = db_query("select classID from ".PRODUCTS_TABLE.
                        " where productID=".(int)$productID);
                $row = db_fetch_row( $q );
                $taxClassID = $row["classID"];

                if ( $taxClassID == null )
                        return 0;

                return taxCalculateTaxByClass2( $taxClassID, $shippingAddress, $billingAddress );
        }


        // *****************************************************************************
        // Purpose
        // Inputs    $taxClassID - tax class ID
        //                        $shippingAddress - array of
        //                                "countryID"
        //                                "zoneID"
        //                                "zip"
        //                        $billingAddress - array of
        //                                "countryID"
        //                                "zoneID"
        //                                "zip"
        // Remarks
        // Returns
        function taxCalculateTaxByClass( $taxClassID, $shippingAddressID, $billingAddressID )
        {
                $shippingAddress        = regGetAddress( $shippingAddressID );
                $billingAddress                = regGetAddress( $billingAddressID );
                return taxCalculateTaxByClass2( $taxClassID, $shippingAddress, $billingAddress );
        }


        // *****************************************************************************
        // Purpose
        // Inputs    $taxClassID - tax class ID
        //                        $shippingAddress - array of
        //                                "countryID"
        //                                "zoneID"
        //                                "zip"
        //                        $billingAddress - array of
        //                                "countryID"
        //                                "zoneID"
        //                                "zip"
        // Remarks
        // Returns
        function taxCalculateTaxByClass2( $taxClassID, $shippingAddress, $billingAddress )
        {
                $class = taxGetTaxClassById( $taxClassID );

                // get address
                if ( $class["address_type"] == 0 )
                {
                        $address = $shippingAddress;
                }
                else
                {
                        $address = $billingAddress;
                }

                if  ( $address == null )
                        return 0;

                // get tax rate
                $address["countryID"] = (int) $address["countryID"];

                $q = db_query( "select value, isByZone from  ".TAX_RATES_TABLE.
                        " where classID=".(int)$taxClassID." AND countryID=".(int)$address["countryID"]  );
                if ( $row=db_fetch_row($q) )
                {
                        $value                = $row["value"];
                        $isByZone        = $row["isByZone"];
                }
                else
                {
                        $q = db_query( "select value, isByZone from ".TAX_RATES_TABLE.
                                " where isGrouped=1 AND classID=".(int)$taxClassID);
                        if ( $row=db_fetch_row($q) )
                        {
                                $value                = $row["value"];
                                $isByZone        = $row["isByZone"];
                        }
                        else
                                return 0;
                }

                if ( $isByZone == 0 )
                        return $value;
                else
                {
                        $res = _getBestZipRate( $taxClassID, $address["countryID"], $address["zip"] );
                        if ( !is_null($res) )
                                return $res;
                        else
                        {
                                if ( is_null($address["zoneID"]) || trim($address["zoneID"]) == "" )
                                        return 0;

                                $q = db_query( "select value from ".TAX_RATES_ZONES_TABLE.
                                        " where classID=".(int)$taxClassID." AND zoneID=".(int)$address["zoneID"] );
                                if ( ($row=db_fetch_row($q)) )
                                        return $row["value"];
                                else
                                {
                                        $q = db_query("select value from ".TAX_RATES_ZONES_TABLE.
                                                " where classID=".(int)$taxClassID." AND isGrouped=1" );
                                        if ( ($row=db_fetch_row($q)) )
                                                return $row["value"];
                                        else
                                                return 0;
                                }
                        }
                }
        }

?>