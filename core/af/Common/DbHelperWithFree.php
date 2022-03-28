<?php

class DbHelperWithFree
{
    public static function GetColumn($DbQuery,$ColumnNum=0)
    {
        $DbRes = mysql_query($DbQuery);
        $res = array();
        while($row = mysql_fetch_array($DbRes))
        {
            $res[]= $row[$ColumnNum];
        }
        mysql_free_result($DbRes);
        return $res;
    }
    
    public static function GetTable($DbQuery)
    {
        $DbRes = mysql_query($DbQuery);
        $ret = array();
        while($row = mysql_fetch_assoc($DbRes))
        {
            $ret[]= $row;
        }
        mysql_free_result($DbRes);
        return $ret; 
    }
    
    public static function GetArray($DbQuery,$KeyKey=0,$ValueKey=1)
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
        $DbRes = db_query($DbQuery);
        $ret = array();
        while($row = db_fetch_row($DbRes))
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

    public static function GetRow($DbQuery)
    {
        $res = mysql_query($DbQuery);
        $ret = mysql_fetch_assoc($res);
        mysql_free_result($res);
        return $ret;  
    }

}

?>