<?php
	#####################################
	# ShopCMS: Скрипт интернет-магазина
	# Copyright (c) 2008 by ADGroup
	# http://shopcms.ru
	#####################################
	// simple search
	if (isset($_GET["inside"]))
		$smarty->assign("search_in_results", $_GET["inside"]);

	if (isset($_GET["searchstring"])) //make a simple search
	{

		function _getUrlToNavigateSearch()
		{
			$url = "index.php?searchstring=".$_GET["searchstring"];
			if ( isset($_GET["x"]) )
				$url .= "&x=".$_GET["x"];
			if ( isset($_GET["y"]) )
				$url .= "&y=".$_GET["y"];
			if ( isset($_GET["sort"]) )
				$url .= "&sort=".$_GET["sort"];
			if ( isset($_GET["direction"]) )
				$url .= "&direction=".$_GET["direction"];
			return $url;
		}

		function _getUrlToSortSearch()
		{
			$url = "index.php?searchstring=".$_GET["searchstring"];
			if ( isset($_GET["x"]) )
				$url .= "&x=".$_GET["x"];
			if ( isset($_GET["y"]) )
				$url .= "&y=".$_GET["y"];
			if ( isset($_GET["offset"]) )
				$url .= "&offset=".$_GET["offset"];
			if ( isset($_GET["show_all"]) )
				$url .= "&show_all=yes";
			return $url;
		}

		function _sortSettingSearch( &$smarty, $urlToSort )
		{
			$sort_string = STRING_PRODUCT_SORT;
			$sort_string = str_replace( "{ASC_NAME}",   "<a href='".$urlToSort."&sort=name&direction=ASC'>".STRING_ASC."</a>",        $sort_string );
			$sort_string = str_replace( "{DESC_NAME}",  "<a href='".$urlToSort."&sort=name&direction=DESC'>".STRING_DESC."</a>",        $sort_string );
			$sort_string = str_replace( "{ASC_PRICE}",   "<a href='".$urlToSort."&sort=Price&direction=ASC'>".STRING_ASC."</a>",        $sort_string );
			$sort_string = str_replace( "{DESC_PRICE}",  "<a href='".$urlToSort."&sort=Price&direction=DESC'>".STRING_DESC."</a>",        $sort_string );
			$sort_string = str_replace( "{ASC_RATING}",   "<a href='".$urlToSort."&sort=customers_rating&direction=ASC'>".STRING_ASC."</a>",        $sort_string );
			$sort_string = str_replace( "{DESC_RATING}",  "<a href='".$urlToSort."&sort=customers_rating&direction=DESC'>".STRING_DESC."</a>",        $sort_string );
			$smarty->assign( "string_product_sort", html_amp($sort_string) );
		}
		function make_serch_string( $string, $arrparts )
		{
			$string2="";
			foreach( $arrparts as $key=> $val )//зачем удаляем последний символ???
			{
				if ( strlen( trim($val) ) > 3 )
				{
					$searchstrings[] = substr(trim($val),0,-1);
				}else  $searchstrings[] = trim($val);
			}
			foreach( $searchstrings as $key=> $val )
			{
				if(strlen($val)<3) continue;
				if(($val=='район') || ($val=='райо')) continue;
				if(($val=='карт')) continue;
				
				if ($key>0)
					$string2.=" or $string like '%".mysql_real_escape_string($val)."%' ";
				else
					$string2.=" $string like '%".mysql_real_escape_string($val)."%' ";
			}

			return $string2;
		}

		function check_instr($values,$arr_columns)
		{
			if(strstr($_GET["searchstring"]," "))
			{ 
				foreach($arr_columns as $val)
					if(strstr(convert_to_lower($values[$val]),convert_to_lower($_GET["searchstring"]))) return true;
					//echo'test--';

			}
			else
			{
				foreach($arr_columns as $val)
				{
					$arrofvals=explode(" ", $values[$val]);
					foreach($arrofvals as $valu)
						if (substr($valu,0,strlen($_GET["searchstring"]))==$_GET["searchstring"])
							return true;
				}
			}
			return false;
		}

		function is_good_result($values,$arr_columns)
		{
			$count=0;
			$tmpa = explode(" ", convert_to_lower($_GET["searchstring"]));
			foreach( $tmpa as $key=> $val )
			{
				if ( strlen( trim($val) ) > 3 )
				{
					$searchstrings[] = substr(trim($val),0,-1);
				}else  $searchstrings[] = trim($val);
			}
			foreach($arr_columns as $val)
			{
				$we_found=array();
				$arr_colinfo=explode(" ", convert_to_lower($values[$val]));
				foreach ($searchstrings as $weneed)
				{
					foreach ($arr_colinfo as $wehave)
					{
						if(!in_array($wehave,$we_found) && (strlen($wehave)-strlen($weneed))<6 && substr($wehave,0,strlen($weneed))==$weneed ){$we_found[]=$wehave; break;}
					}
					if(count($searchstrings)==count($we_found) ) return true;
				}

			}
			return false;
		}
		function convert_to_lower($input_string)
		{
			$array_small=array('й','ц','у','к','е','н','г','ш','щ','з','х','ъ','ф','ы','в','а','п','р','о','л','д','ж','э','я','ч','с','м','и','т','ь','б','ю');
			$array_big=array('Й','Ц','У','К','Е','Н','Г','Ш','Щ','З','Х','Ъ','Ф','Ы','В','А','П','Р','О','Л','Д','Ж','Э','Я','Ч','С','М','И','Т','Ь','Б','Ю');
			return strtolower(str_replace($array_big,$array_small,$input_string));
		}
		function check_preg($values,$arr_columns,$string_to_compare)
		{
			if(strstr($_GET["searchstring"]," ")){
				if($string_to_compare[5]!='' )
					foreach($arr_columns as $val)
						if (preg_match("/".$string_to_compare."/",strtolower($values[$val]))) return true;
						return false;}else return false;

		}
		
		function sortresults($arr_to_sort,$arr_columns)
		{ 
			$arr_full=array();
			$arr_apr=array();
			$arr_rest=array();
			$string2="";
			$normal_id=0;
			$old_maps_id=3000;
			$avto_maps_id=6000;
			$array_old_maps_cats=array(229,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,251,252,253,254,255,256,257,258,259,260);
			$array_avto_maps_cats=array(199,200,202,203,204,205,206,207,208,209,210,211,212,213,215,216,218,219,220,221,222,224,225,226,227,228,230,231,232);
			$tmpa = explode(" ", $_GET["searchstring"]);
			foreach( $tmpa as $key=> $val )
			{
				if ($key>0)
					$string2.="\s".substr(strtolower($val),0,-1)."[а-я]{1}";
				else
					$string2.=substr(strtolower($val),0,-1)."[а-я]{1}";

			}


			foreach ($arr_to_sort as $val)
			{
				if (check_instr($val,$arr_columns))
				{
					if(in_array($val['categoryID'],$array_avto_maps_cats))
					{
						$arr_full[$avto_maps_id]=$val;
						$avto_maps_id++;
					}
					elseif(in_array($val['categoryID'],$array_old_maps_cats))
					{
						$arr_full[$old_maps_id]=$val;
						$old_maps_id++;
					}
					else {$arr_full[$normal_id]=$val;$normal_id++;}
				}
				elseif (check_preg($val,$arr_columns,$string2))
				{
					if(in_array($val['categoryID'],$array_avto_maps_cats))
					{
						$arr_apr[$avto_maps_id]=$val;
						$avto_maps_id++;
					}
					elseif(in_array($val['categoryID'],$array_old_maps_cats))
					{
						$arr_apr[$old_maps_id]=$val;
						$old_maps_id++;
					}
					else {$arr_apr[$normal_id]=$val;$normal_id++;}
				}
				elseif(is_good_result($val,$arr_columns)) 
				{
					if(in_array($val['categoryID'],$array_avto_maps_cats))
					{
						$arr_rest[$avto_maps_id]=$val;
						$avto_maps_id++;
					}
					elseif(in_array($val['categoryID'],$array_old_maps_cats))
					{
						$arr_rest[$old_maps_id]=$val;
						$old_maps_id++;
					}
					else {$arr_rest[$normal_id]=$val;$normal_id++;}
				}
			}
			ksort($arr_full);ksort($arr_apr);ksort($arr_rest);
			$arr_full=array_values($arr_full);$arr_apr=array_values($arr_apr);$arr_rest=array_values($arr_rest);
			return array_merge($arr_full,$arr_apr,$arr_rest);
		}

		function SimpleSearchInCategory($SearchStrign)
		{
			$searchstr = array();
			$searchincategory = array();
			$searchstrings = array();
			$tmp = explode(" ", $SearchStrign);
			$sql_query = "select name, UID  from ".CATEGORIES_TABLE." where (".make_serch_string( "name", $tmp ).') OR ('.make_serch_string( "cat_h1", $tmp ).") order by name, cat_h1";
			$q=db_query($sql_query);
			while($row=db_fetch_row($q))
			{
				$searchincategory[]=$row;
				//$array_categs[$row['categoryID']]='';
				$j++; 
			}
			return $searchincategory;
		}
		search_stat_log($_GET["searchstring"]);
		$searchincategory = SimpleSearchInCategory($_GET["searchstring"]);

		//$arr_columns_cat=array('name','title','cat_h1');
		//$arr_columns_cat=array('name');
		//$searchincategory=sortresults($searchincategory,$arr_columns_cat);

		$j=count($searchincategory);
		$smarty->assign( "searchincategory_count",  $j );
		$smarty->assign( "searchincategory",  $searchincategory );////////////////
		//////////////////////////////////////////
		/*
		if ( isset($_GET["inside"]) )
		{
			$data = ScanGetVariableWithId( array("search_string") );
			foreach( $data as $key => $value )
				$searchstrings[] = $value["search_string"];
		}
		$smarty->hassign( "searchstrings", $_GET["searchstring"] );

		$callBackParam        = array();
		$products                = array();
		$callBackParam["search_simple"] =$tmp;

		if ( isset($_GET["sort"]) )
			$callBackParam["sort"] = $_GET["sort"];
		if ( isset($_GET["direction"]) )
			$callBackParam["direction"] = $_GET["direction"];


		$countTotal = 0;
		$offset = 0;
		$navigatorHtml = GetNavigatorHtml(_getUrlToNavigateSearch(), CONF_PRODUCTS_PER_PAGE, 
			'prdSearchProductByTemplate_new', $callBackParam, $products, $offset, $countTotal);

		if ( CONF_PRODUCT_SORT == '1' )
			_sortSettingSearch( $smarty, _getUrlToSortSearch() );
		if(CONF_ALLOW_COMPARISON_FOR_SIMPLE_SEARCH == 1){

			$show_comparison = 0;
			foreach ($products as $_Key=>$_Product){

				$products[$_Key]['allow_products_comparison'] = 1;
				$show_comparison++;
			}
			$smarty->assign( "show_comparison", $show_comparison );
		}
		*/
		$page_title=htmlspecialchars($_GET["searchstring"])." - поиск на Mapsshop.ru";
		$smarty->assign( "page_title",  $page_title );
		
		$smarty->assign( "chekbox_to_show",  1 );
		$smarty->assign( "search_is",  1 );

		//$smarty->assign( "products_to_show",  $products );////////////////////////////////////
		//$countTotal=count($products);
		//$smarty->assign( "products_found", $countTotal );
		//$smarty->assign( "products_to_show_count", $countTotal );
		//$smarty->assign( "search_navigator", $navigatorHtml );
		$smarty->assign( "main_content_template", "search.tpl.html" );
	}
?>