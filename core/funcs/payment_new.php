<?php
	#####################################
	# ShopCMS: Скрипт интернет-магазина
	# Copyright (c) 2008 by ADGroup
	# http://shopcms.ru
	#####################################

	if(isset($_GET["buy"]) && isset($_GET["product_id"]) && $_GET["buy"] == 1 && (int)$_GET["product_id"] > 0){//papush
		$product_id = (int)$_GET["product_id"];
		$product = GetProduct($product_id);

		$q = mysql_query("SELECT description, seo_gen, Price FROM avl_products_tpl WHERE tplID = ".(int)$product['tplID']) or die(mysql_error().'. Ошибка в строке '.__LINE__);
		$text = mysql_result($q, 0);
		$text = strtr($text, array('{city}' => $product['tpl_city'], '{val_1}' => $product['tpl_val_1']));
		$seo_gen = mysql_result($q, 0, 1);
		$price = mysql_result($q, 0, 2);
		$product["PriceWithUnit"] = show_price( $price );
		if(!empty($text) && $seo_gen == 1){
			preg_match_all('/\[(.*)\]/U', $text, $pattern);
			$indexes = unserialize($product['text_index']);
			foreach($indexes as $k => $v){
				$str = $pattern[1][$k];
				$part = explode('|', $str);
				$to_replace['['.$str. ']'] = $part[$v];
			}    

			$to_show = strtr($text, $to_replace);
			//$smarty->assign("gen_descr", $to_show);
			$description = $to_show;
		} else {
			$description = $text;    
		}

		$product['description'] = $description == '' ? $product['description'] : $description;
		require('core/config/w1.inc.php');

		if($price > 0)
			$price = $price * $multi_koef;
		else
			$price = $product['Price'];
		$robo_display_price = show_price($price);
		$sMerchantLogin = $w1_login;
		//$nOutSum = show_priceWithOutUnit($price/(1+$rk_rate/100));
		$nOutSum = show_priceWithOutUnit($price);
		//$Desc  = 'BASE64:' . base64_encode(iconv('windows-1251', 'utf-8', $product['name']));
		$Desc  = $product['name'];
		//https://merchant.roboxchange.com/WebService/Service.asmx/GetCurrencies?MerchantLogin=demo&Language=ru - список валют

		//require('core/classes/class.robokassa.php');
        if(!class_exists('Robokassa')) require_once('core/classes/class.robokassa.php');
        
		$product['BuyButton'] = Robokassa::GetPaymentButton($nOutSum,$product['name'],1, 0);


		$smarty->assign("robo_display_price", $robo_display_price);
		$smarty->assign("sMerchantLogin", $sMerchantLogin);
		$smarty->assign("nOutSum", $nOutSum);
		$smarty->assign("Desc", $Desc);

		$smarty->assign("w1_success_url", $w1_success_url);
		$smarty->assign("w1_fail_url", $w1_fail_url);
		$smarty->assign("w1_currency_id", $w1_currency_id);


		$smarty->assign("product", $product);
		$smarty->assign("main_content_template", "payment.tpl.html");
	}//
?>
