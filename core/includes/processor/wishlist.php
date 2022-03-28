<?php
  #####################################
  # ShopCMS: Скрипт интернет-магазина
  # Copyright (c) by ADGroup
  # http://shopcms.ru
  #####################################

  function showproducts($cid, $owner) //show products of the selected category

  {
      $q = db_query("select productID, name FROM ".PRODUCTS_TABLE." WHERE categoryID='".$cid."'");
      echo "<table class=\"adn\">";
      while ($row = db_fetch_row($q))
      {
          echo "<tr><td><a href=\"".ADMIN_FILE."?do=wishlist&amp;owner=".$owner."&amp;categoryID=".$cid."&amp;select_product=".$row[0]."\" style=\"font-size: 10px;\">".
              $row[1]."</a></td></tr>";
      }
      echo "</table>";
  }

  $relaccess = checklogin();

  if (CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(1, $relaccess))) //unauthorized

  {
      die(ERROR_FORBIDDEN);
  }

  if (!isset($_GET["owner"])) //'owner product' not set

  {
      echo "<center><font color=red>".ERROR_CANT_FIND_REQUIRED_PAGE."</font>\n<br><br>\n";
      echo "<a href=\"#\" onclick=\"window.close();\">".CLOSE_BUTTON."</a></center></body>\n</html>";
      exit;
  }

  $_GET["owner"] = isset($_GET["owner"]) ? $_GET["owner"] : 0;
  $owner = (int)$_GET["owner"];
  $categoryID = isset($_GET["categoryID"]) ? $_GET["categoryID"] : 0;
  $categoryID = (int)$categoryID;

  if (isset($_GET["select_product"])) //add 2 wish-list (related items list)
  {
    $_GET["select_product"] = (int)$_GET["select_product"];
      if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON

      {
          Redirect(ADMIN_FILE."?do=wishlist&safemode=yes&owner=".$owner);
      }

      $q = db_query("select count(*) FROM ".RELATED_PRODUCTS_TABLE." WHERE productID=".$_GET["select_product"]." AND Owner=".$owner);
      $cnt = db_fetch_row($q);
      if ($cnt[0] == 0) // insert
               db_query("INSERT INTO ".RELATED_PRODUCTS_TABLE." (productID, Owner) VALUES ('".$_GET["select_product"]."', '".$owner."')");
  }

  if (isset($_GET["delete"])) //remove from wish-list

  {
      if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON

      {
          Redirect(ADMIN_FILE."?do=wishlist&safemode=yes&owner=".$owner);
      }
      db_query("DELETE FROM ".RELATED_PRODUCTS_TABLE." WHERE productID=".(int)$_GET["delete"]." AND Owner=".$owner);
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel=STYLESHEET href="data/admin/style.css" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=<?php
  echo DEFAULT_CHARSET;
?>">
<title><?php
  echo STRING_RELATED_ITEMS;
?></title>
</head>
<body>

<?php
  if (isset($_GET["safemode"]))
  {
      echo "<table class=\"adminw\"><tr><td align=\"left\"><table class=\"adn\"><tr>
          <td><img src=\"data/admin/stop2.gif\" align=\"left\" class=\"stop\"></td>
          <td class=\"splin\"><span class=\"error\">".ERROR_MODULE_ACCESS2."</span><br><br>".ERROR_MODULE_ACCESS_DES2."</td>
          </tr></table></td></tr></table>\n";

  }
?>
<table class="adn">
<tr class="lineb">
<td colspan="2" align="left"><?php
  echo STRING_RELATED_ITEMS;
?></td>
</tr>
<tr class="lineb">
<td align="left"><?php
  echo ADMIN_RELATED_PRODUCTS_SELECT;
?></td>
<td WIDTH="50%" align="left"><?php
  echo ADMIN_SELECTED_PRODUCTS;
?></td></tr>
<tr>
<td valign=top><div style="background: #FFFFFF; padding: 5px;">
<?php
  $out = catGetCategoryCompactCList($categoryID);

  //show categories tree
  for ($i = 0; $i < count($out); $i++)
  {
      if ($out[$i]["categoryID"] == 0) continue;

      echo "<table class=\"adn\"><tr><td class=\"l1\">";
      for ($j = 0; $j < $out[$i]["level"] - 1; $j++)
      {
          if ($j == $out[$i]["level"] - 2)
          {
              echo "<img src=\"data/admin/pm.gif\" alt=\"\">";
          }
          else
          {
              echo "<img src=\"data/admin/pmp.gif\" alt=\"\">";
          }
      }

      if ($out[$i]["categoryID"] == $categoryID) //no link on selected category

      {
          echo "<img src=\"data/admin/minus.gif\" alt=\"\"></td><td class=\"l2\">".$out[$i]["name"].
              "\n";
          showproducts($categoryID, $owner);
          echo "</td></tr></table>\n";
      }
      else //make a link

      {
          echo "<img src=\"data/admin/mplus.gif\" alt=\"\"></td><td class=\"l2\">";
          echo "<a href=\"".ADMIN_FILE."?do=wishlist&amp;owner=".$owner."&amp;categoryID=".$out[$i]["categoryID"]."\"";
          echo ">".$out[$i]["name"]."</a></td></tr></table>\n";
      }
  }
?>
</div>
</td>
<td valign=top><div style="background: #FFFFFF; padding: 5px;">
<?php
  $q = db_query("select productID FROM ".RELATED_PRODUCTS_TABLE." WHERE Owner=".$owner);
  while ($row = db_fetch_row($q))
  {
      $p = db_query("select name FROM ".PRODUCTS_TABLE." WHERE productID=".$row[0]);
      if ($r = db_fetch_row($p))
      {
          echo "<table class=\"adn\"><tr>";
          echo "<td class=\"l2\" style=\"font-size: 10px;\">".$r[0]."</td>";
          echo "<td class=\"l3\"><a href=\"".ADMIN_FILE."?do=wishlist&amp;owner=".$owner."&amp;categoryID=".$categoryID.
              "&amp;delete=".$row[0]."\">X</a></td></tr></table>";
      }
  }
?>
</div>
</td>
</tr>
</table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>
<div align="center"><a href="#" onClick="window.opener.document.getElementById('MainForm').submit(); window.close(); return false" class="inl"><?php
  echo SAVE_BUTTON;
?></a></div>
<table class="adn"><tr><td class="se5"></td></tr></table>
</body>
</html>