<?php
	#####################################
	# ShopCMS: —крипт интернет-магазина
	# Copyright (c) by ADGroup
	# http://shopcms.ru
	#####################################

	function MagicQuotesRuntimeSetting()
	{
		ini_set("magic_quotes_runtime",0);
	}

	function TransformDataBaseStringToText( $str )
	{
		$str = str_replace( "&", "&#38;", $str );
		$str = str_replace( "'", "&#39;", $str );
		$str = str_replace( "\"", "&quot;", $str );
		$str = str_replace( "<", "&lt;",  $str );
		$str = str_replace( ">", "&gt;",  $str );
		return $str;
	}

	function isWindows(){

		if(isset($_SERVER["WINDIR"]) || isset($_SERVER["windir"]))
			return true;
		else
			return false;
	}

	function myfile_get_contents($fileName)
	{
		return implode("", file($fileName));
	}


	function correct_URL($url, $mode = "http") //converts

	{
		$URLprefix = trim($url);
		$URLprefix = str_replace("http://", "", $URLprefix);
		$URLprefix = str_replace("https://", "", $URLprefix);
		$URLprefix = str_replace("index.php", "", $URLprefix);
		if ($URLprefix[strlen($URLprefix) - 1] == '/')
		{
			$URLprefix = substr($URLprefix, 0, strlen($URLprefix) - 1);
		}
		return ($mode."://".$URLprefix."/");
	}

	// *****************************************************************************
	// Purpose        sets access rights to files which uploaded with help move_uploaded_file
	//                        function
	// Inputs           $file_name - file name
	// Remarks
	// Returns        nothing
	function SetRightsToUploadedFile($file_name)
	{
		@chmod($file_name, 0666);
	}

	function getmicrotime()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	// *****************************************************************************
	// Purpose        this function works without errors ( as is_writable PHP functoin )
	// Inputs           $url
	// Remarks
	// Returns        nothing
	function IsWriteable($fileName)
	{
		$f = @fopen($fileName, "a");
		return !is_bool($f);
	}


	// *****************************************************************************
	// Purpose        redirects to other PHP page specified URL ( $url )
	// Inputs           $url
	// Remarks        this function uses header
	// Returns        nothing
	function Redirect($url,$permanently=false)
	{
		if($permanently) header ('HTTP/1.1 301 Moved Permanently');
		header("Location: ".$url);
		exit();
	}


	// *****************************************************************************
	// Purpose        redirects to other PHP page specified URL ( $url )
	// Inputs
	// Remarks        if CONF_PROTECTED_CONNECTION == '1' this function uses protected ( https:// ) connection
	//                        else it uses unsecure http://
	//                        $url is relative URL, NOT an absolute one, e.g. index.php, index.php?productID=x, but not http://www.example.com/
	// Returns        nothing
	function RedirectProtected($url)
	{
		if (CONF_PROTECTED_CONNECTION == '1')
		{
			Redirect(correct_URL(CONF_FULL_SHOP_URL, "https").$url); //redirect to HTTPS part of the website
		}
		else  Redirect($url); //relative URL
	}


	// *****************************************************************************
	// Purpose        redirects to other PHP page specified URL ( $url )
	// Inputs           $url
	// Remarks        this function uses JavaScript client script
	// Returns        nothing
	function RedirectJavaScript($url)
	{
		die("<script type='text/javascript'> window.location = '".$url."'; </script>");
	}


	// *****************************************************************************
	// Purpose        round float value to 0.01 precision
	// Inputs           $float_value - value to float
	// Remarks
	// Returns        rounded value
	function roundf($float_value)
	{
		return round(100 * $float_value) / 100;
	}

	function _testExtension($filename, $extension)
	{
		if ($extension == null || trim($extension) == "") return true;
		$i = strlen($filename) - 1;
		for (; $i >= 0; $i--)
		{
			if ($filename[$i] == '.') break;
		}

		if ($filename[$i] != '.') return false;
		else
		{
			$ext = substr($filename, $i + 1);
			return (strtolower($extension) == strtolower($ext));
		}
	}

	function checklogin() {

		$rls = array();

		if (isset($_SESSION["log"])) //look for user in the database

		{ 
			$q = db_query("select cust_password, actions FROM ".CUSTOMERS_TABLE." WHERE Login='".xEscSQL($_SESSION["log"])."'");
			$row = db_fetch_row($q); //found customer - check password

			if (!$row || !isset($_SESSION["pass"]) || $row[0]!=$_SESSION["pass"]) //unauthorized access
			{
				unset($_SESSION["log"]);
				unset($_SESSION["pass"]);
				session_unregister("log"); //calling session_unregister() is required since unset() may not work on some systems
				session_unregister("pass");

			}else{

				$rls = unserialize($row[1]);
				unset($row);

			}
		}

		return $rls;  
	}

	// *****************************************************************************
	// Purpose        gets all files in specified directory
	// Inputs   $dir - full path directory
	// Remarks
	// Returns
	function GetFilesInDirectory( $dir, $extension = "" )
	{
		$dh  = opendir($dir);
		$files = array();
		while (false !== ($filename = readdir($dh)))
		{
			if ( !is_dir($dir.'/'.$filename) && $filename != "." && $filename != ".." )
			{
				if ( _testExtension($filename,$extension) )
					$files[] = $dir."/".$filename;
			}
		}
		return $files;
	}

	// *****************************************************************************
	// Purpose        gets class name in file
	// Inputs   $fileName - full file name
	// Remarks        this file must contains only one class syntax valid declaration
	// Returns        class name
	function GetClassName( $fileName )
	{
		$strContent = myfile_get_contents( $fileName );
		$_match = array();
		$strContent = substr($strContent, strpos($strContent, '@connect_module_class_name'), 100);
		if(preg_match("|\@connect_module_class_name[\t ]+([0-9a-z_]*)|mi", $strContent, $_match)){

			return $_match[1];
		}else {

			return false;
		}
	}

	function InstallModule( $module )
	{
		db_query("insert into ".MODULES_TABLE." ( module_name ) ".
			" values( '".xEscSQL($module->title)."' ) ");
	}

	function GetModuleId( $module )
	{
		$q = db_query("select module_id from ".MODULES_TABLE.
			" where module_name='".xEscSQL($module->title)."' ");
		$row = db_fetch_row($q);
		return (int)$row["module_id"];
	}

	function _formatPrice($price, $rval = 2, $dec = '.', $term = ' ')
	{
		return number_format($price, $rval, $dec, $term);
	}

	//show a number and selected currency sign $price is in universal currency
	function show_price($price, $custom_currency = 0, $code = true, $d = ".", $t = "")
	{
		global $selected_currency_details;
		//if $custom_currency != 0 show price this currency with ID = $custom_currency
		if ($custom_currency == 0)
		{
			if (!isset($selected_currency_details) || !$selected_currency_details) //no currency found

			{
				return $price;
			}
		}
		else //show price in custom currency

		{

			$q = db_query("select code, currency_value, where2show, currency_iso_3, Name, roundval from ".
				CURRENCY_TYPES_TABLE." where CID=".(int)$custom_currency);
			if ($row = db_fetch_row($q))
			{
				$selected_currency_details = $row; //for show_price() function
			}
			else //no currency found. In this case check is there any currency type in the database

			{
				$q = db_query("select code, currency_value, where2show, roundval from ".CURRENCY_TYPES_TABLE);
				if ($row = db_fetch_row($q))
				{
					$selected_currency_details = $row; //for show_price() function
				}
			}

		}

		//is exchange rate negative or 0?
		if ($selected_currency_details[1] == 0) return "";

		$price = roundf($price * $selected_currency_details[1]);
		//now show price
		$price = _formatPrice($price, $selected_currency_details["roundval"], $d, $t);
		if($code)
			return $selected_currency_details[2] ? $price.$selected_currency_details[0] : $selected_currency_details[0].$price;
		else
			return $price;
	}

	function ShowPriceInTheUnit($price, $currencyID)
	{
		$q_currency = db_query("select currency_value, where2show, code, roundval from ".CURRENCY_TYPES_TABLE." where CID=".(int)$currencyID);
		$currency = db_fetch_row($q_currency);
		$price = _formatPrice(roundf($price * $currency["currency_value"]), $currency["roundval"]);
		return $currency["where2show"] ? $price.$currency["code"] : $currency["code"].$price;
	}

	function addUnitToPrice($price)
	{
		global $selected_currency_details;
		$price = _formatPrice($price, $selected_currency_details["roundval"]);
		return $selected_currency_details[2] ? $price.$selected_currency_details[0] : $selected_currency_details[0].
		$price;
	}

	function ConvertPriceToUniversalUnit($priceWithOutUnit)
	{
		global $selected_currency_details;
		return (float)$priceWithOutUnit / (float)$selected_currency_details[1];
	}

	function show_priceWithOutUnit($price)
	{
		global $selected_currency_details;

		if (!isset($selected_currency_details) || !$selected_currency_details) //no currency found

		{
			return $price;
		}

		//is exchange rate negative or 0?
		if ($selected_currency_details[1] == 0) return "";

		//now show price
		$price = round(100 * $price * $selected_currency_details[1]) / 100;
		if (round($price * 10) == $price * 10 && round($price) != $price) $price = "$price"."0"; //to avoid prices like 17.5 - write 17.50 instead
		return (float)$price;
	}

	function getPriceUnit()
	{
		global $selected_currency_details;

		if (!isset($selected_currency_details) || !$selected_currency_details) //no currency found

		{
			return "";
		}
		return $selected_currency_details[0];
	}

	function getLocationPriceUnit()
	{
		global $selected_currency_details;

		if (!isset($selected_currency_details) || !$selected_currency_details) //no currency found

		{
			return true;
		}
		return $selected_currency_details[2];
	}


	/*
	function get_current_time() //get current date and time as a string
	//required to do INSERT queries of DATETIME/TIMESTAMP in different DBMSes
	{
	$timestamp = time();
	if (DBMS == 'mssql')
	// $s = strftime("%H:%M:%S %d/%m/%Y", $timestamp);
	$s = strftime("%m.%d.%Y %H:%M:%S", $timestamp);
	else // MYSQL or IB
	$s = strftime("%Y-%m-%d %H:%M:%S", $timestamp);

	return $s;
	}
	*/

	function ShowNavigator($a, $offset, $q, $path, &$out)
	{
		//shows navigator [prev] 1 2 3 4 Е [next]
		//$a - count of elements in the array, which is being navigated
		//$offset - current offset in array (showing elements [$offset ... $offset+$q])
		//$q - quantity of items per page
		//$path - link to the page (f.e: "index.php?categoryID=1&")

		if ($a > $q) //if all elements couldn't be placed on the page

		{

			//[prev]
			if ($offset > 0) $out .= "<a href=\"".$path."offset=".($offset - $q)."\">&lt;&lt; ".STRING_PREVIOUS.
				"</a>&nbsp;&nbsp;";

			//digital links
			$k = $offset / $q;

			//not more than 4 links to the left
			$min = $k - 5;
			if ($min < 0)
			{
				$min = 0;
			}
			else
			{
				if ($min >= 1)
				{ //link on the 1st page
					$out .= "<a href=\"".$path."offset=0\">1</a>&nbsp;&nbsp;";
					if ($min != 1)
					{
						$out .= "... &nbsp;&nbsp;";
					}
					;
				}
			}

			for ($i = $min; $i < $k; $i++)
			{
				$m = $i * $q + $q;
				if ($m > $a) $m = $a;

				$out .= "<a href=\"".$path."offset=".($i * $q)."\">".($i + 1)."</a>&nbsp;&nbsp;";
			}

			//# of current page
			if (strcmp($offset, "show_all"))
			{
				$min = $offset + $q;
				if ($min > $a) $min = $a;
				$out .= "<b>".($k + 1)."</b>&nbsp;&nbsp;";
			}
			else
			{
				$min = $q;
				if ($min > $a) $min = $a;
				$out .= "<a href=\"".$path."offset=0\">1</a>&nbsp;&nbsp;";
			}

			//not more than 5 links to the right
			$min = $k + 6;
			if ($min > $a / $q)
			{
				$min = $a / $q;
			}
			;
			for ($i = $k + 1; $i < $min; $i++)
			{
				$m = $i * $q + $q;
				if ($m > $a) $m = $a;

				$out .= "<a href=\"".$path."offset=".($i * $q)."\">".($i + 1)."</a>&nbsp;&nbsp;";
			}

			if (ceil($min * $q) < $a)
			{ //the last link
				if ($min * $q < $a - $q) $out .= "... &nbsp;&nbsp;";
				$out .= "<a href=\"".$path."offset=".($a - $a % $q)."\">".(floor($a / $q) + 1)."</a>&nbsp;&nbsp;";
			}

			//[next]
			if (strcmp($offset, "show_all"))
				if ($offset < $a - $q) $out .= "<a href=\"".$path."offset=".($offset + $q)."\">".STRING_NEXT.
					" &gt;&gt;</a>&nbsp;&nbsp;";

				//[show all]
				if (strcmp($offset, "show_all")) $out .= "|&nbsp;&nbsp;<a href=\"".$path."show_all=yes\">".
				STRING_SHOWALL."</a>";
			else  $out .= "|&nbsp;&nbsp;<b>".STRING_SHOWALL."</b>";

		}
	}

	function ShowNavigatormd($a, $offset, $q, $path, &$out)
	{
		//shows navigator [prev] 1 2 3 4 Е [next]
		//$a - count of elements in the array, which is being navigated
		//$offset - current offset in array (showing elements [$offset ... $offset+$q])
		//$q - quantity of items per page
		//$path - link to the page (f.e: "index.php?categoryID=1&")

		if ($a > $q) //if all elements couldn't be placed on the page

		{

			//[prev]
			if ($offset > 0) $out .= "<a href=\"".$path."offset_".($offset - $q).".html\">&lt;&lt; ".STRING_PREVIOUS.
				"</a>&nbsp;&nbsp;";

			//digital links
			$k = $offset / $q;

			//not more than 4 links to the left
			$min = $k - 5;
			if ($min < 0)
			{
				$min = 0;
			}
			else
			{
				if ($min >= 1)
				{ //link on the 1st page
					$out .= "<a href=\"".$path."offset_0.html\">1</a>&nbsp;&nbsp;";
					if ($min != 1)
					{
						$out .= "...&nbsp;&nbsp;";
					}
					;
				}
			}

			for ($i = $min; $i < $k; $i++)
			{
				$m = $i * $q + $q;
				if ($m > $a) $m = $a;

				$out .= "<a href=\"".$path."offset_".($i * $q).".html\">".($i + 1)."</a>&nbsp;&nbsp;";
			}

			//# of current page
			if (strcmp($offset, "show_all"))
			{
				$min = $offset + $q;
				if ($min > $a) $min = $a;
				$out .= "<b>".($k + 1)."</b>&nbsp;&nbsp;";
			}
			else
			{
				$min = $q;
				if ($min > $a) $min = $a;
				$out .= "<a href=\"".$path."offset_0.html\">1</a>&nbsp;&nbsp;";
			}

			//not more than 5 links to the right
			$min = $k + 6;
			if ($min > $a / $q)
			{
				$min = $a / $q;
			}
			;
			for ($i = $k + 1; $i < $min; $i++)
			{
				$m = $i * $q + $q;
				if ($m > $a) $m = $a;

				$out .= "<a href=\"".$path."offset_".($i * $q).".html\">".($i + 1)."</a>&nbsp;&nbsp;";
			}

			if (ceil($min * $q) < $a)
			{ //the last link
				if ($min * $q < $a - $q) $out .= "... &nbsp;&nbsp;";
				$out .= "<a href=\"".$path."offset_".($a - $a % $q).".html\">".(floor($a / $q) + 1)."</a>&nbsp;&nbsp;";
			}

			//[next]
			if (strcmp($offset, "show_all"))
				if ($offset < $a - $q) $out .= "<a href=\"".$path."offset_".($offset + $q).".html\">".
					STRING_NEXT." &gt;&gt;</a>&nbsp;&nbsp;";

				//[show all]
				if (strcmp($offset, "show_all")) $out .= "|&nbsp;&nbsp;<noindex><a href=\"".$path."show_all.html\" rel=\"nofollow\">".
				STRING_SHOWALL."</a></noindex>";
			else  $out .= "|&nbsp;&nbsp;<b>".STRING_SHOWALL."</b>";

		}
	}

	function GetNavigatorHtmlmd($url, $countRowOnPage = CONF_PRODUCTS_PER_PAGE, $callBackFunction, $callBackParam,
		&$tableContent, &$offset, &$count, $urlflag)
	{
		if (isset($_GET["offset"])) $offset = (int)$_GET["offset"];
		else  $offset = 0;
		$offset -= $offset % $countRowOnPage; //CONF_PRODUCTS_PER_PAGE;
		if ($offset < 0) $offset = 0;
		$count = 0;

		if (!isset($_GET["show_all"])) //show 'CONF_PRODUCTS_PER_PAGE' products on this page

		{
			$tableContent = $callBackFunction($callBackParam, $count, array("offset" => $offset, "CountRowOnPage" =>
				$countRowOnPage));
		}
		else //show all products

		{
			$tableContent = $callBackFunction($callBackParam, $count, null);
			$offset = "show_all";
		}

		if ($urlflag) ShowNavigatormd($count, $offset, $countRowOnPage, html_spchars($url."_"), $out);
		else  ShowNavigator($count, $offset, $countRowOnPage, html_spchars($url."&"), $out);
		return $out;
	}

	function GetCurrentURL($file, $exceptKeys)
	{
		$res = $file;
		foreach ($_GET as $key => $val)
		{
			$exceptFlag = false;
			foreach ($exceptKeys as $exceptKey)
				if ($exceptKey == $key)
				{
					$exceptFlag = true;
					break;
				}

				if (!$exceptFlag)
			{
				if ($res == $file) $res .= "?".$key."=".$val;
				else  $res .= "&".$key."=".$val;
			}
		}
		return $res;
	}


	function GetNavigatorHtml($url, $countRowOnPage = CONF_PRODUCTS_PER_PAGE, $callBackFunction, $callBackParam,
		&$tableContent, &$offset, &$count)
	{
		if (isset($_GET["offset"])) $offset = (int)$_GET["offset"];
		else  $offset = 0;
		$offset -= $offset % $countRowOnPage; //CONF_PRODUCTS_PER_PAGE;
		if ($offset < 0) $offset = 0;
		$count = 0;

		if (!isset($_GET["show_all"])) //show 'CONF_PRODUCTS_PER_PAGE' products on this page

		{
			$tableContent = $callBackFunction($callBackParam, $count, array("offset" => $offset, "CountRowOnPage" =>
				$countRowOnPage));
		}
		else //show all products

		{
			$tableContent = $callBackFunction($callBackParam, $count, null);
			$offset = "show_all";
		}

		ShowNavigator($count, $offset, $countRowOnPage, html_spchars($url."&"), $out);

		return $out;
	}


	function moveCartFromSession2DB() //all products in shopping cart, which are in session vars, move to the database

	{
		if (isset($_SESSION["gids"]) && isset($_SESSION["log"]))
		{

			$customerID = regGetIdByLogin($_SESSION["log"]);
			$q = db_query("select itemID from ".SHOPPING_CARTS_TABLE." where customerID=".(int)$customerID);
			$items = array();
			while ($item = db_fetch_row($q)) $items[] = (int)$item["itemID"];

			//$i=0;
			foreach ($_SESSION["gids"] as $key => $productID)
			{
				if ($productID == 0) continue;

				// search product in current user's shopping cart content
				$itemID = null;
				for ($j = 0; $j < count($items); $j++)
				{
					$q = db_query("select count(*) from ".SHOPPING_CART_ITEMS_TABLE." where productID=".
						(int)$productID." AND itemID=".(int)$items[$j]);
					$count = db_fetch_row($q);
					$count = $count[0];
					if ($count != 0)
					{
						// compare configuration
						$configurationFromSession = $_SESSION["configurations"][$key];
						$configurationFromDB = GetConfigurationByItemId($items[$j]);
						if (CompareConfiguration($configurationFromSession, $configurationFromDB))
						{
							$itemID = $items[$j];
							break;
						}
					}
				}


				if ($itemID == null)
				{
					// create new item
					db_query("insert into ".SHOPPING_CART_ITEMS_TABLE." (productID) values(".(int)$productID.")");
					$itemID = db_insert_id();

					// set content item
					foreach ($_SESSION["configurations"][$key] as $vars)
					{
						db_query("insert into ".SHOPPING_CART_ITEMS_CONTENT_TABLE." ( itemID, variantID ) ".
							" values( ".(int)$itemID.", ".(int)$vars." )");
					}

					// insert item into cart
					db_query("insert ".SHOPPING_CARTS_TABLE." (customerID, itemID, Quantity) values ( ".
						(int)$customerID.", ".(int)$itemID.", ".(int)$_SESSION["counts"][$key]." )");
				}
				else
				{
					db_query("update ".SHOPPING_CARTS_TABLE." set Quantity=Quantity + ".(int)$_SESSION["counts"][$key]." where customerID=".(int)$customerID." and itemID=".(int)$itemID);
				}

			}

			unset($_SESSION["gids"]);
			unset($_SESSION["counts"]);
			unset($_SESSION["configurations"]);
			session_unregister("gids"); //calling session_unregister() is required since unset() may not work on some systems
			session_unregister("counts");
			session_unregister("configurations");
		}
	} // moveCartFromSession2DB

	function validate_search_string($s) //validates $s - is it good as a search query

	{
		//exclude special SQL symbols
		$s = str_replace("%", "", $s);
		$s = str_replace("_", "", $s);
		//",',\
		$s = stripslashes($s);
		$s = str_replace("'", "\'", $s);
		return $s;

	} //validate_search_string

	function string_encode($s) // encodes a string with a simple algorythm

	{
		$result = base64_encode($s);
		return $result;
	}

	function string_decode($s) // decodes a string encoded with string_encode()

	{
		$result = base64_decode($s);
		return $result;
	}


	// *****************************************************************************
	// Purpose        this function creates array it containes value POST variables
	// Inputs                     name array
	// Remarks                if <name> is contained in $varnames, then for POST variable
	//                                <name>_<id> in result array $data (see body) item is added
	//                                with key <id> and POST variable <name>_<id> value
	// Returns                array $data ( see Remarks )
	function ScanPostVariableWithId($varnames)
	{
		$data = array();
		foreach ($varnames as $name)
		{
			foreach ($_POST as $key => $value)
			{
				if (strstr($key, $name."_"))
				{
					$key = str_replace($name."_", "", $key);
					$data[$key][$name] = $value;
				}
			}
		}
		return $data;
	}

	function ScanFilesVariableWithId($varnames)
	{
		$data = array();
		foreach ($varnames as $name)
		{
			foreach ($_FILES as $key => $value)
			{
				if (strstr($key, $name."_"))
				{
					$key = str_replace($name."_", "", $key);
					$data[$key][$name] = $value;
				}
			}
		}
		return $data;
	}
	// *****************************************************************************
	// Purpose        this functin does also as ScanPostVariableWithId
	//                        but it uses GET variables
	// Inputs             see ScanPostVariableWithId
	// Remarks        see ScanPostVariableWithId
	// Returns        see ScanPostVariableWithId
	function ScanGetVariableWithId($varnames)
	{
		$data = array();
		foreach ($varnames as $name)
		{
			foreach ($_GET as $key => $value)
			{
				if (strstr($key, $name."_"))
				{
					$key = str_replace($name."_", "", $key);
					$data[$key][$name] = $value;
				}
			}
		}
		return $data;
	}


	function value($variable)
	{
		if (!isset($variable)) return "undefined";

		$res = "";
		if (is_null($variable))
		{
			$res .= "NULL";
		}
		else
			if (is_array($variable))
			{
				$res .= "<b>array</b>";
				$res .= "<ul>";
				foreach ($variable as $key => $value)
				{
					$res .= "<li>";
					$res .= "[ ".value($key)." ]=".value($value);
					$res .= "</li>";
				}
				$res .= "</ul>";
			}
			else
				if (is_int($variable))
				{
					$res .= "<b>integer</b>\n";
					$res .= (string )$variable;
				}
				else
					if (is_bool($variable))
					{
						$res .= "<b>bool</b>\n";
						if ($variable) $res .= "<i>True</i>";
						else  $res .= "<i>False</i>";
					}
					else
						if (is_string($variable))
						{
							$res .= "<b>string</b>\n";
							$res .= "'".(string )$variable."'";
						}
						else
							if (is_float($variable))
							{
								$res .= "<b>float</b>\n";
								$res .= (string )$variable;
							}

							return $res;
	}


	function debug($variable)
	{
		if (!isset($variable))
		{
			echo ("undefined");
		}
		else
		{
			echo "<div align=\"left\">";
			echo (value($variable)."<br>");
			echo "</div>";
		}
	}

	function set_query($_vars, $_request = '', $_store = false)
	{

		if (!$_request)
		{

			global $_SERVER;
			$_request = $_SERVER['REQUEST_URI'];
		}

		$_anchor = '';
		@list($_request, $_anchor) = explode('#', $_request);

		if (strpos($_vars, '#') !== false)
		{

			@list($_vars, $_anchor) = explode('#', $_vars);
		}

		if (!$_vars && !$_anchor) return preg_replace('|\?.*$|', '', $_request).($_anchor ? '#'.$_anchor :
			'');
		elseif (!$_vars && $_anchor) return $_request.'#'.$_anchor;

		$_rvars = array();
		$tr_vars = explode('&', strpos($_request, '?') !== false ? preg_replace('|.*\?|', '', $_request) :
			'');
		foreach ($tr_vars as $_var)
		{

			$_t = explode('=', $_var);
			if ($_t[0]) $_rvars[$_t[0]] = $_t[1];
		}
		$tr_vars = explode('&', preg_replace(array('|^\&|', '|^\?|'), '', $_vars));
		foreach ($tr_vars as $_var)
		{

			$_t = explode('=', $_var);
			if (!$_t[1]) unset($_rvars[$_t[0]]);
			else  $_rvars[$_t[0]] = $_t[1];
		}
		$tr_vars = array();
		foreach ($_rvars as $_var => $_val) $tr_vars[] = "$_var=$_val";

		if ($_store)
		{

			global $_SERVER;
			$_request = $_SERVER['REQUEST_URI'];
			$_SERVER['REQUEST_URI'] = preg_replace('|\?.*$|', '', $_request).(count($tr_vars) ? '?'.implode
				('&', $tr_vars) : '').($_anchor ? '#'.$_anchor : '');
			return $_SERVER['REQUEST_URI'];
		}
		else  return preg_replace('|\?.*$|', '', $_request).(count($tr_vars) ? '?'.implode('&', $tr_vars) :
			'').($_anchor ? '#'.$_anchor : '');
	}

	function getListerRange($_pagenumber, $_totalpages, $_lister_num = 20)
	{

		if ($_pagenumber <= 0) return array('start' => 1, 'end' => 1);
		$lister_start = $_pagenumber - floor($_lister_num / 2);
		$lister_start = ($lister_start + $_lister_num <= $_totalpages ? $lister_start : $_totalpages -
			$_lister_num + 1);
		$lister_start = ($lister_start > 0 ? $lister_start : 1);
		$lister_end = $lister_start + $_lister_num - 1;
		$lister_end = ($lister_end <= $_totalpages ? $lister_end : $_totalpages);
		return array('start' => $lister_start, 'end' => $lister_end);
	}

	function html_spchars($_data)
	{

		if (is_array($_data))
		{

			foreach ($_data as $_ind => $_val)
			{

				$_data[$_ind] = html_spchars($_val);
			}
			return $_data;
		}
		else  return htmlspecialchars($_data, ENT_QUOTES,'cp1252');
	}

	function html_amp($_data)
	{

		if (is_array($_data))
		{

			foreach ($_data as $_ind => $_val)
			{

				$_data[$_ind] = strtr($_val, array('&' => '&amp;'));
			}
			return $_data;
		}
		else  return strtr($_data, array('&' => '&amp;'));
	}

	function ToText($str)
	{
		$str = htmlspecialchars(trim($str), ENT_QUOTES,'cp1251');
		return $str;
	}

	function xToText($str)
	{
		$str = xEscSQL(xHtmlSpecialChars($str));
		return $str;
	}

	function xStripSlashesGPC($_data)
	{

		if (!get_magic_quotes_gpc()) return $_data;
		if (is_array($_data))
		{

			foreach ($_data as $_ind => $_val)
			{

				$_data[$_ind] = xStripSlashesGPC($_val);
			}
			return $_data;
		}
		return stripslashes($_data);
	}

	/**
	* Transform date from template format to DATETIME format
	*
	* @param string $_date
	* @param string $_template template for transform
	* @return string
	*/
	function TransformTemplateToDATE($_date, $_template = '')
	{

		if (!$_template) $_template = CONF_DATE_FORMAT;
		$day = substr($_date, strpos($_template, 'DD'), 2);
		$month = substr($_date, strpos($_template, 'MM'), 2);
		$year = substr($_date, strpos($_template, 'YYYY'), 4);
		return "{$year}-{$month}-{$day} ";
	}

	/**
	* Transform DATE to template format
	*
	* @param string $_date
	* @param string $_template template for transform
	* @return string
	*/
	function TransformDATEToTemplate($_date, $_template = '')
	{

		if (!$_template) $_template = CONF_DATE_FORMAT;
		preg_match('|(\d{4})-(\d{2})-(\d{2})|', $_date, $mathes);
		unset($mathes[0]);
		return str_replace(array('YYYY', 'MM', 'DD'), $mathes, $_template);
	}

	/**
	* Check date in template format
	*
	* @param string $_date
	* @param string $_template template for check
	* @return bool
	*/
	function isTemplateDate($_date, $_template = '')
	{

		if (!$_template) $_template = CONF_DATE_FORMAT;

		$ok = (strlen($_date) == strlen($_template) && (preg_replace('|\d{2}|', '', $_date) == str_replace
			(array('MM', 'DD', 'YYYY'), '', $_template)));
		$ok = ($ok && substr($_date, strpos($_template, 'DD'), 2) < 32 && substr($_date, strpos($_template,
			'MM'), 2) < 13);
		return $ok;
	}

	/**
	* mail txt message from template
	* @param string email
	* @param string email subject
	* @param string template name
	*/
	function xMailTxt($_Email, $_Subject, $_TemplateName, $_AssignArray = array())
	{

		if (!$_Email) return 0;

		$mailSmarty = new Smarty();
		foreach ($_AssignArray as $_var => $_val)
		{

			$mailSmarty->assign($_var, $_val);
		}

		$_msg = $mailSmarty->fetch('email/'.$_TemplateName);

		include_once ("core/classes/class.phpmailer.php");
		$mail = new PHPMailer();
		if (!CONF_MAIL_METHOD) $mail->IsSMTP();
		else  $mail->IsMail();
		$mail->Host = CONF_MAIL_HOST;
		$mail->Username = CONF_MAIL_LOGIN;
		$mail->Password = CONF_MAIL_PASS;
		$mail->SMTPAuth = true;
		$mail->From = CONF_GENERAL_EMAIL;
		$mail->FromName = CONF_SHOP_NAME;
		$mail->CharSet = DEFAULT_CHARSET;
		$mail->Encoding = "8bit";
		$mail->SetLanguage("ru");
		$mail->AddReplyTo(CONF_GENERAL_EMAIL, CONF_SHOP_NAME);
		$mail->IsHTML(true);
		$mail->Subject = $_Subject;
		$mail->Body = $_msg;
		$mail->AltBody = ERROR_NO_TEXT_IN_MAILDATA;

		if (preg_match("/^[_\.a-z0-9-]{1,20}@(([a-z0-9-]+\.)+(com|net|org|mil|edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/is",
			$_Email))
		{
			$mail->ClearAddresses();
			$mail->AddAddress($_Email, '');
			return $mail->Send();
		}
		else  return false;
	}

	function xMailTxtHTML($_Email, $_Subject, $_Text, $castmail = CONF_GENERAL_EMAIL, $castname = CONF_SHOP_NAME)
	{

		if (!$_Email) return 0;

		include_once ("core/classes/class.phpmailer.php");
		$mail = new PHPMailer();
		if (!CONF_MAIL_METHOD) $mail->IsSMTP();
		else  $mail->IsMail();
		$mail->Host = CONF_MAIL_HOST;
		$mail->Username = CONF_MAIL_LOGIN;
		$mail->Password = CONF_MAIL_PASS;
		$mail->SMTPAuth = true;
		$mail->From = $castmail;
		$mail->FromName = $castname;
		$mail->CharSet = DEFAULT_CHARSET;
		$mail->Encoding = "8bit";
		$mail->SetLanguage("ru");
		$mail->AddReplyTo($castmail, $castname);
		$mail->IsHTML(false);
		$mail->Subject = $_Subject;
		$mail->Body = $_Text;

		if (preg_match("/^[_\.a-z0-9-]{1,20}@(([a-z0-9-]+\.)+(com|net|org|mil|edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/is",
			$_Email))
		{
			$mail->ClearAddresses();
			$mail->AddAddress($_Email, '');
			return $mail->Send();
		}
		else  return false;
	}

	function xMailTxtHTMLDATA($_Email, $_Subject, $_Text, $castmail = CONF_GENERAL_EMAIL, $castname = CONF_SHOP_NAME)
	{

		if (!$_Email) return 0;

		include_once ("core/classes/class.phpmailer.php");
		$mail = new PHPMailer();
		if (!CONF_MAIL_METHOD) $mail->IsSMTP();
		else  $mail->IsMail();
		$mail->Host = CONF_MAIL_HOST;
		$mail->Username = CONF_MAIL_LOGIN;
		$mail->Password = CONF_MAIL_PASS;
		$mail->SMTPAuth = true;
		$mail->From = $castmail;
		$mail->FromName = $castname;
		$mail->CharSet = DEFAULT_CHARSET;
		$mail->Encoding = "8bit";
		$mail->SetLanguage("ru");
		$mail->AddReplyTo($castmail, $castname);
		$mail->IsHTML(true);
		$mail->Subject = $_Subject;
		$mail->Body = $_Text;
		$mail->AltBody = ERROR_NO_TEXT_IN_MAILDATA;

		if (preg_match("/^[_\.a-z0-9-]{1,20}@(([a-z0-9-]+\.)+(com|net|org|mil|edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/is",
			$_Email))
		{
			$mail->ClearAddresses();
			$mail->AddAddress($_Email, '');
			return $mail->Send();
		}
		else  return false;
	}

	function _deleteHTML_Elements( $str )
	{
		$search = array ("'&(deg|#176);'i","'&(nbsp|#160);'i","'&(ndash|#8211);'i","'&(mdash|#8212);'i","'&(bull|#149);'i","'&(quot|#34|#034);'i","'&(amp|#38|#038);'i","'&(lt|#60|#060);'i","'&(gt|#62|#062);'i","'&(apos|#39|#039);'i","'&(minus|#45|#045);'i","'&(circ|#94|#094);'i","'&(sup2|#178);'i","'&(tilde|#126);'i","'&(Scaron|#138);'i","'&(lsaquo|#139);'i","'&(OElig|#140);'i","'&(lsquo|#145);'i","'&(rsquo|#146);'i","'&(ldquo|#147);'i","'&(rdquo|#148);'i","'&(ndash|#150);'i","'&(mdash|#151);'i","'&(tilde|#152);'i","'&(trade|#153);'i","'&(scaron|#154);'i","'&(rsaquo|#155);'i","'&(oelig|#156);'i","'&(Yuml|#159);'i","'&(yuml|#255);'i","'&(OElig|#338);'i","'&(oelig|#339);'i","'&(Scaron|#352);'i","'&(scaron|#353);'i","'&(Yuml|#376);'i","'&(fnof|#402);'i","'&(circ|#710);'i","'&(tilde|#732);'i","'&(Alpha|#913);'i","'&(Beta|#914);'i","'&(Gamma|#915);'i","'&(Delta|#916);'i","'&(Epsilon|#917);'i","'&(Zeta|#918);'i","'&(Eta|#919);'i","'&(Theta|#920);'i","'&(Iota|#921);'i","'&(Kappa|#922);'i","'&(Lambda|#923);'i","'&(Mu|#924);'i","'&(Nu|#925);'i","'&(Xi|#926);'i","'&(Omicron|#927);'i","'&(Pi|#928);'i","'&(Rho|#929);'i","'&(Sigma|#931);'i","'&(Tau|#932);'i","'&(Upsilon|#933);'i","'&(Phi|#934);'i","'&(Chi|#935);'i","'&(Psi|#936);'i","'&(Omega|#937);'i","'&(alpha|#945);'i","'&(beta|#946);'i","'&(gamma|#947);'i","'&(delta|#948);'i","'&(epsilon|#949);'i","'&(zeta|#950);'i","'&(eta|#951);'i","'&(theta|#952);'i","'&(iota|#953);'i","'&(kappa|#954);'i","'&(lambda|#955);'i","'&(mu|#956);'i","'&(nu|#957);'i","'&(xi|#958);'i","'&(omicron|#959);'i","'&(pi|#960);'i","'&(rho|#961);'i","'&(sigmaf|#962);'i","'&(sigma|#963);'i","'&(tau|#964);'i","'&(upsilon|#965);'i","'&(phi|#966);'i","'&(chi|#967);'i","'&(psi|#968);'i","'&(omega|#969);'i","'&(thetasym|#977);'i","'&(upsih|#978);'i","'&(piv|#982);'i","'&(ensp|#8194);'i","'&(emsp|#8195);'i","'&(thinsp|#8201);'i","'&(zwnj|#8204);'i","'&(zwj|#8205);'i","'&(lrm|#8206);'i","'&(rlm|#8207);'i","'&(lsquo|#8216);'i","'&(rsquo|#8217);'i","'&(sbquo|#8218);'i","'&(ldquo|#8220);'i","'&(rdquo|#8221);'i","'&(bdquo|#8222);'i","'&(dagger|#8224);'i","'&(Dagger|#8225);'i","'&(bull|#8226);'i","'&(hellip|#8230);'i","'&(permil|#8240);'i","'&(prime|#8242);'i","'&(Prime|#8243);'i","'&(lsaquo|#8249);'i","'&(rsaquo|#8250);'i","'&(oline|#8254);'i","'&(frasl|#8260);'i","'&(euro|#8364);'i","'&(image|#8465);'i","'&(weierp|#8472);'i","'&(real|#8476);'i","'&(trade|#8482);'i","'&(alefsym|#8501);'i","'&(larr|#8592);'i","'&(uarr|#8593);'i","'&(rarr|#8594);'i","'&(darr|#8595);'i","'&(harr|#8596);'i","'&(crarr|#8629);'i","'&(lArr|#8656);'i","'&(uArr|#8657);'i","'&(rArr|#8658);'i","'&(dArr|#8659);'i","'&(hArr|#8660);'i","'&(forall|#8704);'i","'&(part|#8706);'i","'&(exist|#8707);'i","'&(empty|#8709);'i","'&(nabla|#8711);'i","'&(isin|#8712);'i","'&(notin|#8713);'i","'&(ni|#8715);'i","'&(prod|#8719);'i","'&(sum|#8721);'i","'&(minus|#8722);'i","'&(lowast|#8727);'i","'&(radic|#8730);'i","'&(prop|#8733);'i","'&(infin|#8734);'i","'&(ang|#8736);'i","'&(and|#8743);'i","'&(or|#8744);'i","'&(cap|#8745);'i","'&(cup|#8746);'i","'&(int|#8747);'i","'&(there4|#8756);'i","'&(sim|#8764);'i","'&(cong|#8773);'i","'&(asymp|#8776);'i","'&(ne|#8800);'i","'&(equiv|#8801);'i","'&(le|#8804);'i","'&(ge|#8805);'i","'&(sub|#8834);'i","'&(sup|#8835);'i","'&(nsub|#8836);'i","'&(sube|#8838);'i","'&(supe|#8839);'i","'&(oplus|#8853);'i","'&(otimes|#8855);'i","'&(perp|#8869);'i","'&(sdot|#8901);'i","'&(lceil|#8968);'i","'&(rceil|#8969);'i","'&(lfloor|#8970);'i","'&(rfloor|#8971);'i","'&(lang|#9001);'i","'&(rang|#9002);'i","'&(loz|#9674);'i","'&(spades|#9824);'i","'&(clubs|#9827);'i","'&(hearts|#9829);'i","'&(diams|#9830);'i","'&(copy|#169);'i","'&(reg|#174);'i","'&(pound|#163);'i","'&(laquo|#171);'i","'&(raquo|#187);'i","'&(sect|#167);'i","!\s+!");

		$replace = array ("d"," ","_","-","-","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","",""," ");

		return trim(strtr(preg_replace($search, $replace, $str), array("\"" => "", "'" => "","<" => "", ">" => "", "&" => "", " ," => ",")));
	}

	/**
	* replace newline symbols to &lt;br /&gt;
	* @param mixed data for action
	* @param array which elements test
	* @return mixed
	*/
	function xNl2Br($_Data, $_Key = array())
	{


		if (!is_array($_Data))
		{

			return nl2br($_Data);
		}

		if (!is_array($_Key)) $_Key = array($_Key);
		foreach ($_Data as $__Key => $__Data)
		{

			if (count($_Key) && !is_array($__Data))
			{

				if (in_array($__Key, $_Key))
				{

					$_Data[$__Key] = xNl2Br($__Data, $_Key);
				}
			}
			else  $_Data[$__Key] = xNl2Br($__Data, $_Key);

		}
		return $_Data;
	}

	function xStrReplace($_Search, $_Replace, $_Data, $_Key = array())
	{

		if (!is_array($_Data))
		{

			return str_replace($_Search, $_Replace, $_Data);
		}

		if (!is_array($_Key)) $_Key = array($_Key);
		foreach ($_Data as $__Key => $__Data)
		{

			if (count($_Key) && !is_array($__Data))
			{

				if (in_array($__Key, $_Key))
				{

					$_Data[$__Key] = xStrReplace($_Search, $_Replace, $__Data, $_Key);
				}
			}
			else  $_Data[$__Key] = xStrReplace($_Search, $_Replace, $__Data, $_Key);

		}
		return $_Data;
	}

	function xHtmlSpecialCharsDecode($_Data, $_Params = array(), $_Key = array())
	{


		if (!is_array($_Data))
		{
			return html_entity_decode($_Data, ENT_QUOTES);
		}

		if (!is_array($_Key)) $_Key = array($_Key);
		foreach ($_Data as $__Key => $__Data)
		{

			if (count($_Key) && !is_array($__Data))
			{

				if (in_array($__Key, $_Key))
				{

					$_Data[$__Key] = xHtmlSpecialCharsDecode($__Data, $_Params, $_Key);
				}
			}
			else  $_Data[$__Key] = xHtmlSpecialCharsDecode($__Data, $_Params, $_Key);

		}
		return $_Data;
	}

	function xHtmlSpecialChars($_Data, $_Params = array(), $_Key = array())
	{


		if (!is_array($_Data))
		{

			return htmlspecialchars($_Data, ENT_QUOTES,'cp1251');
		}

		if (!is_array($_Key)) $_Key = array($_Key);
		foreach ($_Data as $__Key => $__Data)
		{

			if (count($_Key) && !is_array($__Data))
			{

				if (in_array($__Key, $_Key))
				{

					$_Data[$__Key] = xHtmlSpecialChars($__Data, $_Params, $_Key);
				}
			}
			else  $_Data[$__Key] = xHtmlSpecialChars($__Data, $_Params, $_Key);

		}
		return $_Data;
	}

	function xEscSQL($_Data, $_Params = array(), $_Key = array())
	{

		if (!is_array($_Data))
		{

			return mysql_real_escape_string($_Data);
		}

		if (!is_array($_Key)) $_Key = array($_Key);
		foreach ($_Data as $__Key => $__Data)
		{

			if (count($_Key) && !is_array($__Data))
			{

				if (in_array($__Key, $_Key))
				{

					$_Data[$__Key] = xEscSQL($__Data, $_Params, $_Key);
				}
			}
			else  $_Data[$__Key] = xEscSQL($__Data, $_Params, $_Key);

		}
		return $_Data;
	}

	function xEscapeSQLstring ( $_Data, $_Params = array(), $_Key = array() ){
		return xEscSQL($_Data, $_Params, $_Key);
	}

	function xSaveData($_ID, $_Data, $_TimeControl = 0)
	{

		if (!session_is_registered('_xSAVE_DATA'))
		{

			session_register('_xSAVE_DATA');
			$_SESSION['_xSAVE_DATA'] = array();
		}

		if (intval($_TimeControl))
		{

			$_SESSION['_xSAVE_DATA'][$_ID] = array($_ID.'_DATA' => $_Data, $_ID.'_TIME_CTRL' => array('timetag' =>
				time(), 'timelimit' => $_TimeControl, ), );
		}
		else
		{
			$_SESSION['_xSAVE_DATA'][$_ID] = $_Data;
		}
	}

	function xPopData($_ID)
	{

		if (!isset($_SESSION['_xSAVE_DATA'][$_ID]))
		{
			return null;
		}

		if (is_array($_SESSION['_xSAVE_DATA'][$_ID]))
		{

			if (isset($_SESSION['_xSAVE_DATA'][$_ID][$_ID.'_TIME_CTRL']))
			{

				if (($_SESSION['_xSAVE_DATA'][$_ID][$_ID.'_TIME_CTRL']['timetag'] + $_SESSION['_xSAVE_DATA'][$_ID][$_ID.
					'_TIME_CTRL']['timelimit']) < time())
				{
					return null;
				}
				else
				{

					$Return = $_SESSION['_xSAVE_DATA'][$_ID][$_ID.'_DATA'];
					unset($_SESSION['_xSAVE_DATA'][$_ID]);
					return $Return;
				}
			}
		}

		$Return = $_SESSION['_xSAVE_DATA'][$_ID];
		unset($_SESSION['_xSAVE_DATA'][$_ID]);
		return $Return;
	}

	function xDataExists($_ID)
	{

		if (!isset($_SESSION['_xSAVE_DATA'][$_ID])) return 0;

		if (is_array($_SESSION['_xSAVE_DATA'][$_ID]))
		{

			if (isset($_SESSION['_xSAVE_DATA'][$_ID][$_ID.'_TIME_CTRL']))
			{

				if (($_SESSION['_xSAVE_DATA'][$_ID][$_ID.'_TIME_CTRL']['timetag'] + $_SESSION['_xSAVE_DATA'][$_ID][$_ID.
					'_TIME_CTRL']['timelimit']) >= time())
				{
					return 1;
				}
				else
				{
					return 0;
				}
			}
			else
			{
				return 1;
			}
		}
		else
		{
			return 1;
		}
	}


	function xGetData($_ID)
	{

		if (!isset($_SESSION['_xSAVE_DATA'][$_ID]))
		{
			return null;
		}

		if (is_array($_SESSION['_xSAVE_DATA'][$_ID]))
		{

			if (isset($_SESSION['_xSAVE_DATA'][$_ID][$_ID.'_TIME_CTRL']))
			{

				if (($_SESSION['_xSAVE_DATA'][$_ID][$_ID.'_TIME_CTRL']['timetag'] + $_SESSION['_xSAVE_DATA'][$_ID][$_ID.
					'_TIME_CTRL']['timelimit']) < time())
				{
					return null;
				}
				else
				{

					$Return = $_SESSION['_xSAVE_DATA'][$_ID][$_ID.'_DATA'];
					return $Return;
				}
			}
		}

		$Return = $_SESSION['_xSAVE_DATA'][$_ID];
		return $Return;
	}

	function generateRndCode($_RndLength, $_RndCodes = 'qwertyuiopasdfghjklzxcvbnm0123456789')
	{

		$l_name = '';
		$top = strlen($_RndCodes) - 1;
		srand((double)microtime() * 1000000);
		for ($j = 0; $j < $_RndLength; $j++) $l_name .= $_RndCodes{rand(0, $top)};
		return $l_name;
	}

	function endsWith($haystack, $needle)
	{
		return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
	}

	function startsWith($haystack, $needle)
	{
		return $needle === "" || strpos($haystack, $needle) === 0;
	}

?>