<?php

$target = '/home/mapsshop/domains/alldomains.ru/public_html';
$linkPublic = '/home/mapsshop/domains/shopmaps.ru/public_html';
$linkPrivate = '/home/mapsshop/domains/shopmaps.ru/private_html';
unlink($linkPublic);
unlink($linkPrivate);

//var_dump(scandir($target));

var_dump(symlink($target, $linkPublic));

//var_dump(scandir('/home/mapsshop/domains/shopmaps.ru'));