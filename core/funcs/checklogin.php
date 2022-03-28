<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) 2008 by ADGroup
# http://shopcms.ru
#####################################

        if (isset($_SESSION["log"])) //look for user in the database
        {
                $q = db_query("SELECT cust_password FROM ".CUSTOMERS_TABLE." WHERE Login='".$_SESSION["log"]."'") or die (db_error());
                $row = db_fetch_row($q); //found customer - check password

                if (!$row || !isset($_SESSION["pass"]) || strcmp($row[0], $_SESSION["pass"] )) //unauthorized access
                {
                        unset($_SESSION["log"]);
                        unset($_SESSION["pass"]);
                        session_unregister("log"); //calling session_unregister() is required since unset() may not work on some systems
                        session_unregister("pass");
                }

        }

        $relaccess = array("noadmin");

        if (isset($_SESSION["log"])){

        $q = db_query("select actions from ".CUSTOMERS_TABLE." WHERE Login='".$_SESSION["log"]."'");
        $n = db_fetch_row($q);
        $relaccess = explode( ":", $n[0] );
        }
?>