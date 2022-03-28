<?php
$week_day = date('w');
$sql = mysql_query("SELECT settings_value FROM ".SETTINGS_TABLE." WHERE settings_description='".$week_day."'");
if (mysql_num_rows($sql) > 0) {
	$row = mysql_fetch_assoc($sql);
	$multi_koef = (int)$row['settings_value'];
}
?>