<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        if ( isset($_POST["cart_x"]) ) //add product to cart
        {
                $variants=array();
                foreach( $_POST as $key => $val )
                {
                        if(strstr($key, "option_select_hidden_"))
                                $variants[]=$val;
                }
                unset( $_SESSION["variants"] );
                $_SESSION["variants"] = $variants;
                Redirect("index.php?shopping_cart=yes&add2cart=".(int)$_GET['productID']."&multyaddcount=".(int)$_POST['multyaddcount'] );
        }


        // product detailed information view

        if (isset($_GET["vote"]) && isset($productID)) //vote for a product
        {
          if (!isset($_SESSION["vote_completed"][ $productID ]) && isset($_GET["mark"]) && strlen($_GET["mark"])>0)
          {
                $mark = (int) $_GET["mark"];

                if ($mark>0 && $mark<=5)
                {
                db_query("UPDATE ".PRODUCTS_TABLE." SET customers_rating=(customers_rating*customer_votes+'".$mark."')/(customer_votes+1), customer_votes=customer_votes+1 WHERE productID=".$productID);
                }
          }
          $_SESSION["vote_completed"][ $productID ] = 1;
        }



        if (isset($_POST["request_information"])) //email inquiry to administrator
        {
                $customer_name   = $_POST["customer_name"];
                $customer_email  = $_POST["customer_email"];
                $message_subject = $_POST["message_subject"]." (".CONF_FULL_SHOP_URL."index.php?productID=".$productID.")";
                $message_text    = $_POST["message_text"];

                //validate input data
                if (trim($customer_email)!="" && trim($customer_name)!="" && trim($message_subject)!="" && trim($message_text)!="" && preg_match("/^[_\.a-z0-9-]{1,20}@(([a-z0-9-]+\.)+(com|net|org|mil|edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/is",$customer_email))
                {
                        //send a message to store administrator
                        if(CONF_ENABLE_CONFIRMATION_CODE){
                                 $error_p = 1;
                        if(!$_POST['fConfirmationCode'] || !isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] !==  $_POST['fConfirmationCode']) {
                                 $error_p = 7;
                                 $smarty->assign("error",$error_p);
                        }
                        unset($_SESSION['captcha_keystring']);
                        if($error_p == 1){
                        xMailTxtHTML(CONF_GENERAL_EMAIL, $message_subject, $message_text, $customer_email, $customer_name);
                        Redirect("index.php?productID=".$productID."&sent=yes");
                        }
                        }else{
                        xMailTxtHTML(CONF_GENERAL_EMAIL, $message_subject, $message_text, $customer_email, $customer_name);
                        Redirect("index.php?productID=".$productID."&sent=yes");
                        }
                }
                else if (isset($_POST["request_information"])) $smarty->assign("error",1);
        }


        //show product information
        if (isset($productID) && $productID>0 && !isset($_POST["add_topic"]) && !isset($_POST["discuss"]) )
        {
                $product=GetProduct($productID);

                if (  !$product || $product["enabled"] == 0  )
                {

                header("HTTP/1.0 404 Not Found");
                header("HTTP/1.1 404 Not Found");
                header("Status: 404 Not Found");
                die(ERROR_404_HTML);

                }
                else
                {

                        if ( !isset($_GET["vote"]) ) IncrementProductViewedTimes($productID);

                        $dontshowcategory = 1;

                        $smarty->assign("main_content_template", "product_detailed.tpl.html");

                        $a = $product;
                        $a["PriceWithUnit"] = show_price( $a["Price"] );
                        $a["list_priceWithUnit"] = show_price( $a["list_price"] );

                        if ( ((float)$a["shipping_freight"]) > 0 )
                                $a["shipping_freightUC"] = show_price( $a["shipping_freight"] );

                         if ( isset($_GET["picture_id"]) )
                        {
                                $picture = db_query("select filename, thumbnail, enlarged from ".
                                        PRODUCT_PICTURES." where photoID=".(int)$_GET["picture_id"] );
                                $picture_row = db_fetch_row( $picture );
                        }
                        else if ( !is_null($a["default_picture"]) )
                        {
                                $picture = db_query("select filename, thumbnail, enlarged from ".
                                        PRODUCT_PICTURES." where photoID=".(int)$a["default_picture"] );
                                $picture_row = db_fetch_row( $picture );
                        }
                        else
                        {
                                $picture = db_query(
                                        "select filename, thumbnail, enlarged, photoID from ".PRODUCT_PICTURES.
                                                " where productID=".$productID);
                                if ( $picture_row = db_fetch_row( $picture ) )
                                        $a["default_picture"]=$picture_row["photoID"];
                                else
                                        $picture_row=null;
                        }
                        if ( $picture_row )
                        {
                                $a["picture"]        = $picture_row[ 0 ];
                                $a["thumbnail"] = $picture_row[ 1 ];
                                $a["big_picture"]  = $picture_row[ 2 ];
                        }
                        else
                        {
                                $a["picture"]        = "";
                                $a["thumbnail"] = "";
                                $a["big_picture"]  = "";
                        }

                        if ($a) //product found
                        {
                                if (!isset($categoryID)) $categoryID = $a["categoryID"];

                                //get selected category info
                                $q = db_query("select categoryID, name, description, picture, allow_products_comparison FROM ".CATEGORIES_TABLE." WHERE categoryID=".(int)$categoryID);
                                $row = db_fetch_row($q);
                                if ($row)
                                {
                                        if (!file_exists("data/category/".$row[3])) $row[3] = "";
                                        $smarty->assign("selected_category", $row);
                                        $a["allow_products_comparison"] = $row[4];
                                }
                                else{
                                        $smarty->assign("selected_category", NULL);
                                        $a["allow_products_comparison"] = NULL;
                                    }

                                //calculate a path to the category
                                $smarty->assign("product_category_path",  catCalculatePathToCategory( (int)$categoryID ) );

                                //reviews number
                                $q = db_query("select count(*) FROM ".DISCUSSIONS_TABLE." WHERE productID=".$productID);
                                $k = db_fetch_row($q); $k = $k[0];

                                //extra parameters
                                $extra = GetExtraParametrs((int)$productID);
                                $extracount = count($extra);
                                //related items
                                $related = array();
                                $q = db_query("select count(*) FROM ".RELATED_PRODUCTS_TABLE." WHERE Owner=".$productID);
                                $cnt = db_fetch_row($q);
                                $smarty->assign("product_related_number", $cnt[0]);
                                if ($cnt[0] > 0)
                                {
                                        $q = db_query("select productID FROM ".RELATED_PRODUCTS_TABLE." WHERE Owner=".$productID);

                                        while ($row = db_fetch_row($q))
                                        {
                                                $p = db_query("select productID, name, Price FROM ".PRODUCTS_TABLE." WHERE productID=".$row[0]." and enabled=1");
                                                if ($r = db_fetch_row($p))
                                                {
                                                  $r["Price"] = show_price($r["Price"]);
                                                  $related[] = $r;
                                                }
                                        }

                                }
                                $smarty->assign( "productslinkscat", getcontentprod($productID));
                                //update several product fields
                                if (!file_exists("data/small/".$a["picture"] )) $a["picture"] = 0;
                                if (!file_exists("data/medium/".$a["thumbnail"] )) $a["thumbnail"] = 0;
                                if (!file_exists("data/big/".$a["big_picture"] )) $a["big_picture"] = 0;
                                else if ($a["big_picture"])
                                {
                                        $size = getimagesize("data/big/".$a["big_picture"] );
                                        $a[16] = $size[0]+40;
                                        $a[17] = $size[1]+30;
                                }
                                $a[12] = show_price( $a["Price"] );
                                $a[13] = show_price( $a["list_price"] );
                                $a[14] = show_price( $a["list_price"] - $a["Price"]); //you save (value)
                                $a["PriceWithOutUnit"]=show_priceWithOutUnit( $a["Price"] );
                                if ( $a["list_price"] ) $a[15] =
                                        ceil(((($a["list_price"]-$a["Price"])/
                                                $a["list_price"])*100)); //you save (%)


                                if ( isset($_GET["picture_id"]) )
                                {
                                        $pictures = db_query("select photoID, filename, thumbnail, enlarged from ".
                                                PRODUCT_PICTURES." where photoID!=".(int)$_GET["picture_id"].
                                                " AND productID=".$productID );
                                }
                                else if ( !is_null($a["default_picture"]) )
                                {
                                        $pictures = db_query("select photoID, filename, thumbnail, enlarged from ".
                                                PRODUCT_PICTURES." where photoID!=".$a["default_picture"].
                                                " AND productID=".$productID );
                                }
                                else
                                {
                                        $pictures = db_query("select photoID, filename, thumbnail, enlarged from ".
                                                PRODUCT_PICTURES." where productID=".$productID );
                                }
                                $all_product_pictures = array();
                                $all_product_pictures_id = array();
                                while( $picture=db_fetch_row($pictures) )
                                {
                                        if ( $picture["filename"] != "")
                                        {
                                                if ( file_exists("data/small/".$picture["filename"]))
                                                {
                                                        if (!file_exists("data/medium/".$picture["thumbnail"] )) $picture["thumbnail"] = 0;
                                                        if (!file_exists("data/big/".$picture["enlarged"] )) $picture["enlarged"] = 0;
                                                        $all_product_pictures[]=$picture;
                                                        $all_product_pictures_id[] = $picture[0];
                                                }
                                        }
                                }

                                //eproduct
                                if (strlen($a["eproduct_filename"]) > 0 && file_exists("core/files/".$a["eproduct_filename"]) )
                                {
                                        $size = filesize("core/files/".$a["eproduct_filename"]);
                                        if ($size > 1000) $size = round ($size / 1000);
                                        $a["eproduct_filesize"] = $size." Kb";
                                }
                                else
                                {
                                        $a["eproduct_filename"] = "";
                                }

                                //initialize product "request information" form in case it has not been already submitted
                                if (!isset($_POST["request_information"]))
                                {
                                        if (!isset($_SESSION["log"]))
                                        {
                                                $customer_name = "";
                                                $customer_email = "";
                                        }
                                        else
                                        {
                                                $custinfo = regGetCustomerInfo2( $_SESSION["log"] );
                                                $customer_name = $custinfo["first_name"]." ".$custinfo["last_name"];
                                                $customer_email = $custinfo["Email"];
                                        }

                                        $message_text = "";
                                }

                                $smarty->hassign("customer_name", $customer_name);
                                $smarty->hassign("customer_email", $customer_email);
                                $smarty->hassign("message_text", $message_text);

                                if (isset($_GET["sent"])) $smarty->assign("sent",1);

                                $smarty->assign("all_product_pictures_id", $all_product_pictures_id );
                                $smarty->assign("all_product_pictures", $all_product_pictures );
                                $smarty->assign("product_info", $a);
                                $smarty->assign("product_reviews_count", $k);
                                $smarty->assign("product_extra", $extra);
                                $smarty->assign("product_extra_count", $extracount);
                                $smarty->assign("product_related", $related);
                        }
                        else
                        {
                                //product not found
                                header("HTTP/1.0 404 Not Found");
                                header("HTTP/1.1 404 Not Found");
                                header("Status: 404 Not Found");
                                die(ERROR_404_HTML);
                        }
                }
        }

?>