<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


        //get total number of customers, orders, etc.

        $total = array();

        // --- ORDERS ---
        //excluding CANCELLED status

        $q = db_query("select count(*) from ".ORDERS_TABLE." where statusID > 1");
        $r = db_fetch_row($q);
        $total["orders"] = $r[0];

        $q = db_query("select CID from ".CURRENCY_TYPES_TABLE." where currency_value=1 LIMIT 0,1");
        $r = db_fetch_row($q);
        $currtransform = $r[0];
        //get total revenue
        $q = db_query("select order_amount from ".ORDERS_TABLE." where statusID > 1");
        $revenue = 0;
        while($r = db_fetch_row($q)) $revenue += $r[0];

        $total["revenue"] = show_price( $revenue, $currtransform );

        //pending orders
        if ((int)CONF_NEW_ORDER_STATUS)
        {
                $q = db_query("select count(*) from ".ORDERS_TABLE." where statusID = ".(int)CONF_NEW_ORDER_STATUS);
                $r = db_fetch_row($q);
                $total["orders_pending"] = $r[0];
        }

        //orders today
        $curr_time = time();
        $y = strftime("%Y", $curr_time);
        $m = strftime("%m", $curr_time);
        $d = strftime("%d", $curr_time);

        $TODAY = $y."-".$m."-".$d." 00:00:00";
        $q = db_query("select order_amount, currency_value from ".ORDERS_TABLE." where statusID > 1 and order_time > '".$TODAY."'");
        $n = 0;
        $a = 0;
        while($r = db_fetch_row($q))
        {
                $a += $r[0];
                $n++;
        }
        $total["orders_today"] = $n;
        $total["revenue_today"] = show_price($a, $currtransform );

        $YESTERDAY = strftime( "%Y-%m-%d 00:00:00", time()-24*3600 );
        $q = db_query("select order_amount, currency_value from ".ORDERS_TABLE." where statusID > 1 and order_time > '".$YESTERDAY."' and order_time < '".$TODAY."'");
        $n = 0;
        $a = 0;
        while($r = db_fetch_row($q))
        {
                $a += $r[0];
                $n++;
        }
        $total["orders_yesterday"] = $n;
        $total["revenue_yesterday"] = show_price($a, $currtransform );

        $THISMONTH = "$y-$m-01 00:00:00";
        $q = db_query("select order_amount, currency_value from ".ORDERS_TABLE." where statusID > 1 and order_time > '".$THISMONTH."'");
        $n = 0;
        $a = 0;
        while($r = db_fetch_row($q))
        {
                $a += $r[0];
                $n++;
        }
        $total["orders_thismonth"] = $n;
        $total["revenue_thismonth"] = show_price($a, $currtransform );


        // --- PRODUCTS ---
        $q = db_query("select count(*) from ".PRODUCTS_TABLE);
        $r = db_fetch_row($q);
        $total["products"] = $r[0];
        $q = db_query("select count(*) from ".PRODUCTS_TABLE." where Enabled=1");
        $r = db_fetch_row($q);
        $total["products_enabled"] = $r[0];
        /*if (CONF_CHECKSTOCK)
        {
                $q = db_query("select count(*) from ".PRODUCTS_TABLE." where Enabled=1 and in_stock <= 0");
                $r = db_fetch_row($q);
                $total["products_outofstock"] = $r[0];
        }*/

        $q = db_query("select todayp, todayv, allp, allv from ".COUNTER_TABLE." WHERE tbid=1");
        $n = db_fetch_row($q);

        $total["count_stat1"] = $n[0];
        $total["count_stat2"] = $n[1];
        $total["count_stat3"] = $n[2];
        $total["count_stat4"] = $n[3];

        // --- CATEGORIES ---
        $q = db_query("select count(*) from ".CATEGORIES_TABLE);
        $r = db_fetch_row($q);
        $total["categories"] = $r[0]-1;

        // --- CUSTOMERS ---
        $q = db_query("select count(*) from ".CUSTOMERS_TABLE);
        $r = db_fetch_row($q);
        $total["customers"] = $r[0];

        //cust groups
        $q = db_query("select count(*) from ".CUSTGROUPS_TABLE);
        $r = db_fetch_row($q);
        $total["customer_groups"] = $r[0];

        //newsletter subscribers
        subscrGetAllSubscriber(null, $r);
        $total["newsletter_subscribers"] = $r;

        //ETC
        $q = db_query("select count(*) from ".CURRENCY_TYPES_TABLE);
        $r = db_fetch_row($q);
        $total["currency_types"] = $r[0];
        $q = db_query("select count(*) from ".PAYMENT_TYPES_TABLE." where Enabled=1");
        $r = db_fetch_row($q);
        $total["payment_types"] = $r[0];
        $q = db_query("select count(*) from ".SHIPPING_METHODS_TABLE." where Enabled=1");
        $r = db_fetch_row($q);
        $total["shipping_types"] = $r[0];
        $q = db_query("select count(*) from ".AUX_PAGES_TABLE);
        $r = db_fetch_row($q);
        $total["aux_pages"] = $r[0];
        $q = db_query("select count(*) from ".COUNTRIES_TABLE);
        $r = db_fetch_row($q);
        $total["countries"] = $r[0];
        $q = db_query("select count(*) from ".ZONES_TABLE);
        $r = db_fetch_row($q);
        $total["zones"] = $r[0];

        $q = db_query("select count(*) from ".DISCUSSIONS_TABLE);
        $r = db_fetch_row($q);
        $total["discussion_posts"] = $r[0];

        $smarty->assign("totals", $total);

?>