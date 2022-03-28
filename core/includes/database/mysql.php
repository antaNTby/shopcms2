<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

function db_connect($host,$user,$pass) //create connection
{
        $r = mysql_connect($host,$user,$pass);
        mysql_query('SET session sql_mode=0');
        mysql_query('SET NAMES cp1251 COLLATE cp1251_general_ci');
        return $r;
}

function db_disconnect() //close connection
{
        return mysql_close();
}

function db_select_db($name) //select database
{

        return mysql_select_db($name);
}

function db_query($s) //database query
{
        global $sc_4, $sc_8, $gmc;

        if (isset($gmc) && $gmc == 1) $sc_81 = getmicrotime();

        // $scriptv = getmicrotime();
        $res = array();
        $res["resource"] = mysql_query($s);
        /*
        $scriptp = getmicrotime();
        $rom = $scriptp-$scriptv;
        print $rom." - ".$s."<br>";
        */

        if(!$res['resource']){
            $out = "ERROR: ".mysql_errno().":".mysql_error()."\nSql: ".$s."\nLink: ".$_SERVER["REQUEST_URI"]."\nDate: ".date("d.m.y - H:i:s")."\nDump:\n";
            ob_start();
            var_dump($_GET);
            var_dump($_POST);
            $tmpa=ob_get_contents();
            ob_end_clean();
            $out .= $tmpa;
            mysql_query("insert into ".MYSQL_ERROR_LOG_TABLE." (errors, tstamp) VALUES ('".xEscSQL(ToText($out))."', NOW())");
            $ecount = mysql_fetch_row(mysql_query("select count(*) from ".MYSQL_ERROR_LOG_TABLE));
            $ecount = $ecount[0] - 50;
            if($ecount > 0) mysql_query("delete from ".MYSQL_ERROR_LOG_TABLE." ORDER BY tstamp ASC LIMIT ".$ecount);
		    // die('Wrong database query!');
        }

        $res["columns"]=array();
        $column_index = 0;

        if(is_resource($res["resource"]))
        {
	        while($xwer = @mysql_fetch_field($res["resource"])){

	                $res["columns"][$xwer->name] = $column_index;
	                $column_index++;
	        }
        }

        if (isset($gmc) && $gmc == 1) {
            $sc_82 = getmicrotime();
            $sc_4++;
            $sc_8 = $sc_8+$sc_82-$sc_81;
        }
        return $res;
}

function db_fetch_row($q) //row fetching
{
	//try catch PHP Warning:  mysql_fetch_row(): supplied argument is not a valid MySQL result resource in /home/mapsshop/domains/alldomains.ru/public_html/core/includes/database/mysql.php on line 88
	if(! is_resource($q["resource"])){
            LogObject('$q', $q);
            LogStackTrace();
	}

        $res = mysql_fetch_row($q["resource"]);
        if ( $res )
        {
                foreach( $q["columns"] as $column_name => $column_index )
                        $res[$column_name] = $res[$column_index];
        }
        /*if($res === false) {
            LogError('Error on db_fetch_row():');
            LogObject('$q', $q);
            LogStackTrace();
        }*/
        return $res;
}

function db_fetch_rows($q)
{
	$ret = array();
	while( $res = db_fetch_row($q)){
		$ret[]=$res;
	}
	return $ret;
}

function db_insert_id($gen_name = "") //id of last inserted record

{
        return mysql_insert_id();
}

function db_error() //database error message
{
        return mysql_error();
}

function db_get_all_tables()
{
        $q = db_query( "show tables" );
        $res = array();
        while( $row=db_fetch_row($q) )
                $res[] = strtolower($row[0]);
        return $res;
}

function db_get_all_ss_tables( $xmlFileName )
{
        $res = array();
        $tables = db_get_all_tables();
        $xmlNodeTableArray = GetXmlTableNodeArray( $xmlFileName );
        foreach( $xmlNodeTableArray as $xmlNodeTable )
        {
                $attr = $xmlNodeTable->GetXmlNodeAttributes();
                $existFlag = false;
                foreach( $tables as $tableName )
                {
                        if ( strtolower($attr["NAME"]) == $tableName )
                                $existFlag = true;
                }
                if ( $existFlag )
                        $res[] = $attr["NAME"];
        }
        return $res;
}

function db_delete_table( $tableName )
{
        db_query( "drop table ".$tableName );
}

function db_delete_all_tables()
{
        $tableArray = db_get_all_tables();
        foreach( $tableArray as $tableName )
                db_query( "drop table ".$tableName );
}

function db_add_column( $tableName, $columnName, $type, $default, $nullable )
{
        if ( $nullable )
                $nullableStr = " NULL ";
        else
                $nullableStr = " NOT NULL ";
        if ( $default != null )
                db_query( "alter table ".$tableName." add column ".$columnName." $type ".$nullableStr.
                                                " default ".$default );
        else
                db_query( "alter table ".$tableName." add column ".$columnName." $type ".$nullableStr );
}

function db_rename_column( $tableName, $oldColumnName, $newColumnName, $type, $default, $nullable )
{
        if ( $nullable )
                $nullableStr = " NULL ";
        else
                $nullableStr = " NOT NULL ";
        if ( $default != null )
                db_query( "alter table ".$tableName." change ".$oldColumnName." ".
                                $newColumnName." ".$type." ".$nullableStr." default ".$default );
        else
                db_query( "alter table ".$tableName." change ".$oldColumnName." ".
                                $newColumnName." ".$type." ".$nullableStr );
}

function db_delete_column( $tableName, $columnName )
{
        db_query( "alter table ".$tableName." drop column ".$columnName );
}

function db_getColumns($_TableName){

        $Columns = array();
        $sql = '
                SHOW COLUMNS FROM `'.$_TableName.'`
        ';
        $Result = db_query($sql);
        if(!db_num_rows($Result["resource"]))return $Columns;
        while ($_Row = db_fetch_row($Result)){

                $Columns[strtolower($_Row['Field'])] = $_Row;
        }
        return $Columns;
}
function db_num_rows($_result){

        return mysql_num_rows($_result);
}

function db_get_server_info()
{
        $res = mysql_get_server_info();
        return $res;
}
function db_phquery(){

        $args = func_get_args();
        $tmpl = array_shift($args);
        $sql = sql_placeholder_ex($tmpl, $args, $error);
        if ($sql === false) $sql = PLACEHOLDER_ERROR_PREFIX.$error;
        return db_query($sql);
}

function db_fetch_assoc($Result){

        return mysql_fetch_assoc($Result['resource']);
}

function db_data_seek($Result){

        return mysql_data_seek($Result['resource']);
}

?>