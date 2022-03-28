<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        if ( !strcmp($sub, "coming") )
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(27,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {
        if(isset($_GET["clear"]) ) db_query("update ".COUNTER_TABLE." set todayp=0, todayv=0, allp=0, allv=0, allieb=0, allmozb=0, allopb=0, allozb=0, allrusl=0, allenl=0, allozl=0, allwins=0, alllins=0, allmacs=0, allozs=0 WHERE tbid=1");
        $q = db_query("select todayp, todayv, allp, allv, allieb, allmozb, allopb, allozb, allrusl, allenl, allozl, allwins, alllins, allmacs, allozs from ".COUNTER_TABLE." WHERE tbid=1");
        $n = db_fetch_row($q);

        $bncount = $n[4]+$n[6]+$n[5]+$n[7];
        if($bncount==0)$bncount=1;
        $count_all_ie_c = ceil($n[4]/$bncount*100);
        $count_all_moz_c = ceil($n[5]/$bncount*100);
        $count_all_opera_c = ceil($n[6]/$bncount*100);
        $count_all_nobr_c = ceil($n[7]/$bncount*100);
        $count_all_ie_c2 = round($n[4]/$bncount*100, 2);
        $count_all_moz_c2 = round($n[5]/$bncount*100, 2);
        $count_all_opera_c2 = round($n[6]/$bncount*100, 2);
        $count_all_nobr_c2 = round($n[7]/$bncount*100, 2);

        $lncount = $n[8]+$n[9]+$n[10];
        if($lncount==0)$lncount=1;
        $count_all_ru_c = ceil($n[8]/$lncount*100);
        $count_all_en_c = ceil($n[9]/$lncount*100);
        $count_all_nolng_c = ceil($n[10]/$lncount*100);
        $count_all_ru_c2 = round($n[8]/$lncount*100, 2);
        $count_all_en_c2 = round($n[9]/$lncount*100, 2);
        $count_all_nolng_c2 = round($n[10]/$lncount*100, 2);

        $oncount = $n[11]+$n[12]+$n[13]+$n[14];
        if($oncount==0)$oncount=1;
        $count_all_win_c = ceil($n[11]/$oncount*100);
        $count_all_lin_c = ceil($n[12]/$oncount*100);
        $count_all_mac_c = ceil($n[13]/$oncount*100);
        $count_all_nowin_c = ceil($n[14]/$oncount*100);
        $count_all_win_c2 = round($n[11]/$oncount*100, 2);
        $count_all_lin_c2 = round($n[12]/$oncount*100, 2);
        $count_all_mac_c2 = round($n[13]/$oncount*100, 2);
        $count_all_nowin_c2 = round($n[14]/$oncount*100, 2);

        $smarty->assign( "count_all_ank", $n[2] );
        $smarty->assign( "count_all_ie", $n[4]);
        $smarty->assign( "count_all_opera", $n[6]);
        $smarty->assign( "count_all_moz", $n[5]);
        $smarty->assign( "count_all_nobr", $n[7]);
        $smarty->assign( "count_all_ie_c", $count_all_ie_c);
        $smarty->assign( "count_all_opera_c", $count_all_opera_c);
        $smarty->assign( "count_all_moz_c", $count_all_moz_c);
        $smarty->assign( "count_all_nobr_c", $count_all_nobr_c);
        $smarty->assign( "count_all_ie_c2", $count_all_ie_c2);
        $smarty->assign( "count_all_opera_c2", $count_all_opera_c2);
        $smarty->assign( "count_all_moz_c2", $count_all_moz_c2);
        $smarty->assign( "count_all_nobr_c2", $count_all_nobr_c2);

        $smarty->assign( "count_all_ru", $n[8]);
        $smarty->assign( "count_all_en", $n[9]);
        $smarty->assign( "count_all_nolng", $n[10]);
        $smarty->assign( "count_all_ru_c", $count_all_ru_c);
        $smarty->assign( "count_all_en_c", $count_all_en_c);
        $smarty->assign( "count_all_nolng_c", $count_all_nolng_c);
        $smarty->assign( "count_all_ru_c2", $count_all_ru_c2);
        $smarty->assign( "count_all_en_c2", $count_all_en_c2);
        $smarty->assign( "count_all_nolng_c2", $count_all_nolng_c2);

        $smarty->assign( "count_all_win", $n[11]);
        $smarty->assign( "count_all_lin", $n[12]);
        $smarty->assign( "count_all_mac", $n[13]);
        $smarty->assign( "count_all_nowin", $n[14]);
        $smarty->assign( "count_all_win_c", $count_all_win_c);
        $smarty->assign( "count_all_lin_c", $count_all_lin_c);
        $smarty->assign( "count_all_mac_c", $count_all_mac_c);
        $smarty->assign( "count_all_nowin_c", $count_all_nowin_c);
        $smarty->assign( "count_all_win_c2", $count_all_win_c2);
        $smarty->assign( "count_all_lin_c2", $count_all_lin_c2);
        $smarty->assign( "count_all_mac_c2", $count_all_mac_c2);
        $smarty->assign( "count_all_nowin_c2", $count_all_nowin_c2);

        $smarty->assign( "count_all", $n[3] );
        $smarty->assign( "count_today", $n[1] );
        $smarty->assign( "count_today_ank", $n[0] );
        $smarty->assign( "admin_sub_dpt", "reports_coming.tpl.html" );
        }
        }
?>