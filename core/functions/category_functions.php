<?php
	#####################################
	# ShopCMS: Скрипт интернет-магазина
	# Copyright (c) by ADGroup
	# http://shopcms.ru
	#####################################

	function catInstall()
	{
		db_query("insert into ".CATEGORIES_TABLE." ( name, parent, categoryID ) values ( '".ADMIN_CATEGORY_ROOT."', NULL, 1 )");
	}

	function getcontentcat($categoryID){
		$out = array();
		$cnt = 0;
		$q = db_query("select Owner from ".RELATED_CONTENT_CAT_TABLE." where categoryID=".(int)$categoryID);
		while ($row = db_fetch_row($q))
		{
			$qd = db_query("select aux_page_name from ".AUX_PAGES_TABLE." where aux_page_ID=".(int)$row["Owner"]);
			$rowd = db_fetch_row($qd);
			$out[$cnt][0] = $row["Owner"];
			$out[$cnt][1] = $rowd["aux_page_name"];
			$cnt++;
		}
		return $out;
	}

	function processCategories($level, $path, $sel)
	{
		//returns an array of categories, that will be presented by the category_navigation.tpl template

		//$categories[] - categories array
		//$level - current level: 0 for main categories, 1 for it's subcategories, etc.
		//$path - path from root to the selected category (calculated by calculatePath())
		//$sel -- categoryID of a selected category

		//returns an array of (categoryID, name, level)

		//category tree is being rolled out "by the path", not fully

		$out = array();
		$cnt = 0;

		$parent = $path[$level]["parent"];
		if ( $parent == "" || $parent == null ) $parent = "NULL";

		$q = db_query("select categoryID, name from ".CATEGORIES_TABLE.
			" where parent=".(int)$path[$level]["parent"]." order by sort_order, name");
		$c_path = count($path);
		while ($row = db_fetch_row($q))
		{
			$out[$cnt][0] = $row["categoryID"];
			$out[$cnt][1] = $row["name"];
			$out[$cnt][2] = $level;
			$cnt++;

			//process subcategories?
			if ($level+1<$c_path && $row["categoryID"] == $path[$level+1])
			{
				$sub_out = processCategories($level+1,$path,$sel);
				//add $sub_out to the end of $out
				for ($j=0; $j<count($sub_out); $j++)
				{
					$out[] = $sub_out[$j];
					$cnt++;
				}
			}
		}
		return $out;
	} //processCategories

	function fillTheCList($parent,$level) //completely expand category tree
	{

		$q = db_query("select categoryID, name, products_count, products_count_admin, parent FROM ".
			CATEGORIES_TABLE." WHERE parent=".(int)$parent." ORDER BY sort_order, name");
		$a = array(); //parents
		while ($row = db_fetch_row($q))
		{
			$row["level"] = $level;
			$a[] = $row;
			//process subcategories
			$b = fillTheCList($row[0],$level+1);
			//add $b[] to the end of $a[]
			$cc_b = count($b);
			for ($j=0; $j<$cc_b; $j++) $a[] = $b[$j];
		}
		return $a;

	} //fillTheCList

	function _recursiveGetCategoryCompactCList( $path, $level )
	{
		$q = db_query( "select categoryID, parent, name, products_count, sort_order, UID from ".CATEGORIES_TABLE.
			" where parent=".(int)$path[$level-1]["categoryID"]." order by sort_order, name " );
		$res = array();
		$selectedCategoryID = null;
		$c_path = count($path);
		while( $row=db_fetch_row($q) )
		{

			$row["level"] = $level;
			$res[] = $row;
			if ( $level <= $c_path-1 )
			{
				if ( (int)$row["categoryID"] == (int)$path[$level]["categoryID"] )
				{
					$selectedCategoryID = $row["categoryID"];
					$arres = _recursiveGetCategoryCompactCList( $path, $level+1 );

					$c_arres = count($arres);
					for ($i=0; $i<$c_arres; $i++) $res[] = $arres[$i];
				}
			}
		}

		return $res;
	}

	function getcontentcatresc( $catID )
	{
		$q = db_query( "select name, UID, cat_h1  from ".CATEGORIES_TABLE." where parent=".$catID." order by sort_order, name " );
		$res = array('raion'=>array(),'city'=>array());
		while( $row=db_fetch_row($q) )
		{
			if (strpos($row["name"],' район') !== false || strpos($row["name"],'Район ') !== false || strpos($row["name"],' улус') !== false || strpos($row["name"],' кожуун') !== false)
			{
				$res['raion'][] = array('UID' => $row['UID'], 'name' => $row['cat_h1']);
			}else{  
				$res['city'][] = array('UID' => $row['UID'], 'name' => $row['cat_h1']);
			}
		}

		return $res;
	}

	function catExpandCategory( $categoryID, $sessionArrayName )
	{
		$existFlag = false;
		foreach( $_SESSION[$sessionArrayName] as $key => $value )
			if ( $value == $categoryID )
			{
				$existFlag = true;
				break;
			}
			if ( !$existFlag ) $_SESSION[$sessionArrayName][] = $categoryID;

	}

	function catShrinkCategory( $categoryID, $sessionArrayName )
	{
		foreach( $_SESSION[$sessionArrayName] as $key => $value )
		{
			if ( $value == $categoryID ) unset( $_SESSION[$sessionArrayName][$key] );
		}
	}

	function catExpandCategoryp( $sessionArrayName )
	{
		$categoryID = 0;
		$cats = array();
		$q = db_query("select categoryID FROM ".CATEGORIES_TABLE." ORDER BY sort_order, name");
		while ($row = db_fetch_row($q)) $_SESSION[$sessionArrayName][] = $row[0];
	}

	function catShrinkCategorym( $sessionArrayName )
	{
		unset( $_SESSION[$sessionArrayName]);
		$_SESSION["expcat"] = array(1);
	}

	function catGetCategoryCompactCList( $selectedCategoryID )
	{
		$path = catCalculatePathToCategory( $selectedCategoryID );
		$res = array();
		$res[] = array( 
			"categoryID" => 1,
			"parent" => null,
			"name" => ADMIN_CATEGORY_ROOT,
			"level" => 0 
		);

		$q = db_query( "select categoryID, parent, name, products_count, sort_order, UID from ".CATEGORIES_TABLE.
			" where parent=1 ".
			" order by sort_order, name " );
		$c_path = count($path);
		while( $row = db_fetch_row($q) )
		{
			//$row["name"] = TransformDataBaseStringToText($row["name"]);
			$row["level"] = 1;
			$res[] = $row;
			if ( $c_path > 1 )
			{
				if ( $row["categoryID"] == $path[1]["categoryID"] )
				{
					$arres = _recursiveGetCategoryCompactCList( $path, 2 );
					$c_arres = count($arres);
					//for ($i=0; $i<$c_arres; $i++) $res[] = $arres[$i];
					$res = array_merge($res,$arres);
				}
			}
		}
		return $res;
	}



	// *****************************************************************************
	// Purpose        gets category tree to render it on HTML page
	// Inputs
	//		$parent 	- must be 0
	//      $level      - must be 0
	//      $expcat 	- array of category ID that expanded
	// Remarks
	// 		array of item
	//      for each item
	//      "products_count" -	count product in category including
	//							subcategories excluding enabled product
	//		"products_count_admin" -count product in category
	//								 
	//		"products_count_category" - 
	// Returns        nothing
	function _recursiveGetCategoryCList( $parent, $level, $expcat, $_indexType = 'NUM', $cprod = false, $ccat = true)
	{
		global $fc, $mc;
        if($mc === null) $mc = array();
		$rcat  = array_keys ($mc, (int)$parent);
		$result = array(); //parents

		$crcat = count($rcat);
		for ($i=0; $i<$crcat; $i++) {

			$row = $fc[(int)$rcat[$i]];
			if (!file_exists("data/category/".$row["picture"])) $row["picture"] = "";
			$row["level"] = $level;
			$row["ExpandedCategory"] = false;
			if ( $expcat != null )
			{
				foreach( $expcat as $categoryID )
				{
					if ( (int)$categoryID == (int)$row["categoryID"] )
					{
						$row["ExpandedCategory"] = true;
						break;
					}
				}
			}
			else
				$row["ExpandedCategory"] = true;

			if ($ccat) {$row["products_count_category"] = catGetCategoryProductCount( $row["categoryID"], $cprod );}

			$row["ExistSubCategories"] = ( $row["subcount"] != 0 );

			if($_indexType=='NUM')
				$result[] = $row;
			elseif ($_indexType=='ASSOC')
				$result[$row['categoryID']] = $row;


			if ( $row["ExpandedCategory"] )
			{
				//process subcategories
				$subcategories = _recursiveGetCategoryCList( $row["categoryID"],
					$level+1, $expcat, $_indexType, $cprod, $ccat);

				if($_indexType=='NUM'){

					//add $subcategories[] to the end of $result[]
					for ($j=0; $j<count($subcategories); $j++)
						$result[] = $subcategories[$j];
				}
				elseif ($_indexType=='ASSOC'){

					//add $subcategories[] to the end of $result[]
					foreach ($subcategories as $_sub){

						$result[$_sub['categoryID']] = $_sub;
					}
				}

			}
		}
		return $result;
	}


	// *****************************************************************************
	// Purpose        gets category tree to render it on HTML page
	// Inputs
	// Remarks
	// Returns        nothing
	/*function catGetCategoryCList( $expcat = null, $_indexType='NUM', $cprod = false, $ccat = true  )
	{
	return _recursiveGetCategoryCList( 1, 0, $expcat, $_indexType, $cprod, $ccat);
	}*/

	function CreateChildCats($cat_id = 1, $cat_level = 1) {
		$sql = mysql_query("SELECT categoryID, name, parent, products_count, products_count_admin FROM ".CATEGORIES_TABLE." WHERE parent=".$cat_id." ORDER BY sort_order, name");
		$cat_count = mysql_num_rows($sql);
		$cat_arr = array();
		if ($cat_count > 0) {
			while ($row = mysql_fetch_assoc($sql)) {	
				$cat_arr[] = array( 
					'categoryID' => $row['categoryID'],
					'name' => $row['name'], 
					'parent' => $row['parent'],
					'level' => $cat_level,
					'products_count' => $row['products_count'],
					'products_count_admin' => $row['products_count_admin'],
					'ExistSubCategories' => ($row['products_count_admin'] > 10) ? true : false);
			}
		}
		return $cat_arr;
	}

	function GetParentCatId($cat_id) {
		$sql = mysql_query("SELECT parent FROM ".CATEGORIES_TABLE." WHERE categoryID=".$cat_id);
		$cat_count = mysql_num_rows($sql);
		if ($cat_count > 0) {
			$row = mysql_fetch_assoc($sql);
			return $row['parent'];
		} else {
			return 0;
		}
	}

	function catGetCategoryCList($select_cat = 1) { 
		if (isset($category_list) && isset($category_list_id) && $category_list_id == $select_cat) {
			return $category_list;
		} else {
			static $category_list;
			$category_list = array();
			//Определение уровня выбранной категории
			$sel_cat_1 = GetParentCatId($select_cat);
			if ($select_cat != NULL && ($sel_cat_1 == 1 || $sel_cat_1 == NULL)) {
				$sel_cat = array(
					1 => $select_cat, 
					2 => 0, 
					3 => 0);
			} else {
				$sel_cat_2 = GetParentCatId($sel_cat_1);
				if ($select_cat != NULL && ($sel_cat_2 == 1 || $sel_cat_2 == NULL)) {
					$sel_cat = array(
						1 => $sel_cat_1, 
						2 => $select_cat, 
						3 => 0);
				} else {
					$sel_cat_3 = GetParentCatId($sel_cat_2);
					if ($select_cat != NULL && ($sel_cat_3 == 1 || $sel_cat_3 == NULL)) {
						$sel_cat = array(
							1 => $sel_cat_2, 
							2 => $sel_cat_1, 
							3 => $select_cat);
					} else if ($select_cat == NULL) {
						$sel_cat = array(
							1 => -1, 
							2 => -1, 
							3 => -1);
					} else {
						$sel_cat = array(
							1 => 1, 
							2 => 0, 
							3 => 0);
					}
				}
			}
			//Построение дерева категорий
			$cats_1 = CreateChildCats(1, 1);
			$num = -1;
			foreach ($cats_1 as $cat1) {
				if ($cat1 != NULL) {
					$num++;
					$category_list[$num] = $cat1;
					//Уровень 2
					if ($sel_cat[1] == -1 || $sel_cat[1] == $cat1['categoryID']) {
						$category_list[$num]['ExpandedCategory'] = true;
						$cats_2 = CreateChildCats($cat1['categoryID'], 2);
						foreach ($cats_2 as $cat2) {
							if ($cat2 != NULL) {
								$num++;
								$category_list[$num] = $cat2;
								//Уровень 3
								if ($sel_cat[2] == -1 || $sel_cat[2] == $cat2['categoryID']) {
									$category_list[$num]['ExpandedCategory'] = true;
									$cats_3 = CreateChildCats($cat2['categoryID'], 3);
									foreach ($cats_3 as $cat3) {
										if ($cat3 != NULL) {
											$num++;
											$category_list[$num] = $cat3;
											//Уровень 4
											if ($sel_cat[3] == -1 || $sel_cat[3] == $cat3['categoryID']) {
												$category_list[$num]['ExpandedCategory'] = true;
												$cats_4 = CreateChildCats($cat3['categoryID'], 4);
												foreach ($cats_4 as $cat4) {
													if ($cat4 != NULL) {
														$num++;
														$category_list[$num] = $cat4;
														$category_list[$num]['ExpandedCategory'] = false;
													}
												}
											} else {
												$category_list[$num]['ExpandedCategory'] = false;
											}
										} 
									}
								} else {
									$category_list[$num]['ExpandedCategory'] = false;
								}
							}
						}
					} else {
						$category_list[$num]['ExpandedCategory'] = false;
					}
				}
			}

			return $category_list;
		}
	}

	function GetProductsByCategoryIdMS($CategoryId)
	{
		//Товары этой категории
		$sql_query="SELECT REPLACE( tp.name,'{city}',p.tpl_city ) as name, UID,
		IF(p.tplID>0,tp.folder_pictures, p.folder_pictures) as folder_pictures, p.pictures FROM ".PRODUCTS_TABLE." p
		LEFT JOIN ".PRODUCTS_TPL_TABLE." tp USING(tplID) 
		WHERE categoryID=".$CategoryId.' ORDER BY IF(p.tplID>0,tp.sort_order,p.sort_order), name';
		$sql = mysql_query($sql_query);
		$Products = array();
		while ($row = mysql_fetch_assoc($sql))
		{
			$row['pictures'] = explode(',', $row['pictures']);
			$Products[]=$row;
		}
		return $Products;
	}

	function GetSubCategoriesByCategoryIdMS($CategoryId,$SelectedCategory)
	{
		global $category,$VirtualRootCategory,$ShowVirtualRootCategory;

		if(($CategoryId!=1) && ($SelectedCategory['child'])) return array($SelectedCategory);
		//Подкатегории
		//Если корень - эмулируем
		$sql = mysql_query("SELECT categoryID, parent, cat_h1 as name, UID, sort_order, level FROM ".CATEGORIES_TABLE." WHERE parent=".$CategoryId.' ORDER BY level DESC, sort_order, cat_h1');

		$Categories=array();
		$SelectedCategoryId = $SelectedCategory['categoryID'];
		while ($row = mysql_fetch_assoc($sql)) 
		{
			//if ($CategoryId != 1 || ($row['categoryID'] != 135 && $row['categoryID'] != 229)) 
			{
				if($row['categoryID'] != $SelectedCategoryId )
				{
					$row['categoryID']=(int)$row['categoryID'];
					$row['parent']=(int)$row['parent'];
					$Categories[]=$row;
				}
				else $Categories[]=$SelectedCategory;
			}
		}


		//Добавляем виртуальный
		if(($CategoryId==1) && ($ShowVirtualRootCategory==true))
		{
			//if(($SelectedCategory) && ($SelectedCategory['sort_order']<10000)) return array($SelectedCategory);

			$VirtualCategoryRussia = $VirtualRootCategory;

			$MayBeNewCategories = array_slice($Categories,-2);

			//Set new categories
			$NewCategories = array();
			foreach ($MayBeNewCategories as $MayBeNewCategory )
			{
			 	if($MayBeNewCategory['sort_order']<10000) $NewCategories[] = $MayBeNewCategory;
			}
			
			$NewCategoriesCount = count($NewCategories);

			//$VirtualCategoryRussia['child']=array_slice($Categories,2);

			if(!$SelectedCategory){
				if($NewCategoriesCount > 0) $VirtualCategoryRussia['child']=array_slice($Categories,0,-$NewCategoriesCount);
				else $VirtualCategoryRussia['child'] = $Categories;
			}
			elseif($SelectedCategory['sort_order']>10000)
				$VirtualCategoryRussia['child']=array($SelectedCategory);
			else 
				$VirtualCategoryRussia['child']=array();

			$NewCategories[]=$VirtualCategoryRussia;
			$Categories = $NewCategories;
		}
		
		return $Categories;
	}

	function CreateChildCatsNewMS($cat_id = 1, $SelectedCategory=null) 
	{
		//if ($cat_id == 234) {$cat_id = 1;}

		//Данные про категорию
		$sql = mysql_query("SELECT parent, cat_h1 as name, UID, sort_order,level FROM ".CATEGORIES_TABLE." WHERE categoryID=".$cat_id);
		$row = mysql_fetch_assoc($sql);

		$cat_arr = array();
		$row['categoryID']=$cat_id;
		$row['parent'] = (int)$row['parent'];
		//$cat_arr['name'] = ($cat_id != 1) ? 'Карты '.$row['cat_h1'] : 'Карты России';
		if($cat_id == 1) $row['UID']='/';
		$row['prods']=GetProductsByCategoryIdMS($cat_id);

		$row['child'] = GetSubCategoriesByCategoryIdMS($cat_id,$SelectedCategory);
		//if($cat_id!=1)
		//..	$row['child']=array($SelectedCategory);
		/*else
		{
		/*			if(!$SelectedCategory)
		{
		}
		else 
		*/		


		return $row;
	}

	//Вывод списка категорий для моего шаблона
	function catGetCategoryNewMS($select_cat = 1) {
		//$all_world = CreateChildCatsNew(135);
		$sel_cat = array();
		$sel_cat_1 = CreateChildCatsNewMS($select_cat);
		$sel_cat=$sel_cat_1;
		while($sel_cat_1['parent'] != 0)
		{
			$sel_cat_1 = CreateChildCatsNewMS($sel_cat_1['parent'],$sel_cat);
			$sel_cat=$sel_cat_1;
		}
		return $sel_cat_1;
	}

	//Вывод списка категорий для моего шаблона
	function catGetCategoryNew($select_cat = 1) {
		$sel_cat = array();
		$all_world = CreateChildCatsNew(135);

		$sel_cat_1 = CreateChildCatsNew($select_cat);
		//var_dump($sel_cat_1);die;
		if ($sel_cat_1['categoryID'] == 135) {
			$sel_cat[0] = $all_world;
		} else if ($sel_cat_1['parent'] == NULL || $sel_cat_1['parent'] == 135) {
			$sel_cat[0] = $all_world;
			$sel_cat[1] = $sel_cat_1;
		} else {
			$sel_cat_2 = CreateChildCatsNew($sel_cat_1['parent']);
			if ($sel_cat_2['parent'] == NULL || $sel_cat_2['parent'] == 135) {
				$sel_cat[0] = $all_world;
				$sel_cat[1] = $sel_cat_2; 
				$sel_cat[2] = $sel_cat_1;
			} else {
				$sel_cat_3 = CreateChildCatsNew($sel_cat_2['parent']);
				if ($sel_cat_3['parent'] == NULL || $sel_cat_3['parent'] == 135) {
					$sel_cat[0] = $all_world;
					$sel_cat[1] = $sel_cat_3; 
					$sel_cat[2] = $sel_cat_2; 
					$sel_cat[3] = $sel_cat_1;
				} else {
					$sel_cat_4 = CreateChildCatsNew($sel_cat_3['parent']);
					if ($sel_cat_4['parent'] == NULL || $sel_cat_4['parent'] == 135) {
						$sel_cat[0] = $all_world;
						$sel_cat[1] = $sel_cat_4; 
						$sel_cat[2] = $sel_cat_3;
						$sel_cat[3] = $sel_cat_2;
						$sel_cat[4] = $sel_cat_1;
					}
				}
			}
		}
		//var_dump($sel_cat);die;
		return $sel_cat;
	}

	function CreateChildCatsNew($cat_id = 1) {
		if ($cat_id == 234) {$cat_id = 1;}
		$cat_arr = array();

		//Данные про категорию
		$sql = mysql_query("SELECT parent, cat_h1, UID FROM ".CATEGORIES_TABLE." WHERE categoryID=".$cat_id);
		if (mysql_num_rows($sql) > 0) {
			$row = mysql_fetch_assoc($sql);
			$cat_arr['categoryID'] = $cat_id; 
			$cat_arr['parent'] = (int)$row['parent'];
			$cat_arr['name'] = ($cat_id != 1) ? FUNCS_CATEGORY_MAPS.$row['cat_h1'] : FUNCS_CATEGORY_MAPS_ROOT;
			$cat_arr['UID'] = ($cat_id == 1) ? '/' : $row['UID'];
			$cat_arr['prods'] = array();
		}

		//Товары этой категории
		$sql_query="SELECT REPLACE( tp.name,'{city}',p.tpl_city ) as name, UID,
		IF(p.tplID>0,tp.folder_pictures, p.folder_pictures) as folder_pictures, p.pictures FROM ".PRODUCTS_TABLE." p
		LEFT JOIN ".PRODUCTS_TPL_TABLE." tp USING(tplID) 
		WHERE categoryID=".$cat_id.' ORDER BY IF(p.tplID>0,tp.sort_order,p.sort_order), name';
		$sql = mysql_query($sql_query);
		if (mysql_num_rows($sql) > 0) {
			while ($row = mysql_fetch_assoc($sql)) {
				$pic = explode('jpg', $row['pictures']);
				$cat_arr['prods'][] = array( 
					'name' => $row['name'],
					'UID' => $row['UID'].'.html',
					'folder_pictures' => $row['folder_pictures'],
					'pictures' => $pic[0].'jpg'
				);
			}
		}

		//Подкатегории
		$sql = mysql_query("SELECT categoryID, parent, cat_h1, UID FROM ".CATEGORIES_TABLE." WHERE parent=".$cat_id.' ORDER BY sort_order, cat_h1');
		if (mysql_num_rows($sql) > 0) {
			$cats = array();
			//Число столбцов для подкатегорий
			$x = (count($cat_arr['prods']) > 0 && count($cat_arr['prods']) <= 1) ? count($cat_arr['prods']) : 1;
			//Подкатегории
			while ($row = mysql_fetch_assoc($sql)) {
				if ($cat_id != 1 || ($row['categoryID'] != 135 && $row['categoryID'] != 229)) {
					$cats[] = array(
						'categoryID' => (int)$row['categoryID'], 
						'parent' => (int)$row['parent'],
						'name' => FUNCS_CATEGORY_MAPS.$row['cat_h1'],
						'UID' => $row['UID']
					);
				}
			}
			//Число строк для подкатегорий
			$y = ceil(count($cats)/$x);
			$res = array();
			$k = 0;
			for ($i = 0; $i < $x; $i++) {
				$res[$i] = array();
				for ($j = 0; $j < $y; $j++) {
					if (isset($cats[$k])) {
						$res[$i][$j] = $cats[$k];
						$k++;
					}
				}
			}

			$cat_arr['child'] = $res;
		}

		return $cat_arr;
	}


	function catGetCategoryCListMin()
	{
		return _recursiveGetCategoryCList( 1, 0, null, 'NUM', false, false);
	}

	// *****************************************************************************
	// Purpose        gets product count in category
	// Inputs
	// Remarks  this function does not keep in mind subcategories
	// Returns        nothing
	function catGetCategoryProductCount( $categoryID, $cprod = false )
	{
		if (!$categoryID) return 0;

		$res = 0;
		$sql = "
		select count(*) FROM ".PRODUCTS_TABLE."
		WHERE categoryID=".(int)$categoryID."".($cprod?" AND enabled>0":"");
		$q = db_query($sql);
		$t = db_fetch_row($q);
		$res += $t[0];
		if($cprod)
			$sql = "
			select COUNT(*) FROM ".PRODUCTS_TABLE." AS prot
			LEFT JOIN ".CATEGORIY_PRODUCT_TABLE." AS catprot
			ON prot.productID=catprot.productID
			WHERE catprot.categoryID=".(int)$categoryID." AND prot.enabled>0
			";
		else
			$sql = "
			select count(*) from ".CATEGORIY_PRODUCT_TABLE.
			" where categoryID=".(int)$categoryID
			;
		$q1 = db_query($sql);
		$row = db_fetch_row($q1);
		$res += $row[0];
		return $res;
	}

	function update_sCount($parent)
	{
		global $fc, $mc;

		$rcat = array_keys ($mc, (int)$parent);
		$crcat = count($rcat);
		for ($i=0; $i<$crcat; $i++) {

			$rowsub = $fc[(int)$rcat[$i]];
			$countsub  = count(array_keys ($mc, (int)$rowsub["categoryID"]));

			db_query("UPDATE ".CATEGORIES_TABLE.
				" SET subcount=".(int)$countsub." ".
				" WHERE categoryID=".(int)$rcat[$i]);

			$rowsubExist = ( $countsub != 0 );
			if ( $rowsubExist ) update_sCount($rowsub["categoryID"]);
		}
	}

	function update_pCount($parent)
	{
		update_sCount($parent);

		$q = db_query("select categoryID FROM ".CATEGORIES_TABLE.
			" WHERE categoryID>1 AND parent=".(int)$parent);

		$cnt = array();
		$cnt["admin_count"] = 0;
		$cnt["customer_count"] = 0;

		// process subcategories
		while( $row=db_fetch_row($q) )
		{
			$t = update_pCount( $row["categoryID"] );
			$cnt["admin_count"]     += $t["admin_count"];
			$cnt["customer_count"]  += $t["customer_count"];
		}

		// to administrator
		$q = db_query("select count(*) FROM ".PRODUCTS_TABLE.
			" WHERE categoryID=".(int)$parent);
		$t = db_fetch_row($q);
		$cnt["admin_count"] += $t[0];
		$q1 = db_query("select count(*) from ".CATEGORIY_PRODUCT_TABLE.
			" where categoryID=".(int)$parent);
		$row = db_fetch_row($q1);
		$cnt["admin_count"] += $row[0];

		// to customer
		$q = db_query("select count(*) FROM ".PRODUCTS_TABLE.
			" WHERE enabled=1 AND categoryID=".(int)$parent);
		$t = db_fetch_row($q);
		$cnt["customer_count"] += $t[0];
		$q1 = db_query("select productID, categoryID from ".CATEGORIY_PRODUCT_TABLE.
			" where categoryID=".(int)$parent);
		while( $row = db_fetch_row($q1) )
		{
			$q2 = db_query("select productID from ".PRODUCTS_TABLE.
				" where enabled=1 AND productID=".(int)$row["productID"]);
			if ( db_fetch_row($q2) )
				$cnt["customer_count"] ++;
		}

		db_query("UPDATE ".CATEGORIES_TABLE.
			" SET products_count=".(int)$cnt["customer_count"].", products_count_admin=".
			(int)$cnt["admin_count"]." WHERE categoryID=".(int)$parent);
		return $cnt;
	}

	function update_psCount($parent)
	{
		global $fc, $mc;

		$q = db_query("select categoryID, name, products_count, ".
			"products_count_admin, parent, picture, subcount FROM ".
			CATEGORIES_TABLE. " ORDER BY sort_order, name");
		$fc = array(); //parents
		$mc = array(); //parents
		while ($row = db_fetch_row($q)) {
			$fc[(int)$row["categoryID"]] = $row;
			$mc[(int)$row["categoryID"]] = (int)$row["parent"];
		}
		update_pCount($parent);
	}
	// *****************************************************************************
	// Purpose        get subcategories by category id
	// Inputs   $categoryID
	//                                parent category ID
	// Remarks  get current category's subcategories IDs (of all levels!)
	// Returns        array of category ID
	function catGetSubCategories( $categoryID )
	{
		$q = db_query("select categoryID from ".CATEGORIES_TABLE." where parent=".(int)$categoryID);
		$r = array();
		while ($row = db_fetch_row($q))
		{
			$a = catGetSubCategories($row[0]);
			$c_a = count($a);
			for ($i=0;$i<$c_a;$i++) $r[] = $a[$i];
			$r[] = $row[0];
		}
		return $r;
	}


	// *****************************************************************************
	// Purpose        get subcategories by category id
	// Inputs           $categoryID
	//                                parent category ID
	// Remarks          get current category's subcategories IDs (of all levels!)
	// Returns        array of category ID
	function catGetSubCategoriesSingleLayer( $categoryID )
	{
		$q = db_query("select categoryID, name, products_count FROM ".
			CATEGORIES_TABLE." WHERE parent=".(int)$categoryID." order by sort_order, name");
		$result = array();
		while ($row = db_fetch_row($q)) $result[] = $row;
		return $result;
	}



	// *****************************************************************************
	// Purpose        get category by id
	// Inputs   $categoryID
	//                                - category ID
	// Remarks
	// Returns
	function catGetCategoryById($categoryID)
	{
		$sql_query = "SELECT cat.categoryID, cat.name, cat.parent, cat.products_count, cat.picture, cat.products_count_admin, cat.sort_order, cat.viewed_times,  cat.allow_products_comparison, cat.allow_products_search, cat.show_subcategories_products, 
		tpl_id, UID, typ,
		REPLACE(REPLACE(tpl.description,'{city}',cat.cat_h1), '{year}', YEAR(CURDATE())) as description,
		REPLACE(tpl.meta_description,'{city}',cat.name)as meta_description,
		REPLACE(tpl.meta_keywords,'{city}',cat.name) as meta_keywords,
		REPLACE(tpl.title,'{city}',cat.name) as title,
		REPLACE(tpl.cat_h1,'{city}',cat.cat_h1) as cat_h1,
		REPLACE(tpl.cat_h2_c,'{city}',cat.cat_h1) as cat_h2_c,
		REPLACE(tpl.cat_h2_o,'{city}',cat.cat_h1) as cat_h2_o,
		REPLACE(tpl.cat_h2_r,'{city}',cat.cat_h1) as cat_h2_r,
		level
		FROM ".CATEGORIES_TABLE." cat LEFT JOIN ".CATEGORIES_TPL_TABLE." tpl ON(cat.tpl_id = tpl.id)
		WHERE categoryID=".$categoryID;

		$q = db_query($sql_query);
		$catrow = db_fetch_row($q);
		if($catrow==false)
		{
			$sql_query = 'SELECT * FROM '.CATEGORIES_TABLE.' WHERE categoryID='.$categoryID;
			$catrow = db_fetch_row($q);
		}

		$catrow["name"] = ToText($catrow["name"]);
		$catrow["meta_description"] = ToText($catrow["meta_description"]);
		$catrow["meta_keywords"] = ToText($catrow["meta_keywords"]);
		$catrow["title"] = ToText($catrow["title"]);
		$catrow["cat_h1"] = ToText($catrow["cat_h1"]);
		$catrow["cat_h2_c"] = ToText($catrow["cat_h2_c"]);
		$catrow["cat_h2_o"] = ToText($catrow["cat_h2_o"]);
		$catrow["cat_h2_r"] = ToText($catrow["cat_h2_r"]);

		return $catrow;
	}

	// *****************************************************************************
	// Purpose        gets category META information in HTML form
	// Inputs   $categoryID
	//                                - category ID
	// Remarks
	// Returns
	function catGetMetaTags($categoryID)
	{
		$q = db_query( "select meta_description, meta_keywords from ".
			CATEGORIES_TABLE." where categoryID=".(int)$categoryID );
		$row = db_fetch_row($q);

		$res = "";

		if  ( $row["meta_description"] != "" )
			$res .= "<meta name=\"Description\" content=\"".$row["meta_description"]."\">\n";
		if  ( $row["meta_keywords"] != "" )
			$res .= "<meta name=\"KeyWords\" content=\"".$row["meta_keywords"]."\" >\n";

		return $res;
	}



	// *****************************************************************************
	// Purpose        adds product to appended category
	// Inputs
	// Remarks      this function uses CATEGORIY_PRODUCT_TABLE table in data base instead of
	//                        PRODUCTS_TABLE.categoryID. In CATEGORIY_PRODUCT_TABLE saves appended
	//                        categories
	// Returns        array of item
	//                        "categoryID"
	//                        "category_name"
	function catGetAppendedCategoriesToProduct( $productID )
	{
		$q = db_query( "select ".CATEGORIES_TABLE.".categoryID as categoryID, name as category_name ".
			" from ".CATEGORIY_PRODUCT_TABLE.", ".CATEGORIES_TABLE." ".
			" where ".CATEGORIY_PRODUCT_TABLE.".categoryID = ".CATEGORIES_TABLE.".categoryID ".
			" AND productID = ".(int)$productID  );
		$data = array();
		while( $row = db_fetch_row( $q ) ){
			$wayadd = '';
			$way = catCalculatePathToCategoryA($row["categoryID"]);
			$cway = count($way);
			for ($i=$cway-1; $i>=0; $i--){ if($way[$i]['categoryID']!=1) $wayadd .= $way[$i]['name'].' / '; }
			$row["category_way"]=$wayadd."<b>".$row["category_name"]."</b>";
			$data[] = $row;
		}
		return $data;
	}



	// *****************************************************************************
	// Purpose        adds product to appended category
	// Inputs
	// Remarks      this function uses CATEGORIY_PRODUCT_TABLE table in data base instead of
	//                        PRODUCTS_TABLE.categoryID. In CATEGORIY_PRODUCT_TABLE saves appended
	//                        categories
	// Returns        true if success, false otherwise
	function catAddProductIntoAppendedCategory($productID, $categoryID)
	{
		$q = db_query("select count(*) from ".CATEGORIY_PRODUCT_TABLE.
			" where productID=".(int)$productID." AND categoryID=".(int)$categoryID);
		$row = db_fetch_row( $q );

		$qh = db_query( "select categoryID from ".PRODUCTS_TABLE.
			" where productID=".(int)$productID);
		$rowh = db_fetch_row( $qh );
		$basic_categoryID = $rowh["categoryID"];

		if ( !$row[0] && $basic_categoryID != $categoryID )
		{
			db_query("insert into ".CATEGORIY_PRODUCT_TABLE.
				"( productID, categoryID ) ".
				"values( ".(int)$productID.", ".(int)$categoryID." )" );
			return true;
		}
		else
			return false;
	}


	// *****************************************************************************
	// Purpose        removes product to appended category
	// Inputs
	// Remarks      this function uses CATEGORIY_PRODUCT_TABLE table in data base instead of
	//                        PRODUCTS_TABLE.categoryID. In CATEGORIY_PRODUCT_TABLE saves appended
	//                        categories
	// Returns        nothing
	function catRemoveProductFromAppendedCategory($productID, $categoryID)
	{
		db_query("delete from ".CATEGORIY_PRODUCT_TABLE.
			" where productID = ".(int)$productID." AND categoryID = ".(int)$categoryID);

	}


	// *****************************************************************************
	// Purpose        calculate a path to the category ( $categoryID )
	// Inputs
	// Remarks
	// Returns        path to category
	function catCalculatePathToCategory( $categoryID )
	{
		if (!$categoryID) return NULL;

		$path = array();

		$q = db_query("select count(*) from ".CATEGORIES_TABLE.
			" where categoryID=".(int)$categoryID);
		$row = db_fetch_row($q);
		if ( $row[0] == 0 ) return $path;

		do
		{
			$q = db_query("select categoryID, parent, cat_h1 as name, UID FROM ".
				CATEGORIES_TABLE." WHERE categoryID=".(int)$categoryID);
			$row = db_fetch_row($q);
			$path[] = $row;

			//if ($row["parent"] == $row["categoryID"]) break;
			$categoryID = $row["parent"];
		}
		while ( $categoryID );
		//now reverse $path
		$path = array_reverse($path);
		return $path;
	}

	// *****************************************************************************
	// Purpose        calculate a path to the category ( $categoryID )
	// Inputs
	// Remarks
	// Returns        path to category
	function catCalculatePathToCategoryA( $categoryID )
	{
		if (!$categoryID) return NULL;

		$path = array();

		$q = db_query("select count(*) from ".CATEGORIES_TABLE.
			" where categoryID=".(int)$categoryID);
		$row = db_fetch_row($q);
		if ( $row[0] == 0 ) return $path;
		$curr = $categoryID;
		do
		{
			$q = db_query("select categoryID, parent, name FROM ".
				CATEGORIES_TABLE." WHERE categoryID=".(int)$categoryID);
			$row = db_fetch_row($q);
			if($categoryID != $curr) $path[] = $row;

			if ( $categoryID == 1 ) break;

			$categoryID = $row["parent"];
		}
		while ( 1 );
		//now reverse $path
		$path = array_reverse($path);
		return $path;
	}

	function _deleteSubCategories( $parent )
	{

		$q1 = db_query("select picture FROM ".CATEGORIES_TABLE." WHERE categoryID=".(int)$parent);
		$r = db_fetch_row($q1);
		if ($r["picture"] && file_exists("data/category/".$r["picture"])) unlink("data/category/".$r["picture"]);


		$q = db_query("select categoryID FROM ".CATEGORIES_TABLE." WHERE parent=".(int)$parent);
		while ($row = db_fetch_row($q)){
			$qp = db_query("select productID FROM ".PRODUCTS_TABLE." where categoryID=".(int)$row["categoryID"] );
			while ( $picture = db_fetch_row($qp) )
			{
				DeleteThreePictures2($picture["productID"]);
			}
			db_query("delete FROM ".PRODUCTS_TABLE." WHERE categoryID=".(int)$row["categoryID"]);
			_deleteSubCategories( $row["categoryID"] );
		}
		db_query("delete FROM ".CATEGORIES_TABLE." WHERE parent=".(int)$parent);

	}


	// *****************************************************************************
	// Purpose        deletes category
	// Inputs
	//                 $categoryID - ID of category to be deleted
	// Remarks      delete also all subcategories, all prodoctes remove into root
	// Returns        nothing
	function catDeleteCategory( $categoryID )
	{
		_deleteSubCategories( $categoryID );

		$q=db_query("select productID FROM ".PRODUCTS_TABLE." where categoryID=".(int)$categoryID );
		if ( $picture=db_fetch_row($q) )
		{
			DeleteThreePictures2($picture["productID"]);
		}

		db_query("delete FROM ".PRODUCTS_TABLE." WHERE categoryID=".(int)$categoryID);

		db_query("delete FROM ".CATEGORIES_TABLE." WHERE parent=".(int)$categoryID);
		$q = db_query("select picture FROM ".CATEGORIES_TABLE." WHERE categoryID=".(int)$categoryID);
		$r = db_fetch_row($q);
		if ($r["picture"] && file_exists("data/category/".$r["picture"])) unlink("data/category/".$r["picture"]);

		db_query("delete FROM ".CATEGORIES_TABLE." WHERE categoryID=".(int)$categoryID);
	}

?>