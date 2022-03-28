<?php

set_include_path($_SERVER['DOCUMENT_ROOT'].'/core/');
//var_dump(dirname(__FILE__));die;

include("config/connect.inc.php");
include("includes/database/".DBMS.".php");
include("functions/category_functions.php");
include("functions/setting_functions.php" );
include("functions/functions.php");
include("functions/statistic_functions.php");
include("functions/session_functions.php");
MagicQuotesRuntimeSetting();

//connect 2 database
db_connect(DB_HOST,DB_USER,DB_PASS) or die (db_error());
db_select_db(DB_NAME) or die (db_error());

settingDefineConstants();
define("SECURITY_EXPIRE",60*60*24*CONF_SECURITY_EXPIRE);
session_set_save_handler("sess_open","sess_close","sess_read","sess_write","sess_destroy","sess_gc");
session_set_cookie_params(SECURITY_EXPIRE);
session_start();

//current language
include("config/language_list.php");
if (!isset($_SESSION["current_language"]) ||
    $_SESSION["current_language"] < 0 || $_SESSION["current_language"] > count($lang_list))
    $_SESSION["current_language"] = 0; //set default language

//include a language file
if (isset($lang_list[$_SESSION["current_language"]]) &&
    file_exists("./../../languages/".$lang_list[$_SESSION["current_language"]]->filename))
{
    //include current language file
    include("languages/".$lang_list[$_SESSION["current_language"]]->filename);
}
else
{
    die("<font color=red><b>ERROR: Couldn't find language file!</b></font>");
}

include("config/headers.php");
include("funcs/checklogin.php");

if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(1,$relaccess))) //unauthorized
{
    die (ERROR_FORBIDDEN);
}

?>
