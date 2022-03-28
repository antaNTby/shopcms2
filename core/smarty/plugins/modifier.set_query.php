<?php
/*
* Smarty plugin
* -------------------------------------------------------------
* Файл:     modifier.set_query.php
* Тип:     modifier
* Имя:     query
* Назначение:  работа с переменными в строке запроса
* -------------------------------------------------------------
*/
function smarty_modifier_set_query($_vars, $_request = ''){
	
	if(!$_request){
		
		global $_SERVER;
		$_request = $_SERVER['REQUEST_URI'];
	}
	
	$_anchor = '';
	@list($_request, $_anchor) = explode('#', $_request);
	
	if(strpos($_vars, '#')!==false){
		
		@list($_vars, $_anchor) = explode('#', $_vars);
	}
	
	if(!$_vars && !$_anchor)
		return preg_replace('|\?.*$|','', $_request).($_anchor?'#'.$_anchor:'');
	elseif ($_anchor && !$_vars)
		return $_request.'#'.$_anchor;
		
	$_rvars = array();
	$tr_vars = explode('&', strpos($_request, '?')!==false?preg_replace('|.*\?|','',$_request):'');
	foreach ($tr_vars as $_var){
		
		$_t = explode('=', $_var);
		if($_t[0])$_rvars[$_t[0]] = $_t[1];
	}
	$tr_vars = explode('&', preg_replace(array('|^\&|','|^\?|'), '', $_vars));
	foreach ($tr_vars as $_var){
		
		$_t = explode('=', $_var);
		if(!$_t[1])unset($_rvars[$_t[0]]);
		else $_rvars[$_t[0]] = $_t[1];
	}
	$tr_vars = array();
	foreach ($_rvars as $_var=>$_val)
		$tr_vars[] = "$_var=$_val";
	return preg_replace('|\?.*$|','', $_request).(count($tr_vars)?'?'.implode('&', $tr_vars):'').($_anchor?'#'.$_anchor:'');
}
?> 