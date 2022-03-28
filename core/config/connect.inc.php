<?php

$host = $_SERVER["HTTP_HOST"];
$UserLang = 'ru';
$UserDbCharset = 'cp1251';

/*
$AdminLang = 'ru';
$AdminDbCharset = 'cp1251';
$AdminHtmlCharset = 'cp1251';
$AdminTplDir = 'cp1251';
*/


$AdminLang = 'ru-utf8';
$AdminDbCharset = 'utf8';
$AdminHtmlCharset = 'utf-8';
$AdminTplDir = 'utf8';

$CategoryTreeTpl = 'category_tree_rmap.tpl.html';
$adminEmail = '3481698@gmail.com';
$adminPhone = '+7(499)348-16-98';
$adminPhoneRaw = '+74993481698';

require('domains/'.$host.'.php');

?>