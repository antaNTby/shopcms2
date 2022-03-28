<?php

class DbHelper
{
    public static function GetColumn($DbQuery,$ColumnNum=0)
    {
        $DbRes = db_query($DbQuery);
        $res = array();
        while($row = db_fetch_row($DbRes))
        {
            $res[]= $row[$ColumnNum];
        }
        return $res;
    }
    
    public static function GetTable($DbQuery)
    {
        $DbRes = mysql_query($DbQuery);
        $ret = array();
        while($row = mysql_fetch_array($DbRes))
        {
            $ret[]= $row;
        }
        return $ret; 
    }
    
    public static function GetArray($DbQuery,$KeyKey,$ValueKey)
    {
        $DbRes = db_query($DbQuery);
        $ret = array();
        while($row = db_fetch_row($DbRes))
        {
            $ret[$row[$KeyKey]]= $row[$ValueKey];
        }
        return $ret;
    }
    
    public static function GetColumnGroupBy($DbQuery, $GroupKey,$ValueKey)
    {
        $DbRes = db_query($DbQuery);
        $ret = array();
        while($row = db_fetch_row($DbRes))
        {
            $ret[$row[$GroupKey]][]= $row[$ValueKey];
        }
        return $ret; 
    }
    
    public static function GetTableGroupBy($DbQuery, $GroupKey)
    {
        $DbRes = db_query($DbQuery);
        $ret = array();
        while($row = db_fetch_row($DbRes))
        {
            $ret[$row[$GroupKey]][]= $row;
        }
        return $ret; 
    }
    
    public static function GetTableWithKeyByColumn($DbQuery, $GroupKey)
    {
        $DbRes = mysql_query($DbQuery);
        $ret = array();
        while($row = mysql_fetch_assoc($DbRes))
        {
            $ret[$row[$GroupKey]]= $row;
        }
        return $ret; 
    }
        
    public static function GetScalar($DbQuery)
    {
        $DbRes = db_query($DbQuery);
        $row = db_fetch_row($DbRes);
        $ret = $row[0];
        return $ret;    
    }

}

?>
