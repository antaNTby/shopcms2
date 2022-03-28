<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################
        // show whole price list

      function show_code_p() 
      {
        global $selected_currency_details;

                if (!isset($selected_currency_details) || !$selected_currency_details) //no currency found
                {
                        return "";
                }

        //is exchange rate negative or 0?
        if ($selected_currency_details[1] == 0) return "";
        return $selected_currency_details[0];
      }

      function pricessCategories($parent,$level)
      {

                //same as processCategories(), except it creates a pricelist of the shop

                $out = array();
                $cnt = 0;

                $q1 = db_query("select categoryID, name from ".CATEGORIES_TABLE.
                        " where parent=".(int)$parent." order by sort_order, name");
                while ($row = db_fetch_row($q1))
                {

                        $r = hexdec(substr('999999', 0, 2));
                        $g = hexdec(substr('999999', 2, 2));
                        $b = hexdec(substr('999999', 4, 2));
                        $m = (float)max($r, max($g,$b));
                        $r = round((190+20*min($level,3))*$r/$m);
                        $g = round((190+20*min($level,3))*$g/$m);
                        $b = round((190+20*min($level,3))*$b/$m);
                        $c = dechex($r).dechex($g).dechex($b); //final color

                        //add category to the output
                        $out[$cnt][0] = $row[0];
                        $out[$cnt][1] = $row[1];
                        $out[$cnt][2] = $level;
                        $out[$cnt][3] = 1;
                        $out[$cnt][4] = 0; //0 is for category, 1 - product
                        $cnt++;

                        if ( !isset($_GET["sort"]) )
                                $order_clause = "order by ".CONF_DEFAULT_SORT_ORDER."";
                        else
                        {
                                //verify $_GET["sort"]
                                if (!(!strcmp($_GET["sort"],"name") || !strcmp($_GET["sort"],"Price") || !strcmp($_GET["sort"],"customers_rating")))
                                        $_GET["sort"] = "name";

                                $order_clause = " order by ".xEscSQL($_GET["sort"]);
                                if ( isset($_GET["direction"]) )
                                {
                                        if ( !strcmp( $_GET["direction"] , "DESC" ) )
                                                $order_clause .= " DESC ";
                                        else
                                                $order_clause .= " ASC ";
                                }
                        }

                        //add products
                        $q = db_query("select productID, name, Price, in_stock, product_code from ".PRODUCTS_TABLE.
                                " where categoryID=".$row[0]." and Price>=0 and enabled=1 ".
                                $order_clause );
                        while ($row1 = db_fetch_row($q))
                        {
                                if ($row1[2] < 0){
                                        $cennik = "n/a";
                                        $row1[2] = "n/a";
                                }else{
                                        $cennik  = show_price($row1[2]);
                                        $row1[2] = show_price($row1[2], 0, false);
                                }

                                $out[$cnt][0] = $row1[0];
                                $out[$cnt][1] = $row1[1];
                                $out[$cnt][2] = $level;
                                $out[$cnt][3] = "FFFFFF";
                                $out[$cnt][4] = 1; //0 is for category, 1 - product
                                $out[$cnt][5] = $cennik;
                                $out[$cnt][6] = $row1[3];
                                $out[$cnt][7] = $row1[4];
                                $out[$cnt][8] = $row1[2];
                                $cnt++;
                        }

                        //process all subcategories
                        $sub_out = pricessCategories($row[0], $level+1);

                        //add $sub_out to the end of $out
                        $c_sub_out = count($sub_out);
                        for ($j=0; $j<$c_sub_out; $j++)
                        {
                                $out[] = $sub_out[$j];
                                $cnt++;
                        }
                 }

                return $out;

        } //pricessCategories

        function _sortPriceListSetting( &$smarty, $urlToSort )
        {
                $sort_string = STRING_PRICELIST_ITEM_SORT;
                $sort_string = str_replace( "{ASC_NAME}",
                        "<a href='".$urlToSort."&amp;sort=name&amp;direction=ASC'>".STRING_ASC."</a>",        $sort_string );
                $sort_string = str_replace( "{DESC_NAME}",
                        "<a href='".$urlToSort."&amp;sort=name&amp;direction=DESC'>".STRING_DESC."</a>",        $sort_string );
                $sort_string = str_replace( "{ASC_PRICE}",
                        "<a href='".$urlToSort."&amp;sort=Price&amp;direction=ASC'>".STRING_ASC."</a>",        $sort_string );
                $sort_string = str_replace( "{DESC_PRICE}",
                        "<a href='".$urlToSort."&amp;sort=Price&amp;direction=DESC'>".STRING_DESC."</a>",        $sort_string );
                $smarty->assign( "string_product_sort", $sort_string );
        }

        if (isset($_GET["show_price"])) //show pricelist
        {
                _sortPriceListSetting( $smarty, "index.php?show_price=yes" );

                $pricelist_elements = pricessCategories(1, 0);
                $smarty->assign("pricelist_elements", $pricelist_elements);
                $smarty->assign("main_content_template", "pricelist.tpl.html");
        }

        if (isset($_GET["download_price"])) //show pricelist
        {
                _sortPriceListSetting( $smarty, "index.php?show_price=yes" );
                $currentcur = show_code_p();
                $ddate = strftime("%Y-%m-%d %H:%M:%S", time()+intval(CONF_TIMEZONE)*3600);
                $pricelist_elements2 = pricessCategories(1, 0);
                $pricelist_elements = '<?xml version="1.0" encoding="'.DEFAULT_CHARSET.'"?>
                                       <?mso-application progid="Excel.Sheet"?>
                                       <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                                       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                                       xmlns:x="urn:schemas-microsoft-com:office:excel"
                                       xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml"
                                       xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
                                       xmlns:o="urn:schemas-microsoft-com:office:office"
                                       xmlns:html="http://www.w3.org/TR/REC-html40"
                                       xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet">

                                       <Styles>
                                         <Style ss:ID="Default" ss:Name="Normal">
                                           <Alignment ss:Horizontal="Left" ss:Vertical="Bottom" />
                                           <Borders>
                                             <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#c0c0c0" />
                                             <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#c0c0c0" />
                                             <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#c0c0c0" />
                                             <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#c0c0c0" />
                                           </Borders>
                                           <Font ss:Size="10" ss:Color="#000000" ss:FontName="Arial" />
                                           <Interior />
                                           <NumberFormat />
                                           <Protection />
                                         </Style>
                                         <Style ss:ID="Hedline1" ss:Name="Hedline1">
                                           <Interior ss:Color="#ccffcc" ss:Pattern="Solid" />
                                           <Font ss:Size="10" ss:Color="#000000" ss:Bold="1" ss:FontName="Arial" />
                                         </Style>
                                         <Style ss:ID="Hedline2" ss:Name="Hedline2">
                                           <Alignment ss:Horizontal="Center" ss:Vertical="Bottom" />
                                           <Font ss:Size="14" ss:Color="#000000" ss:FontName="Arial" />
                                           <Interior ss:Color="#ccffcc" ss:Pattern="Solid" />
                                         </Style>
                                         <Style ss:ID="Tab" ss:Name="Tab">
                                           <Alignment ss:Horizontal="Center" ss:Vertical="Bottom" />
                                           <Font ss:Size="10" ss:Color="#000000" ss:FontName="Arial" />
                                         </Style>
                                      </Styles>
                                      <ss:Worksheet ss:Name="Pricelist"><Table><Column ss:Width="500" />
                                        <Column ss:Width="112" ss:StyleID="Tab" />';
                                        if (CONF_DISPLAY_PRCODE == 1) $pricelist_elements .= '<Column ss:Width="112" ss:StyleID="Tab" />';
                                        $pricelist_elements .= '<Row ss:AutoFitHeight="0" ss:Height="20"><Cell ss:MergeAcross="';
                                        if (CONF_DISPLAY_PRCODE == 1) $pricelist_elements .= '2'; else $pricelist_elements .= '1';
                                        $pricelist_elements .= '" ss:StyleID="Hedline1"><Data ss:Type="String">'.STRING_PRICELIST.' '.CONF_SHOP_NAME.'</Data>
                                        </Cell></Row><Row ss:AutoFitHeight="0" ss:Height="12"><Cell ss:MergeAcross="';
if (CONF_DISPLAY_PRCODE == 1) $pricelist_elements .= '2'; else $pricelist_elements .= '1';
$pricelist_elements .= '" ss:StyleID="Hedline1">
<Data ss:Type="String">'.STRING_PRICE_CREATE.' '.$ddate.'</Data></Cell>
</Row><Row ss:AutoFitHeight="0" ss:Height="20"><Cell ss:StyleID="Hedline2"><Data ss:Type="String">'.STRING_PRICE_PRODUCT_NAME.'</Data>
</Cell><Cell ss:StyleID="Hedline2"><Data ss:Type="String">'.CURRENT_PRICE.'('.$currentcur.')</Data></Cell>';
if (CONF_DISPLAY_PRCODE == 1) $pricelist_elements .= '<Cell ss:StyleID="Hedline2"><Data ss:Type="String">'.STRING_PRODUCT_CODE.'</Data></Cell>';
$pricelist_elements .= '</Row>';

                for ($j=0; $j<count($pricelist_elements2); $j++)
                        {


                          $pricelist_elements .= '<Row ss:AutoFitHeight="0" ss:Height="12">';
                          if($pricelist_elements2[$j][4] != 1) {
                          $pricelist_elements .= '<Cell ss:StyleID="Hedline1"';
                          $pricelist_elements .= '><Data ss:Type="String">';
                          for ($h=0; $h<$pricelist_elements2[$j][2]; $h++)
                          {
                          $pricelist_elements .= "    ";
                          }
                          $pricelist_elements .= $pricelist_elements2[$j][1].'</Data></Cell><Cell ss:StyleID="Hedline1"><Data ss:Type="String"></Data></Cell>';
                          if (CONF_DISPLAY_PRCODE == 1) $pricelist_elements .= '<Cell ss:StyleID="Hedline1"><Data ss:Type="String"></Data></Cell>';
                          }else{
                          $pricelist_elements .= '<Cell><Data ss:Type="String">';
                          for ($h=0; $h<$pricelist_elements2[$j][2]; $h++)
                          {
                          $pricelist_elements .= "    ";
                          }
                          $pricelist_elements .= $pricelist_elements2[$j][1].'</Data></Cell><Cell><Data ss:Type="String">'.$pricelist_elements2[$j][8].'</Data></Cell>';
                          if (CONF_DISPLAY_PRCODE == 1) $pricelist_elements .= '<Cell><Data ss:Type="String">'.$pricelist_elements2[$j][7].'</Data></Cell>';
                          }
                          $pricelist_elements .= '</Row>';

                          }
                          $pricelist_elements .= "</Table><x:WorksheetOptions /></ss:Worksheet></Workbook>";

                 header("Pragma: public");
                 header("Expires: 0");
                 header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                 header("Cache-Control: public");
                 header("Content-Description: File Transfer");
                 header("Content-Type: application/vnd.ms-excel; charset=".DEFAULT_CHARSET."; format=attachment;");
                 header("Content-Disposition: attachment; filename=price.xml;");
                 print $pricelist_elements;
                 exit();
        }
?>