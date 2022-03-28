    <?PHP
            // show whole price list

            function pricessCategories_site($parent,$level)
            {

                    //same as processCategories(), except it creates a pricelist of the shop

                    $out1 = array();
                    $c1nt1 = 0;

                    $q11 = db_query("select categoryID, name from ".CATEGORIES_TABLE.
                            " where parent=$parent order by sort_order, name") or die (db_error());
                    while ($row = db_fetch_row($q11))
                    {

                            //define back color of the cell
                            $r1 = hexdec(substr(CONF_MIDDLE_COLOR, 0, 2));
                            $g1 = hexdec(substr(CONF_MIDDLE_COLOR, 2, 2));
                            $b1 = hexdec(substr(CONF_MIDDLE_COLOR, 4, 2));
                            $m1 = (float)max($r1, max($g1,$b1));
                            $r1 = round((190+20*min($level,3))*$r1/$m1);
                            $g1 = round((190+20*min($level,3))*$g1/$m1);
                            $b1 = round((190+20*min($level,3))*$b1/$m1);
                            $c1 = dechex($r1).dechex($g1).dechex($b1); //final color

                            //add category to the output
                            $out1[$c1nt1][0] = $row[0];
                            $out1[$c1nt1][1] = $row[1];
                            $out1[$c1nt1][2] = $level;
                            $out1[$c1nt1][3] = $c1;
                            $out1[$c1nt1][4] = 0; //0 is for category, 1 - product
                            $out1[$c1nt1][7] = gmdate("Y-m-d\TH:i:s")."+00:00";
                            $c1nt1++;

                            if ( !isset($_GET["sort"]) )
                                    $order_clause = "order by sort_order";
                            else
                            {
                                    //verify $_GET["sort"]
                                    switch ($_GET["sort"]){
                                            default:
                                                    $_GET["sort"] = "name";
                                            case 'name':
                                            case 'Price':
                                            case 'customers_rating':
                                                    break;
                                    }

                                    $order_clause = " order by ".$_GET["sort"];
                                    if ( isset($_GET["direction"]) )
                                    {
                                            if ( !strcmp( $_GET["direction"] , "DESC" ) )
                                                    $order_clause .= " DESC ";
                                            else
                                                    $order_clause .= " ASC ";
                                    }
                            }

                            $sql = "
                                    select productID, name, Price, in_stock, date_modified from ".PRODUCTS_TABLE.
                                    " where categoryID=".$row[0]." and Price>=0 and enabled=1 ".
                                    $order_clause."
                            ";
                            //add products
                            $q = db_query( $sql ) or die (db_error());
                            while ($row1 = db_fetch_row($q))
                            {
                                    if ($row1[2] <= 0)
                                            $row1[2]= "n/a";
                                    else
                                            $row1[2] = show_price($row1[2]);

                                    //add product to the output
                                    $out1[$c1nt1][0] = $row1[0];
                                    $out1[$c1nt1][1] = $row1[1];
                                    $out1[$c1nt1][2] = $level;
                                    $out1[$c1nt1][3] = "FFFFFF";
                                    $out1[$c1nt1][4] = 1; //0 is for category, 1 - product
                                    $out1[$c1nt1][5] = $row1[2];
                                    $out1[$c1nt1][6] = $row1[3];
                                    $out1[$c1nt1][7] = str_replace(" ", "T", $row1[4])."+00:00";
                                    $c1nt1++;
                            }

                            //process all subcategories
                            $sub_out1 = pricessCategories_site($row[0], $level+1);

                            //add $sub_out1 to the end of $out1
                            for ($j=0; $j<count($sub_out1); $j++)
                            {
                                    $out1[] = $sub_out1[$j];
                                    $c1nt1++;
                            }
                     }

                    return $out1;

            } //pricessCategories_site
           
            function news_site(){
                $sql = "select NID, add_date from ".NEWS_TABLE;
                //add news
                $q = db_query( $sql ) or die (db_error());
                $news = array();
                while ($row1 = db_fetch_row($q)){
                    $news[] = array($row1[0],0,0,0,2,0,0,$row1[1].gmdate("\TH:i:s")."+00:00"); // 2 - news record
                }
                return $news;
            }

            function articles_site(){
                $sql = "select aux_page_ID from ".AUX_PAGES_TABLE;
                //add articles
                $q = db_query( $sql ) or die (db_error());
                $articles = array();
                while ($row1 = db_fetch_row($q)){
                    $articles[] = array($row1[0],0,0,0,3,0,0,gmdate("Y-m-d\TH:i:s")."+00:00"); // 3 - article
                }
                return $articles;

            }

            function sitemap_replace($str){
               $str=str_replace("&", "&amp;",$str);
               $str=str_replace("'", "&apos;",$str);
               $str=str_replace("\"", "&quot;",$str);
               $str=str_replace(">", "&gt;",$str);
               $str=str_replace("<", "&lt;",$str);
               return $str;
            }

            if ($_GET["sub"]=="site_map")
            {
                    $pricelist_elements = pricessCategories_site(1, 0);
                    $arr = news_site(); foreach($arr as $item) $pricelist_elements[] = $item;
                    $arr = articles_site(); foreach($arr as $item) $pricelist_elements[] = $item;
                    $smarty->assign("pricelist_elements", $pricelist_elements);
                    $smarty->assign("admin_sub_dpt", "reports_site_map.tpl.html");
             if (isset($_GET["creat"]) && $_GET["creat"]=="yes")
              {
                $text="www";
                $smarty->assign("text", $text);
                $smarty->assign("creat", "yes");
                $sitemap_file = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
                $sitemap_file .= " <urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\r\n" ; //xml Google
                 for($i=0;$i<count($pricelist_elements);$i++)
                  {
                   if ($i==0)
                    {
                     $arr1[0]="http://".$_SERVER["SERVER_NAME"]."/index.php";
                     $arr1[1]="http://".$_SERVER["SERVER_NAME"]."/index.php?show_pricelist=yes";
                     $arr1[2]="http://".$_SERVER["SERVER_NAME"]."/index.php?links_exchange=yes";
                     $arr1[3]="http://".$_SERVER["SERVER_NAME"]."/index.php?news=yes";
                      for($j=0;$j<4;$j++)
                       {
                         $spec = sitemap_replace($arr1[$j]);

                         $sitemap_file .= "  <url> \r\n";
                         $sitemap_file .= "   <loc>$spec</loc>\r\n";
                         $sitemap_file .= "   <lastmod>".$pricelist_elements[$i][7]."</lastmod>\r\n";
                         $sitemap_file .= "   <changefreq>weekly</changefreq>\r\n";
                         if ($j==0)
                           $sitemap_file .= "   <priority>1.0</priority>\r\n";
                         if ($j==1)
                           $sitemap_file .= "   <priority>0.8</priority>\r\n";
                         if ($j==2)
                           $sitemap_file .= "   <priority>0.6</priority>\r\n";
                         if ($j==3)
                           $sitemap_file .= "   <priority>0.7</priority>\r\n";
                         $sitemap_file .= "  </url>\r\n";
                       }
                    }
                   if(CONF_MOD_REWRITE == 1){
				   //if(1 == 1){
                       if ($pricelist_elements[$i][4]==1)
                         $arr="http://".$_SERVER["SERVER_NAME"]."/product_".$pricelist_elements[$i][0].".html";
                       elseif($pricelist_elements[$i][4]==0)
                         $arr="http://".$_SERVER["SERVER_NAME"]."/category_".$pricelist_elements[$i][0].".html";
                       elseif($pricelist_elements[$i][4]==2)
                         $arr="http://".$_SERVER["SERVER_NAME"]."/show_news_".$pricelist_elements[$i][0].".html";
                       elseif($pricelist_elements[$i][4]==3)
                         $arr="http://".$_SERVER["SERVER_NAME"]."/page_".$pricelist_elements[$i][0].".html";
                   }
                   else{
                       if ($pricelist_elements[$i][4]==1)
                         $arr="http://".$_SERVER["SERVER_NAME"]."/index.php?productID=";
                       elseif($pricelist_elements[$i][4]==0)
                         $arr="http://".$_SERVER["SERVER_NAME"]."/index.php?categoryID=";
                       elseif($pricelist_elements[$i][4]==2)
                         $arr="http://".$_SERVER["SERVER_NAME"]."/index.php?fullnews=";
                       elseif($pricelist_elements[$i][4]==3)
                         $arr="http://".$_SERVER["SERVER_NAME"]."/index.php?show_aux_page=";

                       $arr.=$pricelist_elements[$i][0];
                   }

                   $spec=sitemap_replace($arr);

                   $sitemap_file .= "  <url> \r\n";
                   $sitemap_file .= "   <loc>$spec</loc>\r\n";
                   $sitemap_file .= "   <lastmod>".$pricelist_elements[$i][7]."</lastmod>\r\n";
                   $sitemap_file .= "   <changefreq>weekly</changefreq>\r\n";
                   if ($pricelist_elements[$i][4]==1)
                     $sitemap_file .= "   <priority>0.8</priority>\r\n";
                   else
                     $sitemap_file .= "   <priority>0.7</priority>\r\n";
                   $sitemap_file .= "  </url>\r\n";
                  }
                  $sitemap_file .= "</urlset>\r\n";
              
                  $file_sitemap="sitemap.xml";
                  $handle = fopen($file_sitemap, "w");
                  if (fwrite($handle, $sitemap_file) === FALSE)
                    { $smarty->assign("message", "Error creating sitemap file: http://".$_SERVER["SERVER_NAME"]."/".$file_sitemap); }
               else
                    { $smarty->assign("message", "Sitemap file  http://".$_SERVER["SERVER_NAME"]."/".$file_sitemap."   created "); }
                  fclose($handle);
              }
            }

    ?>
