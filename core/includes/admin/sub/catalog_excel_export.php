<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        function fputcsvex($f, $listi, $d=",", $q='"') {
                $line = "";
                foreach ($listi as $field) {
                        # remove any windows new lines,
                        # as they interfere with the parsing at the other end
                        $field = str_replace("\r\n", " ", $field);
                        # if a deliminator char, a double quote char or a newline
                        # are in the field, add quotes
                        if(ereg("[$d$q\n\r]", $field)) {
                                $field = $q.str_replace($q, $q.$q, $field).$q;
                        }
                        $line .= $field.$d;
                }
                # strip the last deliminator
                $line = substr($line, 0, -1);
                # add the newline
                $line .= "\n";
                # we don't care if the file pointer is invalid,
                # let fputs take care of it
                return gzputs($f, $line);
        }

        function _exportCategoryLine($categoryID, $level, &$f, $delimiter) //writes a category line into CSV file.
        {
                global $picture_columns_count;
                global $extra_columns_count;

                $q = db_query("select categoryID, name, description, sort_order, picture, meta_keywords, meta_description, title from ".
                        CATEGORIES_TABLE." where categoryID=".(int)$categoryID);
                $cat_data = db_fetch_row($q);
                if (!$cat_data) return;

                $lev = "";
                for ($i=0;$i<$level;$i++) $lev .= "!";
                $cat_data["name"] = $lev.$cat_data["name"];

                $lines = array($cat_data["sort_order"],"",xHtmlSpecialCharsDecode($cat_data["name"]),xHtmlSpecialCharsDecode($cat_data["title"]),$cat_data["description"],"","","","","",xHtmlSpecialCharsDecode($cat_data["meta_keywords"]),xHtmlSpecialCharsDecode($cat_data["meta_description"]),"","","","","","",$cat_data["picture"]);

                for ($i=1;$i<$picture_columns_count+$extra_columns_count;$i++)
                {
                        $lines[]="";
                }
                fputcsvex($f,$lines,$delimiter);
        }

        function _exportProducts($categoryID, &$f, $delimiter) //writes all products inside a single category to a CSV file
        {
                global $picture_columns_count;
                global $extra_columns_count;

                //products
                $q1 = db_query("select sort_order, product_code, name, description, brief_description, Price, list_price, in_stock, items_sold, meta_keywords,
                meta_description, shipping_freight, weight, free_shipping, min_order_amount, eproduct_filename, eproduct_available_days, eproduct_download_times,
                default_picture, productID, title from ".PRODUCTS_TABLE." where categoryID=".(int)$categoryID." ORDER BY sort_order, name");

                //extra options
                $vararr = array();
                $q2 = db_query("select optionID from ".PRODUCT_OPTIONS_TABLE." ORDER BY sort_order, name");
                while ($row2 = db_fetch_row($q2)) $vararr[] = (int)$row2[0];

                while ($row1 = db_fetch_row($q1))
                {
                        foreach ($row1 as $key => $val)
                        {
                                if (!strcmp($key,"Price") || !strcmp($key,"list_price"))
                                {
                                        $val = round(100*$val)/100;
                                        if (round($val*10) == $val*10 && round($val)!=$val)
                                                $val = (string)$val."0"; //to avoid prices like 17.5 - write 17.50 instead
                                        $row1[$key] = $val;
                                }
                        }

                        $lines = array($row1["sort_order"],xHtmlSpecialCharsDecode($row1["product_code"]),xHtmlSpecialCharsDecode($row1["name"]),xHtmlSpecialCharsDecode($row1["title"]),$row1["description"],$row1["brief_description"],$row1["Price"],
                        $row1["list_price"],$row1["in_stock"],$row1["items_sold"],xHtmlSpecialCharsDecode($row1["meta_keywords"]),xHtmlSpecialCharsDecode($row1["meta_description"]),$row1["shipping_freight"],$row1["weight"],$row1["min_order_amount"],
                        $row1["eproduct_filename"],$row1["eproduct_available_days"],$row1["eproduct_download_times"]);

                        //pictures
                        //at first, fetch default picture
                        $cnt = 0;
                        if (!$row1["default_picture"]) $row1["default_picture"] = 0; //no default picture defined;
                        $qp = db_query("select filename, thumbnail, enlarged from ".PRODUCT_PICTURES." where productID=".(int)$row1["productID"]." and photoID=".(int)$row1["default_picture"]);
                        $rowp = db_fetch_row($qp);
                        $s = "";
                        if ($rowp)
                        {
                                if ($rowp[0]) $s .= $rowp[0];
                                if ($rowp[1]) {$s .= ",".$rowp[1];}elseif ($rowp[2]){$s .= ",";}
                                if ($rowp[2]) $s .= ",".$rowp[2];
                        }
                        $lines[] = $s;
                        $cnt++;
                        //the rest of the photos
                        $qp = db_query("select filename, thumbnail, enlarged from ".PRODUCT_PICTURES." where productID=".(int)$row1["productID"]." and photoID!=".(int)$row1["default_picture"]);
                        while ($rowp = db_fetch_row($qp))
                        {
                                $s = "";
                                if ($rowp)
                                {
                                        if ($rowp[0]) $s .= $rowp[0];
                                        if ($rowp[1]) {$s .= ",".$rowp[1];}elseif ($rowp[2]){$s .= ",";}
                                        if ($rowp[2]) $s .= ",".$rowp[2];
                                }
                                $lines[] = $s;
                                $cnt++;
                        }
                        if ($cnt < $picture_columns_count) for ($i=$cnt; $i<$picture_columns_count; $i++) $lines[]="";

                        //browse options list
                        foreach ($vararr as $keyz => $extid)
                        {
                                //browser all option values of current product
                                $q3 = db_query("select option_value, option_type, variantID from ".PRODUCT_OPTIONS_VALUES_TABLE." where productID=".(int)$row1['productID']." and optionID=".$extid);
                                $row3 = db_fetch_row($q3);
                                if (!$row3) $row3 = array("",0,0);

                                if ((int)$row3[1] == 1) //selectable option - prepare a string to insert into a CSV file, e.g {red=3,blue=1,white}
                                {
                                        if (!$row3[2]) $row3[2] = 0;
                                        //prepare an array of available option variantIDs. the first element (0'th) is the default varinatID
                                        $available_variants = array( array($row3[2],0) );

                                        $q4 = db_query( "select variantID, price_surplus from ".PRODUCTS_OPTIONS_SET_TABLE." where productID=".(int)$row1['productID']." and optionID=".$extid);
                                        while ($row4 = db_fetch_row($q4))
                                        {
                                                if ($row4[0] == $row3[2]) //is it a default variantID
                                                {
                                                        $available_variants[0] = $row4;
                                                }
                                                else
                                                        $available_variants[] = $row4; //add this value to array
                                        }
                                        //now write all variants
                                        $s = "{";
                                        $tmp = "";

                                        foreach ($available_variants as $key => $val)
                                                if ($val[0])
                                                {
                                                        $qvar = db_query("select option_value from ".PRODUCTS_OPTIONS_VALUES_VARIANTS_TABLE." where optionID=".$extid." and variantID=".(int)$val[0]);
                                                        $rowvar = db_fetch_row($qvar);
                                                        $s .= $tmp;
                                                        $s .= xHtmlSpecialCharsDecode($rowvar[0])."";
                                                        if ($val[1]) $s .= "=".$val[1];
                                                        $tmp = ",";
                                                }
                                        $s .= "}";

                                        $row3[0] = $s;
                                }

                                $lines[] = $row3[0];
                        }
                        fputcsvex($f,$lines,$delimiter);
                }
        }

        function _exportSubCategoriesAndProducts($parent, $level, &$f, $delimiter) //exports products and subcategories of $parent to a CSV file $f
                //a recurrent function
        {
                $cnt = 0;
                $q = db_query("select categoryID from ".
                        CATEGORIES_TABLE.
                        " where parent=".$parent." order by sort_order, name");

                //fetch all subcategories
                while ($row = db_fetch_row($q))
                {
                                _exportCategoryLine($row[0], $level, $f, $delimiter);
                                _exportProducts($row[0], $f, $delimiter);

                                //process all subcategories
                                _exportSubCategoriesAndProducts($row[0], $level+1, $f, $delimiter);
                }

        } //_exportSubCategoriesAndProducts




        //products and categories catalog import from MS Excel .CSV files
        if (!strcmp($sub, "excel_export"))
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(6,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {
                if (isset($_POST["excel_export"])) //export products
                {
                          if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=catalog&sub=excel_export&safemode=yes");
                        }
                        @set_time_limit(0);

                        if ($_POST["delimiter"] == 2)
                        {
                                $delimiter = ",";
                        }
                        elseif ($_POST["delimiter"] == 3)
                        {
                                $delimiter = "\t";
                        }else{
                                $delimiter = ";";
                        }
                        if (file_exists("core/temp/catalog.csv.gz")) unlink("core/temp/catalog.csv.gz");
                        $f = gzopen("core/temp/catalog.csv.gz","w");

                        //write a header line
                        $lines = array(ADMIN_SORT_ORDER,ADMIN_PRODUCT_CODE,ADMIN_PRODUCT_NAME,ADMIN_PRODUCT_TITLE_PAGE,ADMIN_PRODUCT_DESC,
                        ADMIN_PRODUCT_BRIEF_DESC,ADMIN_PRODUCT_PRICE,ADMIN_PRODUCT_LISTPRICE,ADMIN_PRODUCT_INSTOCK,ADMIN_PRODUCT_SOLD,
                        ADMIN_META_KEYWORDS,ADMIN_META_DESCRIPTION,ADMIN_SHIPPING_FREIGHT,ADMIN_PRODUCT_WEIGHT,ADMIN_MIN_ORDER_AMOUNT,ADMIN_EPRODUCT_FILENAME,ADMIN_EPRODUCT_AVAILABLE_DAYS2,ADMIN_EPRODUCT_DOWNLOAD_TIMES,ADMIN_PHOTOS);

                        //calculate the number of 'Picture' columns
                        $q = db_query("select productID from ".PRODUCT_PICTURES." order by productID");
                        $max = 0;
                        $currID = 0;
                        $result = array();
                        while ($row = db_fetch_row($q))
                        {
                                if ($currID != $row[0]) $cnt = 0;
                                $cnt++;
                                $currID = $row[0];

                                if ($max < $cnt) $max = $cnt;
                        }
                        //record as many PICTURE columns in the file as located in the database
                        for ($i=1;$i<$max;$i++)
                        {
                                $lines[] = ADMIN_PHOTOS;
                        }
                        $picture_columns_count = $max;

                        //extra parameters columns
                        $q = db_query("select name from ".PRODUCT_OPTIONS_TABLE." ORDER BY sort_order, name ");
                        $cnt = 0;
                        while ($row = db_fetch_row($q))
                        {
                                $lines[] = xHtmlSpecialCharsDecode($row[0]);
                                $cnt++;
                        }
                        $extra_columns_count = $cnt;

                        fputcsvex($f,$lines,$delimiter);
                        unset($lines);
                        //export selected products and categories
                        //root
                        if (isset($_POST["categ_1"]))
                        {
                                _exportProducts(1, $f, $delimiter);
                        }
                        //other categories
                        $q = db_query("select categoryID, name from ".CATEGORIES_TABLE." where parent=1 order by sort_order, name");
                        $result = array();
                        while ($row = db_fetch_row($q))
                                if (isset($_POST["categ_$row[0]"]))
                                {
                                        _exportCategoryLine($row[0], 0, $f, $delimiter);
                                        _exportProducts($row[0], $f, $delimiter);
                                        _exportSubCategoriesAndProducts($row[0], 1, $f, $delimiter);
                                }

                        gzclose($f);
                        Redirect(ADMIN_FILE."?dpt=catalog&sub=excel_export&export_completed=yes");

                }

                if (isset($_GET["export_completed"])) //show successful save confirmation message
                {
                        if (file_exists("core/temp/catalog.csv.gz"))
                        {
                                $getFileParam = cryptFileParamCrypt( "GetCSVCatalog", null );
                                $smarty->assign( "getFileParam", $getFileParam );

                                $smarty->assign("excel_export_successful", 1);
                                $smarty->assign("excel_filesize", (string) round(filesize("core/temp/catalog.csv.gz") / 1048576, 3));
                        }
                }
                else //prepare categories list
                {
                        $q = db_query("select categoryID, name from ".CATEGORIES_TABLE." where parent=1 order by sort_order, name");
                        $result = array();
                        while ($row = db_fetch_row($q)) $result[] = $row;
                        $smarty->assign("categories",$result);
                }

                $smarty->assign("admin_sub_dpt", "catalog_excel_export.tpl.html");
        }
        }
?>