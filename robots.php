<?php 

header('Content-type: text/plain');
$content = file_get_contents('robots.txt');
$host = $_SERVER['HTTP_HOST'];
$content = str_replace('{host}',$host,$content);
echo $content;

?>