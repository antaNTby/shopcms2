<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


        //special offers

        $result = array();

        $q = db_query("select s.productID, s.categoryID, s.name, s.Price, s.brief_description, s.product_code,
        s.default_picture, s.enabled, b.productID, t.filename FROM ".SPECIAL_OFFERS_TABLE."
        AS b INNER JOIN ".PRODUCTS_TABLE." AS s on (b.productID=s.productID) INNER JOIN ".PRODUCT_PICTURES." AS
        t on (s.default_picture=t.photoID AND s.productID=t.productID) WHERE s.enabled=1 order by b.sort_order");

        while ($row = db_fetch_row($q))
        {
              if (strlen($row["filename"])>0 && file_exists( "data/small/".$row["filename"])){
                                        $row["default_picture"] = "small/".$row["filename"];
                                        $row["cena"] = $row[3];
                                        $row["Price"] = show_price($row[3]);
                                        $result[] = $row;
              }

        }

        $smarty->assign("special_offers",$result);


        $cifra = 8; //количество последних товаров для выбора
        $result = array();

        $q = db_query("select s.productID, s.name, s.Price, s.enabled, t.filename FROM ".PRODUCTS_TABLE." AS s LEFT JOIN ".PRODUCT_PICTURES."
        AS t on (s.default_picture=t.photoID AND s.productID=t.productID) WHERE s.categoryID!=1 AND s.enabled=1 ORDER BY s.date_added DESC LIMIT 0,".$cifra);

        while ($row = db_fetch_row($q))
        {
              if (strlen($row["filename"])>0 && file_exists( "data/small/".$row["filename"])){
                                        $row["filename"] = "small/".$row["filename"];
                                        $row["cena"] = $row["Price"];
                                        $row["Price"] = show_price($row["Price"]);
                                        $result[] = $row;

              }else{
                                        $row["filename"] = "empty.gif";
                                        $row["cena"] = $row["Price"];
                                        $row["Price"] = show_price($row["Price"]);
                                        $result[] = $row;
              }
        }
        $smarty->assign("new_products", $result);


        $cifra = 8; //количество последних товаров для выбора
        $result = array();

        $q = db_query("select s.productID, s.name, s.Price, s.enabled, t.filename FROM ".PRODUCTS_TABLE." AS s LEFT JOIN ".PRODUCT_PICTURES."
        AS t on (s.default_picture=t.photoID AND s.productID=t.productID) WHERE s.categoryID!=1 AND s.enabled=1 ORDER BY s.items_sold DESC LIMIT 0,".$cifra);

        while ($row = db_fetch_row($q))
        {
              if (strlen($row["filename"])>0 && file_exists( "data/small/".$row["filename"])){
                                        $row["filename"] = "small/".$row["filename"];
                                        $row["cena"] = $row["Price"];
                                        $row["Price"] = show_price($row["Price"]);
                                        $result[] = $row;

              }else{
                                        $row["filename"] = "empty.gif";
                                        $row["cena"] = $row["Price"];
                                        $row["Price"] = show_price($row["Price"]);
                                        $result[] = $row;
              }
        }
        $smarty->assign("popular_products", $result);


/*
        $result = array();
        $q = db_query("select productID FROM ".PRODUCTS_TABLE." WHERE categoryID!=1 AND enabled=1");
        while ($row = db_fetch_row($q))$result[] = $row[0];
        $q = db_query("select s.productID, s.name, s.Price, s.enabled, t.filename FROM ".PRODUCTS_TABLE." AS s LEFT JOIN ".PRODUCT_PICTURES."
        AS t on (s.default_picture=t.photoID AND s.productID=t.productID) WHERE s.productID=".$result[rand(0, count($result)-1)]);
        $result = array();
        $row = db_fetch_row($q);

              if (strlen($row["filename"])>0 && file_exists( "data/small/".$row["filename"])){
                                        $row["filename"] = "small/".$row["filename"];
                                        $row["cena"] = $row["Price"];
                                        $row["Price"] = show_price($row["Price"]);
                                        $result[] = $row;

              }else{
                                        $row["filename"] = "empty.gif";
                                        $row["cena"] = $row["Price"];
                                        $row["Price"] = show_price($row["Price"]);
                                        $result[] = $row;
              }

        $smarty->assign("rand_product", $result[0]);
*/
?>