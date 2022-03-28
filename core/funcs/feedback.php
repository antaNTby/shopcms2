<?php
    #####################################
    # ShopCMS: Скрипт интернет-магазина
    # Copyright (c) 2008 by ADGroup
    # http://shopcms.ru
    #####################################
    $phone = null;
    if (isset($_GET["feedback"]) || isset($_POST["feedback"]))
    {
        //$categoryID = 1;
        if (isset($_POST["feedback"]))
        {
            //$customer_name = $_POST["customer_name"];
            $customer_email = $_POST["customer_email"];
            $phone = $_POST["phone"];
            //$message_subject = $_POST["message_subject"];
            $message_text = $_POST["message_text"];
        }
        else
        {
            //$customer_name = "";
            $customer_email = "";
            //$message_subject = "";
            $message_text = "";
        }

        //validate input data
        /*if (trim($customer_email)!="" && trim($message_text)!="" && preg_match("/^[_\.a-z0-9-]{1,20}@(([a-z0-9-]+\.)+(com|net|org|mil|edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/is",$customer_email))*/
        if (trim($message_text)!="" && trim($customer_email)!="")
        {
            if(CONF_ENABLE_CONFIRMATION_CODE){
                $error_f = 1;
                if(!$_POST['fConfirmationCode'] || !isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] !==  $_POST['fConfirmationCode']) {
                    $error_f = 2;
                    $smarty->assign("error",$error_f);
                }
                unset($_SESSION['captcha_keystring']);
                if($error_f == 1){
                    if (xMailTxtHTML(CONF_GENERAL_EMAIL, "", "Тел.:".$phone."\n".$message_text, $customer_email, "")){
                        Redirect("index.php?feedback=1&sent=1");
                    }else{
                        $smarty->assign("error",3);
                    }
                }
            }else{
                if (xMailTxtHTML(CONF_GENERAL_EMAIL, "", "Тел.:".$phone."\n".$message_text, $customer_email, "")){
                    Redirect("index.php?feedback=1&sent=1");
                }else{
                    $smarty->assign("error",3);
                }
            }
        }
        else if (isset($_POST["feedback"])) $smarty->assign("error",1);

            //extract input to Smarty
            //$smarty->hassign("customer_name",$customer_name);
            $smarty->hassign("customer_email",$customer_email);
        $smarty->hassign("phone",$phone);
        //$smarty->hassign("message_subject",$message_subject);
        $smarty->hassign("message_text",$message_text);

        if (isset($_GET["sent"])) $smarty->assign("sent",1);

        $smarty->assign("main_content_template", "feedback.tpl.html");
    }

?>