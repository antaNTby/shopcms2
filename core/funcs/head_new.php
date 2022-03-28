<?php
	#####################################
	# ShopCMS: Скрипт интернет-магазина
	# Copyright (c) by ADGroup
	# http://shopcms.ru
	#####################################

	// <head> variables definition: title, meta
	// TITLE & META Keywords & META Description

	if ( isset($_GET["show_aux_page"]) ) // aux page => get title and META information from database
	{
		$page = auxpgGetAuxPage( $show_aux_page );
		if ($page["aux_page_title"]) $page_title = $page["aux_page_title"];
		elseif ($page["aux_page_name"]) $page_title = $page["aux_page_name"];
		$meta_tags = [];
		if  ( $page["meta_description"] != "" )
			$meta_tags['meta_description'] = $page["meta_description"];
		if  ( $page["meta_keywords"] != "" )
			$meta_tags["meta_keywords"] = $page["meta_keywords"];
	}
	elseif (isset($_GET["fullnews"]))  //  fullnews => get title
	{
		$fullnews_array_head = newsGetFullNewsToCustomer($_GET["fullnews"]);
		if ($fullnews_array_head["title"]) $page_title = $fullnews_array_head["title"];
		$meta_tags = [];
		if  ( CONF_HOMEPAGE_META_DESCRIPTION != "" )
			$meta_tags['meta_description'] = CONF_HOMEPAGE_META_DESCRIPTION;
		if  ( CONF_HOMEPAGE_META_KEYWORDS != "" )
			$meta_tags['meta_keywords'] = CONF_HOMEPAGE_META_KEYWORDS;
	}
	else  //not an aux page, e.g. homepage, product/category page, registration form, checkout, etc.
	{
		if (isset($categoryID) && !isset($productID) && $categoryID>0 && isset($category)) //category page
		{
			$page_title = $category['title'];
			$meta_tags['meta_description'] = $category['meta_description'];
			$meta_tags['meta_keywords'] = $category['meta_keywords'];
		}
		elseif (isset($productID) && $productID>0) //product information page
		{
			$page_title  = $product['title'];
			$meta_tags['meta_description'] = $product['meta_description'];
			$meta_tags['meta_keywords'] = $product['meta_keywords'];
		}
	}

	if(!isset($page_title)) $page_title = CONF_DEFAULT_TITLE;

    if (!isset($meta_tags)) {
        $meta_tags = [];
    }

    if(!isset($meta_tags['meta_description'])) $meta_tags['meta_description'] =  CONF_HOMEPAGE_META_DESCRIPTION;
	if(!isset($meta_tags['meta_keywords'])) $meta_tags['meta_description'] = CONF_HOMEPAGE_META_KEYWORDS;

	$smarty->assign("page_title", $page_title );
	$smarty->assign("page_meta_tags", $meta_tags );         

?>