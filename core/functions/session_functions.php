<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

  function sess_open($save_path, $session_name)
  {
  return true;
  }

  function sess_close()
  {
  return true;
  }

  function sess_read($key)
  {
  $r = db_query("select data, IP from ".SESSION_TABLE." where id='".mysql_real_escape_string($key)."'");
  if (!$r)
    {
    return "";
    }
  else
    {
    $result = db_fetch_row($r);
    if (!empty($result))
      {
       if(CONF_SECURE_SESSIONS)  {
         if (stGetCustomerIP_Address() != $result[1])   {
            db_query("delete from ".SESSION_TABLE." where id='".mysql_real_escape_string($key)."'");
             return "";
               }
         }
      return $result[0];
      }
    else
      {
      return "";
      }
    }
  }

  function sess_write($key, $val)
  {
  db_query("replace into ".SESSION_TABLE." values ('".mysql_real_escape_string($key)."', '".mysql_real_escape_string($val)."', UNIX_TIMESTAMP() + ".SECURITY_EXPIRE.", '".mysql_real_escape_string(stGetCustomerIP_Address())."', '".mysql_real_escape_string($_SERVER["HTTP_REFERER"])."', '".mysql_real_escape_string($_SERVER["HTTP_USER_AGENT"])."', '".mysql_real_escape_string($_SERVER["REQUEST_URI"])."')");
  }

  function sess_destroy($key)
  {
  db_query("delete from ".SESSION_TABLE." where id='".mysql_real_escape_string($key)."'");
  return true;
  }

  function sess_gc($maxlifetime)
  {
  db_query("delete from ".SESSION_TABLE." where expire < UNIX_TIMESTAMP()");
  }

?>