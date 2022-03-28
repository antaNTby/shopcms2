<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

if (!strcmp($sub, "linkexchange"))
{
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(22,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {
$ob_per_list = 20;
if(!empty($_POST['fACTION'])) require 'core/modules/linkexchange/postactions.proc.php';
if(empty($_GET['categoryID']))$_GET['categoryID'] = 0;
else $_GET['categoryID'] = intval($_GET['categoryID']);
$TotalPages = ceil(le_getLinksNumber( $_GET['categoryID']?array('le_lCategoryID'=>(int)$_GET['categoryID']):'1')/$ob_per_list);
if(empty($_GET['page']))$_GET['page'] = 1;
else $_GET['page'] = intval($_GET['page'])>$TotalPages?$TotalPages:intval($_GET['page']);

if(isset($_GET['show_all'])||isset($_POST['show_all'])){

        $ob_per_list = $ob_per_list*$TotalPages;
        $smarty->assign('showAllLinks', '1');
        $_GET['page'] = 1;
}

$lister = getListerRange($_GET['page'], $TotalPages);

if(isset($show_new_link)){

        $smarty->assign('show_new_link', 'yes');
        if(isset($_POST['LINK']))$smarty->assign('pst_LINK', html_spchars($_POST['LINK']));
}
if(isset($error_message))
        $smarty->assign('error_message', $error_message);
if(isset($_GET['safemode'])){

        $error_message  = ADMIN_SAFEMODE_WARNING;
}
$_SERVER['REQUEST_URI'] = set_query('safemode=&action=');
$le_Categories = le_getCategories();
foreach ($le_Categories as $_ind=>$_val)
        $le_Categories[$_ind]['links_num'] = le_getLinksNumber( "le_lCategoryID = {$_val['le_cID']}" );

$smarty->assign('le_LinksNumInCategories', le_getLinksNumber());
$smarty->assign('REQUEST_URI', $_SERVER['REQUEST_URI']);
$smarty->assign('url_allcategories', set_query('categoryID='));
$smarty->assign('le_categories', $le_Categories);
$smarty->assign('le_categories_num', count($le_Categories));
$smarty->assign('le_CategoryID', $_GET['categoryID']);
$smarty->assign('curr_page',$_GET['page']);
$smarty->assign('last_page', $TotalPages);
$smarty->assign('le_links', le_getLinks(
                (int)$_GET['page'],
                (int)$ob_per_list,
                ($_GET['categoryID']?array('le_lCategoryID'=>(int)$_GET['categoryID']):'1'),
                'le_lID, le_lText, le_lDesk, le_lURL, le_lCategoryID, le_lVerified',
                'le_lVerified ASC, le_lURL ASC'));
$smarty->assign('le_lister_range', range($lister['start'], $lister['end']));
$smarty->assign("admin_sub_dpt", "modules_linkexchange.tpl.html");
}
}
?>