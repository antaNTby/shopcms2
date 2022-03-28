<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        if (!strcmp($sub, "customer_log"))
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(25,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {

                if ( isset($_POST["clear"]) )
                {

                        stClearCustomerLogReport();
                }

                $customer_log_report = stGetCustomerLogReport();

                $smarty->assign("customer_log_report", $customer_log_report );

                //set sub-department template
                $smarty->assign("admin_sub_dpt", "reports_customer_log.tpl.html");
        }
        }
?>