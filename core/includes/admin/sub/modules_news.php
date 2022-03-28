<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################
        //news module

        if (!strcmp($sub, "news"))
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(18,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {


                function _getUrlToSubmit()
                {
                        $url = ADMIN_FILE."?dpt=modules&sub=news";
                        if ( isset($_GET["offset"]) )
                                $url .= "&offset=".$_GET["offset"];
                        if ( isset($_GET["show_all"]) )
                                $url .= "&show_all=".$_GET["show_all"];
                        return $url;
                }

                function _getUrlToDelete()
                {
                        return _getUrlToSubmit();
                }

                if (isset($_GET["save_successful"])) //show successful save confirmation message
                        $smarty->assign("configuration_saved", 1);

                //current time
                $s = dtConvertToStandartForm( get_current_time() );
                $smarty->assign( "current_date", $s );

                if ( isset($_POST["news_save"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect( _getUrlToSubmit()."&safemode=yes" );
                        }

                        $picture = "";

                        $NID = newsAddNews($_POST["add_date"], $_POST["title"], $_POST["textToPrePublication"],
                                        $_POST["textToPublication"], $_POST["textToMail"] );
                        if ( isset($_POST["send"]) ) //send news to subscribers
                                newsSendNews( $NID );

                        Redirect( _getUrlToSubmit()."&save_successful=yes" );
                }

                if ( isset($_GET["edit"]) )
                {
                $edit_news = newsGetNewsToEdit($_GET["edit"]);
                $edit_news["textToPrePublication"] = html_spchars($edit_news["textToPrePublication"]);
                $edit_news["textToPublication"] = html_spchars($edit_news["textToPublication"]);
                $smarty->assign( "edit_news", $edit_news );
                $smarty->assign( "edit_news_id", (int)$_GET["edit"]);
                $smarty->assign( "news_editor", 1);
                }

                if ( isset($_GET["add_news"]) )
                {
                $smarty->assign( "news_editor", 1);
                }

                if ( isset($_POST["update_news"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect( _getUrlToSubmit()."&safemode=yes" );
                        }

                        newsUpdateNews($_POST["add_date"], $_POST["title"], $_POST["textToPrePublication"], $_POST["textToPublication"], $_POST["textToMail"], $_POST["edit_news_id"] );
                        if ( isset($_POST["send"]) ) //send news to subscribers
                                newsSendNews($_POST["edit_news_id"]);
                        Redirect( _getUrlToSubmit()."&save_successful=yes" );
                }

                if (isset($_GET["delete"]))
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect( _getUrlToDelete()."&safemode=yes" );
                        }
                        newsDeleteNews($_GET["delete"]);
                        Redirect( _getUrlToDelete() );
                }


                $callBackParam        = array();
                $news_posts                = array();
                $navigatorHtml = GetNavigatorHtml(ADMIN_FILE."?dpt=modules&sub=news", 20,
                                                'newsGetAllNews', $callBackParam, $news_posts, $offset, $count );
                $smarty->assign( "navigator", $navigatorHtml );
                $smarty->assign( "news_posts", $news_posts );

                $smarty->hassign( "urlToSubmit", _getUrlToSubmit() );
                $smarty->hassign( "urlToDelete", _getUrlToDelete() );

                //set sub-department template
                $smarty->assign( "admin_sub_dpt", "modules_news.tpl.html" );
        }
        }
?>