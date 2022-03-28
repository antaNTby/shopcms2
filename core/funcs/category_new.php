<?php

	if ( isset($categoryID)) {
		if($categoryID==1) return;
		$smarty->assign("category_is", "1");
	}

	function CatSlider($dir_path) {
		/*var_dump($dir_path);
		$str = array();
		if (($dir = opendir($dir_path)) !== false) {
		while (($file_name = readdir($dir)) !== false) {
		if (strstr($file_name, '.') !== false && $file_name !== '.' && $file_name !== '..') {
		$str[] = $file_name;
		}
		}
		closedir($dir);
		}
		$cat_arr = $str;
		$num = array();
		$cat_slider_arr = array();
		while (true) {
		$a = rand(0, count($cat_arr)-1);
		if (!in_array($a, $num)) {
		$num[] = $a;
		$cat_slider_arr[] = $cat_arr[$a];
		}
		if (count($num) === count($cat_arr)) {break;}
		}

		if (count($cat_slider_arr) > 0) {
		$cat_str_slider = '"'.$cat_slider_arr[0].'"';
		for ($i=1; $i<count($cat_slider_arr); $i++) {
		$cat_str_slider .= ',"'.$cat_slider_arr[$i].'"';
		}
		} else {$cat_str_slider = '';}

		return array(0 => $cat_slider_arr, 1 => $cat_str_slider);*/
	}

	if ( isset($categoryID) && $categoryID != 1 && !isset($dontshowcategory)) {

		if ( isset($_GET["prdID"]) )  $_GET["prdID"] = (int)$_GET["prdID"];
		if ( isset($_GET["search_price_from"]) )
			if ( trim($_GET["search_price_from"]) != "" )  $_GET["search_price_from"] = (int)$_GET["search_price_from"];
			if ( isset($_GET["search_price_to"]) )
			if (  trim($_GET["search_price_to"])!="" ) $_GET["search_price_to"] = (int)$_GET["search_price_to"];
			if ( isset($_GET["categoryID"]) )  $_GET["categoryID"] = (int)$_GET["categoryID"];
		if ( isset($_GET["offset"]) ) $_GET["offset"] = (int)$_GET["offset"];
		if ( isset($_GET["maps_sort"]) ) $_GET["maps_sort"] = (int)$_GET["maps_sort"];

		function _getUrlToNavigate( $categoryID ) {
			$url = "index.php?categoryID=".$categoryID;
			$data = ScanGetVariableWithId( array("param") );
			if ( isset($_GET["search_name"]) ) $url .= "&search_name=".$_GET["search_name"];
			if ( isset($_GET["search_price_from"]) ) $url .= "&search_price_from=".$_GET["search_price_from"];
			if ( isset($_GET["search_price_to"]) ) $url .= "&search_price_to=".$_GET["search_price_to"];
			foreach( $data as $key => $val ) {
				$url .= "&param_".$key;
				$url .= "=".$val["param"];
			}
			if ( isset($_GET["search_in_subcategory"]) ) $url .= "&search_in_subcategory=1";
			if ( isset($_GET["sort"]) ) $url .= "&sort=".$_GET["sort"];
			if ( isset($_GET["direction"]) ) $url .= "&direction=".$_GET["direction"];
			if ( isset($_GET["advanced_search_in_category"]) ) $url .= "&advanced_search_in_category=".$_GET["advanced_search_in_category"];
			if (CONF_MOD_REWRITE && $url == "index.php?categoryID=".$categoryID) $url = "category_".$categoryID;
			return $url;
		}

		function _getUrlToSort( $categoryID ) {
			$url = "index.php?categoryID=$categoryID";
			$data = ScanGetVariableWithId( array("param") );
			if ( isset($_GET["search_name"]) ) $url .= "&search_name=".$_GET["search_name"];
			if ( isset($_GET["search_price_from"]) ) $url .= "&search_price_from=".$_GET["search_price_from"];
			if ( isset($_GET["search_price_to"]) ) $url .= "&search_price_to=".$_GET["search_price_to"];
			foreach( $data as $key => $val ) {
				$url .= "&param_".$key;
				$url .= "=".$val["param"];
			}
			if ( isset($_GET["offset"]) ) $url .= "&offset=".$_GET["offset"];
			if ( isset($_GET["show_all"]) ) $url .= "&show_all=yes";
			if ( isset($_GET["search_in_subcategory"]) ) $url .= "&search_in_subcategory=1";
			if ( isset($_GET["advanced_search_in_category"]) ) $url .= "&advanced_search_in_category=".$_GET["advanced_search_in_category"];
			return $url;
		}

		function _sortSetting( &$smarty, $urlToSort ) {
			if(CONF_USE_RATING == 1){
				$sort_string = STRING_PRODUCT_SORTN;
			}else{
				$sort_string = STRING_PRODUCT_SORT;
			}
			$sort_string = str_replace( "{ASC_NAME}",   "<a href='".$urlToSort."&sort=name&direction=ASC'>".STRING_ASC."</a>",        $sort_string );
			$sort_string = str_replace( "{DESC_NAME}",  "<a href='".$urlToSort."&sort=name&direction=DESC'>".STRING_DESC."</a>",        $sort_string );
			$sort_string = str_replace( "{ASC_PRICE}",   "<a href='".$urlToSort."&sort=Price&direction=ASC'>".STRING_ASC."</a>",        $sort_string );
			$sort_string = str_replace( "{DESC_PRICE}",  "<a href='".$urlToSort."&sort=Price&direction=DESC'>".STRING_DESC."</a>",        $sort_string );
			$sort_string = str_replace( "{ASC_RATING}",   "<a href='".$urlToSort."&sort=customers_rating&direction=ASC'>".STRING_ASC."</a>",        $sort_string );
			$sort_string = str_replace( "{DESC_RATING}",  "<a href='".$urlToSort."&sort=customers_rating&direction=DESC'>".STRING_DESC."</a>",        $sort_string );
			$smarty->assign( "string_product_sort", html_amp($sort_string));

		}

		//get selected category info
		$category = catGetCategoryById( $categoryID );
		$smarty->assign("selected_category", $category );

		if ( !$category ){
			Redirect("index.php");
		}
		else 	
		{
			if (strlen($category["cat_h1"])<3)$category["cat_h1"] = $category["title"];
			if(isset($adminislog) && !$adminislog) IncrementCategoryViewedTimes($categoryID);

			if( isset($_GET["prdID"]) ) {
				if(  isset($_POST["cart_".$_GET["prdID"]."_x"])  ) {
					$variants=array();
					foreach( $_POST as $key => $val ){
						if ( strstr($key, "option_select_hidden") )	{
							$arr=explode( "_", str_replace("option_select_hidden_","",$key) );
							if ( (string)$arr[1] == (string)$_GET["prdID"] )	$variants[]=$val;
						}
					}
					unset($_SESSION["variants"]);
					$_SESSION["variants"]=$variants;
					Redirect( "index.php?shopping_cart=yes&add2cart=".$_GET["prdID"] );
				}
			}

			//category thumbnail
			if (!file_exists("data/pictures/".$category["picture"]))   $category["picture"] = "";

			if ( $category["show_subcategories_products"] == 1 ) $smarty->assign( "show_subcategories_products", 1 );

			if ( $category["allow_products_search"] ) $smarty->assign( "allow_products_search", 1 );

			$callBackParam   = array();
			$products= array();
			$callBackParam["categoryID"]   = $categoryID;
			$callBackParam["enabled"]= 1;

			if (  isset($_GET["search_in_subcategory"]) ) 
				if ( $_GET["search_in_subcategory"] == 1 ) {
					$callBackParam["searchInSubcategories"] = true;
					$callBackParam["searchInEnabledSubcategories"] = true;
				}
				if ( isset($_GET["maps_sort"]) ) $callBackParam["maps_sort"] = $_GET["maps_sort"];
			if ( isset($_GET["sort"]) ) $callBackParam["sort"] = $_GET["sort"];
			if ( isset($_GET["direction"]) ) $callBackParam["direction"] = $_GET["direction"];

			// search parametrs to advanced search
			if (isset($extraParametrsTemplate) && ( $extraParametrsTemplate != null )) $callBackParam["extraParametrsTemplate"] = $extraParametrsTemplate;
			if (isset($searchParamName) && ( $searchParamName != null )) $callBackParam["name"] = $searchParamName;
			if (isset($searchParamName) && ( $rangePrice != null )) $callBackParam["price"] = $rangePrice;

			if ( $category["show_subcategories_products"] ) $callBackParam["searchInSubcategories"] = true;

			$count = 0;

			if (CONF_MOD_REWRITE){
				$urlfarse = _getUrlToNavigate( $categoryID );
				if($urlfarse == "category_".$categoryID) $urlflag = 1; else $urlflag = 0;
				$navigatorHtml = GetNavigatorHtmlmd(
					$urlfarse, CONF_PRODUCTS_PER_PAGE,
					'prdSearchProductByTemplate', $callBackParam, $products, $offset, $count, $urlflag 
				);
			} else {
				$navigatorHtml = GetNavigatorHtml(
					_getUrlToNavigate( $categoryID ), CONF_PRODUCTS_PER_PAGE,
					'prdSearchProductByTemplate', $callBackParam, $products, $offset, $count );
			}
			$show_comparison = $category["allow_products_comparison"];

			require('core/config/w1.inc.php');

			require_once('core/classes/class.robokassa.php');
			for($i=0; $i<count($products); $i++)  {
				$products[$i]["allow_products_comparison"] = $show_comparison;
				$price = $products[$i]["Price"]  * $multi_koef;
				$robo_display_price = show_price($price);

				$nOutSum = show_priceWithOutUnit($price);
				$Desc  = $products[$i]['name'];

				$products[$i]["robo_display_price"] = $robo_display_price;
				$products[$i]["nOutSum"]= $nOutSum;
				$products[$i]["Desc"] = $Desc;
				$products[$i]['BuyButton'] = Robokassa::GetPaymentButton($nOutSum,$Desc);
			}


			if (CONF_PRODUCT_SORT) _sortSetting( $smarty, _getUrlToSort($categoryID) );

			if(CONF_SHOW_PARENCAT){
				$catrescur=getcontentcatresc($categoryID);
				$smarty->assign( "catrescur", $catrescur['city']);
				$smarty->assign( "catrescur2", $catrescur['raion']);
			}
			//var_dump($catrescur);die;
			$smarty->assign( "subcategories_to_be_shown", catGetSubCategoriesSingleLayer($categoryID) );
			//$smarty->assign( "categorylinkscat", getcontentcat($categoryID));

			//calculate a path to the category
			$product_category_path = catCalculatePathToCategory($categoryID);
			$smarty->assign( "product_category_path",  $product_category_path);
			$smarty->assign("desc",$category["description"]);
			$smarty->assign( "show_comparison", $show_comparison );
			$smarty->assign( "catalog_navigator", $navigatorHtml );

			if (isset($cat_tree)) {
				$cat_info = end($cat_tree);
				for ($i=0; $i<count($cat_info['prods']); $i++) {
					$cat_info['prods'][$i]['price'] = $products[$i]['Price'];
				}
				$smarty->assign( "cat_info", $cat_info);
			}
			//var_dump($cat_info);die;
			$smarty->assign( "products_to_show", $products);

			if(isset($_GET["advanced_search_in_category"])) $smarty->assign( "products_to_showc", count($products));
			else $smarty->assign( "products_to_showc", $category["products_count"]);

			$smarty->assign( "cat_h1", $category["cat_h1"]);
			$smarty->assign( "cat_h2_o", $category["cat_h2_o"]);
			$smarty->assign( "cat_h2_c", $category["cat_h2_c"]);
			$smarty->assign( "cat_h2_r", $category["cat_h2_r"]);
			$smarty->assign( "catalog_navigator", $category["typ"]);
			$smarty->assign( "categoryID", $categoryID);
			$smarty->assign( "categoryName", $category["title"]);
			$smarty->assign( "main_content_template", "category.tpl.html");
			//list($cat_slider_arr_rajon, $cat_str_slider_rajon) = CatSlider($_SERVER['DOCUMENT_ROOT'].'/data/slider_rajon');
			//$smarty->assign( "cat_slider_arr_rajon", $cat_slider_arr_rajon);
			//$smarty->assign( "cat_str_slider_rajon", $cat_str_slider_rajon);
			list($cat_slider_arr_punkt, $cat_str_slider_punkt) = CatSlider($_SERVER['DOCUMENT_ROOT'].'/data/slider_punkt');
			$smarty->assign( "cat_slider_arr_punkt", $cat_slider_arr_punkt);
			$smarty->assign( "cat_str_slider_punkt", $cat_str_slider_punkt);
		}
	} 
	else 
	{
		if(CONF_SHOW_PARENCAT){
			$catrescur=getcontentcatresc($categoryID);
			$smarty->assign( "catrescur", $catrescur['city']);
			$smarty->assign( "catrescur2", $catrescur['raion']);
		}

		//get selected category info
		$category = catGetCategoryById( $categoryID );
		$smarty->assign("selected_category", $category );

		list($cat_slider_arr, $cat_str_slider) = CatSlider($_SERVER['DOCUMENT_ROOT'].'/data/slider');
		$smarty->assign( "cat_slider_arr", $cat_slider_arr);
		$smarty->assign( "cat_str_slider", $cat_str_slider);
	}
?>