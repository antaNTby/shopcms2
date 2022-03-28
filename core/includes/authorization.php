<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        //forgot password page

        if (isset($_GET["logout"])) //user logout
        {
                unset($_SESSION["log"]);
                unset($_SESSION["pass"]);
                session_unregister("log"); //calling session_unregister() is required since unset() may not work on some systems
                session_unregister("pass");
                RedirectJavaScript( "index.php" );
        }
        elseif (isset($_POST["enter"]) && !isset($_SESSION["log"])) //user login
        {

                if ( regAuthenticate($_POST["user_login"],$_POST["user_pw"]) )
                {
                        if (!isset($_POST["order"]))
                        {
                                if (in_array(100,$relaccess)) Redirect( ADMIN_FILE );
                                else Redirect( "index.php?user_details=yes" );                                
                        }
                } else $wrongLoginOrPw = 1;
        }


        if (isset($_POST["forgotpw"])) //forgot password?
        {
                $smarty->hassign("forgotpw", $_POST["forgotpw"]);
                $res = regSendPasswordToUser( $_POST["forgotpw"], $smarty_mail );
                if ( $res )
                        $smarty->assign("login_was_found", 1);
                else
                        $smarty->assign("login_wasnt_found", 1);
                $show_password_form = 1;
        }
        //wrong password page
        if (isset($_GET["logging"]) || isset($show_password_form) || isset($wrongLoginOrPw))
        {
                if (isset($wrongLoginOrPw)) $smarty->assign("wrongLoginOrPw", 1);
                $smarty->assign("main_content_template", "password.tpl.html");
        }

?>