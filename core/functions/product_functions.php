<?php
	#####################################
	# ShopCMS: ������ ��������-��������
	# Copyright (c) by ADGroup
	# http://shopcms.ru
	#####################################

	function prdProductExists( $productID )
	{
		$q = db_query( "select count(*) from ".PRODUCTS_TABLE." where productID=".(int)$productID );
		$row = db_fetch_row($q);
		return ($row[0]!=0);
	}

	function getcontentprod($productID)
	{
		$out = array();
		$cnt = 0;
		$q = db_query("select Owner from ".RELATED_CONTENT_TABLE." where productID=".(int)$productID);
		while ($row = db_fetch_row($q))
		{
			$outpre = $row["Owner"];
			$qh = db_query("select aux_page_name from ".AUX_PAGES_TABLE." where aux_page_ID=".(int)$outpre);
			$rowh = db_fetch_row($qh);
			$out[$cnt][0] = $outpre;
			$out[$cnt][1] = $rowh["aux_page_name"];
			$cnt++;
		}
		return $out;
	}

	// *****************************************************************************
	// Purpose        gets product
	// Inputs   $productID - product ID
	// Remarks
	// Returns        array of fieled value
	//                        "name"                                - product name
	//                        "product_code"                - product code
	//                        "description"                - description
	//                        "brief_description"        - short description
	//                        "customers_rating"        - product rating
	//                        "in_stock"                        - in stock (this parametr is persist if CONF_CHECKSTOCK == 1 )
	//                        "option_values"                - array of
	//                                        "optionID"                - option ID
	//                                        "name"                        - name
	//                                        "value"        - option value
	//                                        "option_type" - option type
	//                        "ProductIsProgram"                - 1 if product is program, 0 otherwise
	//                        "eproduct_filename"                - program filename
	//                        "eproduct_available_days"        - program is available days to download
	//                        "eproduct_download_times"        - attempt count download file
	//                        "weight"                        - product weigth
	//                        "meta_description"                - meta tag description
	//                        "meta_keywords"                        - meta tag keywords
	//                        "free_shipping"                        - 1 product has free shipping,
	//                                                        0 - otherwise
	//                        "min_order_amount"                - minimum order amount
	//                        "classID"                        - tax class ID

	function GetProduct( $productID)
	{
		$db_query = 'SELECT p.productID, p.categoryID, p.customers_rating, p.customer_votes, p.items_sold, p.enabled, p.default_picture, p.date_added, p.date_modified, p.viewed_times, p.eproduct_filename, p.eproduct_available_days, p.eproduct_download_times, p.maps_sort, p.tplID, p.tpl_city, p.upd_tpl, p.tpl_val_1, p.tpl_val_2, p.tpl_val_3, p.tpl_val_4, p.tpl_val_5, p.pictures, p.UID, p.text_index, 
		t.Price, t.in_stock, t.brief_description, t.list_price, t.product_code, t.sort_order, t.weight, t.free_shipping, t.min_order_amount, t.shipping_freight, t.classID, t.kolvo_pictures, t.folder_pictures, t.seo_gen, t.sub_products,'."
		REPLACE(t.title,'{city}',p.tpl_city) as title,
		REPLACE(t.name,'{city}',p.tpl_city) as name,
		REPLACE(REPLACE( t.description,'{city}',p.tpl_city ),'{val_1}',p.tpl_val_1) as description,
		REPLACE(t.meta_description,'{city}',p.tpl_city) as meta_description,
		REPLACE(t.meta_keywords,'{city}',p.tpl_city) as meta_keywords
		".'FROM '.PRODUCTS_TABLE.' p JOIN '.PRODUCTS_TPL_TABLE.' t USING(tplID) WHERE productID='.$productID;

		$q = db_query($db_query);
		$product=db_fetch_row($q );
		if($product['tplID']==0)
		{
			$db_query = 'select * FROM '.PRODUCTS_TABLE.' WHERE productID='.$productID;
			$q = db_query($db_query);
			$product=db_fetch_row($q );
		}		

		if( !$product ) return false;
		
		SetUpdateMothAndYear($product);
		
		$product["ProductIsProgram"] = ( trim($product["eproduct_filename"]) != "" );
		$sql = 'select pot.optionID,pot.name,povt.option_value,povt.option_value as value,povt.option_type FROM '.PRODUCT_OPTIONS_VALUES_TABLE.' as povt
		LEFT JOIN '.PRODUCT_OPTIONS_TABLE.' as pot ON pot.optionID=povt.optionID
		WHERE productID='.(int)$productID.'
		';
		$Result = db_query($sql);
		$product['option_values'] = array();

		while ($_Row = db_fetch_row($Result)) {

			$product['option_values'][] = $_Row;
		}

		$product['date_added']=format_datetime( $product['date_added'] );
		$product['date_modified']=format_datetime( $product['date_modified'] );
		return $product;
	}
	
	
	function SetUpdateMothAndYear(&$Product)
	{
		$UpdateTime = strtotime('-2 month');
		$UpdMonth = (int) date('n',$UpdateTime);
		$UpdYear = (int) date('Y',$UpdateTime);
		$Year = date('Y');
		
		$Monthes = array(
			'������',
			'�������',
			'�����',
			'������',
			'���',
			'����',
			'����',
			'�������',
			'��������',
			'�������',
			'������',
			'�������'		
		);
		
		$UpdMonth = $Monthes[$UpdMonth-1];
		$Replaces = array(
			'{upd_month}'=>$UpdMonth,
			'{upd_year}'=>$UpdYear,
			'{year}'=>$Year
		);
		$Product['description'] = strtr($Product['description'],$Replaces);
	}



	// *****************************************************************************
	// Purpose        updates product
	// Inputs   $productID - product ID
	//                                $categoryID                        - category ID ( see CATEGORIES_TABLE )
	//                                $name                                - name of product
	//                                $Price                                - price of product
	//                                $description                - product description
	//                                $in_stock                        - stock counter
	//                                $customers_rating        - rating
	//                                $brief_description  - short product description
	//                                $list_price                        - old price
	//                                $product_code                - product code
	//                                $sort_order                        - sort order
	//                                $ProductIsProgram                - 1 if product is program, 0 otherwise
	//                                $eproduct_filename                - program filename
	//                                $eproduct_available_days        - program is available days to download
	//                                $eproduct_download_times        - attempt count download file
	//                                $weight                        - product weigth
	//                                $meta_description        - meta tag description
	//                                $meta_keywords                - meta tag keywords
	//                                $free_shipping                - 1 product has free shipping,
	//                                                        0 - otherwise
	//                                $min_order_amount        - minimum order amount
	//                                $classID                - tax class ID
	// Remarks
	// Returns
	function UpdateProduct( $productID,
		$categoryID, $name, $Price, $description,
		$in_stock, $customers_rating,
		$brief_description, $list_price,
		$product_code, $sort_order,
		$ProductIsProgram,
		$eproduct_filename,
		$eproduct_available_days,
		$eproduct_download_times,
		$weight, $meta_description, $meta_keywords,
		$free_shipping, $min_order_amount, $shipping_freight, $classID, $title, $updateGCV = 1, $url="", $tpl_city="", $tpl_val_1="", $tpl_val_2="")
	{
		if ( $min_order_amount == 0 )$min_order_amount = 1;

		if ( !$ProductIsProgram ) $eproduct_filename = "";

		if ( !$free_shipping ) $free_shipping = 0;
		else $free_shipping = 1;

		$q = db_query("select eproduct_filename from ".PRODUCTS_TABLE." where productID=".(int)$productID);
		$old_file_name = db_fetch_row( $q );
		$old_file_name = $old_file_name[0];

		if ( $classID == null ) $classID = "NULL";

		if ( $eproduct_filename != "" && $ProductIsProgram)
		{
			if ( trim($_FILES[$eproduct_filename]["name"]) != ""  )
			{
				if ( trim($old_file_name) != "" && file_exists("core/files/".$old_file_name) )
					unlink("core/files/$old_file_name");

				if ( $_FILES[$eproduct_filename]["size"]!=0 )
					$r = move_uploaded_file($_FILES[$eproduct_filename]["tmp_name"],
						"core/files/".$_FILES[$eproduct_filename]["name"]);
				$eproduct_filename = trim($_FILES[$eproduct_filename]["name"]);
				SetRightsToUploadedFile( "core/files/".$eproduct_filename );
			}
			else
				$eproduct_filename = $old_file_name;
		}
		elseif ($old_file_name != "") unlink("core/files/".$old_file_name);

		$s = "UPDATE ".PRODUCTS_TABLE." SET ".
		"categoryID=".(int)$categoryID.", ".
		"name='".xToText(trim($name))."', ".
		"Price=".(double)$Price.", ".
		"description='".xEscSQL($description)."', ".
		"in_stock=".(int)$in_stock.", ".
		"customers_rating=".(float)$customers_rating.", ".
		"brief_description='".xEscSQL($brief_description)."', ".
		"list_price=".(double)$list_price.", ".
		"product_code='".xToText(trim($product_code))."', ".
		"sort_order=".(int)$sort_order.", ".
		"date_modified='".xEscSQL(get_current_time())."', ".
		"eproduct_filename='".xEscSQL($eproduct_filename)."', ".
		"eproduct_available_days=".(int)$eproduct_available_days.", ".
		"eproduct_download_times=".(int)$eproduct_download_times.",  ".
		"weight=".(float)$weight.", meta_description='".xToText(trim($meta_description))."', ".
		"meta_keywords='".xToText(trim($meta_keywords))."', free_shipping=".(int)$free_shipping.", ".
		"min_order_amount = ".(int)$min_order_amount.", ".
		"shipping_freight = ".(double)$shipping_freight.", ".
		"title = '".xToText(trim($title))."', ".
		"tpl_city = '".$tpl_city."', ".
		"tpl_val_1 = '".$tpl_val_1."', ".
		"tpl_val_2 = '".$tpl_val_2."' ";

		if ($classID != null) $s .= ", classID = ".(int)$classID;

		$s .= " where productID=".(int)$productID;
		db_query($s);

		db_query("delete from ".CATEGORIY_PRODUCT_TABLE." where productID = ".(int)$productID." and categoryID = ".(int)$categoryID);

		if ($updateGCV == 1 && CONF_UPDATE_GCV == '1') //update goods count values for categories in case of regular file editing. do not update during import from excel
			update_psCount(1);

		//���� �� ������ ���� � ���, �� ��������� ���
		if(!empty($url))
		{
			//��������� ������ �� ����� �� ����
			db_query('UPDATE '.PRODUCTS_TABLE." SET UID='".$url."' WHERE productID=".(int)$productID);

			$k = mysql_query("SELECT * FROM ".ROUTES_TABLE." WHERE target='index.php?productID=".(int)$productID."'");
			if (mysql_num_rows($k) > 0) {
				db_query("UPDATE ".ROUTES_TABLE." SET url='".$url."' WHERE target='index.php?productID=".(int)$productID."'");
			} else {
				db_query("INSERT INTO ".ROUTES_TABLE." (url, target, kindID, enabled) VALUES ('".$url."', 'index.php?productID=".$productID."', 0, 1)");

			}

		}
		//����� ����������
		else
		{
			#$path = catCalculatePathToCategory($categoryID);
			#foreach($path as $cat) $UID .= trim($cat['UID'], '/').'/' ;
			#$UID .= routeTransliteration(xToText(trim($name)));
			$category = catGetCategoryById($categoryID);
			$replace = array('/' => '_', '\\' => '_');
			//$tmp = explode('-', $name);
			//$rout_name = trim($tmp[1]);
			$product = GetProduct($productID);
			$UID = '/'.strtr(routeTransliteration(xToText(trim($name))), $replace);
			$q = mysql_query("SELECT COUNT(*) FROM ".PRODUCTS_TABLE." WHERE UID='$UID'") or die(mysql_error().'. ������ � ������ '.__LINE__);
			$cnt = @mysql_result($q, 0);
			if($cnt > 1){
				$UID = '/'.strtr(routeTransliteration(xToText(trim($name))), $replace).'_'.$productID;
			}

			//$product = getProduct($insert_id);
			/*$UID = routeGetUID($UID, 'index.php?productID='.$productID);

			db_query('UPDATE '.PRODUCTS_TABLE." SET UID='".$UID."' WHERE productID=".$productID);
			routeDropOtherUrl($UID, 'index.php?productID='.$productID);   
			routeUpdateUrl($UID, 'index.php?productID='.$productID, $row['UID']);*/
			echo("INSERT INTO ".ROUTES_TABLE." (url, target, kindID, enabled) VALUES ('".$_UID."', 'index.php?productID=".$productID."', 0, 1)");die;
			db_query("INSERT INTO ".ROUTES_TABLE." (url, target, kindID, enabled) VALUES ('".$_UID."', 'index.php?productID=".$productID."', 0, 1)");

		}
	}




	// *****************************************************************************
	// Purpose        sets product file
	// Inputs
	// Remarks
	// Returns
	function SetProductFile( $productID, $eproduct_filename )
	{
		db_query( "update ".PRODUCTS_TABLE." set eproduct_filename='".xEscSQL($eproduct_filename)."' ".
			" where productID=".(int)$productID );

	}



	// *****************************************************************************
	// Purpose        adds product
	// Inputs
	//                                $categoryID                        - category ID ( see CATEGORIES_TABLE )
	//                                $name                                - name of product
	//                                $Price                                - price of product
	//                                $description                - product description
	//                                $in_stock                        - stock counter
	//                                $brief_description  - short product description
	//                                $list_price                        - old price
	//                                $product_code                - product code
	//                                $sort_order                        - sort order
	//                                $ProductIsProgram                - 1 if product is program,
	//                                                                        0 otherwise
	//                                $eproduct_filename                - program filename ( it is index of $_FILE variable )
	//                                $eproduct_available_days        - program is available days
	//                                                                        to download
	//                                $eproduct_download_times        - attempt count download file
	//                                $weight                        - product weigth
	//                                $meta_description        - meta tag description
	//                                $meta_keywords                - meta tag keywords
	//                                $free_shipping                - 1 product has free shipping,
	//                                                        0 - otherwise
	//                                $min_order_amount        - minimum order amount
	//                                $classID                - tax class ID
	// Remarks
	// Returns
	function AddProduct(
		$categoryID, $name, $Price, $description,
		$in_stock,
		$brief_description, $list_price,
		$product_code, $sort_order,
		$ProductIsProgram, $eproduct_filename,
		$eproduct_available_days, $eproduct_download_times,
		$weight, $meta_description, $meta_keywords,
		$free_shipping, $min_order_amount, $shipping_freight,
		$classID, $title, $updateGCV = 1 )
	{
		// special symbol prepare
		if ( $free_shipping )
			$free_shipping = 1;
		else
			$free_shipping = 0;

		if ( $classID == null ) $classID = "NULL";

		if ( $min_order_amount == 0 ) $min_order_amount = 1;

		if ( !$ProductIsProgram ) $eproduct_filename = "";

		if ( $eproduct_filename != "" )
		{
			if ( trim($_FILES[$eproduct_filename]["name"]) != ""  )
			{
				if ( $_FILES[$eproduct_filename]["size"]!=0 )
					$r = move_uploaded_file($_FILES[$eproduct_filename]["tmp_name"],
						"core/files/".$_FILES[$eproduct_filename]["name"]);
				$eproduct_filename = trim($_FILES[$eproduct_filename]["name"]);
				SetRightsToUploadedFile( "core/files/".$eproduct_filename );
			}
		}

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
			"        free_shipping, min_order_amount, shipping_freight, classID, title ".
			" ) ".
			" VALUES (".
			(int)$categoryID.",'".
			xToText(trim($name))."','".
			xEscSQL($description)."', ".
			"0, '".
			(double)$Price."', ".
			(int)$in_stock.", ".
			" 0, 0, 1, '".
			xEscSQL($brief_description)."', '".
			(double)$list_price."', '".
			xToText(trim($product_code))."', ".
			(int)$sort_order.", '".
			xEscSQL(get_current_time())."',  '".
			xEscSQL($eproduct_filename)."', ".
			(int)$eproduct_available_days.", ".
			(int)$eproduct_download_times.",  ".
			(float)$weight.", ".
			"'".xToText(trim($meta_description))."', ".
			"'".xToText(trim($meta_keywords))."', ".
			(int)$free_shipping.", ".
			(int)$min_order_amount.", ".
			(double)$shipping_freight.", ".
			(int)$classID.", '".
			xToText(trim($title))."' ".
			");" );
		$insert_id = db_insert_id();
		if ( $updateGCV == 1 && CONF_UPDATE_GCV == '1') update_psCount(1);
		return $insert_id;
	}


	// *****************************************************************************
	// Purpose        deletes product
	// Inputs   $productID - product ID
	// Remarks
	// Returns        true if success, else false otherwise
	function DeleteProduct($productID, $updateGCV = 1)
	{
		$whereClause = " where productID=".(int)$productID;

		$q = db_query( "select itemID from ".SHOPPING_CART_ITEMS_TABLE." ".$whereClause );
		while( $row=db_fetch_row($q) )
			db_query( "delete from ".SHOPPING_CARTS_TABLE." where itemID=".(int)$row["itemID"] );

		// delete all items for this product
		db_query("update ".SHOPPING_CART_ITEMS_TABLE.
			" set productID=NULL ".$whereClause);

		// delete all product option values
		db_query("delete from ".PRODUCTS_OPTIONS_SET_TABLE.$whereClause);
		db_query("delete from ".PRODUCT_OPTIONS_VALUES_TABLE.$whereClause);

		// delete pictures
		DeleteThreePictures2($productID);

		// delete additional categories records
		db_query("delete from ".CATEGORIY_PRODUCT_TABLE.$whereClause);

		// delete discussions
		db_query("delete from ".DISCUSSIONS_TABLE.$whereClause);

		// delete special offer
		db_query("delete from ".SPECIAL_OFFERS_TABLE.$whereClause);

		// delete related items
		db_query("delete from ".RELATED_PRODUCTS_TABLE.$whereClause );
		db_query("delete from ".RELATED_PRODUCTS_TABLE." where Owner=".(int)$productID);

		// delete product
		db_query("delete from ".PRODUCTS_TABLE.$whereClause);


		if ( $updateGCV == 1 && CONF_UPDATE_GCV == '1') update_psCount(1);

		return true;
	}


	// *****************************************************************************
	// Purpose        deletes all products of category
	// Inputs   $categoryID - category ID
	// Remarks
	// Returns        true if success, else false otherwise
	function DeleteAllProductsOfThisCategory($categoryID)
	{
		$q=db_query("select productID from ".PRODUCTS_TABLE.
			" where categoryID=".(int)$categoryID);
		$res=true;
		while( $r=db_fetch_row( $q ) )
		{
			if ( !DeleteProduct( $r["productID"], 0 ) )
				$res = false;
		}

		if ( CONF_UPDATE_GCV == '1') update_psCount(1);

		return $res;
	}


	// *****************************************************************************
	// Purpose        gets extra parametrs
	// Inputs   $productID - product ID
	// Remarks
	// Returns        array of value extraparametrs
	//                                each item of this array has next struture
	//                                        first type "option_type" = 0
	//                                                "name"                                        - parametr name
	//                                                "option_value"                        - value
	//                                                "option_type"                        - 0
	//                                        second type "option_type" = 1
	//                                                "name"                                        - parametr name
	//                                                "option_show_times"                - how times does show in client side this
	//                                                                                                parametr to select
	//                                                "variantID_default"                - variant ID by default
	//                                                "values_to_select"                - array of variant value to select
	//                                                        each item of "values_to_select" array has next structure
	//                                                                "variantID"                        - variant ID
	//                                                                "price_surplus"                - to added to price
	//                                                                "option_value"                - value
	function GetExtraParametrs( $productID ){

		if(!is_array($productID)){

			$ProductIDs = array($productID);
			$IsProducts = false;
		}elseif(count($productID)) {

			$ProductIDs = &$productID;
			$IsProducts = true;
		}else {

			return array();
		}

		$ProductsExtras = array();
		$sql = 'select povt.productID,pot.optionID,pot.name,povt.option_value,povt.option_type,povt.option_show_times, povt.variantID, povt.optionID
		FROM ?#PRODUCT_OPTIONS_VALUES_TABLE as povt LEFT JOIN  ?#PRODUCT_OPTIONS_TABLE as pot ON pot.optionID=povt.optionID
		WHERE povt.productID IN (?@) ORDER BY pot.sort_order, pot.name
		';
		$Result = db_phquery($sql, $ProductIDs);

		while ($_Row = db_fetch_assoc($Result)) {

			$_Row;
			$b=null;
			if (($_Row['option_type']==0 || $_Row['option_type']==NULL) && strlen( trim($_Row['option_value']))>0){

				$ProductsExtras[$_Row['productID']][] = array(
					'option_type' => $_Row['option_type'],
					'name' => $_Row['name'],
					'option_value' => $_Row['option_value']
				);
			}
			/**
			* @features "Extra options values"
			* @state begin
			*/
			else if ( $_Row['option_type']==1 ){

				//fetch all option values variants
				$sql = 'select povvt.option_value, povvt.variantID, post.price_surplus
				FROM '.PRODUCTS_OPTIONS_SET_TABLE.' as post
				LEFT JOIN '.PRODUCTS_OPTIONS_VALUES_VARIANTS_TABLE.' as povvt
				ON povvt.variantID=post.variantID
				WHERE povvt.optionID='.$_Row['optionID'].' AND post.productID='.$_Row['productID'].' AND povvt.optionID='.$_Row['optionID'].'
				ORDER BY povvt.sort_order, povvt.option_value
				';
				$q2=db_query($sql);
				$_Row['values_to_select']=array();
				$i=0;
				while( $_Rowue = db_fetch_assoc($q2)  ){

					$_Row['values_to_select'][$i]=array();
					$_Row['values_to_select'][$i]['option_value'] = $_Rowue['option_value'];
					// if ( $_Rowue['price_surplus'] > 0 )$_Row['values_to_select'][$i]['option_value'] .= ' (+ '.show_price($_Rowue['price_surplus']).')';
					// elseif($_Rowue['price_surplus'] < 0 )$_Row['values_to_select'][$i]['option_value'] .= ' (- '.show_price(-$_Rowue['price_surplus']).')';

					$_Row['values_to_select'][$i]['option_valueWithOutPrice'] = $_Rowue['option_value'];
					$_Row['values_to_select'][$i]['price_surplus'] = show_priceWithOutUnit($_Rowue['price_surplus']);
					$_Row['values_to_select'][$i]['variantID']=$_Rowue['variantID'];
					$i++;
				}
				$_Row['values_to_select_count'] = count($_Row['values_to_select']);
				$ProductsExtras[$_Row['productID']][] = $_Row;
			}
			/**
			* @features "Extra options values"
			* @state end
			*/
		}
		if(!$IsProducts){

			if(!count($ProductsExtras))return array();
			else {
				return $ProductsExtras[$productID];
			}
		}
		return $ProductsExtras;
	}


	function _setPictures( & $product )
	{
		if($product['pictures'])
		{
			$product['pictures']=explode(',',$product['pictures']);
		}
					
		if ( isset($product['default_picture'])&&isset($product['productID']) )
		{
			$pictire=db_query("select filename, thumbnail, enlarged from ".
				PRODUCT_PICTURES." where photoID=".(int)$product["default_picture"] );
			$pictire_row=db_fetch_row($pictire);
			$product['picture'] = $pictire_row['filename'];
			$product['thumbnail']= $pictire_row['thumbnail'];
			$product['big_picture']= $pictire_row['enlarged'];
		}
	}


	function GetProductInSubCategories( $callBackParam, &$count_row, $navigatorParams = null )
	{

		if ( $navigatorParams != null )
		{
			$offset                        = $navigatorParams["offset"];
			$CountRowOnPage        = $navigatorParams["CountRowOnPage"];
		}
		else
		{
			$offset = 0;
			$CountRowOnPage = 0;
		}

		$categoryID        = $callBackParam["categoryID"];
		$subCategoryIDArray = catGetSubCategories( $categoryID );
		$cond = "";
		foreach( $subCategoryIDArray as $subCategoryID )
		{
			if ( $cond != "" )
				$cond .= " OR categoryID=".(int)$subCategoryID;
			else
				$cond .= " categoryID=".(int)$subCategoryID." ";
		}
		$whereClause = "";
		if ( $cond != "" )
			$whereClause = " where ".$cond;

		$result = array();
		if ( $whereClause == "" )
		{
			$count_row = 0;
			return $result;
		}

		$q=db_query("select categoryID, name, brief_description, ".
			" customers_rating, Price, in_stock, ".
			" customer_votes, list_price, ".
			" productID, default_picture, sort_order from ".PRODUCTS_TABLE.
			" ".$whereClause." order by ".CONF_DEFAULT_SORT_ORDER);
		$i=0;
		while( $row=db_fetch_row($q) )
		{
			if ( ($i >= $offset && $i < $offset + $CountRowOnPage) ||
				$navigatorParams == null  )
			{
				$row["PriceWithUnit"]                = show_price($row["Price"]);
				$row["list_priceWithUnit"]         = show_price($row["list_price"]);
				// you save (value)
				$row["SavePrice"]                = show_price($row["list_price"]-$row["Price"]);

				// you save (%)
				if ($row["list_price"])
					$row["SavePricePercent"] = ceil(((($row["list_price"]-$row["Price"])/$row["list_price"])*100));

				_setPictures( $row );

				$row["product_extra"]=GetExtraParametrs($row["productID"]);
				$row["PriceWithOutUnit"]= show_priceWithOutUnit( $row["Price"] );
				$result[] = $row;
			}
			$i++;
		}
		$count_row = $i;
		return $result;
	}


	// *****************************************************************************
	// Purpose        gets all products by categoryID
	// Inputs             $callBackParam item
	//                        "categoryID"
	//                        "fullFlag"
	// Remarks
	// Returns
	function prdGetProductByCategory( $callBackParam, &$count_row, $navigatorParams = null )
	{

		if ( $navigatorParams != null )
		{
			$offset                        = $navigatorParams["offset"];
			$CountRowOnPage        = $navigatorParams["CountRowOnPage"];
		}
		else
		{
			$offset = 0;
			$CountRowOnPage = 0;
		}

		$result = array();

		$categoryID        = $callBackParam["categoryID"];
		$fullFlag        = $callBackParam["fullFlag"];
		if ( $fullFlag )
		{
			$conditions = array( " categoryID=".(int)$categoryID." " );
			$q = db_query("select productID from ".
				CATEGORIY_PRODUCT_TABLE." where  categoryID=".(int)$categoryID);
			while( $products = db_fetch_row( $q ) )
				$conditions[] = " productID=".(int)$products[0];

			$data = array();
			foreach( $conditions as $cond )
			{
				$q=db_query("select categoryID, name, brief_description, ".
					" customers_rating, Price, in_stock, ".
					" customer_votes, list_price, ".
					" productID, default_picture, sort_order, items_sold, enabled, product_code from ".PRODUCTS_TABLE.
					" where ".$cond." order by ".CONF_DEFAULT_SORT_ORDER);
				while( $row = db_fetch_row($q) )
				{
					$row["PriceWithUnit"]                = show_price($row["Price"]);
					$row["list_priceWithUnit"]         = show_price($row["list_price"]);
					// you save (value)
					$row["SavePrice"]                = show_price($row["list_price"]-$row["Price"]);

					// you save (%)
					if ($row["list_price"])
						$row["SavePricePercent"] = ceil(((($row["list_price"]-$row["Price"])/$row["list_price"])*100));
					_setPictures( $row );
					$row["product_extra"]=GetExtraParametrs($row["productID"]);
					$row["product_extra_count"]=count($row["product_extra"]);
					$row["PriceWithOutUnit"]= show_priceWithOutUnit( $row["Price"] );
					$data[] = $row;
				}
			}

			function _compare( $row1, $row2 )
			{
				if ( (int)$row1["sort_order"] == (int)$row2["sort_order"] )
					return 0;
				return ((int)$row1["sort_order"] < (int)$row2["sort_order"]) ? -1 : 1;
			}

			usort($data, "_compare");

			$result = array();
			$i = 0;
			$ccdata = count($data);
			for ($s=0; $s<$ccdata; $s++)
			{
				if ( ($i >= $offset && $i < $offset + $CountRowOnPage) ||
					$navigatorParams == null )
					$result[] = $data[$s];
				$i++;
			}
			$count_row = $i;
			return $result;
		}
		else
		{
			$q=db_query("select categoryID, name, brief_description, ".
				" customers_rating, Price, in_stock, ".
				" customer_votes, list_price, ".
				" productID, default_picture, sort_order, items_sold, enabled, product_code from ".PRODUCTS_TABLE.
				" where categoryID=".(int)$categoryID." order by ".CONF_DEFAULT_SORT_ORDER);
			$i=0;
			while( $row=db_fetch_row($q) )
			{
				if ( ($i >= $offset && $i < $offset + $CountRowOnPage) ||
					$navigatorParams == null  )
					$result[] = $row;
				$i++;
			}
			$count_row = $i;
			return $result;
		}
	}




	function _getConditionWithCategoryConjWithSubCategories( $condition, $categoryID ) //fetch products from current category and all its subcategories
	{
		$new_condition = "";
		$tempcond = "";

		$categoryID_Array = catGetSubCategories( $categoryID );
		$categoryID_Array[] = (int)$categoryID;

		foreach( $categoryID_Array as $catID )
		{
			if ( $new_condition != "" )
				$new_condition .= " OR ";

			$new_condition .= _getConditionWithCategoryConj($tempcond, $catID);

		}
		if ( $condition == "" ) return $new_condition;
		else return $condition." AND (".$new_condition.")";
	}


	function _getConditionWithCategoryConj( $condition, $categoryID ) //fetch products from current category
	{
		$category_condition = "";
		$q = db_query("select productID from ".
			CATEGORIY_PRODUCT_TABLE." where categoryID=".(int)$categoryID);
		$icounter = 0;
		while( $product = db_fetch_row( $q ) )
		{
			if ( $icounter==0 )
				$category_condition .= " productID IN (";
			if ( $icounter>0 )
				$category_condition .= ",";
			$category_condition .= (int)$product[0];
			$icounter++;
		}
		if ( $icounter>0 ) $category_condition .= ")";

		if ( $condition == "" )
		{
			if ( $category_condition == "" )
				return "categoryID=".(int)$categoryID;
			else
				return "(".$category_condition." OR categoryID=".(int)$categoryID.")";
		}
		else
		{
			if ( $category_condition == "" )
				return $condition." AND categoryID=".(int)$categoryID;
			else
				return "(".$condition." AND (".$category_condition." OR categoryID=".(int)$categoryID."))";
		}      
	}


	// *****************************************************************************
	// Purpose
	// Inputs
	//                                $productID - product ID
	//                                $template  - array of item
	//                                        "optionID"        - option ID
	//                                        "value"                - value or variant ID
	// Remarks
	// Returns        returns true if product matches to extra parametr template
	//                        false otherwise
	function _testExtraParametrsTemplate( $productID, &$template ){

		// get category ID
		$categoryID = $template["categoryID"];

		foreach( $template as $key => $item ){

			if( !isset($item["optionID"]) ) continue;

			if((string)$key == "categoryID" ) continue;

			// get value to search
			if ( $item['set_arbitrarily'] == 1 ){

				$valueFromForm = $item["value"];
			}else{

				if ( (int)$item["value"] == 0 ) continue;

				if(!isset($template[$key]['__option_value_from_db'])){

					$SQL = 'select option_value FROM ?#PRODUCTS_OPTIONS_VALUES_VARIANTS_TABLE WHERE variantID=?
					';
					$option_value = db_fetch_assoc(db_phquery($SQL, (int)$item['value']));
					$template[$key]['__option_value_from_db'] = $option_value['option_value'];
				}
				$valueFromForm = $template[$key]['__option_value_from_db'];
			}

			// get option value
			$SQL = 'select option_value, option_type FROM ?#PRODUCT_OPTIONS_VALUES_TABLE WHERE optionID=? AND productID=?
			';
			$q = db_phquery($SQL,(int)$item['optionID'],(int)$productID);

			if(!($row=db_fetch_row($q))){

				if ( trim($valueFromForm) == '' ) continue;
				else return false;
			}

			$option_value = $row['option_value'];
			$option_type        = $row['option_type'];
			$valueFromDataBase = array();

			if ( $option_type == 0 ){

				$valueFromDataBase[] = $option_value;
			}else{

				$SQL = 'select povv.option_value FROM ?#PRODUCTS_OPTIONS_SET_TABLE as pos
				LEFT JOIN ?#PRODUCTS_OPTIONS_VALUES_VARIANTS_TABLE as povv ON pos.variantID=povv.variantID WHERE pos.optionID=? AND pos.productID=?
				';
				$Result = db_phquery($SQL,(int)$item["optionID"], (int)$productID);
				while ($Row = db_fetch_assoc($Result)){

					$valueFromDataBase[] = $Row['option_value'];
				}
			}

			if ( trim($valueFromForm) != '' ){

				$existFlag = false;
				$vcount = count($valueFromDataBase);
				for ($v=0; $v<$vcount; $v++) {
					if(strstr(strtolower((string)trim($valueFromDataBase[$v])),strtolower((string)trim($valueFromForm)))){
						$existFlag = true;
						break;
					}
				}
				if ( !$existFlag ) return false;
			}
		}
		return true;
	}




	function _deletePercentSymbol( &$str )
	{
		$str = str_replace( "%", "", $str );
		return $str;
	}


	function prdSearchProductByTemplateAdmin($callBackParam, &$count_row, $navigatorParams = null )
	{
		// navigator params
		if ( $navigatorParams != null )
		{
			$offset                        = xEscSQL($navigatorParams["offset"]);
			$CountRowOnPage        = xEscSQL($navigatorParams["CountRowOnPage"]);
		}
		else
		{
			$offset = 0;
			$CountRowOnPage = 0;
		}

		if ( isset($callBackParam["extraParametrsTemplate"]) ){

			$replicantExtraParametersTpl = $callBackParam["extraParametrsTemplate"];
		}
		// special symbol prepare
		if ( isset($callBackParam["search_simple"]) )
		{
			/*                for( $i=0; $i<count($callBackParam["search_simple"]); $i++ )
			{
			$callBackParam["search_simple"][$i] = $callBackParam["search_simple"][$i];
			}*/
			_deletePercentSymbol( $callBackParam["search_simple"] );
		}
		if ( isset($callBackParam["name"]) )
		{
			for( $i=0; $i<count($callBackParam["name"]); $i++ )
				$callBackParam["name"][$i] = xToText(trim($callBackParam["name"][$i]) );
			_deletePercentSymbol( $callBackParam["name"][$i] );
		}
		if ( isset($callBackParam["product_code"]) )
		{
			for( $i=0; $i<count($callBackParam["product_code"]); $i++ )
			{
				$callBackParam["product_code"][$i] = xToText(trim($callBackParam["product_code"][$i] ));
			}
			_deletePercentSymbol( $callBackParam["product_code"] );
		}

		if ( isset($callBackParam["extraParametrsTemplate"]) )
		{
			foreach( $callBackParam["extraParametrsTemplate"] as $key => $value )
			{
				if ( is_int($key) )
				{
					$callBackParam["extraParametrsTemplate"][$key] = xEscSQL(trim($callBackParam["extraParametrsTemplate"][$key]) );
					_deletePercentSymbol( $callBackParam["extraParametrsTemplate"][$key] );
				}
			}
		}


		$where_clause = "";

		if ( isset($callBackParam["search_simple"]) )
		{
			if (!count($callBackParam["search_simple"])) //empty array
			{
				$where_clause = " where 0";
			}
			else //search array is not empty
			{
				$sscount = count($callBackParam["search_simple"]);
				for ($n=0; $n<$sscount; $n++)
				{
					if ( $where_clause != "" ) $where_clause .= " AND ";
					$where_clause .= " ( LOWER(name) LIKE '%".xToText(trim(strtolower($callBackParam["search_simple"][$n])))."%' OR ".
					"   LOWER(description) LIKE '%".xEscSQL(trim(strtolower($callBackParam["search_simple"][$n])))."%' OR ".
					"   LOWER(product_code) LIKE '%".xEscSQL(trim(strtolower($callBackParam["search_simple"][$n])))."%' OR ".
					"   LOWER(brief_description) LIKE '%".xEscSQL(trim(strtolower($callBackParam["search_simple"][$n])))."%' ) ";
				}

				if ( $where_clause != "" )
				{
					$where_clause = " where categoryID>1 and enabled=1 and ".$where_clause;
				}
				else
				{
					$where_clause = " where categoryID>1 and enabled=1";
				}


			}

		}
		else
		{

			// "enabled" parameter
			if ( isset($callBackParam["enabled"]) )
			{
				if ( $where_clause != "" )
					$where_clause .= " AND ";
				$where_clause.=" enabled=".(int)$callBackParam["enabled"];
			}

			// take into "name" parameter
			if ( isset($callBackParam["name"]) )
			{
				foreach( $callBackParam["name"] as $name )
					if (strlen($name)>0)
					{
						if ( $where_clause != "" )
							$where_clause .= " AND ";
						$where_clause .= " LOWER(name) LIKE '%".xToText(trim(strtolower($name)))."%' ";
					}
			}

			// take into "product_code" parameter
			if ( isset($callBackParam["product_code"]) )
			{
				foreach( $callBackParam["product_code"] as $product_code )
				{
					if ( $where_clause != "" )
						$where_clause .= " AND ";
					$where_clause .= " LOWER(product_code) LIKE '%".xToText(trim(strtolower($product_code)))."%' ";
				}
			}

			// take into "price" parameter
			if ( isset($callBackParam["price"]) )
			{
				$price = $callBackParam["price"];

				if ( trim($price["from"]) != "" && $price["from"] != null )
				{
					if ( $where_clause != "" )
						$where_clause .= " AND ";
					$from        = ConvertPriceToUniversalUnit( $price["from"] );
					$where_clause .= " Price>=".(double)$from." ";
				}
				if ( trim($price["to"]) != "" && $price["to"] != null )
				{
					if ( $where_clause != "" )
						$where_clause .= " AND ";
					$to                = ConvertPriceToUniversalUnit( $price["to"] );
					$where_clause .= " Price<=".(double)$to." ";
				}
			}


			// categoryID
			if ( isset($callBackParam["categoryID"]) )
			{
				$searchInSubcategories = false;
				if ( isset($callBackParam["searchInSubcategories"]) )
				{
					if ( $callBackParam["searchInSubcategories"] )
						$searchInSubcategories = true;
					else
						$searchInSubcategories = false;
				}

				if ( $searchInSubcategories )
				{
					$where_clause = _getConditionWithCategoryConjWithSubCategories( $where_clause,
						$callBackParam["categoryID"] );
				}
				else
				{
					$where_clause = _getConditionWithCategoryConj( $where_clause,
						$callBackParam["categoryID"] );
				}
			}

			if ( $where_clause != "" )
				$where_clause = "where ".$where_clause;

		}



		$order_by_clause = "order by ".CONF_DEFAULT_SORT_ORDER;

		if ( isset($callBackParam["sort"]) )
		{
			if (        $callBackParam["sort"] == "categoryID"                        ||
				$callBackParam["sort"] == "name"                                ||
				$callBackParam["sort"] == "brief_description"        ||
				$callBackParam["sort"] == "in_stock"                        ||
				$callBackParam["sort"] == "Price"                                ||
				$callBackParam["sort"] == "customer_votes"                ||
				$callBackParam["sort"] == "customers_rating"        ||
				$callBackParam["sort"] == "list_price"                        ||
				$callBackParam["sort"] == "sort_order"                        ||
				$callBackParam["sort"] == "items_sold"                        ||
				$callBackParam["sort"] == "product_code"                ||
				$callBackParam["sort"] == "shipping_freight"        ||
				$callBackParam["sort"] == "viewed_times" )
			{
				$order_by_clause = " order by ".xEscSQL($callBackParam["sort"])." ASC ";
				if (  isset($callBackParam["direction"]) )
					if (  $callBackParam["direction"] == "DESC" )
						$order_by_clause = " order by ".xEscSQL($callBackParam["sort"])." DESC ";
			}
		}

		$sqlQueryCount = "select count(*) from ".PRODUCTS_TABLE." ".$where_clause;
		$q = db_query( $sqlQueryCount );
		$products_count = db_fetch_row($q);
		$products_count = $products_count[0];
		$limit_clause= (isset($callBackParam["extraParametrsTemplate"]) || !$CountRowOnPage)?"":" LIMIT ".$offset.",".$CountRowOnPage;
		$sqlQuery = "select categoryID, name, brief_description, ".
		" customers_rating, Price, in_stock, tpl_city,".
		" customer_votes, list_price, ".
		" productID, default_picture, sort_order, items_sold, enabled, ".
		" product_code, description, shipping_freight, viewed_times, min_order_amount from ".PRODUCTS_TABLE." ".
		$where_clause." ".$order_by_clause.$limit_clause;

		$q = db_query( $sqlQuery );
		$result = array();
		$i = 0;

		if ($offset >= 0 && $offset <= $products_count )
		{
			while( $row = db_fetch_row($q) )
			{

				if ( isset($callBackParam["extraParametrsTemplate"]) ){

					// take into "extra" parametrs
					$testResult = _testExtraParametrsTemplate( $row["productID"], $replicantExtraParametersTpl );
					if ( !$testResult ) continue;
				}

				if ( (($i >= $offset || !isset($callBackParam["extraParametrsTemplate"])) && $i < $offset + $CountRowOnPage) ||
					$navigatorParams == null  )
				{
					$row["PriceWithUnit"]     = show_price($row["Price"]);
					$row["list_priceWithUnit"]= show_price($row["list_price"]);
					// you save (value)
					$row["SavePrice"]         = show_price($row["list_price"]-$row["Price"]);

					// you save (%)
					if ($row["list_price"]) $row["SavePricePercent"] = ceil(((($row["list_price"]-$row["Price"])/$row["list_price"])*100));
					_setPictures( $row );
					$row["product_extra"]     = GetExtraParametrs( $row["productID"] );
					$row["product_extra_count"]  = count($row["product_extra"]);
					$row["PriceWithOutUnit"]  = show_priceWithOutUnit( $row["Price"] );
					if ( ((double)$row["shipping_freight"]) > 0 ) $row["shipping_freightUC"] = show_price( $row["shipping_freight"] );
					$row["name"]              = ($row["name"]) ? $row["name"] : $row['tpl_city'];
					$row["description"]       = $row["description"];
					$row["brief_description"] = $row["brief_description"];
					$row["product_code"]      = $row["product_code"];
					$row["viewed_times"]      = $row["viewed_times"];
					$row["items_sold"]        = $row["items_sold"];
					$result[] = $row;
				}
				$i++;
			}
		}
		$count_row = isset($callBackParam["extraParametrsTemplate"])?$i:$products_count;
		return $result;
	}

	// *****************************************************************************
	// Purpose        gets all products by categoryID
	// Inputs             $callBackParam item
	// 	"search_simple" 	- string search simple
	//  "direction"        	- sort direction DESC - by descending, by ascending otherwise
	//  "searchInSubcategories" - if true function searches product in subcategories, otherwise it does not
	//  "searchInEnabledSubcategories"  - this parametr is actual when "searchInSubcategories" parametr is specified
	//   	if true this function take in mind enabled categories only
	//  "categoryID"  - is not set or category ID to be searched
	//  "name"      - array of name template
	//  "product_code"    - array of product code template
	//  "price"      - array of item
	//  "from"  - down price range
	//  "to"  - up price range
	//  "enabled" - value of column "enabled" in database
	//  "extraParametrsTemplate"
	// Remarks
	// Returns
	function prdSearchProductByTemplate($callBackParam, &$count_row, $navigatorParams = null )
	{
		// navigator params
		if ( $navigatorParams != null )
		{
			$offset                        = xEscSQL($navigatorParams["offset"]);
			$CountRowOnPage        = xEscSQL($navigatorParams["CountRowOnPage"]);
		}
		else
		{
			$offset = 0;
			$CountRowOnPage = 0;
		}

		if ( isset($callBackParam["extraParametrsTemplate"]) ){

			$replicantExtraParametersTpl = $callBackParam["extraParametrsTemplate"];
		}
		// special symbol prepare
		if ( isset($callBackParam["search_simple"]) )
		{
			/*                for( $i=0; $i<count($callBackParam["search_simple"]); $i++ )
			{
			$callBackParam["search_simple"][$i] = $callBackParam["search_simple"][$i];
			}*/
			_deletePercentSymbol( $callBackParam["search_simple"] );
		}
		if ( isset($callBackParam["name"]) )
		{
			for( $i=0; $i<count($callBackParam["name"]); $i++ )
				$callBackParam["name"][$i] = xToText(trim($callBackParam["name"][$i]) );
			_deletePercentSymbol( $callBackParam["name"][$i] );
		}
		if ( isset($callBackParam["product_code"]) )
		{
			for( $i=0; $i<count($callBackParam["product_code"]); $i++ )
			{
				$callBackParam["product_code"][$i] = xToText(trim($callBackParam["product_code"][$i] ));
			}
			_deletePercentSymbol( $callBackParam["product_code"] );
		}

		if ( isset($callBackParam["extraParametrsTemplate"]) )
		{
			foreach( $callBackParam["extraParametrsTemplate"] as $key => $value )
			{
				if ( is_int($key) )
				{
					$callBackParam["extraParametrsTemplate"][$key] = xEscSQL(trim($callBackParam["extraParametrsTemplate"][$key]) );
					_deletePercentSymbol( $callBackParam["extraParametrsTemplate"][$key] );
				}
			}
		}


		$where_clause = "";

		if ( isset($callBackParam["search_simple"]) )
		{
			if (!count($callBackParam["search_simple"])) //empty array
			{
				$where_clause = " where 0";
			}
			else //search array is not empty
			{
				$sscount = count($callBackParam["search_simple"]);
				for ($n=0; $n<$sscount; $n++)
				{
					if ( $where_clause != "" ) $where_clause .= " AND ";
					$where_clause .= " ( LOWER(name) LIKE '%".xToText(trim(strtolower($callBackParam["search_simple"][$n])))."%' OR ".
					"   LOWER(description) LIKE '%".xEscSQL(trim(strtolower($callBackParam["search_simple"][$n])))."%' OR ".
					"   LOWER(product_code) LIKE '%".xEscSQL(trim(strtolower($callBackParam["search_simple"][$n])))."%' OR ".
					"   LOWER(brief_description) LIKE '%".xEscSQL(trim(strtolower($callBackParam["search_simple"][$n])))."%' ) ";
				}

				if ( $where_clause != "" )
				{
					$where_clause = " where categoryID>1 and enabled=1 and ".$where_clause;
				}
				else
				{
					$where_clause = " where categoryID>1 and enabled=1";
				}

				if(CONF_CHECKSTOCK && CONF_SHOW_NULL_STOCK){
					if ( $where_clause != "" )
						$where_clause .= " AND in_stock>0 ";
					else
						$where_clause = "where in_stock>0 ";		
				}
			}

		}
		else
		{

			// "enabled" parameter
			if ( isset($callBackParam["enabled"]) )
			{
				if ( $where_clause != "" )
					$where_clause .= " AND ";
				$where_clause.=" enabled=".(int)$callBackParam["enabled"];
			}

			// take into "name" parameter
			if ( isset($callBackParam["name"]) )
			{
				foreach( $callBackParam["name"] as $name )
					if (strlen($name)>0)
					{
						if ( $where_clause != "" )
							$where_clause .= " AND ";
						$where_clause .= " LOWER(name) LIKE '%".xToText(trim(strtolower($name)))."%' ";
					}
			}

			// take into "product_code" parameter
			if ( isset($callBackParam["product_code"]) )
			{
				foreach( $callBackParam["product_code"] as $product_code )
				{
					if ( $where_clause != "" )
						$where_clause .= " AND ";
					$where_clause .= " LOWER(product_code) LIKE '%".xToText(trim(strtolower($product_code)))."%' ";
				}
			}

			// take into "price" parameter
			if ( isset($callBackParam["price"]) )
			{
				$price = $callBackParam["price"];

				if ( trim($price["from"]) != "" && $price["from"] != null )
				{
					if ( $where_clause != "" )
						$where_clause .= " AND ";
					$from        = ConvertPriceToUniversalUnit( $price["from"] );
					$where_clause .= " Price>=".(double)$from." ";
				}
				if ( trim($price["to"]) != "" && $price["to"] != null )
				{
					if ( $where_clause != "" )
						$where_clause .= " AND ";
					$to                = ConvertPriceToUniversalUnit( $price["to"] );
					$where_clause .= " Price<=".(double)$to." ";
				}
			}

			if(CONF_CHECKSTOCK && CONF_SHOW_NULL_STOCK){
				if ( $where_clause != "" )
					$where_clause .= " AND in_stock>0 ";
				else
					$where_clause = "where in_stock>0 ";		
			}

			// categoryID
			if ( isset($callBackParam["categoryID"]) )
			{
				$searchInSubcategories = false;
				if ( isset($callBackParam["searchInSubcategories"]) )
				{
					if ( $callBackParam["searchInSubcategories"] )
						$searchInSubcategories = true;
					else
						$searchInSubcategories = false;
				}

				if ( $searchInSubcategories )
				{
					$where_clause = _getConditionWithCategoryConjWithSubCategories( $where_clause,
						$callBackParam["categoryID"] );
				}
				else
				{
					$where_clause = _getConditionWithCategoryConj( $where_clause,
						$callBackParam["categoryID"] );
				}
			}

			if ( $where_clause != "" )
				$where_clause = "where ".$where_clause;

		}



		$order_by_clause = "order by ".CONF_DEFAULT_SORT_ORDER."";

		if ( isset($callBackParam["sort"]) )
		{
			if (        $callBackParam["sort"] == "categoryID"                        ||
				$callBackParam["sort"] == "name"                                ||
				$callBackParam["sort"] == "brief_description"        ||
				$callBackParam["sort"] == "in_stock"                        ||
				$callBackParam["sort"] == "Price"                                ||
				$callBackParam["sort"] == "customer_votes"                ||
				$callBackParam["sort"] == "customers_rating"        ||
				$callBackParam["sort"] == "list_price"                        ||
				$callBackParam["sort"] == "sort_order"                        ||
				$callBackParam["sort"] == "items_sold"                        ||
				$callBackParam["sort"] == "product_code"                ||
				$callBackParam["sort"] == "shipping_freight"        ||
				$callBackParam["sort"] == "viewed_times" )
			{
				$order_by_clause = " order by ".xEscSQL($callBackParam["sort"])." ASC ";
				if (  isset($callBackParam["direction"]) )
					if (  $callBackParam["direction"] == "DESC" )
						$order_by_clause = " order by ".xEscSQL($callBackParam["sort"])." DESC ";
			}
		}

		$sqlQueryCount = "select count(*) from ".PRODUCTS_TABLE." ".$where_clause;
		$q = db_query( $sqlQueryCount );
		$products_count = db_fetch_row($q);
		$products_count = $products_count[0];
		$limit_clause= (isset($callBackParam["extraParametrsTemplate"]) || !$CountRowOnPage)?"":" LIMIT ".$offset.",".$CountRowOnPage;
		$sqlQuery = "select productID, categoryID, p.UID, customers_rating, customer_votes, default_picture, items_sold, 
		enabled, viewed_times, pictures, tpl_city,
		IF(p.tplID>0,tp.Price,p.Price) Price,
		IF(p.tplID>0,tp.in_stock,p.in_stock) in_stock,
		IF(p.tplID>0,tp.list_price,p.list_price) list_price,
		IF(p.tplID>0,tp.sort_order,p.sort_order) sort_order,
		IF(p.tplID>0,tp.product_code,p.product_code) product_code,
		IF(p.tplID>0,tp.folder_pictures,p.folder_pictures) folder_pictures,
		IF(p.tplID>0,REPLACE( tp.name,'{city}',p.tpl_city ),p.name) name,
		IF(p.tplID>0,REPLACE( tp.brief_description,'{city}',p.tpl_city ),p.brief_description) brief_description,
		IF(p.tplID>0,REPLACE( tp.description,'{city}',p.tpl_city ),p.description) description,
		IF(p.tplID>0,tp.shipping_freight,p.shipping_freight) shipping_freight,
		IF(p.tplID>0,tp.min_order_amount,p.min_order_amount) min_order_amount
		from ".PRODUCTS_TABLE." p LEFT JOIN ".
		PRODUCTS_TPL_TABLE." tp USING(tplID) ".$where_clause." ".$order_by_clause.$limit_clause;

		$q = db_query( $sqlQuery );
  		$result = array();
		$i = 0;

		if ($offset >= 0 && $offset <= $products_count )
		{
			while( $row = db_fetch_row($q) )
			{

				if ( isset($callBackParam["extraParametrsTemplate"]) ){

					// take into "extra" parametrs
					$testResult = _testExtraParametrsTemplate( $row["productID"], $replicantExtraParametersTpl );
					if ( !$testResult ) continue;
				}

				if ( (($i >= $offset || !isset($callBackParam["extraParametrsTemplate"])) && $i < $offset + $CountRowOnPage) ||
					$navigatorParams == null  )
				{
					$row["PriceWithUnit"]     = show_price($row["Price"]);
					$row["list_priceWithUnit"]= show_price($row["list_price"]);
					// you save (value)
					$row["SavePrice"]         = show_price($row["list_price"]-$row["Price"]);

					// you save (%)
					if ($row["list_price"]) $row["SavePricePercent"] = ceil(((($row["list_price"]-$row["Price"])/$row["list_price"])*100));
					$row["product_extra"]     = GetExtraParametrs( $row["productID"] );
					$row["product_extra_count"]  = count($row["product_extra"]);
					$row["PriceWithOutUnit"]  = show_priceWithOutUnit( $row["Price"] );
					if ( ((double)$row["shipping_freight"]) > 0 ) $row["shipping_freightUC"] = show_price( $row["shipping_freight"] );
					
					_setPictures( $row );
					
					$result[] = $row;
				}
				$i++;
			}
		}
		$count_row = isset($callBackParam["extraParametrsTemplate"])?$i:$products_count;
		return $result;
	}


	function prdGetMetaKeywordTag( $productID )
	{
		$q = db_query("select meta_description from ".PRODUCTS_TABLE." where productID=".(int)$productID);
		if ( $row=db_fetch_row($q) )
			return  $row["meta_description"];
		else
			return "";
	}

	function prdGetMetaTags( $productID ) //gets META keywords and description - an HTML code to insert into <head> section
	{
		$q = db_query( "select meta_description, meta_keywords from ".
			PRODUCTS_TABLE." where productID=".(int)$productID );
		$row = db_fetch_row($q);
		$meta_description  = $row["meta_description"];
		$meta_keywords     = $row["meta_keywords"];

		$res = "";

		if  ( $meta_description != "" )
			$res .= "<meta name=\"Description\" content=\"".$meta_description."\">\n";
		if  ( $meta_keywords != "" )
			$res .= "<meta name=\"KeyWords\" content=\"".$meta_keywords."\" >\n";

		return $res;
	}

	function prdSearchProductByTemplate_new(
		$callBackParam, &$count_row, $navigatorParams = null )
	{
		// navigator params
		$where_clause='where enabled=1  and( ('.make_serch_string( "LOWER(p.`title`)", $callBackParam["search_simple"] ).
		') or ('.make_serch_string( "LOWER(p.`name`)", $callBackParam["search_simple"] ).') or ('.make_serch_string( "LOWER(p.`tpl_city`)", $callBackParam["search_simple"] ).
		') )';

		//$q=db_query("select categoryID, `name`,`title`,`cat_h1`  from ".CATEGORIES_TABLE." where (".make_serch_string( "LOWER(`name`)", $tmp ).") OR (".make_serch_string( "LOWER(`title`)", $tmp ).") or (".make_serch_string( "LOWER(`cat_h1`)", $tmp ).")  order by name")
		// special symbol prepare
		$order_by_clause = "order by ".CONF_DEFAULT_SORT_ORDER."";

		$sqlQuery = "select p.productID, p.categoryID, p.default_picture, p.enabled, p.pictures, p.UID, p.maps_sort, p.tpl_city as n2,
IF(tplID>0, REPLACE(tp.name,'{city}',p.tpl_city),p.name) name,
IF(tplID>0,tp.Price, p.Price) Price,
IF(tplID>0,tp.folder_pictures,tp.folder_pictures) folder_pictures,
IF(tplID>0,tp.product_code,p.product_code) product_code,
IF(tplID>0,REPLACE(tp.title,'{city}',p.tpl_city),p.title) title from ".PRODUCTS_TABLE.
		" as p left join ".PRODUCTS_TPL_TABLE." as tp USING(tplID)
		 $where_clause order by p.tpl_city ";
		//echo '$sqlQuery='.$sqlQuery; exit;
		$q = db_query( $sqlQuery );
		$result = array();
		$i = 0;

		while( $row = db_fetch_row($q) )
		{

			if($row["folder_pictures"]){
				$row["brif_picture"] = explode(",", $row["pictures"]);
				$row["brif_picture"] = $row["brif_picture"][0];
				$row["pic_path"] = "/data/pictures_tpl/".$row["folder_pictures"]."/80/";
			}else{
				$row["pic_path"] = "/data/pictures/lmenu/";
			}


			$row["PriceWithUnit"]                = show_price($row["Price"]);
			$row["list_priceWithUnit"]         = isset($row["list_price"]) ? show_price($row["list_price"]):"";//papush
			// you save (value)
			if(isset($row["list_price"]))
				$row["SavePrice"]                = show_price($row["list_price"]-$row["Price"]);
			else $row["SavePrice"] = 0;
			// you save (%)
			//_setPictures( $row );
			$row["maps_sort"]        = (int)$row["maps_sort"];
			//if ($row["n2"]!="")$row["name"] = $row["name"].' ��� '.$row["n2"];
			$row["strlen"]=strlen($row["name"]);

			if($row["folder_pictures"]){
				$row["brief_pictures"] = explode(",", $row["pictures"]);
				$row["brief_picture"] = "pictures_tpl/".$row["folder_pictures"]."/380/".$row["brief_pictures"][0];
			}else{
				$row["brief_picture"] = "pictures/main_pic/".$row["big_picture"];
			}

			$result[] = $row;
			$i++;
		}
		$products_count = COUNT($row);

		$count_row = $i;
		$arr_columns_prod=array('name','title');
		$result=sortresults($result,$arr_columns_prod);

		return $result;
	}

?>