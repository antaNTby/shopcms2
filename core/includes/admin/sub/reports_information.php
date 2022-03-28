<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        function php_gd() {
        ob_start();
        phpinfo(8);
        $module_info = ob_get_contents();
        ob_end_clean();
        if (preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info, $matches)) {
                $gdversion = $matches[1];
        } else {
                $gdversion = 0;
        }
        return $gdversion;
        }

        function db_version() {
        list($dbversion) = db_fetch_row(db_query("SELECT VERSION()"));
        return $dbversion;
        }

        if ( !strcmp($sub, "information") )
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(26,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {

        $postsize = ini_get('upload_max_filesize');
        $executiontime = ini_get('max_execution_time');
        $registerglobals = ((ini_get('register_globals')==1) ? "On" : "Off");
        $safemodel = ((ini_get('safe_mode') == 1 || strtolower(ini_get('safe_mode'))=='on') ? "On" : "Off");
        $magicquotesgpc = ((ini_get('magic_quotes_gpc')==1 || strtolower(ini_get('magic_quotes_gpc'))=='on') ? "On" : "Off");
        $fileup = ((ini_get('file_uploads')==1 || strtolower(ini_get('file_uploads'))=='on') ? "On" : "Off");

        $smarty->assign("postsize", $postsize);
        $smarty->assign("executiontime", $executiontime);
        $smarty->assign("registerglobals", $registerglobals);
        $smarty->assign("safemodel", $safemodel);
        $smarty->assign("magicquotesgpc", $magicquotesgpc);
        $smarty->assign("fileup", $fileup);

        $rd = db_version();
        $smarty->assign("mver", $rd);
        $phpver = phpversion();
        $smarty->assign("pver", $phpver);
        if (! extension_loaded('gd')) {
        $gd_ver = 0;
        }else{
        $gd_ver = php_gd();
        }

        if ($gd_ver == 0){
        $smarty->assign("gd_ver",ADMIN_NOGD);
        }else
        {
        $smarty->assign("gd_ver",$gd_ver);
        }
        $qzz = db_query("select count(*) from ".ORDERS_TABLE." WHERE statusID!=0 ");
        $nzz = db_fetch_row($qzz);
        $smarty->assign("orders_count", $nzz[0]);
        $vers = db_query("select value from ".SYSTEM_TABLE." WHERE varName='version_number' ");
        $versr = db_fetch_row($vers);
        $smarty->assign("version_id", $versr[0]);
        $qno = db_query("select count(*) from ".ORDERS_TABLE." WHERE statusID=".(int)CONF_COMPLETED_ORDER_STATUS);
        $nno = db_fetch_row($qno);
        $qpr = db_query("select count(*) from ".PRODUCTS_TABLE);
        $npr = db_fetch_row($qpr);
        $smarty->assign("oll_prod", $npr[0]);
        $qprno = db_query("select count(*) from ".PRODUCTS_TABLE." WHERE enabled=0");
        $nprno = db_fetch_row($qprno);
        $smarty->assign("oll_prod_no", $nprno[0]);
        $qcat = db_query("select count(*) from ".CATEGORIES_TABLE);
        $ncat = db_fetch_row($qcat);
        $pl = db_query("select value from ".SYSTEM_TABLE." where varName='version_number'");
        $vall = db_fetch_row($pl);
        $value = $vall["value"];
        $smarty->assign("valuel", $value);
        $smarty->assign("oll_cat", $ncat[0]-1);
        $smarty->assign("orders_count_no", $nno[0]);
        $smarty->assign("mver", $rd);
        $smarty->assign("pver", $phpver);
        $smarty->assign("gd_ver", $gd_ver);
        $smarty->assign( "admin_sub_dpt", "reports_information.tpl.html" );
        }
        }
?>