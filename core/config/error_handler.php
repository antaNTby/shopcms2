<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

function error_reporting_log($error_num, $error_var, $error_file, $error_line) {
    $error_write = false;
    switch ( $error_num ) {
        case 1 :
            $error_desc = "ERROR";
            $error_write = true;
            break;

        case 2 :
            $error_desc = "WARNING";
            $error_write = true;
            break;

        case 4 :
            $error_desc = "PARSE";
            $error_write = true;
            break;

        case 8 :
            $error_desc = "NOTICE";
            $error_write = false;
            break;
    }
    if ( $error_write ) {
        if ( strpos($error_file, "mysql.php") == false && strpos($error_file, "smarty") == false ) {
            $out = $error_desc.": ".$error_var."\nLine: ".$error_line."\nFile: ".$error_file."\nLink: ".$_SERVER["REQUEST_URI"]."\nDate: ".date("d.m.y - H:i:s")."\nDump:\n";
            ob_start();
            var_dump($_GET);
            var_dump($_POST);
            $tmpa = ob_get_contents();
            ob_end_clean();
            $out .= $tmpa;
            db_query("insert into ".ERROR_LOG_TABLE." (errors, tstamp) VALUES ('".xEscSQL(ToText($out))."', NOW())");
            $ecount = db_fetch_row(db_query("select count(*) from ".ERROR_LOG_TABLE));
            $ecount = $ecount[0] - 50;
            if ( $ecount > 0 ) db_query("delete from ".ERROR_LOG_TABLE." ORDER BY tstamp ASC LIMIT ".$ecount);
        }
    }
}

//set_error_handler('error_reporting_log');
?>