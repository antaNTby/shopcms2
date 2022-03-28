<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        // category navigation form

        if ( isset($categoryID) )
                $out = catGetCategoryCompactCList( $categoryID );
        else
                $out = catGetCategoryCompactCList( 1 );
        $smarty->assign( "categories_tree_count", count($out) );
        $smarty->assign( "categories_tree", $out ); 

        $smarty->assign( "big_categories_tree_count", count($cats) );
        $smarty->assign( "big_categories_tree", $cats );

?>