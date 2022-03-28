<?php

function LogMessage($Message, $Filename = ERROR_LOG_FILE)
{
    file_put_contents($Filename, $Message, FILE_APPEND);
}

function LogError($ErrorMessage)
{
    $Message = "\n--------------- ERROR :".$ErrorMessage."\n";
    LogMessage($Message);
}

function LogException(Exception $e)
{
    $Message = 'CODE: '.$e->getCode()."\nMessage: ".$e->getMessage()."\nStackTrace: ".$e->getTraceAsString();
    LogError($Message);
}

function LogInfo($Message)
{
    $Message = "\nINFO:".$Message."\n";
    LogMessage($Message);
}

function LogObject($Name, $Object)
{
    $Message = "{$Name}: ";
    ob_start();
    var_dump($Object);
    $Message .= ob_get_contents()."\n";
    ob_end_clean();
    LogMessage($Message);
}

function LogFuncCall($Callable)
{
    ob_start();
    call_user_func($Callable);
    $Message = ob_get_contents()."\n";
    ob_end_clean();
    LogMessage($Message);
}

function LogStackTrace()
{
    ob_start();
    debug_print_backtrace();
    $Message = ob_get_contents()."\n";
    ob_end_clean();
    LogInfo($Message);
}

function LogSqlQuery($Sql)
{
    $Message = "SQL QUERY: ".$Sql."\n";
    LogMessage($Message);
}

function LogSqlQueryError($Sql)
{
    $Message = "MY_SQL ERROR: ".mysql_errno().": ". mysql_error()." \n QUERY: ".$Sql." \n ";
    $Filename = ERROR_LOG_FILE_SQL;
    LogMessage($Message, $Filename);
}

function LogSqlResult( $Result)
{
    LogObject("SQL_QUERY_RESULT", $Result);
}
?>