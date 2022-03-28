<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        if ( !strcmp($sub, "discuss") )
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(34,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {

                function GetUrlToFind()
                {
                        $res = ADMIN_FILE."?dpt=catalog&sub=discuss";
                        if ( isset( $_GET["offset"] ) )
                                $res .= "&offset=".$_GET["offset"];
                        if ( isset( $_GET["sort"] ) )
                                $res .= "&sort=".$_GET["sort"];
                        return $res;
                }

                function GetUrlToNavigate()
                {
                        $res = ADMIN_FILE."?dpt=catalog&sub=discuss";
                        if ( isset( $_GET["productID"] ) )
                                $res .= "&productID=".$_GET["productID"];
                        if ( isset( $_GET["sort"] ) )
                                $res .= "&sort=".$_GET["sort"];
                        return $res;
                }

                function GetUrlToSort()
                {
                        $res = ADMIN_FILE."?dpt=catalog&sub=discuss";
                        if ( isset( $_GET["productID"] ) )
                                $res .= "&productID=".$_GET["productID"];
                        if ( isset( $_GET["offset"] ) )
                                $res .= "&offset=".$_GET["offset"];
                        return $res;
                }

                function GetFullUrl()
                {
                        $res = ADMIN_FILE."?dpt=catalog&sub=discuss";
                        if ( isset( $_GET["productID"] ) )
                                $res .= "&productID=".$_GET["productID"];
                        if ( isset( $_GET["offset"] ) )
                                $res .= "&offset=".$_GET["offset"];
                        if ( isset( $_GET["sort"] ) )
                                $res .= "&sort=".$_GET["sort"];
                        if ( isset( $_GET["direction"] ) )
                                $res .= "&direction=".$_GET["direction"];
                        return $res;
                }






                if ( isset($_GET["answer"]) )
                {
                        $discussion = discGetDiscussion( $_GET["answer"] );
                        $return_url = GetFullUrl();

                        if ( isset($_POST["add"]) )
                        {
                                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(ADMIN_FILE."?dpt=catalog&sub=discuss&safemode=yes");
                                }
                                discAddDiscussion( $discussion["productID"],
                                        $_POST["newAuthor"], $_POST["newTopic"], $_POST["newBody"] );

                                Redirect( $return_url );
                        }


                        $smarty->hassign( "return_url", $return_url );
                        $smarty->assign( "discussion", $discussion );
                        $smarty->assign( "answer", 1);
                }
                else
                {
                        if ( isset($_GET["delete"]) )
                        {
                                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(ADMIN_FILE."?dpt=catalog&sub=discuss&productID=".$_GET["productID"]."&safemode=yes");
                                }
                                discDeleteDiscusion( $_GET["delete"] );
                                Redirect( GetUrlToNavigate() );
                        }

                        $callBackParam = array();
                        if ( isset($_GET["sort"])  )
                                $callBackParam["sort"] = $_GET["sort"];

                        if ( isset($_GET["direction"]) )
                                $callBackParam["direction"] = $_GET["direction"];


                        $discussions        = array();
                        $navigatorHtml        = "";

                        $discussed_products = discGetAllDiscussedProducts();

                        $smarty->assign( "products", $discussed_products );

                        if ( isset($_GET["productID"]) )
                        {
                                $callBackParam["productID"] = $_GET["productID"];
                                $smarty->assign( "productID", $_GET["productID"] );

                                $count = 0;
                                $navigatorHtml = GetNavigatorHtml( GetUrlToNavigate(), 20,
                                                        'discGetAllDiscussion', $callBackParam,
                                                        $discussions, $offset, $count );

                                if ( count($discussions) == 0 )
                                {
                                        if (count($discussed_products)>0)
                                                Redirect( GetUrlToFind()."&productID=".$discussed_products[0]["productID"] );
                                        else
                                                Redirect( GetUrlToFind() );
                                }
                        }
                        else
                                $smarty->assign( "productID", 0 );


                        $smarty->assign( "discussions", $discussions);
                        $smarty->assign( "navigator", $navigatorHtml );
                        $smarty->hassign( "fullUrl", GetFullUrl() );
                        $smarty->hassign( "urlToSort", GetUrlToSort() );
                        $smarty->hassign( "urlToFind", GetUrlToFind() );
                }


                //set sub-department template
                $smarty->assign("admin_sub_dpt", "catalog_discuss.tpl.html");
        }
        }
?>