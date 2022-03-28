<?php

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     count
 * Purpose:  assign a number of array members to 'item' param
 * -------------------------------------------------------------
 */
function smarty_function_count($params, &$smarty)
{
	if (!isset($params['item']))return 'Not set item param';
	if (isset($params['array'])){
		if (!is_array($params['array'])) $smarty->assign($params['item'], 0);
		else 
			$smarty->assign($params['item'], count($params['array']));
	}else $smarty->assign($params['item'], 0);
}
?>