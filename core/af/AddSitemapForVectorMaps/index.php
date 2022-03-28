<?php

if ((!isset($_GET["run"])) || !$_GET["run"]=="true") return;

include_once('StandartIncludes.php');
require_once("../Common/DbHelperWithFree.php");
require_once("NodeListWriter.php");
require_once("SitemapWriter.php");
echo "BeforeParadox";
require_once("SitemapIndexWriter.php");
require_once("SitemapGenerator.php");
require_once("SitemapCreatorForVectorMaps.php");
/*
$OutFile = 'sitemap.xml';

$SqlQuery = "SELECT UID FROM `avl_products` WHERE `tplID` IN (28,29,30,31)";

$Domain = rmap;

$fp = fopen($OutFile,"w");
$res = mysql_query($SqlQuery);
while($row = mysql_fetch_array($res))
{
    $AddStr = <<<HDD
<url>
  <loc>http://{$Domain}{$row['UID']}.html</loc>
  <lastmod>+00:00</lastmod>
  <changefreq>weekly</changefreq>
  <priority>0.6</priority>
</url>
HDD;
    fwrite($fp,$AddStr);
}*/

$Host = $_SERVER['HTTP_HOST'];
if(isset($_GET['domain'])) $Host = $_GET['domain'];
$Domain = 'http://'.$Host;

$DocumentRoot = $_SERVER['DOCUMENT_ROOT'];

$WebDirectory = '/sitemap/'.$Host.'/';
$WorkDirectory = $DocumentRoot.$WebDirectory;

//$SitemapCreator = new SitemapCreatorByIterators();
$SitemapCreator = new SitemapCreatorForVectorMaps();
$SitemapCreator->CreateSitemap($WorkDirectory,$Domain,$WebDirectory);
echo 'Sitemap Created!';