<?php
  #####################################
  # ShopCMS: Скрипт интернет-магазина
  # Copyright (c) by ADGroup
  # http://shopcms.ru
  #####################################
 
  Header("Content-Type: text/xml");

  echo "<?xml version=\"1.0\" encoding=\"".DEFAULT_CHARSET."\"?>\n\n";
  echo "<rss version=\"2.0\">\n\n";
  echo "<channel>\n";
  echo "<title>".CONF_SHOP_NAME."</title>\n";
  echo "<link>".CONF_FULL_SHOP_URL."</link>\n";
  echo "<description>".CONF_HOMEPAGE_META_DESCRIPTION."</description>\n";
  echo "<generator>ShopCMS</generator>\n";
  echo "<copyright>Copyright (c) ".CONF_SHOP_NAME."</copyright>\n";
  echo "<language>ru</language>\n";
  echo "<lastBuildDate>".date("Y-m-d H:i:s")."</lastBuildDate>\n\n";

  $result = db_query("select NID, title, textToPrePublication, add_stamp as formatted FROM ".NEWS_TABLE." WHERE add_stamp<=NOW() ORDER BY NID DESC LIMIT ".CONF_NEWS_COUNT_IN_CUSTOMER_PART);

  while ( list($NID, $title, $textToPrePublication, $formatted) = db_fetch_row($result)) {
    $s_data = date("Y-m-d H:i:s", $formatted);
    echo "<item>\n";
    echo "<title>".$title."</title>\n";
    echo "<link>".CONF_FULL_SHOP_URL."index.php?fullnews=".$NID."</link>\n";
    echo "<description><![CDATA[".$textToPrePublication."]]></description>\n";
    echo "<pubDate>".$s_data."</pubDate>\n";
    echo "</item>\n\n";
  }

  echo "</channel>\n";
  echo "</rss>";
?>