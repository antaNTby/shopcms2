<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) 2008 by ADGroup
# http://shopcms.ru
#####################################

function GetAllCatsTpl()
{
	$data = array();
	$q = db_query("SELECT id, name FROM ".CATEGORIES_TPL_TABLE." ORDER BY name ASC");
	while($r = db_fetch_row($q))
	{
	   $data[] = $r;
	}
	return $data;
}

function GetCatTplByID($id)
{
	$q = db_query("SELECT * FROM ".CATEGORIES_TPL_TABLE." WHERE id = ".(int)$id);
	$r = db_fetch_row($q);
	return $r;
}

function AddCatTpl()
{
		$name          = xEscSQL(ToText($_POST['name']));
        $description       = xEscSQL($_POST['desc']);
        $meta_description       = xEscSQL($_POST['meta_d']);
        $type       = xEscSQL($_POST['type']);
        $meta_keywords       = xEscSQL($_POST['meta_k']);
        $title      = xEscSQL($_POST['title']);
        $cat_h1      = xEscSQL($_POST['cat_h1']);
        $cat_h2_c       = xEscSQL($_POST['cat_h2_c']);
        $cat_h2_o       = xEscSQL($_POST['cat_h2_o']);
        $cat_h2_r       = xEscSQL($_POST['cat_h2_r']);
        $city      = xEscSQL($_POST['city']);
		//babenkoma 29.04.2013
		if (isset($_POST['seo_gen']) && $_POST['seo_gen'] == 1) {$seo_gen = $_POST['seo_gen'];} else {$seo_gen = 0;}
		db_query("INSERT INTO ".CATEGORIES_TPL_TABLE." SET
                name = '$name',
                description = '$description',
                meta_description = '$meta_description',
                meta_keywords = '$meta_keywords',
                title = '$title',
                cat_h1 = '$cat_h1',
                type = '$type',
                cat_h2_c = '$cat_h2_c',
                cat_h2_o = '$cat_h2_o',
                cat_h2_r = '$cat_h2_r',
                city = '$city',
				seo_gen = '$seo_gen'" );
 
		$insert_id = db_insert_id();
		//babenkoma\
		
        return $insert_id;
}

function UpdateCatTpl()
{
        $name          = xEscSQL(ToText($_POST['name']));
        $description       = xEscSQL($_POST['desc']);
        $meta_description       = xEscSQL($_POST['meta_d']);
        $type       = xEscSQL($_POST['type']);
        $meta_keywords       = xEscSQL($_POST['meta_k']);
        $title      = xEscSQL($_POST['title']);
        $cat_h1      = xEscSQL($_POST['cat_h1']);
        $cat_h2_c       = xEscSQL($_POST['cat_h2_c']);
        $cat_h2_o       = xEscSQL($_POST['cat_h2_o']);
        $cat_h2_r       = xEscSQL($_POST['cat_h2_r']);
        $city       = xEscSQL($_POST['city']);
		
		//babenkoma 29.04.2013
		if (isset($_POST['seo_gen']) && $_POST['seo_gen'] == 1) {$seo_gen = $_POST['seo_gen'];} else {$seo_gen = 0;}
        db_query("UPDATE ".CATEGORIES_TPL_TABLE." SET
                name = '$name',
                description = '$description',
                meta_description = '$meta_description',
                meta_keywords = '$meta_keywords',
                title = '$title',
                cat_h1 = '$cat_h1',
                type = '$type',
                cat_h2_c = '$cat_h2_c',
                cat_h2_o = '$cat_h2_o',
                cat_h2_r = '$cat_h2_r',
                city = '$city',
				seo_gen = '$seo_gen'
                WHERE id = ".(int)$_GET['edit']);
		
		if (!empty($description) && $seo_gen == 1) {
			
			
			preg_match_all('/\[(.*)\]/U', $description, $patterns);
			$q = mysql_query("SELECT categoryID FROM ".CATEGORIES_TABLE." WHERE tpl_id=".(int)$_GET['edit']) or die(mysql_error().'. Ошибка в строке '.__LINE__);
			while($row = mysql_fetch_row($q)){
				$index = $tmp = array();
				foreach($patterns[1] as $k => $pattern){
					$tmp = explode('|', $pattern);
					$index[$k] = rand(0, count($tmp)-1);
				}
				$indexes = serialize($index);
				mysql_query("UPDATE ".CATEGORIES_TABLE." SET seo_index = '$indexes' WHERE categoryID = $row[0]") or die(mysql_error().'. Ошибка в строке '.__LINE__);	
			}
			
		}
		//babenkoma\
}

function DeleteCatTpl($id)
{
	db_query("DELETE FROM ".CATEGORIES_TPL_TABLE." WHERE id = ".(int)$id);
}

        if (!strcmp($sub, "cat_tpl"))
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(100,$relaccess))) //unauthorized
        {
            $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
        } else {

                if ( isset($_GET["delete"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect( "admin.php?dpt=catalog&sub=cat_tpl&safemode=yes" );
                        }
                        DeleteCatTpl( $_GET["delete"] );
                        Redirect( "admin.php?dpt=catalog&sub=cat_tpl" );
                }
	
                if ( isset($_GET["add_new"]) )
                {
                        if ( isset($_POST["save_cat"]) && $_POST["save_cat"] == 1)
                        {
                                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
								{
									Redirect( "admin.php?dpt=catalog&sub=cat_tpl&safemode=yes" );
								}
								
                                $tplID = AddCatTpl();
                                Redirect("admin.php?dpt=catalog&sub=cat_tpl");
                        }
                        $smarty->assign( "add_new", 1 );
                }
                else if ( isset($_GET["edit"]) )
                {
                        if ( isset($_POST["save_cat"]) && $_POST["save_cat"] == 1)
                        {
                                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect( "admin.php?dpt=catalog&sub=cat_tpl&safemode=yes&edit=".$_GET["edit"] );
                                }

                                UpdateCatTpl();
                                Redirect("admin.php?dpt=catalog&sub=cat_tpl");
                        }

                        $cat = GetCatTplByID( $_GET["edit"] );
                        $smarty->assign( "cat", $cat );
                        $smarty->assign( "edit", (int)$_GET['edit'] );
                }
                else
                {
                        $tpls = GetAllCatsTpl();
                        $smarty->assign("tpls", $tpls);
                }
                
                function addCat($tpl_id, $name, $parent = 1, $count, $sort = 10001, $cat_h1 = '')
                {
                	$q = db_query("SELECT title, meta_description, meta_keywords, city, cat_h2_o FROM ".CATEGORIES_TPL_TABLE." WHERE id = $tpl_id");
                    $meta = db_fetch_row($q);
                    
                    if(empty($cat_h1)){
                        $title = str_replace("{city}", $meta['city'], $meta['title']);	
                        $meta_description = str_replace("{city}", $meta['city'], $meta['meta_description']);	
                        $meta_keywords = str_replace("{city}", $meta['city'], $meta['meta_keywords']);
						$name_cat = 'Карты '.$name;
						$cat_h1 = $name;
						$cat_h2_o = 'Описание карт '.$name;
                    } else {
                        $name_cat = $name;
						$title = str_replace("{city}", $cat_h1, $meta['title']);	
                        $meta_description = str_replace("{city}", $cat_h1, $meta['meta_description']);	
                        $meta_keywords = str_replace("{city}", $cat_h1, $meta['meta_keywords']);
                        $cat_h2_o = str_replace("{city}", $cat_h1, $meta['cat_h2_o']);
                    }	
                        
                    db_query("INSERT INTO ".CATEGORIES_TABLE." SET
                                name = '$name_cat',
                                parent = $parent,
                                sort_order = $sort,
                               	products_count_admin = $count,
                 	 	        products_count  = $count,
                                allow_products_comparison = 0,
                                title = '$title',
                                cat_h1 = '$cat_h1',
                                cat_h2_o = '$cat_h2_o',
                                meta_description = '$meta_description',
                                meta_keywords = '$meta_keywords',
                               	show_subcategories_products = 0, 
                                tpl_id = $tpl_id
                            ");
                    $insert_id = db_insert_id();

                    $categoryID = $insert_id; 
                    $UID = '/'.routeTransliteration(xToText(trim('Карты '.$name)));

                    $q = mysql_query("SELECT COUNT(*) FROM ".CATEGORIES_TABLE." WHERE UID='$UID'") or die(mysql_error().'. Ошибка в строке '.__LINE__);
                    $cnt = @mysql_result($q, 0);
                    if($cnt > 0){
                        $UID = $UID.'_'.$categoryID;
                    } 
                      
                    db_query('UPDATE '.CATEGORIES_TABLE." SET UID='".$UID."' WHERE categoryID=".$categoryID);
                    routeDropUrl($UID);
                    routeAddUrl($UID, 'index.php?categoryID='.$categoryID);
                    
                    $new_cat['id'] = $insert_id;
                    $new_cat['UID'] = $UID;
                    
                    return $new_cat;
                }
                
                function rand_pictures_from_folder($folder, $num_picture){
                    // Получаем список картинок в заданной папке
                    $pictures   = glob($_SERVER['DOCUMENT_ROOT']."/pictures_tpl/".$folder."/*.jpg");        
                    
                    // Обрезаем пути файлов до имён файлов
                    $pictures = array_map("basename", $pictures);  
                    // Перемешиваем массиы с именами файлов
                    shuffle($pictures);   
                    // Получаем из массива нужное кол-во картинок
                    $pictures = array_slice($pictures, 0, $num_picture);
                    // Упаковываем в строку     
                    $pictures = implode(",", $pictures);  
                    
                    return  $pictures; 
                }
                
                
                function AddProductByTplNew($tpl_id, $tpl_city, $tpl_val_1, $categoryID)
                {
            	    $t_city = $tpl_city;
            		$q = db_query("SELECT * FROM ".PRODUCTS_TPL_TABLE." WHERE tplID=$tpl_id");
            		if ($r = db_fetch_row($q))
            		{
            			$r['title'] = str_replace("{city}", $t_city, $r['title']);
            			$r['name'] = str_replace("{city}", $t_city, $r['name']);
            			$r['description'] = str_replace("{city}", $t_city, $r['description']);
            			$r['brief_description'] = str_replace("{city}", $t_city, $r['brief_description']);
            			$r['meta_description'] = str_replace("{city}", $t_city, $r['meta_description']);
            			$r['meta_keywords'] = str_replace("{city}", $t_city, $r['meta_keywords']);
            
            			$r['title'] = str_replace("{val_1}", $tpl_val_1, $r['title']);
                        
            			if ($categoryID>1)
            			{
            				$name              = xEscSQL(ToText($r['name']));
            				$description       = '';
            				$Price = (float)$r['Price'];
            				$in_stock = (int)$r['in_stock'];
            				$sort_order = (int)$r['sort_order'];
            				$brief_description = xEscSQL($r['brief_description']);
            				$list_price = (float)$r['list_price'];
            				$product_code      = xEscSQL(ToText($r['product_code']));
            				$eproduct_filename = "";
            				$eproduct_available_days = 0;
            				$eproduct_download_times =0;
            				$meta_description  = xEscSQL(ToText($r['meta_description']));
            				$meta_keywords     = xEscSQL(ToText($r['meta_keywords']));
            				$title             = xEscSQL(ToText($r['title']));
            				$free_shipping = 0;
            				$classID = "NULL";
            
            				$weight = (float)$r['weight'];
            				$min_order_amount = (int)$r['min_order_amount'];
            				if ( $min_order_amount == 0 ) $min_order_amount = 1;
            
            				$shipping_freight = (float)$r['shipping_freight'];
            				if ( trim($name) == "" ) $name = "?";
            				db_query("INSERT INTO ".PRODUCTS_TABLE.
            					" ( categoryID, name, description,".
            					"        customers_rating, Price, in_stock, ".
            					"        customer_votes, items_sold, enabled, ".
            					"        brief_description, list_price, ".
            					"        product_code, sort_order, date_added, ".
            					"         eproduct_filename, eproduct_available_days, ".
            					"         eproduct_download_times, ".
            					"        weight, meta_description, meta_keywords, ".
            					"        free_shipping, min_order_amount, shipping_freight, classID, title, tplID, tpl_city, tpl_val_1, tpl_val_2, tpl_val_3, tpl_val_4, tpl_val_5, upd_tpl, folder_pictures ".
            				" ) ".
            				" VALUES ('".
            					$categoryID."','".
            					$name."','".
            					$description."', ".
            					"0, '".
            					$Price."', '".
            					$in_stock."', ".
            					" 0, 0, 1, '".
            					$brief_description."', '".
            					$list_price."', '".
            					$product_code."', '".$sort_order."', '".
            					get_current_time()."',  '".
            					$eproduct_filename."', ".
            					$eproduct_available_days.", ".
            					$eproduct_download_times.",  ".
            					$weight.", ".
            					"'".$meta_description."', ".
            					"'".$meta_keywords."', ".
            					$free_shipping.", ".
            					$min_order_amount.", ".
            					$shipping_freight.", ".
            					$classID.", '".
            					$title."', '".(int)$tpl_id."', '".xEscSQL($t_city)."', '".xEscSQL($tpl_val_1)."', '".xEscSQL($val_2)."', '".xEscSQL($val_3)."', '".xEscSQL($val_4)."', '".xEscSQL($val_5)."', 1, '".$r['folder_pictures'].
            				"');" );
            				$insert_id = db_insert_id();
                            
                            $category = catGetCategoryById($categoryID);
                            $replace = array('/' => '_', '\\' => '_');
                            $product = GetProduct($insert_id);
                            $UID = '/'.strtr(routeTransliteration(xToText(trim($r['name']))), $replace);
                            $q = mysql_query("SELECT COUNT(*) FROM ".PRODUCTS_TABLE." WHERE UID='$UID'") or die(mysql_error().'. Ошибка в строке '.__LINE__);
                            $cnt = @mysql_result($q, 0);
                            if($cnt > 1){
                                $UID = $UID.'_'.$insert_id;
                            }  
                                $UID = routeGetUID($UID, 'index.php?productID='.$insert_id);
                                
                                db_query('UPDATE '.PRODUCTS_TABLE." SET UID='".$UID."' WHERE productID=".$insert_id);
                                routeDropOtherUrl($UID, 'index.php?productID='.$insert_id);
                                routeAddUrl($UID, 'index.php?productID='.$insert_id);
                            
            				//Рекомендуемые
            				db_query(" update ".RELATED_PRODUCTS_TABLE."_v2 set Owner=".$insert_id." WHERE Owner='0'");
            				db_query(" update ".RELATED_PRODUCTS_TABLE." set Owner=".$insert_id." WHERE Owner='0'");
            				
                            
            			    $pictures = rand_pictures_from_folder($r['folder_pictures'], $r['kolvo_pictures']);
                            db_query("update ".PRODUCTS_TABLE." set pictures = '$pictures', kolvo_pictures = '".$r['kolvo_pictures']."' where productID='".$insert_id."'");
 
                        }
            		}
                }
                 
               
               
                 
                 //Добавление стран   
                 if($_POST['add_country'] != ''){
                    $cat_id = $_POST['country_id'];
    
                    $country_id = $_POST['country'];
                    $country_name = $_POST['country_name'];
                    
                    $region_id = $_POST['region'];
                    
                    $area_id = $_POST['area'];
                    $city_id = $_POST['city'];
                    
                    $lists = $_POST['list'];
                    if(!empty($country_name) || $cat_id > 1){
                        if($cat_id > 1){
                            $country_cat['id'] = $cat_id;
                            $q = mysql_query("SELECT UID FROM ".CATEGORIES_TABLE." WHERE categoryID = $cat_id") or die(mysql_error().'. Ошибка в строке '.__LINE__);
                            $country_cat['UID'] = mysql_result($q, 0);    
                        } else {
                            $q = mysql_query("SELECT COUNT(*) FROM ".CATEGORIES_TABLE." WHERE name = '$country_name' AND parent = 135") or die(mysql_error().'. Ошибка в строке '.__LINE__);
                            $cnt = mysql_result($q, 0);
                            if($cnt == 0){
                                $country_cat = addCat($country_id, $country_name, 135, 7, 0);    
                                $tpl_val_1 = $country_cat['UID'];

                                $tpls_r = array(14, 16, 8, 17, 18, 19, 20);
                                
                                foreach($tpls_r as $tpl_id){
                                    AddProductByTplNew($tpl_id, $country_name, $tpl_val_1, $country_cat['id']);
                                }
                            } else {
                                $q = mysql_query("SELECT categoryID FROM ".CATEGORIES_TABLE." WHERE name = '$country_name' AND parent = 135") or die(mysql_error().'. Ошибка в строке '.__LINE__);
                                $country_cat['id'] = mysql_result($q, 0);
                            }     
                        }

                        if(strstr($lists, '*')){                    
                            $lists = explode("*", $lists);
                            foreach($lists as $list){
                                $regions = explode("\n", trim($list));
                                $region_name = $regions[0];
                                
                                if($country_cat['id'] > 0 && $region_name != ''){
                                    $q = mysql_query("SELECT COUNT(*) FROM ".CATEGORIES_TABLE." WHERE name = '$region_name' AND parent = {$country_cat['id']}") or die(mysql_error().'. Ошибка в строке '.__LINE__);
                                    $cnt = mysql_result($q, 0);
                                    if($cnt == 0){
                                        $region_cat = addCat($region_id, $region_name, $country_cat['id'], 6, 0);    
                                            $tpl_val_1 = '/'.$region_cat['UID'];
                                        
                                            $tpls_r = array(10, 12, 13, 5, 4, 11);
                                            
                                            foreach($tpls_r as $tpl_id){
                                                AddProductByTplNew($tpl_id, $region_name, $tpl_val_1, $region_cat['id']);
                                            }
                                    }                                    
                                    foreach($regions as $region){
                                        if($region != $regions[0])
                                        if(strstr($region, 'район')){
                                        $area = $region;
                                        $q = mysql_query("SELECT COUNT(*) FROM ".CATEGORIES_TABLE." WHERE name = '$area' AND parent = {$region_cat['id']}") or die(mysql_error().'. Ошибка в строке '.__LINE__);
                                        $cnt = mysql_result($q, 0);
                                        if($cnt == 0){
                                            $area_cat = addCat($area_id, $area, $region_cat['id'], 5, 0);
                                            $tpl_val_1 = '/'.$area_cat['UID'];
                                            
                                            $tpls_a = array(3, 9, 7, 15, 6);
                                    
                                            foreach($tpls_a as $tpl_id){
                                                AddProductByTplNew($tpl_id, $area, $tpl_val_1, $area_cat['id']);
                                            }
                                        }
                                    } else {
                                        $city = $region; 
                                        $q = mysql_query("SELECT COUNT(*) FROM ".CATEGORIES_TABLE." WHERE name = '$city' AND parent = {$region_cat['id']}") or die(mysql_error().'. Ошибка в строке '.__LINE__);
                                        $cnt = mysql_result($q, 0);
                                        if($cnt == 0){
                                            $city_cat = addCat($city_id, $city, $region_cat['id'], 2, 0);
                                            
                                            $tpl_val_1 = '/'.$city_cat['UID'];
                                            
                                            $tpls_c = array(2, 1);
                                    
                                            foreach($tpls_c as $tpl_id){
                                                AddProductByTplNew($tpl_id, $city, $tpl_val_1, $city_cat['id'], 2);
                                            }
                                        }
                                        }
                                    }
                                } 
                            }
                        } else {
                            $regions = explode("\n", $lists);
                            foreach($regions as $region){
                                if(strstr($region, 'район')){
                                            $area = $region;
                                            $q = mysql_query("SELECT COUNT(*) FROM ".CATEGORIES_TABLE." WHERE name = '$area' AND parent = {$country_cat['id']}") or die(mysql_error().'. Ошибка в строке '.__LINE__);
                                            $cnt = mysql_result($q, 0);
                                            if($cnt == 0){
                                                $area_cat = addCat($area_id, $area, $country_cat['id'], 5, 0);
                                                $tpl_val_1 = '/'.$area_cat['UID'];
                                                
                                                $tpls_a = array(3, 9, 7, 15, 6);
                                        
                                                foreach($tpls_a as $tpl_id){
                                                    AddProductByTplNew($tpl_id, $area, $tpl_val_1, $area_cat['id']);
                                                }
                                            }
                                        } else {
                                            $city = $region; 
                                            $q = mysql_query("SELECT COUNT(*) FROM ".CATEGORIES_TABLE." WHERE name = '$city' AND parent = {$country_cat['id']}") or die(mysql_error().'. Ошибка в строке '.__LINE__);
                                            $cnt = mysql_result($q, 0);
                                            if($cnt == 0){
                                                $city_cat = addCat($city_id, $city, $country_cat['id'], 2, 0);
                                                
                                                $tpl_val_1 = '/'.$city_cat['UID'];
                                                
                                                $tpls_c = array(2, 1);
                                        
                                                foreach($tpls_c as $tpl_id){
                                                    AddProductByTplNew($tpl_id, $city, $tpl_val_1, $city_cat['id'], 2);
                                                }
                                            }
                                        }
                            }
                            
                        } 
                    }
                 } else {               
					$cat_id = $_POST['cat_id'];
					
                    $region_id = $_POST['region'];
                    $region_name = $_POST['region_name'];
                    
                    $area_id = $_POST['area'];
                    $city_id = $_POST['city'];
                    
                    $lists = $_POST['list'];
                    /*$areas = $_POST['list_area'];
                    $cities = $_POST['list_city'];*/
   
                    if(!empty($region_name) || $cat_id > 1){
                        if($cat_id > 1){
                            $region_cat['id'] = $cat_id;
                            $q = mysql_query("SELECT UID FROM ".CATEGORIES_TABLE." WHERE categoryID = $cat_id") or die(mysql_error().'. Ошибка в строке '.__LINE__);
                            $region_cat['UID'] = mysql_result($q, 0);
                        } else{
                            $q = mysql_query("SELECT COUNT(*) FROM ".CATEGORIES_TABLE." WHERE name = '$region_name' AND parent = 1") or die(mysql_error().'. Ошибка в строке '.__LINE__);
                            $cnt = mysql_result($q, 0);
                            if($cnt == 0){
                                $region_cat = addCat(7, $region_name, 1, 6);    
                                $tpl_val_1 = '/'.$region_cat['UID'];
                            
                                $tpls_r = array(10, 12, 13, 5, 4, 11, 25);
                                
                                foreach($tpls_r as $tpl_id){
                                    AddProductByTplNew($tpl_id, $region_name, $tpl_val_1, $region_cat['id']);
                                }
                            }                    
                        }
                        
                        $lists = explode("\n", $lists);
                        if(count($lists) > 0 && $lists[0] !== ''){
                            foreach($lists as $list){
                                if(strstr($list, 'район')){
                                    $area = $list;
                                    $q = mysql_query("SELECT COUNT(*) FROM ".CATEGORIES_TABLE." WHERE name = '$area' AND parent = {$region_cat['id']}") or die(mysql_error().'. Ошибка в строке '.__LINE__);
                                    $cnt = mysql_result($q, 0);
                                    if($cnt == 0){
										$area_cat = addCat(7, $area, $region_cat['id'], 5);
                                        $tpl_val_1 = '/'.$area_cat['UID'];
                                        
                                        $tpls_a = array(3, 9, 7, 15, 6, 22, 24, 25);
                                
                                        foreach($tpls_a as $tpl_id){
                                            AddProductByTplNew($tpl_id, $area, $tpl_val_1, $area_cat['id']);
                                        }
                                    }
                                } else {
                                    $city = $list; 
                                    $q = mysql_query("SELECT COUNT(*) FROM ".CATEGORIES_TABLE." WHERE name = '$city' AND parent = {$region_cat['id']}") or die(mysql_error().'. Ошибка в строке '.__LINE__);
                                    $cnt = mysql_result($q, 0);
                                    if($cnt == 0){
                                        $city_cat = addCat(7, $city, $region_cat['id'], 2);
                                        
                                        $tpl_val_1 = '/'.$city_cat['UID'];
                                        
                                        $tpls_c = array(2, 1, 23, 25);
                                
                                        foreach($tpls_c as $tpl_id){
                                            AddProductByTplNew($tpl_id, $city, $tpl_val_1, $city_cat['id'], 2);
                                        }
                                    }
                                }
                            }
                        }
						
						///////////////
						if (isset($_FILES['file_csv'])) {
							$towns_str = file_get_contents($_FILES['file_csv']['tmp_name']);
							$towns_arr = explode("\n", $towns_str);
								
							function GetChangeText($text) {
								if (strpos($text, ' р-н') !== false) {$a = explode(' р-н', $text); return $b = array(0=>$a[0], 1=>'района '.$a[0]);} else
								if (strpos($text, ' с.') !== false) {$a = explode(' с.', $text); return $b = array(0=>$a[0], 1=> 'села '.$a[0]);} else
								if (strpos($text, ' пгт') !== false) {$a = explode(' пгт', $text); return $b = array(0=>$a[0], 1=> 'пгт '.$a[0]);} else
								if (strpos($text, ' ж/д ст.') !== false) {$a = explode(' ж/д ст.', $text); return $b = array(0=>$a[0], 1=> 'ж/д ст. '.$a[0]);} else
								if (strpos($text, ' ст.') !== false) {$a = explode(' ст.', $text); return $b = array(0=>$a[0], 1=> 'ст. '.$a[0]);} else
								if (strpos($text, ' рзд') !== false) {$a = explode(' рзд', $text); return $b = array(0=>$a[0], 1=> 'рзд '.$a[0]);} else
								if (strpos($text, ' г.') !== false) {$a = explode(' г.', $text); return $b = array(0=>$a[0], 1=> 'города '.$a[0]);} else
								if (strpos($text, ' хутор') !== false) {$a = explode(' хутор', $text); return $b = array(0=>$a[0], 1=> 'хутора '.$a[0]);} else
								if (strpos($text, ' ст-ца') !== false) {$a = explode(' ст-ца', $text); return $b = array(0=>$a[0], 1=> 'станицы '.$a[0]);} else
								if (strpos($text, ' п.') !== false) {$a = explode(' п.', $text); return $b = array(0=>$a[0], 1=> 'поселка '.$a[0]);} else
								if (strpos($text, ' аул') !== false) {$a = explode(' аул', $text); return $b = array(0=>$a[0], 1=> 'аула '.$a[0]);} else
								if (strpos($text, ' нп') !== false) {$a = explode(' нп', $text); return $b = array(0=>$a[0], 1=> 'нп '.$a[0]);} else
								if (strpos($text, ' дп') !== false) {$a = explode(' дп', $text); return $b = array(0=>$a[0], 1=> 'дп '.$a[0]);} else
								if (strpos($text, ' рп') !== false) {$a = explode(' рп', $text); return $b = array(0=>$a[0], 1=> 'рп '.$a[0]);} else
								if (strpos($text, ' б/о') !== false) {$a = explode(' б/о', $text); return $b = array(0=>$a[0], 1=> 'б/о '.$a[0]);} else
								if (strpos($text, ' ж/д казарма') !== false) {$a = explode(' ж/д казарма', $text); return $b = array(0=>$a[0], 1=> 'ж/д казармы '.$a[0]);} else
								if (strpos($text, ' д.') !== false) {$a = explode(' д.', $text); return $b = array(0=>$a[0], 1=> 'деревни '.$a[0]);} else
								if (strpos($text, ' ж/д оп') !== false) {$a = explode(' ж/д оп', $text); return $b = array(0=>$a[0], 1=> 'ж/д оп '.$a[0]);} else
								if (strpos($text, ' сдт тер') !== false) {$a = explode(' сдт тер', $text); return $b = array(0=>$a[0], 1=> 'сдт тер '.$a[0]);} else
								if (strpos($text, ' починок') !== false) {$a = explode(' починок', $text); return $b = array(0=>$a[0], 1=> 'починока '.$a[0]);} else {return $text;}
							}
											
							function LogWrite($text) {
								$file = $_SERVER['DOCUMENT_ROOT'].'/log.txt';
								if (file_exists($file)) {
									$in_text = file_get_contents($file);
								} else {
									$in_text = '';
								}
								$out_text = $in_text.$text."\n";
								file_put_contents('log.txt', $out_text);
							}
							$town_cat_id = 1;//область
							$town_id = 1;
							$flag = false;	
					
							foreach ($towns_arr as $value) {
								$towns = explode(';', $value);
								if ($towns[0] != '') {	//первая колонка							
									//Смена области
									$town_cat_id = $towns[0];
								} else if ($towns[1] != '') {		//вторая колонка							
									$get_cat = GetChangeText($towns[1]);									
									$sql_cat = mysql_query("SELECT `categoryID` FROM `".CATEGORIES_TABLE."` WHERE `name` LIKE '%".$get_cat[0]."%' AND `parent`=".(int)$town_cat_id." LIMIT 1");
									
									if (mysql_num_rows($sql_cat) > 0) {
										continue;	
									} else {
										//Добавление нового города в область
										$area_cat = addCat(7, $get_cat[1], $town_cat_id, 4);
										$tpl_val_1 = '/'.$area_cat['UID'];											
										$tpls_a = array(2, 1, 23, 25);									
										foreach($tpls_a as $tpl_id){
											AddProductByTplNew($tpl_id, $get_cat[1], $tpl_val_1, $area_cat['id']);
										}
									}
								} else if ($towns[2] != '') {		//третья колонка							
									//Смена района
									$get_ray = GetChangeText($towns[2]); //Имя текущего района
									$sql_cat = mysql_query("SELECT `categoryID` FROM `".CATEGORIES_TABLE."` WHERE `name` LIKE '".$get_ray[0]." район' AND `parent`=".(int)$town_cat_id." LIMIT 1");
									if (mysql_num_rows($sql_cat) > 0) {
										$town_arr = mysql_fetch_assoc($sql_cat);
										$town_id = $town_arr['categoryID'];  //id текущего района
										$flag = true;		//есть район								
									} else {
										LogWrite($get_cat[0]);
										$flag = false;   //нет района
										continue;
									}												
								} else if ($towns[3] != '') {	//четвертая колонка
									if ($flag) {
										$get_cat = GetChangeText($towns[3]);										
										$sql_cat = mysql_query("SELECT `categoryID` FROM `".CATEGORIES_TABLE."` WHERE `name` LIKE '%".$get_cat[0]."%' AND `name` NOT LIKE '".$get_ray[0]." район' AND `parent`=".(int)$town_cat_id." LIMIT 1");									
										if (mysql_num_rows($sql_cat) > 0) {
											//Перенос города (села, деревни и пр) из области в район
											$town_arr = mysql_fetch_assoc($sql_cat);
											mysql_query("UPDATE `".CATEGORIES_TABLE."` SET `parent`=".$town_id." WHERE `categoryID`=".$town_arr['categoryID']);
										} else {
											$sql_prod = mysql_query("SELECT `categoryID` FROM `".CATEGORIES_TABLE."` WHERE `name` LIKE '%".$get_cat[0]."%' AND `parent`=".$town_id." LIMIT 1");
											if (mysql_num_rows($sql_prod) > 0) {
												continue;	 
											} else {
												//Добавление нового населенного пункта в район
												$area_cat = addCat(7, $get_cat[1], $town_id, 4);
												$tpl_val_1 = '/'.$area_cat['UID'];											
												$tpls_a = array(2, 1, 23, 25);									
												foreach($tpls_a as $tpl_id){
													AddProductByTplNew($tpl_id, $get_cat[1], $tpl_val_1, $area_cat['id']);
												}
											}
										}
									}
								}			
							}

						}
						
						///////////////
                        
                        $q_prd_cnt = mysql_query("SELECT COUNT(*) FROM ".PRODUCTS_TABLE." WHERE categoryID IN ({$region_cat['id']}, {$area_cat['id']}, {$city_cat['id']})");
                        $prd_cnt = mysql_result($q_prd_cnt, 0);
                        db_query("UPDATE ".CATEGORIES_TABLE." SET products_count_admin = ".(int)$prd_cnt.', products_count = '.(int)$prd_cnt.' WHERE categoryID = '.$region_cat['id']);
    
                        Redirect('admin.php?dpt=catalog&sub=cat_tpl');
                    }
                
                }
                
                $query = db_query("SELECT id, name, type FROM ".
                                CATEGORIES_TPL_TABLE) or die (db_error());
                while ($res = db_fetch_row($query))
                {
                    $tpl[$res['type']][] = $res;
                    /*switch($res['type']){
                        case 'region' : $tpl['region'][] = $res;
                        break; 
                        case 'area' : $tpl['area'][] = $res;
                        break; 
                        case 'city' : $tpl['city'][] = $res;
                        break; 
                    }*/
                }
                 
                $smarty->assign("country_tpls",$tpl);
                unset($tpl['country']);
                $smarty->assign("cat_tpls",$tpl);
                //var_dump($cat_id);die;
                $cat_list = '<select name="cat_id" >';
                                        //fill the category combobox
                                        //$core_category = 1
										if (!isset($cat_id)) {$cat_id = 1;}
                                        $cats = catGetCategoryCList($cat_id);

                                        for ($i=0; $i<count($cats); $i++)
                                        {
                                                $cat_list .= "<option value=\"".$cats[$i]["categoryID"]."\"";
                                                $cat_list .= ">";
                                                for ($j=0;$j<$cats[$i]["level"];$j++) $cat_list .= "&nbsp;&nbsp;";
                                                        $cat_list .= $cats[$i]["name"];
                                                $cat_list .= "</option>";
}

                $cat_list .= '</select>';
                
                $smarty->assign("cat_list", $cat_list);
                
                $country_list = '<select name="country_id" >';
                                        //fill the category combobox
                                        $core_category = 135;
                                        
                                        $countries = catGetCategoryCList($core_category);

                                        for ($i=0; $i<count($countries); $i++)
                                        {
                                                $country_list .= "<option value=\"".$countries[$i]["categoryID"]."\"";
                                                $country_list .= ">";
                                                for ($j=0;$j<$countries[$i]["level"];$j++) $country_list .= "&nbsp;&nbsp;";
                                                        $country_list .= $countries[$i]["name"];
                                                $country_list .= "</option>";
}

                $country_list .= '</select>';
                
                $smarty->assign("country_list", $country_list);
                
                //set sub-department template
                $smarty->assign("admin_sub_dpt", "catalog_cat_tpl.tpl.html");
        }
        }
?>