<?php
if (CONF_AUTOSAVE){

$interval_update = 24;   // Интервал сохранения БД в часах. По умолчанию - сутки.
$delete_interval = 168;  // Интервал удаления старых дампов БД в часах. По умолчанию - неделя.
$deletedump = 1;         // Автоудаление дампов БД. "0" - выкл, "1" - вкл.

  $result = db_query( "select last_update from ".DUMP_TABLE." where type=1" );
  $results = db_fetch_row($result);
  $last_update = $results["last_update"];
  if ((time()-$last_update) > ($interval_update*3600))
  {
  $querys = "update ".DUMP_TABLE." set last_update = '".time()."' where type=1";
  $results = db_query($querys);
  $path = "core/backup";
      if (!is_dir($path)) return false;
      $handle=opendir ($path);
      $patterns[0] = "/-/";
      $replacements[0] = ":";
           while (false !== ($file = readdir ($handle))) {

                if (preg_match("/dump_20(.*?)\.sql\.gz/", $file, $matches))
                {
                preg_match("/(.*?)_(.*)/",$matches[1] , $matches);
                $filedate=$matches[1];
                $filetime=preg_replace($patterns,$replacements,$matches[2]);
                $filetimestamp=strtotime($filedate." ".$filetime);
                if (time()-$delete_interval*3600>=$filetimestamp && $deletedump>0)  unlink($path."/".$file);
                }

           }

  closedir($handle);
  include_once('core/classes/class.dump.php');
  $SK = new dumper();
  $SK->backup();
  }
}


?>