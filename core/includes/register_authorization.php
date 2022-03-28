<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

  if (CONF_USER_SYSTEM > 0){

        if ( isset($register_authorization) )
        {
                if ( !cartCheckMinOrderAmount() ) Redirect( "index.php?shopping_cart=yes" );


                if ( isset($_GET["remind_password"]) )
                        $smarty->assign("remind_password" , 1);

                if ( isset($_POST["user_login"])  )
                {
                        $smarty->hassign( "user_login", $_POST["user_login"] );
                        $smarty->assign( "login_to_remind_password", $_POST["user_login"] );
                }

                if ( isset($_POST["remind_password"]) ){

                        $Reminded = regSendPasswordToUser( $_POST["login_to_remind_password"], $smarty_mail )?'yes':'no';
                        if($Reminded=='no') $smarty->hassign('remind_user_login', $_POST["login_to_remind_password"]);
                        $smarty->assign( "password_sent_notifycation",  $Reminded);
                }
                if ( isset($_POST["login"]) )
                {
                        if ( trim($_POST["user_login"]) != "" )
                        {
                                $cartIsEmpty = cartCartIsEmpty($_POST["user_login"]);
                                if ( regAuthenticate( $_POST["user_login"], $_POST["user_pw"] ) )
                                {
                                        if ( $cartIsEmpty )
                                                Redirect( "index.php?order2_shipping=yes&shippingAddressID=".
                                                        regGetDefaultAddressIDByLogin($_SESSION["log"]) );
                                        else
                                                Redirect( "index.php?shopping_cart=yes&make_more_exact_cart_content=yes" );
                                }
                                else $smarty->assign("remind_password" , 1);
                        }
                }

                $smarty->assign("check_order", "yes");
                $smarty->assign("main_content_template", "register_authorization.tpl.html");
        }
  }else{
        if ( isset($register_authorization) ) Redirect( "index.php?quick_register=yes" );
  }
?>