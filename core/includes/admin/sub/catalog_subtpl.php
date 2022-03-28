<?php
#####################################
# ShopCMS: ������ ��������-��������
# Copyright (c) 2008 by ADGroup
# http://shopcms.ru
#####################################

function getFolder_tpl()
{
	$f_name = rand(1000, 99999);
	if (file_exists("pictures_tpl/".$f_name))
		return getFolder_tpl();
	
	return $f_name;
}

function GetAllTpl()
{
	$data = array();
        //babenkoma
	$q = db_query("SELECT tplID, tpl_name FROM ".PRODUCTS_SUBTPL_TABLE." ORDER BY tpl_name ASC");
        //babenkoma\
	while($r = db_fetch_row($q))
	{
		//$q1 = db_query("SELECT COUNT(*) FROM ".PRODUCTS_TABLE." WHERE tplID='".(int)$r['tplID']."'");
		//$r1 = db_fetch_row($q1);
		$r['count_ptpl'] = 0;

		//$q2 = db_query("SELECT COUNT(*) FROM ".PRODUCTS_TABLE." WHERE tplID='".(int)$r['tplID']."' AND upd_tpl=1");
		//$r2 = db_fetch_row($q2);
		$r['count_ptpl_noupd'] = 0;

		$data[] = $r;
	}
	return $data;
}

function GetTplByID($id)
{       //babenkoma
	$q = db_query("SELECT * FROM ".PRODUCTS_SUBTPL_TABLE." WHERE tplID='".(int)$id."'");
        //babenkoma\
	$r = db_fetch_row($q);
	return $r;
}

function AddTpl()
{
		$tpl_name          = xEscSQL(ToText($_POST['tpl_name']));
        $name              = xEscSQL(ToText($_POST['name']));
        $description       = xEscSQL($_POST['description']);
        $brief_description = xEscSQL($_POST['brief_description']);
        $product_code      = xEscSQL(ToText($_POST['product_code']));
        $meta_description  = xEscSQL(ToText($_POST['meta_description']));
        $meta_keywords     = xEscSQL(ToText($_POST['meta_keywords']));
        $title             = xEscSQL(ToText($_POST['title']));
        $sub_title         = xEscSQL(ToText($_POST['sub_title']));
        
        //babenkoma
        //$parentTplID = $_POST['parent_tpl'];
        //babenkoma\
        
        if ( isset($_POST['free_shipping']) )
                $free_shipping = 1;
        else
                $free_shipping = 0;

		$Price = (float)$_POST['price'];
		$list_price = (float)$_POST['list_price'];
		$in_stock = (int)$_POST['in_stock'];
		$sort_order = (int)$_POST['sort_order'];
				
        $classID = "NULL";
        $weight = (float)$_POST['weight'];
        $min_order_amount = (int)$_POST['min_order_amount'];
        if ( $min_order_amount == 0 )
                $min_order_amount = 1;

        $shipping_freight = (float)$_POST['shipping_freight'];

        if ( trim($name) == "" ) $name = "?";
		
		$kolvo_pictures = (int)$_POST['kolvo_pictures'];
		$folder_pictures = xEscSQL($_POST['folder_pictures']);
		//babenkoma
		db_query("INSERT INTO ".PRODUCTS_SUBTPL_TABLE.
                " ( tpl_name, name, description, brief_description, ".
                "        Price, list_price, in_stock, product_code, sort_order, weight, meta_description, meta_keywords, free_shipping, min_order_amount, shipping_freight, title, kolvo_pictures, folder_pictures, sub_title) ".
                " VALUES ('".$tpl_name."', '".$name."','".$description."', '".$brief_description."', '".
                                $Price."', '".$list_price."', '".$in_stock."', '".$product_code."', '".$sort_order."', '".$weight."', '".$meta_description."', '".$meta_keywords."', '".$free_shipping."', '".$min_order_amount."', '".$shipping_freight."', '".$title."', '".$kolvo_pictures."', '".$folder_pictures."', '".$sub_title."');" );
                //babenkoma\" 
        $insert_id = db_insert_id();
		
		$folder = "pictures_tpl/".$folder_pictures;
		if (!file_exists($folder))
			mkdir($folder);
        
        $description_to_file = strtr($_POST['description'], array('?' => '&#9679;', '{' => '{$'));
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/design/user/new/tpl/'.$insert_id.'.tpl.html', $description_to_file);
		
        return $insert_id;
}

function UpdateTpl()
{ 
		db_query("UPDATE ".PRODUCTS_TABLE." SET upd_tpl=0 WHERE tplID='".(int)$_GET['edit']."'");

                $tpl_name          = xEscSQL(ToText($_POST['tpl_name']));
                $name              = xEscSQL(ToText($_POST['name']));
                $description       = xEscSQL($_POST['description']);
                $brief_description = xEscSQL($_POST['brief_description']);
                $product_code      = xEscSQL(ToText($_POST['product_code']));
                $meta_description  = xEscSQL(ToText($_POST['meta_description']));
                $meta_keywords     = xEscSQL(ToText($_POST['meta_keywords']));
                $title             = xEscSQL(ToText($_POST['title']));
                $sub_title         = xEscSQL(ToText($_POST['sub_title']));//babenkoma
                $seo_gen           = isset($_POST['seo_gen'])? intval($_POST['seo_gen']):0;
		$Price = (float)$_POST['price'];
		$list_price = (float)$_POST['list_price'];
		$in_stock = (int)$_POST['in_stock'];
		$sort_order = (int)$_POST['sort_order'];
		
                //babenkoma
                //$parentTplID = $_POST['parent_tpl'];
                //babenkoma\
                
        $weight = (float)$_POST['weight'];
        $min_order_amount = (int)$_POST['min_order_amount'];
        if ( $min_order_amount == 0 )
                $min_order_amount = 1;

        $shipping_freight = (float)$_POST['shipping_freight'];

        if ( trim($name) == "" ) $name = "?";
		
                  
		$kolvo_pictures = (int)$_POST['kolvo_pictures'];
		$folder_pictures = xEscSQL($_POST['folder_pictures']);
                //babenkoma
		db_query("UPDATE ".PRODUCTS_SUBTPL_TABLE.
                " SET tpl_name='".$tpl_name."', ".
				" name='".$name."', ".
				" description='".$description."', ".
				" brief_description='".$brief_description."', ".
                                " Price='".$Price."', ".
				" list_price='".$list_price."', ".
				" in_stock='".$in_stock."', ".
				" product_code='".$product_code."', ".
				" sort_order='".$sort_order."', ".
				" weight='".$weight."', ".
				" meta_description='".$meta_description."', ".
				" meta_keywords='".$meta_keywords."', ".
				" min_order_amount='".$min_order_amount."', ".
				" shipping_freight='".$shipping_freight."', ".
				" title='".$title."', ".
				" kolvo_pictures='".$kolvo_pictures."', ".
				" folder_pictures='".$folder_pictures."', ".
				" seo_gen='".$seo_gen."', ".
                                " sub_title='".$sub_title."' ".
				" WHERE tplID='".(int)$_GET['edit']."'");
                //babenkoma\
		if (isset($_POST['update_check']) && $_POST['update_check'])
			update_Tpl($_GET['edit']);
                if (isset($_POST['update_pictures']) && $_POST['update_pictures'])
                        update_pictures($_GET['edit']);
        
        
        $description_to_file = strtr($_POST['description'], array('?' => '&#9679;', '{' => '{$'));
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/design/user/new/tpl/'.(int)$_GET['edit'].'.tpl.html', $description_to_file);
            
                    
}

function update_pictures($id)
{
    $rs = GetTplByID($id);
    $z = db_query("SELECT productID, categoryID, tpl_city, tpl_val_1, tpl_val_2, tpl_val_3, tpl_val_4, tpl_val_5  FROM ".PRODUCTS_TABLE." WHERE tplID='".(int)$id."' AND upd_tpl!=1 ORDER BY productID ASC");
    while($pr = db_fetch_row($z))
    {            // exit("OK");
            // �������� ��������� ��������
            $folder_pictures    = xEscSQL(ToText($rs['folder_pictures']));
            $kolvo_pictures    = xEscSQL(ToText($rs['kolvo_pictures']));
            $pictures = rand_pictures_from_folder($rs['folder_pictures'], $rs['kolvo_pictures']);
           
            db_query("UPDATE ".PRODUCTS_TABLE." SET ".
                                "date_modified='".get_current_time()."', ".
                                "upd_tpl='1', ".
                                "kolvo_pictures = ".$kolvo_pictures.", ".
                                "folder_pictures = '".$folder_pictures."', ".
                                "pictures = '".$pictures."' ".
                        " WHERE productID='".(int)$pr['productID']."'");   
    }     
}


function update_Tpl($id)
{
	$rs = GetTplByID($id);
                                   
    preg_match_all('/\[(.*)\]/U', $rs['description'], $patterns);
    $q = mysql_query("SELECT productID FROM ".PRODUCTS_TABLE." WHERE tplID=".(int)$id." AND upd_tpl != 1 ORDER BY productID ASC") or die(mysql_error().'. ������ � ������ '.__LINE__);
    while($row = mysql_fetch_row($q)){
        $index = $tmp = array();
        foreach($patterns[1] as $k => $pattern){
            $tmp = explode('|', $pattern);
            $index[$k] = rand(0, count($tmp)-1);
        }
        $indexes = serialize($index);
        mysql_query("UPDATE ".PRODUCTS_TABLE." SET text_index = '$indexes', upd_tpl = 1 WHERE productID = $row[0]") or die(mysql_error().'. ������ � ������ '.__LINE__);	
    }

    //����� � ����� �� ������ �������� �� ����
    /*$parts = unserialize('a:7:{i:0;i:3;i:1;i:2;i:2;i:0;i:3;i:1;i:4;i:1;i:5;i:0;i:6;i:0;}');
    foreach($parts as $k => $v){
        $str = $pattern[1][$k];
        $part = explode('|', $str);
        $to_replace['['.$str. ']'] = $part[$v];
    }*/
    /*$rs = GetTplByID($id);
	$z = db_query("SELECT productID, categoryID, tpl_city, tpl_val_1, tpl_val_2, tpl_val_3, tpl_val_4, tpl_val_5  FROM ".PRODUCTS_TABLE." WHERE tplID='".(int)$id."' AND upd_tpl!=1 ORDER BY productID ASC");
	while($pr = db_fetch_row($z))
	{
		$r = array();
		$r = $rs;
		$t = $pr['tpl_city'];

		$r['title'] = str_replace("{city}", $t, $r['title']);
		$r['name'] = str_replace("{city}", $t, $r['name']);
		$r['description'] = str_replace("{city}", $t, $r['description']);
		$r['brief_description'] = str_replace("{city}", $t, $r['brief_description']);
		$r['meta_description'] = str_replace("{city}", $t, $r['meta_description']);
		$r['meta_keywords'] = str_replace("{city}", $t, $r['meta_keywords']);
		
		if ($pr['tpl_val_1']!="")
		{
			$r['title'] = str_replace("{val_1}", $pr['tpl_val_1'], $r['title']);
			$r['name'] = str_replace("{val_1}", $pr['tpl_val_1'], $r['name']);
			$r['description'] = str_replace("{val_1}", $pr['tpl_val_1'], $r['description']);
			$r['brief_description'] = str_replace("{val_1}", $pr['tpl_val_1'], $r['brief_description']);
			$r['meta_description'] = str_replace("{val_1}", $pr['tpl_val_1'], $r['meta_description']);
			$r['meta_keywords'] = str_replace("{val_1}", $pr['tpl_val_1'], $r['meta_keywords']);
		}
		if ($pr['tpl_val_2']!="")
		{
			$r['title'] = str_replace("{val_2}", $pr['tpl_val_2'], $r['title']);
			$r['name'] = str_replace("{val_2}", $pr['tpl_val_2'], $r['name']);
			$r['description'] = str_replace("{val_2}", $pr['tpl_val_2'], $r['description']);
			$r['brief_description'] = str_replace("{val_2}", $pr['tpl_val_2'], $r['brief_description']);
			$r['meta_description'] = str_replace("{val_2}", $pr['tpl_val_2'], $r['meta_description']);
			$r['meta_keywords'] = str_replace("{val_2}", $pr['tpl_val_2'], $r['meta_keywords']);
		}
		if ($pr['tpl_val_3']!="")
		{
			$r['title'] = str_replace("{val_3}", $pr['tpl_val_3'], $r['title']);
			$r['name'] = str_replace("{val_3}", $pr['tpl_val_3'], $r['name']);
			$r['description'] = str_replace("{val_3}", $pr['tpl_val_3'], $r['description']);
			$r['brief_description'] = str_replace("{val_3}", $pr['tpl_val_3'], $r['brief_description']);
			$r['meta_description'] = str_replace("{val_3}", $pr['tpl_val_3'], $r['meta_description']);
			$r['meta_keywords'] = str_replace("{val_3}", $pr['tpl_val_3'], $r['meta_keywords']);
		}
		if ($pr['tpl_val_4']!="")
		{
			$r['title'] = str_replace("{val_4}", $pr['tpl_val_4'], $r['title']);
			$r['name'] = str_replace("{val_4}", $pr['tpl_val_4'], $r['name']);
			$r['description'] = str_replace("{val_4}", $pr['tpl_val_4'], $r['description']);
			$r['brief_description'] = str_replace("{val_4}", $pr['tpl_val_4'], $r['brief_description']);
			$r['meta_description'] = str_replace("{val_4}", $pr['tpl_val_4'], $r['meta_description']);
			$r['meta_keywords'] = str_replace("{val_4}", $pr['tpl_val_4'], $r['meta_keywords']);
		}
		if ($pr['tpl_val_5']!="")
		{
			$r['title'] = str_replace("{val_5}", $pr['tpl_val_5'], $r['title']);
			$r['name'] = str_replace("{val_5}", $pr['tpl_val_5'], $r['name']);
			$r['description'] = str_replace("{val_5}", $pr['tpl_val_5'], $r['description']);
			$r['brief_description'] = str_replace("{val_5}", $pr['tpl_val_5'], $r['brief_description']);
			$r['meta_description'] = str_replace("{val_5}", $pr['tpl_val_5'], $r['meta_description']);
			$r['meta_keywords'] = str_replace("{val_5}", $pr['tpl_val_5'], $r['meta_keywords']);
		}
		
		$categoryID = (int)$pr['categoryID'];
		if ($categoryID>1)
		{
			$name              = xEscSQL(ToText($r['name']));
			$description       = xEscSQL($r['description']);
			$Price = (float)$r['Price'];
			$in_stock = (int)$r['in_stock'];
			$sort_order = (int)$r['sort_order'];
			$brief_description = xEscSQL($r['brief_description']);
			$list_price = (float)$r['list_price'];
			$product_code      = xEscSQL(ToText($r['product_code']));
			$meta_description  = xEscSQL(ToText($r['meta_description']));
			$meta_keywords     = xEscSQL(ToText($r['meta_keywords']));
			$title             = xEscSQL(ToText($r['title']));
            $folder_pictures    = xEscSQL(ToText($r['folder_pictures']));
            $kolvo_pictures    = xEscSQL(ToText($r['kolvo_pictures']));
            
			$weight = (float)$r['weight'];
			$min_order_amount = (int)$r['min_order_amount'];
			if ( $min_order_amount == 0 ) $min_order_amount = 1;

			$shipping_freight = (float)$r['shipping_freight'];
			if ( trim($name) == "" ) $name = "?";

			db_query("UPDATE ".PRODUCTS_TABLE." SET ".
                                "name='".$name."', ".
                                "Price='".$Price."', ".
                                "description='".$description."', ".
                                "in_stock='".$in_stock."', ".
                                "brief_description='".$brief_description."', ".
                                "list_price='".$list_price."', ".
                                "product_code='".$product_code."', ".
                                "sort_order='".$sort_order."', ".
                                "date_modified='".get_current_time()."', ".
								"upd_tpl='1', ".
                                "weight=".$weight.", meta_description='".$meta_description."', ".
                                "meta_keywords='".$meta_keywords."', ".
                                "min_order_amount = ".$min_order_amount.", ".
                                "shipping_freight = ".$shipping_freight.", ".
                                "kolvo_pictures = ".$kolvo_pictures.", ".
                                "folder_pictures = '".$folder_pictures."', ".
                                
                                "title = '".$title."' ".
						" WHERE productID='".(int)$pr['productID']."'");
		}
	}*/
}

// ������� �������� ����������� $num_picture �������� �� ����� $folder
function rand_pictures_from_folder($folder, $num_picture){
    // �������� ������ �������� � �������� �����
    $pictures   = glob("./pictures_tpl/".$folder."/*.jpg");        
    
    // �������� ���� ������ �� ��� ������
    $pictures = array_map("basename", $pictures);  
    // ������������ ������ � ������� ������
    shuffle($pictures);   
    // �������� �� ������� ������ ���-�� ��������
    $pictures = array_slice($pictures, 0, $num_picture);
    // ����������� � ������     
    $pictures = implode(",", $pictures);  
    return  $pictures; 
}


function DeleteTpl($id)
{
    //babenkoma
    db_query("DELETE FROM ".PRODUCTS_SUBTPL_TABLE." WHERE tplID='".(int)$id."'");
    //babenkoma\
    unlink($_SERVER['DOCUMENT_ROOT'].'/design/user/new/tpl/'.(int)$id.'.tpl.html');
}


        //babenkoma
        if (!strcmp($sub, "subtpl"))
        //babenkoma\
        {
			if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(100,$relaccess))) //unauthorized
			{
				$smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
			} else {


                if ( isset($_GET["delete"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                            //babenkoma
                            Redirect( "admin.php?dpt=catalog&sub=subtpl&safemode=yes" );
                            //babenkoma\
                        }
                        DeleteTpl( $_GET["delete"] );
                        //babenkoma
                        Redirect( "admin.php?dpt=catalog&sub=subtpl" );
                        //babenkoma\
                }
	
                if ( isset($_GET["add_new"]) )
                {
                        if ( isset($_POST["save_product"]) && $_POST["save_product"] == 1)
                        {
                                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
								{
                                                                        //babenkoma
									Redirect( "admin.php?dpt=catalog&sub=subtpl&safemode=yes" );
                                                                        //babenkoma\                                                                        
                                                                }
                                $tplID = AddTpl();
                                //babenkoma
                                Redirect("admin.php?dpt=catalog&sub=subtpl");
                                //babenkoma\
                        }
						
						$product['Price'] = 0;
						$product['list_price'] = 0;
						$product['min_order_amount'] = 1;
						$product['sort_order'] = 0;
						$product['in_stock'] = 0;
						$product['weight'] = 0;
						$product['shipping_freight'] = 0;
						$product['kolvo_pictures'] = 1;
						$product['folder_pictures'] = getFolder_tpl();
                        //babenkoma
                        /*
                        $q = mysql_query("SELECT * FROM ".PRODUCTS_TPL_TABLE);
                        $parent_tpl = array();
                        while($row = mysql_fetch_array($q)){
                            if(!empty($row)) $parent_tpl[] = $row;
                        }
                        $smarty->assign("parent_tpl", $parent_tpl);
                         */
                        //babenkoma\
                        $smarty->assign( "product", $product );

                        $smarty->assign( "add_new", 1 );
                }
                else if ( isset($_GET["edit"]) )
                {
                        if ( isset($_POST["save_product"]) && $_POST["save_product"] == 1)
                        {
                                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect( "admin.php?dpt=catalog&sub=tpl&safemode=yes&edit=".$_GET["edit"] );
                                }

                                UpdateTpl();
                                //babenkoma
                                Redirect("admin.php?dpt=catalog&sub=subtpl");
                                //babenkoma\
                        }

                        $product = GetTplByID( $_GET["edit"] );
						if (file_exists("pictures_tpl/".$product['folder_pictures']))
						{
							$includes_dir = opendir("pictures_tpl/".$product['folder_pictures']);
							$file_count = 0;
							while ( ($inc_file = readdir($includes_dir)) != false )
							{
								if ($inc_file != "." && $inc_file != "..")
								{
									$file_count++;
								}
							}
							closedir($includes_dir);
						}
                                              
                        //babenkoma
                        /*
                        $q = mysql_query("SELECT * FROM ".PRODUCTS_TPL_TABLE);
                        $parent_tpl = array();
                        while($row = mysql_fetch_array($q)){
                            if(!empty($row)) $parent_tpl[] = $row;
                        }
                        $smarty->assign("parent_tpl", $parent_tpl);
                        */
                        //babenkoma\
						$smarty->assign("count_f", $file_count);
                        $smarty->assign( "product", $product );
                        $smarty->assign( "edit", (int)$_GET['edit'] );
                }
				elseif (isset($_GET['upd_prod']))
				{
					update_Tpl($_GET['upd_prod']);
                                        //babenkoma
					Redirect("admin.php?dpt=catalog&sub=subtpl");
                                        //babenkoma\
				}
                else
                {
                        $tpls = GetAllTpl();
                        $smarty->assign("tpls", $tpls);
                }

                //set sub-department template
                //babenkoma
                $smarty->assign("admin_subtpl_dpt", "catalog_subtpl.tpl.html");
                //babenkoma\
			}       
        }
?>