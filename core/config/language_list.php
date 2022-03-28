<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

//this file indicates listing of all available languages

class Language
{
        var $description; //language name
        var $filename; //language PHP constants file
        var $template; //template filename
	var $iso2;
}

        //a list of languages
        $lang_list = array();

        //to add new languages add similiar structures
    //ru-win1251
	$LangRu = new Language();	
	$LangRu->description = "Русский (win1251)";
	$LangRu->filename = "russian.php";
	$LangRu->iso2 = "ru";
	$lang_list['ru'] = $LangRu;

    //ru-utf-8
    $LangRuUtf8 = new Language();
    $LangRuUtf8->description = "Русский (utf-8)";
    $LangRuUtf8->filename = "russian-utf8.php";
    $LangRuUtf8->iso2 = "ru-utf8";
    $lang_list['ru-utf8'] = $LangRuUtf8;

    #pl-utf8
    $LangPlUtf8 = new Language();
    $LangPlUtf8->description = "Polska";
    $LangPlUtf8->filename = "polska-utf8.php";
    $LangPlUtf8->iso2 = "pl-utf8";
    $lang_list['pl-utf8'] = $LangPlUtf8;

?>