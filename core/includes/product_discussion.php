<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################
        // product discussion page

        if (isset($_POST["add_topic"]) && isset($productID)) // add post to the product discussion
        {
                if ( !prdProductExists($productID) ){
                                //product not found
                                header("HTTP/1.0 404 Not Found");
                                header("HTTP/1.1 404 Not Found");
                                header("Status: 404 Not Found");
                                die(ERROR_404_HTML);
                }

                if(CONF_ENABLE_CONFIRMATION_CODE){
                                 $error_p = 1;
                        if(!$_POST['fConfirmationCode'] || !isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] !==  $_POST['fConfirmationCode']) {
                                 $error_p = 7;
                                 $smarty->assign("error",$error_p);
                        }
                        unset($_SESSION['captcha_keystring']);
                        if($error_p == 1){
                                 discAddDiscussion( $productID, $_POST["nick"], $_POST["topic"], $_POST["body"] );
                                 Redirect("index.php?productID=$productID&discuss=yes");
                        }
                }else{
                discAddDiscussion( $productID, $_POST["nick"], $_POST["topic"], $_POST["body"] );
                                 Redirect("index.php?productID=$productID&discuss=yes");
                }

        }

                if (isset($_POST["add_topic"]) && isset($productID)) // add data to page
                {
                        $dis_nic = $_POST["nick"];
                        $dis_subject = $_POST["topic"];
                        $dis_text = $_POST["body"];
                }
                else
                {
                        $dis_nic = "";
                        $dis_subject = "";
                        $dis_text = "";
                }

                $smarty->hassign("dis_nic",$dis_nic);
                $smarty->hassign("dis_subject",$dis_subject);
                $smarty->hassign("dis_text",$dis_text);

        if (isset($_GET["remove_topic"]) && isset($productID) && isset($_SESSION["log"])) // delete topic in the discussion
        {

        if (isset($_SESSION["log"]) && in_array(100,$relaccess)) {
                if ( !prdProductExists($productID) ){
                                //product not found
                                header("HTTP/1.0 404 Not Found");
                                header("HTTP/1.1 404 Not Found");
                                header("Status: 404 Not Found");
                                die(ERROR_404_HTML);
                }
                discDeleteDiscusion( $_GET["remove_topic"] );
                Redirect("index.php?productID=$productID&discuss=yes");
        }
        }

        if (isset($productID) && $productID>0 && (isset($_GET["discuss"]) || isset($_POST["discuss"]))) //show discussion form
        {
                if ( !prdProductExists($productID) ){
                                //product not found
                                header("HTTP/1.0 404 Not Found");
                                header("HTTP/1.1 404 Not Found");
                                header("Status: 404 Not Found");
                                die(ERROR_404_HTML);
                }

                $smarty->assign("discuss","yes");
                $smarty->assign("main_content_template", "product_discussion.tpl.html");

                $q = db_query("select name from ".PRODUCTS_TABLE." where productID=".$productID." and enabled=1");
                $a = db_fetch_row($q);
                if ($a)
                {
                        $smarty->assign("product_name", $a[0]);
                        $q = db_query("select count(*) from ".DISCUSSIONS_TABLE." WHERE productID=".$productID);
                        $cnt = db_fetch_row($q);
                        if ($cnt[0])
                        {
                                $q = db_query(
                                        "select Author, Body, add_time, DID, Topic FROM ".DISCUSSIONS_TABLE.
                                        " WHERE productID=".$productID." ORDER BY add_time DESC");
                                $result = array();
                                while ($row = db_fetch_row($q))
                                {
                                        $row["add_time"]= format_datetime( $row["add_time"] );
                                        $result[] = $row;
                                }

                                $smarty->assign("product_reviews", $result);
                        }
                        else
                        {
                                $smarty->assign("product_reviews", NULL);
                        }
                }
        }
?>