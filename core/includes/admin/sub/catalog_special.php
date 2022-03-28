<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


        //catalog: products extra parameters list

        if (!strcmp($sub, "special"))
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(4,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {

                if (isset($_GET["save_successful"])) //update was successful
                {
                        $smarty->assign( "save_successful", ADMIN_UPDATE_SUCCESSFUL );
                }

                if (isset($_POST["save_offers"])) //save extra product options
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=catalog&sub=special&safemode=yes");
                        }

                        //save existing
                        db_query("delete from ".SPECIAL_OFFERS_TABLE);

                        $offers = array();
                        foreach ($_POST as $key => $val)
                        {
                          if(strstr($key, "offer_productID_") != false)
                          {
                                $a = str_replace("offer_productID_","",$key);
                                $offers[$a]["productID"] = $val;
                          }
                          if(strstr($key, "offer_sort_") != false)
                          {
                                $a = str_replace("offer_sort_","",$key);
                                $offers[$a]["sort"] = $val;
                          }
                        }
                        foreach ($offers as $key => $value)
                        {
                                db_query("insert into ".SPECIAL_OFFERS_TABLE." (offerID, productID, sort_order) ".
                                        " values (".(int)$key.", ".(int)$value["productID"].", ".(int)$value["sort"].")");
                        }
                        Redirect(ADMIN_FILE."?dpt=catalog&sub=special&save_successful=yes");
                }

                if (isset($_GET["new_offer"])) //add new special offer
                {
                         if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=catalog&sub=special&safemode=yes");
                        }
                        db_query("insert into ".SPECIAL_OFFERS_TABLE." (productID, sort_order) values ('".(int)$_GET["new_offer"]."',0)");
                        Redirect(ADMIN_FILE."?dpt=catalog&sub=special");
                }

                if (isset($_GET["delete"])) //delete special offer
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=catalog&sub=special&safemode=yes");
                        }

                        db_query("delete from ".SPECIAL_OFFERS_TABLE." where offerID=".(int)$_GET["delete"]);
                        Redirect(ADMIN_FILE."?dpt=catalog&sub=special");
                }

                //now select all available product options
                $q = db_query("select offerID, productID, sort_order from ".SPECIAL_OFFERS_TABLE." order by sort_order");
                $result = array();
                while ($row = db_fetch_row($q))
                {
                        //get product name
                        $q1 = db_query("select categoryID, name from ".PRODUCTS_TABLE." where productID=".(int)$row[1]);
                        if ($row1 = db_fetch_row($q1))
                        {
                                $row[3] = $row[1];
                                $row[4] = $row1[1];
                                $result[] = $row;
                        }
                }
                $smarty->assign("offers", $result);

                //set sub-department template
                $smarty->assign("admin_sub_dpt", "catalog_special.tpl.html");
        }
        }
?>