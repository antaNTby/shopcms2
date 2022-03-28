<?php
	# =============================================================================
	#                           made for ShopCMS
	# -----------------------------------------------------------------------------
	#                        Copyright 2011 Petrenko A.L.
	#                         http:// www.webamator.com/
	# =============================================================================
?><?php

	/* Likemusic
	Судя по содержиомому таблицы логичнее было бы сделать столбец с id, и флагом:продукт/категория
	Ну да ладно - оставляем как есть.
	*/

	function routeExecute($uri)
	{

		#$uri = rtrim($_SERVER['REQUEST_URI'], '/');
		//$uri = $_SERVER['REQUEST_URI'];
		$uri = urldecode($uri);
		if (strstr($uri, '?')) {
			$temp = explode('?', $uri);
			$uri = $temp[0];
		}
		$uri_orig = $uri;
		$uri = str_replace('.html', '', $uri);

		if (preg_match("/(\/offset_[0-9]+)/", $uri, $matches)) {

			$uri = str_replace($matches[0], '', $uri);
			$offset = explode('_', $matches[0]);
			$_GET['offset'] = trim($offset[1], '/');
		}

		if (preg_match("/(\/show_all)/", $uri, $matches)) {

			$uri = str_replace($matches[0], '', $uri);
			$_GET['show_all'] = 'yes';
		}

		if (preg_match("/\/sort_(Price|name|customers_rating)/i", $uri, $matches)) {

			$uri = str_replace($matches[0], '', $uri);
			$sort = str_replace('sort_', '', $matches[0]);
			$_GET['sort'] = trim($sort, '/');
		}

		if (preg_match("/\/direction_(ASC|DESC)/", $uri, $matches)) {

			$uri = str_replace($matches[0], '', $uri);
			$offset = explode('_', $matches[0]);
			$_GET['direction'] = trim($offset[1], '/');
		}

		if (preg_match("/\/view_(full|foto)/", $uri, $matches)) {

			$uri = str_replace($matches[0], '', $uri);
			$offset = explode('_', $matches[0]);
			$_GET['view'] = trim($offset[1], '/');
		}

		/*if (preg_match("/(_offset_[0-9]+)/", $uri, $matches)) {

		$uri = str_replace($matches[0], '', $uri);
		$offset = explode('_', $matches[0]);
		$_GET['offset'] = $offset[2];
		}

		if (preg_match("/(_show_all)/", $uri, $matches)) {

		$uri = str_replace('_show_all', '', $uri);
		$offset = explode('_', '_show_all');
		$_GET['show_all'] = 'yes';
		}

		if (preg_match("/_sort_(Price|name|customers_rating)/i", $uri, $matches)) {

		$uri = str_replace($matches[0], '', $uri);
		$sort = str_replace('_sort_', '', $matches[0]);
		$_GET['sort'] = $sort;
		}

		if (preg_match("/(_direction_(ASC|DESC))/", $uri, $matches)) {

		$uri = str_replace($matches[0], '', $uri);
		$offset = explode('_', $matches[0]);
		$_GET['direction'] = $offset[2];
		}

		if (preg_match("/(_view_(full|foto))/", $uri, $matches)) {

		$uri = str_replace($matches[0], '', $uri);
		$offset = explode('_', $matches[0]);
		$_GET['direction'] = $offset[2];
		}*/

		# $paramsURI = explode('?', $uri);
		# $parts = explode('/', $paramsURI[0]);

		# $task = $parts[1];
		# $task = trim($paramsURI[0], '/'); 
		//if($uri != '/') $task = rtrim($uri, '/');
        //else $task = $uri;
        $task = rtrim($uri, '/');
		
		define('URL_SUBPRODUCT_MARKER','_');
		
		function UrlContainSubProductMarker($url)
		{
			$SubproductMarker = URL_SUBPRODUCT_MARKER;
			if(endsWith($url,$SubproductMarker))
				return true;
			else 
				return false;
		}
		
		function RemoveSubProductMarkerFromUrl($url)
		{
			$SubproductMarker = URL_SUBPRODUCT_MARKER;
			$url = substr($url,0,strlen($url)-strlen($SubproductMarker));
			return $url;			
		}
		
		function routeGetBySubProdctUID(&$task)
		{

			FixUrlBugNovaja($task);
			FixUrlBugsOther($task);
			
			$SubProductUrl = GetSubProductUrl($task);
			if(!$SubProductUrl) return false;
			
			$task = substr($task,0,-strlen($SubProductUrl));
			$row = routeGetByUID($task);
			if(!$row) return false;
			
			$ProductId= $row['productID'];
			if(!$ProductId) return false;
			
			$SubProductTplId = GetSubProductTplId($SubProductUrl,$ProductId);
			if(!$SubProductTplId) Redirect($task.'.html',true);
			
			$row['target'].='&subID='.$SubProductTplId;
			return $row;		
		}

		function FixUrlBugNovaja($task)
		{
			$odl_end = 'novaja';
			$new_end = '_'.$odl_end;
			if(endsWith($task,$odl_end)&& (!endsWith($task,$new_end)))
			{
				$odl_end_length = strlen($odl_end);
				$task = substr($task,0,strlen($task)-$odl_end_length);
				$task .= $new_end.'.html';
				Redirect($task,true);
			}			
		}
		
		function FixUrlBugsOther($task)
		{
			$UrlConv = array(
				'_bumagnaja'=>'_bymagnaja',
				'_google_karta'=>'_google',
				'_dla_telephona'=>'_dla_telefona',
				'_jandeks_karta'=>'_yandex'
			);
			
			foreach($UrlConv as $OldUrl=>$NewUrl)
			{
				if(endsWith($task,$OldUrl))
				{
					$task = substr($task,0,-strlen($OldUrl));
					$task .= $NewUrl.'.html';
					Redirect($task,true);
				}
			}
		}
		
		/*
		function GetSubProductsUrls()
		{
		$sql_query = 'SELECT tplID, sub_title FROM '.PRODUCTS_SUBTPL_TABLE;
		$dq = db_query($sql_query);
		$res=db_fetch_rows($dq);
		return $res;
		}
		*/

		function GetSubProductsDistinctUrls()
		{
			$sql_query = 'SELECT DISTINCT sub_title FROM '.PRODUCTS_SUBTPL_TABLE;
			$dq = db_query($sql_query);
			$res=db_fetch_rows($dq);
			return $res;
		}
		
		function GetSubProductTplId($SubProductUrl,$ProductId)
		{
			$sql_query = 'SELECT s.tplID, t.sub_products FROM '.PRODUCTS_TABLE.' p JOIN '.PRODUCTS_TPL_TABLE.' t USING(tplID) JOIN '.  PRODUCTS_SUBTPL_TABLE.' s USING(map_type) WHERE p.productID='.$ProductId." AND s.sub_title='".$SubProductUrl."'";
			
			$dq = db_query($sql_query);
			$res=db_fetch_row($dq);
			if(!$res) return false;
			
			$tplID = $res['tplID'];
			if(!$tplID) return false;
			
			if(!$res['sub_products']) return false;
			
			$SubProducts = unserialize($res['sub_products']);
			$IsFind = false;
			foreach($SubProducts as $SubProduct)
			{
				if($SubProduct['sub_sel']==$tplID) 
				{
					$IsFind = true;
					break;					
				}
			}
			if(!$IsFind) return false;
			
			return $tplID;
		}
		
		function GetSubProductUrl($task)
		{
			//получаем список всех возможных окончаний url подпродуктов
			$SubProductsRows = GetSubProductsDistinctUrls();
			
			//$SubProductRows = GetSubProductsUrls();
			$IsFind = false;
			
			//ищем шаблон на который оканчивается
			foreach($SubProductsRows as $SubProductsRow)
			{
				if(endsWith($task,$SubProductsRow['sub_title']))
				{
					$IsFind=true;
					$SubProductUrl=$SubProductsRow['sub_title'];
					break;
				}
			}

			//ничего не нашли						
			if(!$IsFind) return false;
			return $SubProductUrl;			
		}


		//если нет признака подтовара
		if(!UrlContainSubProductMarker($task))
			$row = routeGetByUID($task);//пробуем получить товар
		else
			$task = RemoveSubProductMarkerFromUrl($task);

		if(!$row)
			$row = routeGetBySubProdctUID($task);			

		//babenkoma
		//Если нет такого подтовара, то переходим на товар
		if (!(isset($row) && $row['enabled'])) {
			for ($i=strlen($task)-1; $i>=0; $i--){
				$t = substr($task, 0, $i);
				$r = routeGetByUID($t);
				if (isset($r) && $r['enabled']){
					header("HTTP/1.1 301 Moved Permanently");
					header('Location:'.$r['url'].'.html'); 
					die;
				}
			}

		}
		//babenkoma\

		//likemusic
		//Если установлен редирект - редиректим
		if($row['redirect']){
			$row = routeGetById($row['redirect']);
			Redirect($row['url'],true);
		}

		if (isset($row) && $row['enabled'] ) {

			/*if (defined('CONF_USE_EASY_URL_PREFIX') && CONF_USE_EASY_URL_PREFIX == '.html' && 
			!preg_match("/\.html/i", $uri_orig) && !preg_match("/404\.html/i", $uri_orig)) {

			header("HTTP/1.0 404 Not Found");
			header("Status: 404 Not Found");
			$_GET['404'] = 1;
			}

			if (defined('CONF_USE_EASY_URL_PREFIX') && CONF_USE_EASY_URL_PREFIX == '' && 
			preg_match("/\.html/i", $uri_orig) && !preg_match("/404\.html/i", $uri_orig) && 
			preg_match("/categoryID=/i", $row['target'])) {
			//var_dump($_SERVER['SERVER_NAME'], $uri_orig, str_replace('.html', '', $uri_orig));die;
			header ('HTTP/1.1 301 Moved Permanently');
			header ('Location: http://'.$_SERVER['SERVER_NAME'].'/'.str_replace('.html', '', $uri_orig));
			}*/

			$target = $row['target'];
			$params = explode('?', $target);

			$vars = $params[0];

			if ( isset($params[1]) ) {
				$add_params = explode('&', $params[1]);

				foreach($add_params as $add_param) {
					$param = explode('=', $add_param);
					if ( isset($param[0]) && isset($param[1]) )
						$_GET[$param[0]] = $param[1];
				}
			}
			if ( !empty($url['fragment']) )
				$_GET['anchor'] = $url['fragment'];
		} 

		if ( isset($paramsURI[1]) ) {
			$add_params = explode('&', $params[1]);

			foreach($add_params as $add_param) {
				$param = explode('=', $add_param);
				if ( isset($param[0]) && isset($param[1]) )
					$_GET[$param[0]] = $param[1];
			}
		}

		$temp = $_GET['_url'];
		unset($_GET['_url']); 
		$count = count($_GET);  
		$_GET['_url'] = $temp;

		if (!empty($_SERVER['REQUEST_URI']) && !$count) {

			header("HTTP/1.0 404 Not Found");
			header("HTTP/1.1 404 Not Found");
			header("Status: 404 Not Found");
			die('ERROR_404_HTML');
		}
	}

	function routeUpdateUrl($_UID, $_url) {
		$sql = 'SELECT url, target FROM '.ROUTES_TABLE." WHERE target='".$_url."'";
		$q = db_query($sql);
		$row = db_fetch_assoc($q);

		if ( !isset($row) /*&& strcmp($row['target'], $_url)*/ ) {
			$sql = 'UPDATE '.ROUTES_TABLE." SET url='".$_UID."' WHERE target='".$_url."'";
			db_query($sql);
		}
		else
			routeAddUrl($_UID, $_url);
	}

	function routeAddUrl($_UID, $_url) {
		$sql = 'INSERT INTO '.ROUTES_TABLE." (url, target, kindID, enabled) VALUES ('".$_UID."', '".$_url."', 0, 1)";
		db_query($sql);
	}

	function rounteAddProductUrl($ProductId, $UID=null){
		if(empty($UID))
		{
			$sql = 'SELECT `name` FROM '.PRODUCTS_TABLE." WHERE productID='$ProductId'";
			$name = db_query($sql);
			$UID = routeTransliteration($name);
		}
		routeAddUrl($UID, 'index.php?productID='.$ProductId);
	}



	function routeDropUrl($_UID) {
		$sql = 'DELETE FROM '.ROUTES_TABLE." WHERE url='".$_UID."'";
		db_query($sql);
	}

	function routeDropOtherUrl($_target) {
		$sql = 'DELETE FROM '.ROUTES_TABLE." WHERE target='".$_target."'";
		db_query($sql);
	}

	function routeExistsCount($_UID, $_target = NULL) {
		$sql = 'SELECT COUNT(*) as _count FROM '.ROUTES_TABLE." WHERE url='".$_UID."'".(isset($_target)?" AND target<>'".$_target."'":'');
		$q = db_query($sql);
		$row = db_fetch_assoc($q);
		//var_dump($sql, $row);echo '<br /><br />';
		return $row['_count'];
	}

	function routeGetByUID($_UID, $_target = NULL) {
		$sql = 'SELECT * FROM '.ROUTES_TABLE." WHERE url='".$_UID."'".(isset($_target)?" AND target<>'".$_target."'":'');
		$q = db_query($sql);
		$row = db_fetch_assoc($q);
		return $row;
	}

	function routeGetById($id){
		$sql = 'SELECT * FROM '.ROUTES_TABLE." WHERE ID=".$id;
		$q = db_query($sql);
		$row = db_fetch_assoc($q);
		return $row;
	}

	function routeGetUID($string, $_target = NULL, $_action = 'update', $_separator = '_', $_case = 'lower') {

		$output = routeTransliteration($string);
		$prefix = '';

		$exists = routeExistsCount($output.$prefix, $_target);
		if ($_action == 'new' && $exists > 1) {

			$output .= '_'.uniqid(rand());

		} elseif ($_action != 'update' && $exists >= 1) {

			$output .= '_'.uniqid(rand());    
		}
		$output .= $prefix;

		return strtolower($output);
	}

	function routeTransliteration($string, $_separator = '_', $_case = 'lower') {

		$string = stripslashes($string);
		$string = trim($string);

		# main
		$table = array(
			' ' => $_separator,
			#'/' => $_separator,
			'<' => '',
			'>' => '',
			',' => '',
			'.' => '',
			'"' => '',
			"'" => '',
			'!' => '',
			'«' => '',
			'»' => '',
			'(' => '',
			')' => '',
			'№' => '',
			':' => '',
			'+' => '-',
			' - ' => '-',
			'&quot;' => '',
			'?' => ''
		);

		$table_cyr = array(
			'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Ѓ' => 'G`',
			'Ґ' => 'G`', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Є' => 'YE',
			'Ж' => 'ZH', 'З' => 'Z', 'Ѕ' => 'Z', 'И' => 'I', 'Й' => 'J',
			'Ј' => 'J', 'І' => 'I', 'Ї' => 'YI', 'К' => 'K', 'Ќ' => 'K',
			'Л' => 'L', 'Љ' => 'L', 'М' => 'M', 'Н' => 'N', 'Њ' => 'N',
			'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
			'У' => 'U', 'Ў' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
			'Ч' => 'CH', 'Џ' => 'DH', 'Ш' => 'SH', 'Щ' => 'SHH', 'Ъ' => '',
			'Ы' => 'y', 'Ь' => '`', 'Э' => 'E`', 'Ю' => 'YU', 'Я' => 'ia',
			'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ѓ' => 'g',
			'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'є' => 'ye',
			'ж' => 'zh', 'з' => 'z', 'ѕ' => 'z', 'и' => 'i', 'й' => 'j',
			'ј' => 'j', 'і' => 'i', 'ї' => 'yi', 'к' => 'k', 'ќ' => 'k',
			'л' => 'l', 'љ' => 'l', 'м' => 'm', 'н' => 'n', 'њ' => 'n',
			'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
			'у' => 'u', 'ў' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
			'ч' => 'ch', 'џ' => 'dh', 'ш' => 'sh', 'щ' => 'shh', 'ь' => 'i',
			'ы' => 'y', 'ъ' => "", 'э' => 'e`', 'ю' => 'yu', 'я' => 'ia'
		);
		$table = array_merge($table_cyr, $table);
		$output = strtr($string,$table);
		$output = trim($output, '_');

		# after
		$table = array('_-_' => '-');
		$output = strtr($output, $table);
		$output = preg_replace('/(_)+/' ,'_', $output);
		return strtolower($output);
	}

	function routeGetBackUrl($_URL) {
		$sql = 'SELECT * FROM '.ROUTES_TABLE." WHERE target='".$_URL."'";
		$q = db_query($sql);
		$row = db_fetch_assoc($q);
		return $row;
	}

?>
