<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        // registration form

        if (isset($_GET["user_details"]) && isset($_SESSION["log"])) //show user's account
        {
                $cust_password           = null;
                $Email                   = null;
                $first_name              = null;
                $last_name               = null;
                $subscribed4news         = null;
                $additional_field_values = null;
                regGetContactInfo( $_SESSION["log"], $cust_password, $Email, $first_name,
                                $last_name, $subscribed4news, $additional_field_values );
                $smarty->assign("additional_field_values", $additional_field_values );
                $smarty->assign("first_name", $first_name );
                $smarty->assign("last_name", $last_name );
                $smarty->assign("Email", $Email );
                $smarty->assign("login", $_SESSION["log"] );


                $customerID = regGetIdByLogin( $_SESSION["log"] );
                $custgroup = GetCustomerGroupByCustomerId( $customerID );
                $smarty->assign( "custgroup_name", $custgroup["custgroup_name"] );

                $smarty->assign('affiliate_customers', affp_getCustomersNum($customerID));

                if ( CONF_DISCOUNT_TYPE == '2' )
                        if ( $custgroup["custgroup_discount"] > 0 )
                                $smarty->assign( "discount", $custgroup["custgroup_discount"] );

                if ( CONF_DISCOUNT_TYPE == '4' || CONF_DISCOUNT_TYPE == '5' )
                        if ( $custgroup["custgroup_discount"] > 0 )
                                $smarty->assign( "min_discount", $custgroup["custgroup_discount"] );

                $defaultAddressID = regGetDefaultAddressIDByLogin( $_SESSION["log"] );
                $addressStr = regGetAddressStr( $defaultAddressID );
                $smarty->assign("addressStr", $addressStr );

                $smarty->assign("visits_count", stGetVisitsCount( $_SESSION["log"] ) );
                $smarty->assign("status_distribution", ordGetDistributionByStatuses( $_SESSION["log"] ) );
                $smarty->assign("main_content_template", "user_account.tpl.html");
        }

?>