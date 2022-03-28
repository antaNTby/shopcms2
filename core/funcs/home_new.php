<?php
	#####################################
	# ShopCMS: Скрипт интернет-магазина
	# Copyright (c) by ADGroup
	# http://shopcms.ru
	#####################################

	if($categoryID != 1) return;
	if(isset($_GET["buy"]) && isset($_GET["product_id"])) return;
	if(isset($show_aux_page)) return;
	require_once('core/config/w1.inc.php');
	require_once('core/classes/class.robokassa.php');
	//require_once('core/config/robokassa.inc.php');

	/*$smarty->assign("w1_success_url", $w1_success_url);
	$smarty->assign("w1_fail_url", $w1_fail_url);
	$smarty->assign("w1_currency_id", $w1_currency_id);
	$smarty->assign("w1Login", $w1_login);

	$sMerchantLogin = $rk_login;
	$nInvId = 0;
	$Desc = $a['name'];
	$sSignatureValue = md5("$sMerchantLogin:$nOutSum:$nInvId:$rk_pass1");
	//$smarty->assign("Desc", $Desc);
	$smarty->assign("sMerchantLogin", $sMerchantLogin);
	$smarty->assign("nInvId", $nInvId);
	$smarty->assign("sSignatureValue", $sSignatureValue);*/
	//special offers

	$result = array();

	$sql_query="select s.productID, s.categoryID, s.UID,
	IF(tplID>0,REPLACE(tp.name,'{city}',s.tpl_city),s.name) name,
	IF(tplID>0,tp.Price,s.Price) Price,
	IF(tplID>0,REPLACE(tp.brief_description,'{city}',s.tpl_city),s.brief_description) brief_description,
	IF(tplID>0,tp.product_code,s.product_code) product_code,
	tp.folder_pictures,
	s.default_picture, s.enabled,s.pictures, b.productID, t.filename FROM ".SPECIAL_OFFERS_TABLE." AS b 
	INNER JOIN ".PRODUCTS_TABLE." AS s on (b.productID=s.productID) 
	INNER JOIN ".PRODUCTS_TPL_TABLE." AS tp USING(tplID)
	LEFT JOIN ".PRODUCT_PICTURES." AS t on (s.default_picture=t.photoID AND s.productID=t.productID) WHERE s.enabled=1 order by tp.sort_order, s.sort_order";
	$q = db_query($sql_query);

	while ($row = db_fetch_row($q))
	{
		//if (strlen($row["filename"])>0 && file_exists( "pictures/".$row["filename"])){ 
		if(!$row["filename"])
		{
			$pictures = explode(",", $row["pictures"]);
			$row["picture"] = "data/pictures_tpl/" . $row["folder_pictures"] . "/240/" . $pictures[0];
		}          
		else
		{   
			$row["picture"] =  "data/pictures/osn/".$row["filename"];    
		}

		if(!file_exists($row["picture"]) || is_dir($row["picture"])){
			$row["picture"] = TPL_PATH.'/images/foto1.gif';
		}                   

		$Price = $row["Price"] * $multi_koef;
		$row["robo_display_price"] = show_price($Price);
		$row["nOutSum"]= show_priceWithOutUnit($Price/(1+$rk_rate/100));
		$row['BuyButton'] = Robokassa::GetPaymentButton($row["nOutSum"],$row['name']);
		/*
		$row["cena"] = $row["Price"];
		$row["price"] = show_price($row["Price"]);
		$row["Desc"] = $Desc;
		$Desc  = $row['name'];
		$row["sSignatureValue"] = md5("$sMerchantLogin:$nOutSum:$nInvId:$rk_pass1");
		*/
		$result[] = $row;
	}

	$smarty->assign("special_offers",$result);
	
?>