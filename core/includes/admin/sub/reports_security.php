<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        if ( !strcmp($sub, "security") )
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(29,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {
        if (isset($_POST['security_error_logs_warn'])){
                     if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(ADMIN_FILE."?dpt=reports&sub=security&safemode=yes" );
                                }
                db_query("delete from ".ERROR_LOG_TABLE);
                db_query("delete from ".MYSQL_ERROR_LOG_TABLE);
        }
        $lines = array();
        $result = db_query("SELECT errors from ".ERROR_LOG_TABLE." LIMIT 0,50");
                while (list($errors) = db_fetch_row($result)) $lines[] = $errors;

        $linesql = array();
        $result = db_query("SELECT errors from ".MYSQL_ERROR_LOG_TABLE." LIMIT 0,50");
                while (list($errors) = db_fetch_row($result)) $linesql[] = $errors;

        $smarty->assign( "arrview", $lines );
        $smarty->assign( "arrviewmysql", $linesql );
        $smarty->assign( "admin_sub_dpt", "reports_security.tpl.html" );
        }
        }
?>