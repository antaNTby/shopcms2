<?PHP
	if ($_GET["sub"]!="search_stat") return;
	$smarty->assign("admin_sub_dpt", "reports_search_stat.tpl.html");
	$rows = search_stat_get_stat();
	$smarty->assign("search_stat_rows", $rows);
	
	function search_stat_get_stat()
	{
		$sql_query = 'SELECT `searchID`, `query`, `count` FROM `'.SEARCH_STAT_TABLE.'` ORDER BY `count` DESC';
		$resource = db_query($sql_query);
		$rows = db_fetch_rows($resource);
		return $rows;
	}
	
	

?>
