<?php

	function search_stat_log($query)
	{
		$query = xEscSQL(trim($query));	
		$query_id = search_stat_get_query_id($query);
		if($query_id) search_stat_increment_count_by_id($query_id);
		else search_stat_add_query($query);
	}
	
	function search_stat_get_query_id($query)
	{
		$sql_query = 'SELECT searchID FROM `'.SEARCH_STAT_TABLE."` WHERE `query`='$query'";
		$res = db_query($sql_query);
		$row = db_fetch_assoc($res);
		if(!$row) return false;
		$query_id = $row['searchID'];
		return $query_id;
	}
	
	function search_stat_add_query($query)
	{
		$sql_query = 'INSERT INTO `'.SEARCH_STAT_TABLE."` SET `query`='$query'";
		$res = db_query($sql_query);
		if(!$res['resource']) throw new Exception();
	}
	
	function search_stat_increment_count_by_id($id)
	{
		$sql_query = 'UPDATE `'.SEARCH_STAT_TABLE.'` SET `count`=`count`+1 WHERE searchID='.$id;
		$res = db_query($sql_query);
		if(!$res['resource']) throw new Exception();
	}
?>
