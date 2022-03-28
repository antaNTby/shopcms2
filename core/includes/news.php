<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


        $news_array = newsGetNewsToCustomer();
        $smarty->assign( "news_array", $news_array );
        $pre_news_array = newsGetPreNewsToCustomer();
        $smarty->assign( "pre_news_array", $pre_news_array );
        if ( isset($_POST["subscribe"]) )
        {
                $error = subscrVerifyEmailAddress($_POST["email"]);
              if ( $_POST["modesubs"] == 0 ) {
              if ( $error == "" )
                {

                        if( _subscriberIsSubscribed ( $_POST["email"] )){

                        subscrUnsubscribeSubscriberByEmail2( $_POST["email"] );
                        $smarty->assign( "un_pol", 1);

                        }else{

                        $smarty->assign( "un_pol", 2);

                        }
                }
                else
                        $smarty->assign( "error_message", $error );
              }else{
                if ( $error == "" )
                {
                        $smarty->assign( "subscribe", 1 );
                        subscrAddUnRegisteredCustomerEmail( $_POST["email"] );
                }
                else
                        $smarty->assign( "error_message", $error );
                        }

        $smarty->assign( "main_content_template", "subscribe.tpl.html" );
        }

        if ( isset($_POST["email"]) )
                $smarty->hassign( "email_to_subscribe", $_POST["email"] );
        else
                $smarty->assign( "email_to_subscribe", "Email" );

        if ( isset($_GET["news"]) ) $smarty->assign( "main_content_template", "show_news.tpl.html" );
    
        if ( isset($_GET["fullnews"]) ){
        
	    $fullnews_array = newsGetFullNewsToCustomer($_GET["fullnews"]);

	    if ( $fullnews_array )
                {
                        $smarty->assign( "news_full_array", $fullnews_array );
                        $smarty->assign( "main_content_template", "show_full_news.tpl.html" );
                }
                else
                {
                        header("HTTP/1.0 404 Not Found");
                        header("HTTP/1.1 404 Not Found");
                        header("Status: 404 Not Found");
                        die(ERROR_404_HTML);
                }

        }
?>