<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

    if (!isset ( $_GET["do"] ) ||  in_array($_GET["do"],array("cart","invoice_jur","invoice_phys","configurator","wishcat","wishlist","wishprod"))) {
        @ini_set('zlib.output_compression_level', 9);
        // ob_end_flush();
        ob_start('ob_gzhandler');
		
		header ("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
		//Вроде бы эти страницы сейчас никогда не вызываются, поэтому закомментил, чтобы не сбивали с толку
        //header ("Content-Type: text/html; charset=windows-1251");
        //header ("Content-Type: text/html; charset=iso-8859-2");

    }
?>