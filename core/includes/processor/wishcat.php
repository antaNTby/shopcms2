<?php
  #####################################
  # ShopCMS: Скрипт интернет-магазина
  # Copyright (c) by ADGroup
  # http://shopcms.ru
  #####################################

  $relaccess = checklogin();

  if (CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(16, $relaccess))) //unauthorized

  {
      die(ERROR_FORBIDDEN);
  }

  if (!isset($_GET["owner"])) //'owner product' not set

  {
      echo "<center><font color=red>".ERROR_CANT_FIND_REQUIRED_PAGE."</font>\n<br><br>\n";
      echo "<a href=\"javascript:window.close();\">".CLOSE_BUTTON."</a></center></body>\n</html>";
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
          Redirect(ADMIN_FILE."?do=wishcat&safemode=yes&owner=".$owner);
      }

      $q = db_query("select count(*) FROM ".RELATED_CONTENT_CAT_TABLE." WHERE categoryID=".$_GET["select_product"]." AND Owner=".$owner);
      $cnt = db_fetch_row($q);
      if ($cnt[0] == 0) // insert
               db_query("INSERT INTO ".RELATED_CONTENT_CAT_TABLE." (categoryID, Owner) VALUES ('".$_GET["select_product"]."', '".$owner."')");
  }

  if (isset($_GET["delete"])) //remove from wish-list

  {
      if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON

      {
          Redirect(ADMIN_FILE."?do=wishcat&safemode=yes&owner=".$owner);
      }
      db_query("DELETE FROM ".RELATED_CONTENT_CAT_TABLE." WHERE categoryID=".(int)$_GET["delete"]." AND Owner=".$owner);
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
  echo STRING_CAT_TITLE_W;
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
  echo STRING_CAT_TITLE_W;
?></td>
</tr>
<tr class="lineb">
<td width="50%" align="left"><?php
  echo ADMIN_RELATED_CAT_SELECT;
?></td>
<td width="50%" align="left"><?php
  echo ADMIN_SELECTED_CATS;
?></td>
</tr>
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
          echo "<img src=\"data/admin/dr.gif\" alt=\"\"></td><td class=\"l2\"><b>".$out[$i]["name"].
              "</b>\n";
          echo "<br><br><a href=\"".ADMIN_FILE."?do=wishcat&amp;owner=".$owner."&amp;categoryID=".$categoryID."&amp;select_product=".$categoryID."\" class=\"inl\">".
              ADMIN_GO_TOCAT." &gt;&gt;</a>\n";
          echo "<br><br></td></tr></table>\n";
      }
      else //make a link

      {
          echo "<a href=\"".ADMIN_FILE."?do=wishcat&amp;owner=".$owner."&amp;categoryID=".$out[$i]["categoryID"]."\"";
          echo "><img src=\"data/admin/mplus.gif\" alt=\"\"></a></td><td class=\"l2\">";
          echo "<a href=\"".ADMIN_FILE."?do=wishcat&amp;owner=".$owner."&amp;categoryID=".$out[$i]["categoryID"]."\"";
          echo ">".$out[$i]["name"]."</a></td></tr></table>\n";
      }
  }
?>
</div>
</td>
<td valign=top><div style="background: #FFFFFF; padding: 5px;">
<?php
  $q = db_query("select categoryID FROM ".RELATED_CONTENT_CAT_TABLE." WHERE Owner=".$owner);
  while ($row = db_fetch_row($q))
  {
      $p = db_query("select name FROM ".CATEGORIES_TABLE." WHERE categoryID=".$row[0]);
      if ($r = db_fetch_row($p))
      {
          echo "<table class=\"adn\"><tr>";
          echo "<td class=\"l2\">".$r[0]."</td>";
          echo "<td class=\"l3\"><a href=\"".ADMIN_FILE."?do=wishcat&amp;owner=".$owner."&amp;categoryID=".$categoryID."&amp;delete=".$row[0]."\">X</a></td></tr></table>";
      }
  }
?>
</div>
</td>
</tr>
</table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>
<div align="center"><a href="#" onClick="window.close();" class="inl"><?php
  echo SAVE_BUTTON;
?></a></div>
<table class="adn"><tr><td class="se5"></td></tr></table>
</body>
</html>