<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


        if (!strcmp($sub, "countries"))
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(30,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {

                function _getUrlToDelete()
                {
                        $res = ADMIN_FILE."?dpt=conf&sub=countries";
                        if ( isset($_GET["offset"]) )
                                $res .= "&offset=".$_GET["offset"];
                        if ( isset($_GET["show_all"]) )
                                $res .= "&show_all=".$_GET["show_all"];
                        return $res;
                }

                function _getUrlToSubmit()
                {
                        return _getUrlToDelete();
                }


                if ( isset($_GET["delete"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect( _getUrlToDelete()."&safemode=yes" );
                        }
                        cnDeleteCountry( $_GET["delete"] );
                        Redirect( _getUrlToDelete() );
                }

                if ( isset($_POST["save_countries"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect( _getUrlToSubmit()."&safemode=yes" );
                        }

                        // add new manufacturer
                        $name=$_POST["new_country_name"];
                        $iso2=$_POST["new_country_iso2"];
                        $iso3=$_POST["new_country_iso3"];
                        if ( $name != "" )
                                cnAddCountry($name, $iso2, $iso3 );

                        // update manufacturers
                        $data = ScanPostVariableWithId( array("country_name",
                                        "country_iso2", "country_iso3" ) );

                        // update existing pictures
                        foreach( $data as $key => $val )
                        {
                                cnUpdateCountry($key,
                                        $data[$key]["country_name"],
                                        $data[$key]["country_iso2"],
                                        $data[$key]["country_iso3"] );
                        }
                        Redirect( _getUrlToSubmit() );
                }


                $callBackParam = array();
                $countries = array();

                $count = 0;
                $navigatorHtml = GetNavigatorHtml( ADMIN_FILE."?dpt=conf&sub=countries", 20,
                                'cnGetCountries', $callBackParam,
                                $countries, $offset, $count );

                if ( isset($_POST["save_countries"]) )
                        Redirect( _getUrlToSubmit() );

                $smarty->hassign("urlToDelete", _getUrlToDelete() );
                $smarty->hassign("urlToSubmit", _getUrlToSubmit() );

                $smarty->assign("countries",$countries);
                $smarty->assign("navigator", $navigatorHtml );

                //set sub-department template
                $smarty->assign("admin_sub_dpt", "conf_countries.tpl.html");
        }
        }
?>