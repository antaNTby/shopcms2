<?php

	$ShowTree = true;

	if(!$ShowTree)
	{
		if ( isset($categoryID) )
			$cat_tree = catGetCategoryNew( $categoryID );
		else
			$cat_tree = catGetCategoryNew();
		//var_dump($cat_tree);die;
		$smarty->assign( "cat_tree_count", count($cat_tree) );
		$smarty->assign( "cat_tree", $cat_tree );
	}
	else
	{
		
		if ( isset($categoryID) )
			$cat_tree = catGetCategoryNewMS( $categoryID );
		else
			$cat_tree = catGetCategoryNewMS();
		//var_dump($cat_tree);die;
		$smarty->assign( "cat_tree_count", count($cat_tree) );
		$smarty->assign( "cat_tree", $cat_tree );
		
		
		//----------------------------------------------------------
		/*
		// category navigation form
		if ( isset($categoryID) )
			$out = catGetCategoryCompactCList( $categoryID );
		else
			$out = catGetCategoryCompactCList( 1 );

		$select_level = 0;
		if(isset($category))
		{
			foreach ($out as $cats) {
				if(isset($cats['categoryID']) && isset($category['categoryID']))
				{
					if ($cats['categoryID'] == $category['categoryID']) {
						$select_level = $cats['level'];
					}
				}
			}
		}
		$smarty->assign( "select_level", $select_level );

		$smarty->assign( "categories_tree_count", count($out) );
		$smarty->assign( "categories_tree", $out );

		$smarty->assign( "cat_tree_count", count($out) );
		$smarty->assign( "cat_tree", $out );
		*/
	}

?>