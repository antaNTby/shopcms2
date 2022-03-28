<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

  $user_agent = (!empty($_SERVER['HTTP_USER_AGENT'])) ? strtolower(htmlspecialchars((string) $_SERVER['HTTP_USER_AGENT'])) : '';
  $accept_language = (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? strtolower(htmlspecialchars((string) $_SERVER['HTTP_ACCEPT_LANGUAGE'])) : '';
  $today=date('d.m.Y',time());

  $q = db_query("select today from ".COUNTER_TABLE." WHERE tbid=1");
  $n = db_fetch_row($q);
  $date = $n[0];
  $past = time()-CONF_ONLINE_EXPIRE*60;
  $ctime = time();

  if($today!=$date)
  {
  db_query("UPDATE ".COUNTER_TABLE." SET todayp=0, todayv=0, today='".$today."' WHERE tbid=1");
  db_query("DELETE FROM ".ONLINE_TABLE);
  db_query("DELETE FROM ".SESSION_TABLE." where expire < UNIX_TIMESTAMP()");
  }

  $todayp = 0;
  $allp = 0;
  $ip = stGetCustomerIP_Address();
  if ($ip) {

    $uniqhash = md5($ip.$user_agent);
    db_query("replace into ".ONLINE_TABLE." values ('".$uniqhash."', '".$ctime."')");
    $matches = mysql_affected_rows();
    if ($matches == 1) {
    $todayp = 1;
    $allp = 1;
    }

    $allieb=0;$allmozb=0;$allopb=0;$allozb=0;$allrusl=0;$allenl=0;$allozl=0;$allwins=0;$alllins=0;$allmacs=0;$allozs=0;

        switch (true)
        {
        case (preg_match("/win/i",$user_agent)):
                $allwins = 1;
                break;
        case (preg_match("/linux/i",$user_agent)):
                $alllins = 1;
                break;
        case (preg_match("/mac/i",$user_agent)):
                $allmacs = 1;
                break;
        default:
                $allozs = 1;
                break;
        }

        switch (true)
        {
        case (preg_match("/opera/i",$user_agent)):
                $allopb = 1;
                break;
        case (preg_match("/msie/i",$user_agent)):
                $allieb = 1;
                break;
        case (preg_match("/mozilla/i",$user_agent)):
                $allmozb = 1;
                break;
        default:
                $allozb = 1;
                break;
        }

        switch (true)
        {
        case (preg_match("/ru/i",$accept_language)):
                $allrusl = 1;
                break;
        case (preg_match("/en/i",$accept_language)):
                $allenl = 1;
                break;
        default:
                $allozl = 1;
                break;
        }

        db_query("UPDATE ".COUNTER_TABLE." SET todayp=todayp+".$todayp.", todayv=todayv+1, allp=allp+".$allp.", allv=allv+1, allieb=allieb+".$allieb.", allmozb=allmozb+".$allmozb.", allopb=allopb+".$allopb.", allozb=allozb+".$allozb.", allrusl=allrusl+".$allrusl.", allenl=allenl+".$allenl.", allozl=allozl+".$allozl.", allwins=allwins+".$allwins.", alllins=alllins+".$alllins.", allmacs=allmacs+".$allmacs.", allozs=allozs+".$allozs." WHERE tbid=1");
    }

        $past = time()-CONF_ONLINE_EXPIRE*60;
        $result = db_query("select count(*) from ".ONLINE_TABLE." WHERE time > ".$past);
        $u = db_fetch_row($result);
        if (!$u[0]){ $usersonline = 1; }else{ $usersonline = $u[0];}
        $smarty->assign("online_users",$usersonline);
        $result = db_query("select todayp, todayv, allp, allv from ".COUNTER_TABLE." WHERE tbid=1");
        $u = db_fetch_row($result);
        if (!$u[0]) {$usr1 = 1; }else{ $usr1 = $u[0];}
        if (!$u[1]) {$usr2 = 1; }else{ $usr2 = $u[1];}
        if (!$u[2]) {$usr3 = 1; }else{ $usr3 = $u[2];}
        if (!$u[3]) {$usr4 = 1; }else{ $usr4 = $u[3];}
        $smarty->assign("online_usr1",$usr2);
        $smarty->assign("online_usr2",$usr1);
        $smarty->assign("online_usr3",$usr4);
        $smarty->assign("online_usr4",$usr3);
?>