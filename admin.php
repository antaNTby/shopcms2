<?php
  #####################################
  # ShopCMS: Скрипт интернет-магазина
  # Copyright (c) by ADGroup
  # http://shopcms.ru
  #####################################

  define('ERROR_DB_INIT', 'Database connection problem!');

  include("core/config/init.php");
  include("core/config/tables.inc.php");

  $far_1 = array(
  "core/config/language_list.php"
  ,"core/config/paths.inc.php"
  ,"core/classes/class.virtual.shippingratecalculator.php"
  ,"core/classes/class.virtual.paymentmodule.php"
  ,"core/classes/class.xmlnodex.php");

  $far_2 = glob("core/functions/*.php"); 
  $far_3 = glob("core/functions/admin/*.php");
  $far = array_merge($far_1,$far_2,$far_3);

  include("core/config/connect.inc.php");
  $cfar = count($far);
  $afcache_filename = "core/cache/{$host}/afcache.php";
  if(file_exists($afcache_filename)) include ($afcache_filename);
  else for ($n=0; $n<$cfar; $n++) include ($far[$n]);

    $DbCharset = $AdminHtmlCharset;
  include("core/includes/database/mysql.php");

  define('PATH_DELIMITER', isWindows()?';':':');
  
  $_POST = xStripSlashesGPC($_POST);
  $_GET = xStripSlashesGPC($_GET);
  $_COOKIE = xStripSlashesGPC($_COOKIE);
  
  db_connect(DB_HOST, DB_USER, DB_PASS) or die(ERROR_DB_INIT);
    mysql_query("set names {$AdminDbCharset}");
  db_select_db(DB_NAME) or die(db_error());
  /*mysql_query('Set character_set_connection=cp1251');
  mysql_query('Set character_set_results=cp1251');
  mysql_query('Set character_set_client=cp1251');*/

  settingDefineConstants();

  if ((int)CONF_SMARTY_FORCE_COMPILE)
  {
      if(file_exists($afcache_filename)) unlink ($afcache_filename);
  }else{
          ob_start();
          for ($n=0; $n<$cfar; $n++) readfile ($far[$n]);
          $_res = ob_get_contents();
          ob_end_clean();
          $fh = fopen($afcache_filename, 'w');
          fwrite($fh, $_res);
          fclose($fh);
          unset($_res);
  }

  include ("core/config/headers.php");
  //header ("Content-Type: text/html; charset={$AdminHtmlCharset}");

  include ("core/config/error_handler.php");
  
  function set_cookie($Name, $Value = '', $Expires = '', $Secure = false, $Path = '', $Domain = '', $HTTPOnly = false) {
    header('Set-Cookie: '  . rawurlencode($Name) . '=' . rawurlencode($Value)
                          . (empty($Expires) ? '' : '; expires=' . gmdate('D, d-M-Y H:i:s', $Expires) . ' GMT')
                          . (empty($Path) ? '' : '; path=' . $Path)
                          . (empty($Domain) ? '' : '; domain=' . $Domain)
                          . (!$Secure ? '' : '; secure')
                          . (!$HTTPOnly ? '' : '; HttpOnly'), false);
  }

  # стартуем сессию
  define("SECURITY_EXPIRE", 60 * 60 * CONF_SECURITY_EXPIRE);
  session_set_save_handler("sess_open", "sess_close", "sess_read", "sess_write", "sess_destroy", "sess_gc");
  session_start();

  # посылаем cookie сессии
  if (isset ($_COOKIE["PHPSESSID"])){
    if(SECURITY_EXPIRE > 0){
      set_cookie("PHPSESSID", $_COOKIE["PHPSESSID"], time() + SECURITY_EXPIRE);
    }else{
      set_cookie("PHPSESSID", $_COOKIE["PHPSESSID"]);
    }
  }


    //current language session variable
    //if (isset($_POST["lang"])) $_SESSION["current_language"] = $_POST["lang"];
    $AdminLangIndex = "admin_current_language";

    //current language session variable
    if (!isset($_SESSION[$AdminLangIndex])) $_SESSION[$AdminLangIndex] = $AdminLang;
    if ((!isset($lang_list[$_SESSION[$AdminLangIndex] ]))) $_SESSION[$AdminLangIndex] = key($lang_list); //set default language

    //include a language file
    if (isset($lang_list[$_SESSION[$AdminLangIndex]]) && file_exists("core/languages/".$lang_list[$_SESSION[$AdminLangIndex]]->
            filename))
    {
        //include current language file
        include ("core/languages/".$lang_list[$_SESSION[$AdminLangIndex]]->filename);
    }
    else
    {
        die("<font color=red><b>ERROR: Couldn't find language file!</b></font>");
    }

if ( isset ( $_GET["do"] )) {
    if ( in_array($_GET["do"], array( "invoice", "configurator", "wishcat", "wishlist", "wishprod", "get_file" ))) {
        include ( "core/includes/processor/".$_GET["do"].".php" );
    }
    else {
        header("HTTP/1.0 404 Not Found");
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        die(ERROR_404_HTML);
    }
  } else {  

  $relaccess = checklogin();

  if (CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(100, $relaccess)))
  {

      if (isset($_POST['user_login']) && isset($_POST['user_pw']))
      {

          if (regAuthenticate($_POST['user_login'], $_POST['user_pw'])) Redirect(set_query('&__tt='));
          die(ERROR_FORBIDDEN);
      }

      die(ERROR_FORBIDDEN);
  }
  
  if (isset($_GET['categoryID'])) {
	$cat_id = $_GET['categoryID'];
	} else if (isset($_POST['categoryID'])) {
		$cat_id = $_POST['categoryID'];
	} else {
		if (isset($_GET['productID'])) {
			$sql = mysql_query("SELECT categoryID FROM ".PRODUCTS_TABLE." WHERE productID=".(int)$_GET['productID']);
			$cat_count = mysql_num_rows($sql);
			if ($cat_count > 0) {
				$row = mysql_fetch_assoc($sql);
				$cat_id = $row['categoryID'];
			}
		} else if (isset($_POST['productID'])) {
			$sql = mysql_query("SELECT categoryID FROM ".PRODUCTS_TABLE." WHERE productID=".(int)$_POST['productID']);
			$cat_count = mysql_num_rows($sql);
			if ($cat_count > 0) {
				$row = mysql_fetch_assoc($sql);
				$cat_id = $row['categoryID'];
			}
		} else {
			$cat_id = 1;
		}
	}

  $eaction = isset($_REQUEST['eaction']) ? $_REQUEST['eaction'] : '';
  switch ($eaction)
  {
      case 'cat':

          if (isset($_SESSION["log"])) $admintempname = $_SESSION["log"];
          //get new orders count
          $q = db_query("select count(*) from ".ORDERS_TABLE." WHERE statusID=".(int)CONF_NEW_ORDER_STATUS);
          $n = db_fetch_row($q);
          $new_orders_count = $n[0];

          $past = time() - CONF_ONLINE_EXPIRE * 60;
          $result = db_query("select count(*) from ".ONLINE_TABLE." WHERE time > '".xEscSQL($past)."'");
          $u = db_fetch_row($result);
          $online_users = $u[0];
/*
          $q = db_query("select categoryID, name, products_count, products_count_admin, parent, picture, subcount FROM ".CATEGORIES_TABLE." ORDER BY sort_order, name");
          $fc = array(); //parents
          $mc = array(); //parents
          while ($row = db_fetch_row($q)) {
                $fc[(int)$row["categoryID"]] = $row;
                $mc[(int)$row["categoryID"]] = (int)$row["parent"];
          }
*/
          if (isset($_POST) && count($_POST) > 0)
          {
              if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON

              {
                  if (!isset($_POST["must_delete"])) //adding a new category
                           Redirect(ADMIN_FILE."?safemode=yes&eaction=cat");
                  else //editing an existing category
                           Redirect(ADMIN_FILE."?safemode=yes&categoryID=".$_POST["must_delete"]."&eaction=cat");
              }
          }

          if (isset($_GET["picture_remove"])) //delete category thumbnail from server

          {
              if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON

              {
                  Redirect(ADMIN_FILE."?safemode=yes&categoryID=".$_GET["categoryID"]."&eaction=cat");
              }

              $q = db_query("select picture FROM ".CATEGORIES_TABLE." WHERE categoryID=".(int)$_GET["categoryID"]);
              $r = db_fetch_row($q);
              if ($r[0] && file_exists("data/category/".$r[0])) unlink("data/category/".$r[0]);
              db_query("update ".CATEGORIES_TABLE." SET picture='' WHERE categoryID=".(int)$_GET["categoryID"]);
          }

          if (isset($_GET["categoryID"]) && isset($_GET["del"])) //delete category

          {
              if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON

              {
                  Redirect(ADMIN_FILE."?safemode=yes&categoryID=".$_GET["categoryID"]."&eaction=cat");
              }

              catDeleteCategory($_GET["categoryID"]);
              if (CONF_UPDATE_GCV == 1)  update_psCount(1);
              Redirect(ADMIN_FILE."?dpt=catalog&sub=products_categories&categoryID=1");
          }


          if (isset($_POST["save"]) && $_POST["name"])
          { //save changes

              $allow_products_comparison = isset($_POST["allow_products_comparison"]) ? 1 : 0;
              $allow_products_search = isset($_POST["allow_products_search"]) ? 1 : 0;
              $show_subcategories_products = isset($_POST["show_subcategories_products"]) ? 1 : 0;

              if (!isset($_POST["must_delete"])) //add new category

              {
                  $q = db_query("insert into ".CATEGORIES_TABLE." (name, parent, products_count, description, picture, ".
                       " products_count_admin, sort_order, allow_products_comparison, allow_products_search, show_subcategories_products, ".
                       " meta_description, meta_keywords, title ) "." VALUES ('".xToText(trim($_POST["name"]))."', ".
                       (int)$_POST["parent"].",0,'".xEscSQL($_POST["desc"])."','',0, ".(int)$_POST["sort_order"].", ".$allow_products_comparison.", ".$allow_products_search.", ".
                       $show_subcategories_products.", '".xToText(trim($_POST["meta_d"]))."', '".xToText(trim($_POST["meta_k"]))."', '".xToText(trim($_POST["title"]))."');");
                  $pid = db_insert_id("CATEGORIES_GEN");
              }
              else //update existing category

              {
                  if (isset($_POST["removeto"]))
                  {
                      if ($_POST["removeto"] != "zero")
                      {
                          db_query("update ".PRODUCTS_TABLE." SET categoryID=".(int)$_POST["removeto"]." WHERE categoryID=".(int)$_POST["must_delete"]);
                      }
                  }

                  if ($_POST["must_delete"] != $_POST["parent"]) //if not moving category to itself

                  {

                      //if category is being moved to any of it's subcategories - it's
                      //neccessary to 'lift up' all it's subcategories

                      if (category_Moves_To_Its_SubDirectories($_POST["must_delete"], $_POST["parent"]))
                      {
                          //lift up is required

                          //get parent
                          $q = db_query("select parent FROM ".CATEGORIES_TABLE." WHERE categoryID=".(int)$_POST["must_delete"]);
                          $r = db_fetch_row($q);

                          //lift up
                          db_query("update ".CATEGORIES_TABLE." SET parent=".(int)$r[0]." WHERE parent=".(int)$_POST["must_delete"]);

                          //move edited category
                          db_query("update ".CATEGORIES_TABLE." SET name='".xToText(trim($_POST["name"]))."', description='".xEscSQL($_POST["desc"])."', parent=".(int)$_POST["parent"].
                              ", sort_order = ".(int)$_POST["sort_order"].", allow_products_comparison=".$allow_products_comparison." ".
                              ", allow_products_search=".$allow_products_search." ".", show_subcategories_products=".$show_subcategories_products." ".
                              ", meta_description='".xToText(trim($_POST["meta_d"]))."', meta_keywords='".xToText(trim($_POST["meta_k"])).
                              "', title='".xToText(trim($_POST["title"]))."'  WHERE categoryID=".(int)$_POST["must_delete"]);
                      }
                      else //just move category
                               db_query("update ".CATEGORIES_TABLE." SET name='".xToText(trim($_POST["name"]))."', description='".xEscSQL($_POST["desc"])."', parent=".
                              (int)$_POST["parent"].", sort_order = ".(int)$_POST["sort_order"].", allow_products_comparison=".$allow_products_comparison." ".
                              ", allow_products_search=".$allow_products_search." ".", show_subcategories_products=".$show_subcategories_products." ".
                              ", meta_description='".xToText(trim($_POST["meta_d"]))."', meta_keywords='".xToText(trim($_POST["meta_k"])).
                              "', title='".xToText(trim($_POST["title"]))."' WHERE categoryID=".(int)$_POST["must_delete"]);
                  }
                  $pid = (int)$_POST["must_delete"];



              }

              if (CONF_UPDATE_GCV == 1)  update_psCount(1);

              // update serarch option settings
              $categoryID = (int)$pid;
              schUnSetOptionsToSearch($categoryID);
              $data = ScanPostVariableWithId(array("checkbox_param"));
              foreach ($data as $optionID => $val)
              {
                  schUnSetVariantsToSearch($categoryID, $optionID);
                  if (isset($_POST["select_arbitrarily_".$optionID])) $set_arbitrarily = $_POST["select_arbitrarily_".$optionID];
                  else  $set_arbitrarily = 1;
                  schSetOptionToSearch($categoryID, $optionID, $set_arbitrarily);
                  if ($set_arbitrarily == 0)
                  {
                      $variants = optGetOptionValues($optionID);
                      foreach ($variants as $var)
                          if (isset($_POST["checkbox_variant_".$var["variantID"]])) schSetVariantToSearch($categoryID, $optionID, $var["variantID"]);
                  }
              }

              if (isset($_FILES["picture"]) && $_FILES["picture"]["name"] && $_FILES["picture"]["size"] >
                  0) //upload category thumbnail

              {

                  //old picture
                  $q = db_query("select picture FROM ".CATEGORIES_TABLE." WHERE categoryID=".(int)$pid);
                  $row = db_fetch_row($q);

                  //upload new photo
                  $picture_name = str_replace(" ", "_", $_FILES["picture"]["name"]);
                  $lastdot = strrpos($picture_name, ".");
                  $ext = substr($picture_name, ($lastdot + 1));
                  $filename = substr(time(), -5, 5);
                  $picture_name = $filename.".".$ext;
                  if ($row[0] && file_exists("data/category/".$row[0])) unlink("data/category/".$row[0]);

                  if (file_exists("data/category/".$picture_name))
                  {
                      $taskDone2 = false;
                      for ($i = 1; (($i < 200) && ($taskDone2 == false)); $i++)
                      {
                          if (!file_exists("data/category/".$filename."_".$i.".".$ext))
                          {
                              if (is_uploaded_file($_FILES['picture']['tmp_name']))
                              {
                                  if (move_uploaded_file($_FILES['picture']['tmp_name'], "data/category/".
                                      $filename."_".$i.".".$ext))
                                  {
                                      SetRightsToUploadedFile("data/category/".$filename."_".$i.".".$ext);
                                      db_query("update ".CATEGORIES_TABLE." SET picture='".xEscSQL($filename.
                                          "_".$i.".".$ext)."' "." WHERE categoryID=".(int)$pid);
                                  }
                                  else
                                  {
                                      echo "<div align=\"center\"><span style=\"color: #BB0000\">".ERROR_FAILED_TO_UPLOAD_FILE.
                                          "</span></div>\n";
                                      exit;
                                  }
                              }
                              else
                              {

                                  echo "<div align=\"center\"><span style=\"color: #BB0000\">".ERROR_FAILED_TO_UPLOAD_FILE.
                                      "</span></div>\n";
                                  exit;
                              }
                              $taskDone2 = true;
                          }
                      }

                  }
                  else
                  {

                      if (is_uploaded_file($_FILES['picture']['tmp_name']))
                      {
                          if (move_uploaded_file($_FILES['picture']['tmp_name'], "data/category/".$picture_name))
                          {
                              SetRightsToUploadedFile("data/category/".$picture_name);

                              db_query("update ".CATEGORIES_TABLE." SET picture='".xEscSQL($picture_name)."' ".
                                  " WHERE categoryID=".(int)$pid);
                          }
                          else
                          {
                              echo "<div align=\"center\"><span style=\"color: #BB0000\">".ERROR_FAILED_TO_UPLOAD_FILE.
                                  "</span></div>\n";
                              exit;
                          }
                      }
                      else
                      {
                          echo "<div align=\"center\"><span style=\"color: #BB0000\">".ERROR_FAILED_TO_UPLOAD_FILE.
                              "</span></div>\n";
                          exit;
                      }

                  }

                  //remove old picture...

                  Redirect(ADMIN_FILE."?categoryID=".$pid."&eaction=cat");

              }
              Redirect(ADMIN_FILE."?dpt=catalog&sub=products_categories&categoryID=".$pid."&expandCat=".$pid);

          }
          else //category edition from

          {

              if (isset($_GET["categoryID"])) //edit existing category

              {

                  $row = catGetCategoryById($_GET["categoryID"]);

                  if (!$row) //can't find category....

                  {
                      echo "<center><font color=BB0000>".ERROR_CANT_FIND_REQUIRED_PAGE."</font>\n<br><br>\n";
                      echo "</center>";
                      exit;
                  }
                  $title = ADMIN_CAT_EDITN;
                  $n = $row["name"];
                  $t = $row["title"];
                  $d = html_spchars($row["description"]);
                  $meta_d = $row["meta_description"];
                  $meta_k = $row["meta_keywords"];
                  $picture = $row["picture"];
                  $sort_order = $row["sort_order"];
                  $parent = $row["parent"];
                  $allow_products_comparison = $row["allow_products_comparison"];
                  $allow_products_search = $row["allow_products_search"];
                  $show_subcategories_products = $row["show_subcategories_products"];

              }
              else //create new

              {
                  $title = ADMIN_CATEGORY_NEW;
                  $n = "";
                  $d = "";
                  $t = "";
                  $meta_d = "";
                  $meta_k = "";
                  $picture = "";
                  $sort_order = 0;
                  $allow_products_comparison = 1;
                  $allow_products_search = 1;
                  if (isset($_GET["catslct"])) $parent = (int)$_GET["catslct"];
                  else  $parent = 1;
                  $show_subcategories_products = 1;
              }

              $options = _getOptions();

              $showSelectParametrsTable = 0;
              if (isset($_GET["SelectParametrsHideTable_hidden"])) $showSelectParametrsTable = $_GET["SelectParametrsHideTable_hidden"];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html class="admin">
<head>
<meta http-equiv="content-type" content="text/html; charset={$smarty.const.DEFAULT_CHARSET}">
<link rel="stylesheet" href="data/admin/style.css" type="text/css">
<link rel="icon" href="data/admin/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="data/admin/favicon.ico" type="image/x-icon">
<title><?php
              echo ADMIN_CATEGORY_TITLE;
?></title>
<script type="text/javascript" src="data/admin/admin.js"></script>
</head>
<body class="ibody">
  <table class="adn">
    <tr>
      <td colspan="2">
        <table class="adn">
          <tr>
            <td class="head"><img src="data/admin/sep.gif" alt=""></td>
            <td class="head toph">&nbsp;&nbsp;<?php
              echo ADMIN_TMENU1;
?>: <b><?php
              if (CONF_BACKEND_SAFEMODE) echo "demo";
              else  echo $admintempname;
?></b></td>
            <td class="head"><img src="data/admin/sep2.gif" alt=""></td>
            <td class="head toph"><?php
              echo ADMIN_TMENU2;
?>: <b><?php
              echo $online_users;
?></b></td>
            <td class="head"><img src="data/admin/sep2.gif" alt=""></td>
            <td class="head last toph" width="100%"><a href="<?php echo ADMIN_FILE; ?>?order_search_type=SearchByStatusID&amp;checkbox_order_status_<?php
              echo CONF_NEW_ORDER_STATUS;
?>=1&amp;dpt=custord&amp;sub=new_orders&amp;search="><?php
              echo ADMIN_TMENU3;
?>: <b><?php
              echo $new_orders_count;
?></b></a></td>
            <td class="head">
              <table class="adw">
                <tr>
                <td class="head last toph"><a href="<?php echo ADMIN_FILE; ?>"><?php
              echo ADMINISTRATE_LINK;
?></a></td>
                  <td class="head"><img src="data/admin/sep2.gif" alt=""></td>
                  <td class="head last toph"><a href="index.php"><?php
              echo ADMIN_BACK_TO_SHOP;
?></a></td>
                  <td class="head"><img src="data/admin/sep2.gif" alt=""></td>
                  <td class="head last toph2 toph"><a href="index.php?logout=yes"><?php
              echo ADMIN_LOGOUT_LINK;
?></a></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td class="indexb1">
<table class="adn"><tr><td class="se"></td></tr></table>
<table width="186" class="adw" style="margin: auto;">
                               <tr>
                                   <td class="head2">
                                       <table class="adn">
                                           <tr>
                                               <td class="head4" onclick="menuresetit('menu1')"><?php
              echo ADMIN_CATALOG;
?></td>
                                               <td align="right" class="head7"><img src="data/admin/004.gif" alt="" onclick="menuresetit('menu1')" id="menu12" style="cursor: pointer;"></td>
                                           </tr>
                                       </table>
                                   </td>
                               </tr>
                               <tr id='menu13' style="display: none">
<td>
<div class="dvmenu">
<table class="adn"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=catalog&amp;sub=products_categories"><?php
              echo ADMIN_CATEGORIES_PRODUCTS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=catalog&amp;sub=dbsync"><?php
              echo ADMIN_SYNCHRONIZE_TOOLS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=catalog&amp;sub=extra"><?php
              echo ADMIN_PRODUCT_OPTIONS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=catalog&amp;sub=special"><?php
              echo ADMIN_SPECIAL_OFFERS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=catalog&amp;sub=excel_import"><?php
              echo ADMIN_IMPORT_FROM_EXCEL;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=catalog&amp;sub=excel_export"><?php
              echo ADMIN_EXPORT_TO_EXCEL;
?></a></td></tr></table>
</div></td>

                               </tr>
                               <tr>
                                   <td class="se"></td>
                               </tr>
                               <tr>
                                   <td class="head2">
                                       <table class="adn">
                                           <tr>
                                               <td class="head4" onclick="menuresetit('menu2')"><?php
              echo ADMIN_CUSTOMERS_AND_ORDERS;
?></td>
                                               <td align="right" class="head7"><img src="data/admin/004.gif" alt="" onclick="menuresetit('menu2')" id="menu22" style="cursor: pointer;"></td>
                                           </tr>
                                       </table>
                                   </td>
                               </tr>
                               <tr id='menu23' style="display: none">
<td>
<div class="dvmenu">
<table class="adn"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=new_orders"><?php
              echo ADMIN_NEW_ORDERS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=subscribers"><?php
              echo ADMIN_NEWS_SUBSCRIBERS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=order_statuses"><?php
              echo ADMIN_ORDER_STATUES;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=reg_fields"><?php
              echo ADMIN_CUSTOMER_REG_FIELDS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=discounts"><?php
              echo ADMIN_DISCOUNT_MENU;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=aux_pages"><?php
              echo ADMIN_TX7;
?></a></td></tr></table>
</div></td>
                               </tr>
                               <tr>
                                   <td class="se"></td>
                               </tr>
                               <tr>
                                   <td class="head2">
                                       <table class="adn">
                                           <tr>
                                               <td class="head4" onclick="menuresetit('menu3')"><?php
              echo ADMIN_SETTINGS;
?></td>
                                               <td align="right" class="head7"><img src="data/admin/004.gif" alt="" onclick="menuresetit('menu3')" id="menu32" style="cursor: pointer;"></td>
                                           </tr>
                                       </table>
                                   </td>
                               </tr>
                               <tr id='menu33' style="display: none">
<td>
<div class="dvmenu">
<table class="adn"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=setting&amp;settings_groupID=2"><?php
              echo ADMIN_SETTINGS_GENERAL;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=currencies"><?php
              echo ADMIN_CURRENCY_TYPES;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=shipping"><?php
              echo ADMIN_STRING_SHIPPING_TYPE;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=payment"><?php
              echo ADMIN_STRING_PAYMENT_TYPE;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=blocks_edit"><?php
              echo ADMIN_TX20;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=admin_edit"><?php
              echo ADMIN_CONF_ADMINS;
?></a></td></tr></table>
</div></td>
                               </tr>
                               <tr>
                                   <td class="se"></td>
                               </tr>
                               <tr>
                                   <td class="head2">
                                       <table class="adn">
                                           <tr>
                                               <td class="head4" onclick="menuresetit('menu4')"><?php
              echo ADMIN_MODULES;
?></td>
                                               <td align="right" class="head7"><img src="data/admin/004.gif" alt="" onclick="menuresetit('menu4')" id="menu42" style="cursor: pointer;"></td>
                                           </tr>
                                       </table>
                                   </td>
                               </tr>
                               <tr id='menu43' style="display: none">
<td>
<div class="dvmenu">
<table class="adn"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=modules&amp;sub=news"><?php
              echo ADMIN_NEWS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=modules&amp;sub=survey"><?php
              echo ADMIN_VOTING;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=modules&amp;sub=shipping"><?php
              echo ADMIN_STRING_SHIPPING_MODULES;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=modules&amp;sub=payment"><?php
              echo ADMIN_STRING_PAYMENT_MODULES;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=modules&amp;sub=linkexchange"><?php
              echo ADMIN_STRING_MODULES_LINKEXCHANGE;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=modules&amp;sub=yandex"><?php
              echo ADMIN_STRING_YANDEX;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=affiliate"><?php
echo STRING_AFFILIATE_PROGRAM; ?></a></td></tr></table>
</div></td>

                               </tr>
                               <tr>
                                   <td class="se"></td>
                               </tr>
                                   <tr>
                                       <td class="head2">
                                           <table class="adn">
                                               <tr>
                                                   <td class="head4" onclick="menuresetit('menu5')"><?php
              echo ADMIN_REPORTS;
?></td>
                                               <td align="right" class="head7"><img src="data/admin/004.gif" alt="" onclick="menuresetit('menu5')" id="menu52" style="cursor: pointer;"></td>
                                               </tr>
                                           </table>
                                       </td>
                                   </tr>
                                   <tr id='menu53' style="display: none">
<td>
<div class="dvmenu">
<table class="adn"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=reports&amp;sub=category_viewed_times"><?php
              echo ADMIN_CATEGORY_VIEWED_TIMES;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=reports&amp;sub=customer_log"><?php
              echo ADMIN_CUSTOMER_LOG;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=reports&amp;sub=information"><?php
              echo ADMIN_INFORMATION2;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=reports&amp;sub=coming"><?php
              echo ADMIN_COMING;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=reports&amp;sub=security"><?php
              echo ADMIN_SECURITY;
?></a></td></tr></table>
</div></td>
                                   </tr>
                               <tr>
                                   <td class="se"></td>
                               </tr>
                                <tr>
                                       <td class="head2">
                                           <table class="adn">
                                               <tr>
                                                   <td class="head4" onclick="menuresetit('menu6')"><?php
              echo ADMIN_LIST_ALL;
?></td>
                                               <td align="right" class="head7"><img src="data/admin/004.gif" alt="" onclick="menuresetit('menu6')" id="menu62" style="cursor: pointer;"></td>
                                               </tr>
                                           </table>
                                       </td>
                                   </tr>
                                   <tr id='menu63' style="display: none">
<td>
<div class="dvmenu">
<table class="adn"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?login=&amp;first_name=&amp;last_name=&amp;email=&amp;groupID=0&amp;fActState=-1&amp;dpt=custord&amp;sub=custlist&amp;search=Find"><?php
              echo ADMIN_CUSTOMERS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=custgroup"><?php
              echo ADMIN_CUSTGROUP;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=countries"><?php
              echo ADMIN_MENU_TOWNS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=zones"><?php
              echo ADMIN_MENU_TAXEZ;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=catalog&amp;sub=discuss"><?php
              echo ADMIN_DISCUSSIONS;
?></a></td></tr></table>
</div></td>
</tr>
</table>
<script type="text/javascript">
megamenu();
</script>
</td>
<td valign="top">
<table class="adn">
  <tr>
    <td class="zeb2 nbc">
      <table class="adn ggg">
         <tr>
           <td>
            <table class="adn">
              <tr>
              <td class="nbc2"><span class="titlecol"><?php
              echo $title;
?></span></td>
<td align="right" valign="middle" id="preproc"></td></tr>
              <tr>
               <td class="nbcl" colspan="2"></td>
              </tr>
            </table>
           </td>
         </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td valign="top" align="center" class="zeb">
      <table class="adn">
        <tr>
          <td align="left">
<?php
              if (isset($_GET["safemode"]))
              {
                  echo "<table class=\"adminw\"><tr><td align=\"left\"><table class=\"adn\"><tr><td><img src=\"data/admin/stop2.gif\" align=\"left\" class=\"stop\"></td><td class=\"splin\"><span class=\"error\">".
                      ERROR_MODULE_ACCESS2."</span><br><br>".ERROR_MODULE_ACCESS_DES2."</td></tr></table></td></tr></table>\n";

              }
?>
<form enctype="multipart/form-data" action="<?php echo ADMIN_FILE; ?>?eaction=cat"  method=post name='MainForm' id='MainForm'>
<table class="adn">
<tr class="lineb">
<td align="left"><?php
              echo ADMIN_SETTINGS_GENERAL_CAT;
?></td></tr>
<tr class="lins">
<td align="left"><?php
              echo STRING_CATEGORY;
?>: <select name="parent" id="parent" <?php
              if (!CONF_FULLY_EXPAND_CATEGORIES_IN_ADMIN_MODE) // update list

              {
                  echo " onChange=\"window.location='".ADMIN_FILE."?";
                  if (isset($_GET["categoryID"])) echo "categoryID=".$_GET["categoryID"]."&amp;";
                  echo "change_category='+document.getElementById('parent').value+'&amp;eaction=cat';\"";
              }
?>>
                                <!--<option value="1"><?php
              echo ADMIN_CATEGORY_ROOT;
?></option>-->
                                <?php
              if (CONF_FULLY_EXPAND_CATEGORIES_IN_ADMIN_MODE == 1) echo "<option value=\"1\">".ADMIN_CATEGORY_ROOT.
                      "</option>";

              //fill the category combobox
              $core_category = (isset($_GET["change_category"])) ? (int)$_GET["change_category"] : $parent;

              if (CONF_FULLY_EXPAND_CATEGORIES_IN_ADMIN_MODE == 0) $cats = catGetCategoryCompactCList($core_category);
              else  $cats = catGetCategoryCList($cat_id);

              for ($i = 0; $i < count($cats); $i++)
              {
                  echo "<option value=\"".$cats[$i]["categoryID"]."\"";
                  if ($core_category == $cats[$i]["categoryID"]) //select category
                           echo " selected";
                  echo ">";
                  for ($j = 0; $j < $cats[$i]["level"]; $j++) echo "&nbsp;&nbsp;";
                  echo $cats[$i]["name"];
                  echo "</option>";
              }
?>
</select></td></tr>
<tr class="lins"><td align="left"><?php
              echo ADMIN_CATEGORY_NAME;
?>: <input type="text" name="name" value="<?php
              echo str_replace("\"", "&quot;", $n);
?>" style="width: 400px;" class="textp">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php
              echo ADMIN_SORT_ORDER;
?>: <input type="text" name="sort_order" value="<?php
              echo $sort_order
?>" class="textp" size="10"></td></tr>
<tr class="lins"><td align="left"><?php
              echo ADMIN_PRODUCT_TITLE_PAGE;
?>: <input type="text" name="title" value="<?php
              echo str_replace("\"", "&quot;", $t);
?>" style="width: 590px;" class="textp"></td></tr>
<tr class="lins"><td align="left"><table class="adw houp"><tr><td align="center"><?php
              echo STRING_PICTURE_CAT_NEW;
?>: <input type="file" class="file" name="picture">&nbsp;&nbsp;&nbsp;</td><td><img src="data/admin/plus.gif" alt="" style="cursor: pointer" onclick="document.getElementById('MainForm').submit()"></td><td>&nbsp;&nbsp;&nbsp;<?php
              echo STRING_PICTURE_EXE;
?>:
<script type="text/javascript" src="data/admin/highslide.packed.js"></script>
<script type="text/javascript">
<!--
    hs.graphicsDir = 'data/admin/';
    hs.outlineType = 'rounded';

    hs.restoreTitle = '<?php
              echo STRING_HS_RESTORETITLE;
?>';
    hs.loadingText = '<?php
              echo STRING_HS_LOADINGTEXT;
?>';
    hs.loadingTitle = '<?php
              echo STRING_HS_LOADINGTITLE;
?>';
    hs.focusTitle = '<?php
              echo STRING_HS_FOCUSTITLE;
?>';
    hs.fullExpandTitle = '<?php
              echo STRING_HS_FULLEXPANDTITLE;
?>';

//-->
</script>
<?php
              if ($picture != "" && file_exists("data/category/".$picture))
              {
                  echo "<a href=\"data/category/".$picture."\" onclick=\"return hs.expand(this)\" class=\"inl\">".ADMIN_OPEN_IMAGE."</a>";
                  echo "&nbsp;&nbsp;&nbsp;<a href=\"#\" onclick=\"confirmDeletep('".QUESTION_DELETE_PICTURE.
                      "','".ADMIN_FILE."?categoryID=".$_GET["categoryID"]."&amp;picture_remove=yes&amp;eaction=cat'); return false\" class=\"inl\">".
                      DELETE_BUTTON."</a>\n";
              }
              else  echo ADMIN_PICTURE_NOT_UPLOADED;
?></td></tr></table></td></tr>
<tr class="lins"><td align="left"><?php
              echo ADMIN_ALLPROD_TO;
?>: <select name="removeto" id="removeto" <?php
              if (CONF_FULLY_EXPAND_CATEGORIES_IN_ADMIN_MODE == 0) // update list

              {
                  echo " onChange=\"window.location='".ADMIN_FILE."?";
                  if (isset($_GET["categoryID"])) echo "categoryID=".$_GET["categoryID"]."&amp;";
                  echo "removeto_category='+document.getElementById('removeto').value+'&amp;eaction=cat';\"";
              }
?>>
                                <!--<option value="1"><?php
              echo ADMIN_CATEGORY_ROOT;
?></option>-->
                                <?php
              echo "<option value=\"zero\">".ADMIN_NOT_DEFINED_REM."</option>";
              if (CONF_FULLY_EXPAND_CATEGORIES_IN_ADMIN_MODE == 1) echo "<option value=\"1\">".ADMIN_CATEGORY_ROOT.
                      "</option>";

              $zeroval = 0;

              $core_category = (isset($_GET["removeto_category"])) ? (int)$_GET["removeto_category"] :
                  $zeroval;

              if (CONF_FULLY_EXPAND_CATEGORIES_IN_ADMIN_MODE == 0) $cats = catGetCategoryCompactCList($core_category);
              else  $cats = catGetCategoryCList($cat_id);

              for ($i = 0; $i < count($cats); $i++)
              {
                  echo "<option value=\"".$cats[$i]["categoryID"]."\"";
                  if ($core_category == $cats[$i]["categoryID"]) //select category
                           echo " selected";
                  echo ">";
                  for ($j = 0; $j < $cats[$i]["level"]; $j++) echo "&nbsp;&nbsp;";
                  echo $cats[$i]["name"];
                  echo "</option>";
              }
?>
</select></td></tr></table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>
<a href="#" onclick="document.getElementById('MainForm').submit(); return false" class="inl"><?php
              echo SAVE_BUTTON;
?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a
<?php
              if (isset($_GET["categoryID"]))
              {
                  echo "href=\"".ADMIN_FILE."?dpt=catalog&amp;sub=products_categories&amp;categoryID=".$_GET["categoryID"].
                      "\"";
              }
              else
              {
                  if (isset($_GET["catslct"]))
                  {
                      echo "href=\"".ADMIN_FILE."?dpt=catalog&amp;sub=products_categories&amp;categoryID=".
                          $_GET["catslct"]."\"";
                  }
                  else
                  {
                      echo "href=\"".ADMIN_FILE."?dpt=catalog&amp;sub=products_categories&amp;categoryID=1\"";
                  }
              }
?>
 class="inl"><?php
              echo CANCEL_BUTTON;
?></a>
<?php
              //$must_delete indicated which query should be made: insert/update
              if (isset($_GET["categoryID"]))
              {
                  echo "&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"#\" class=\"inl\" onClick=\"confirmDeletep('".
                      QUESTION_DELETE_CONFIRMATION."','".ADMIN_FILE."?categoryID=".str_replace("\"", "", $_GET["categoryID"]).
                      "&amp;del=1&amp;eaction=cat'); return false\">".DELETE_BUTTON."</a>";
              }
?>
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adn">
<tr class="lineb">
<td align="left"><?php
              echo ADMIN_SETADD;
?></td></tr>
<tr class="lins"><td align="left">
<table class="adn houp">
<tr><td align="left"><input type="checkbox" name="allow_products_comparison" id="allow_products_comparison" value='1'
<?php
              if ($allow_products_comparison == 1)
              {
?>
checked
<?php
              }
?>
></td><td align="left" width="100%"><label for="allow_products_comparison"> &nbsp;<?php
              echo ADMIN_ALLOW_PRODUCTS_COMPARISON;
?></label></td></tr>
<tr><td align="left" valign="middle" nowrap><input type="checkbox" name="allow_products_search" id="allow_products_search"
value='1'
<?php
              if ($allow_products_search == 1)
              {
?>
checked
<?php
              }
?>
></td><td align="left"><label for="allow_products_search"> &nbsp;<?php
              echo ADMIN_ALLOW_SEARCH_IN_CATEGORY;
?></label></td></tr>
<tr><td align="left" valign="middle"><input type="checkbox" name='show_subcategories_products' id="show_subcategories_products" value='1'
<?php
              if ($show_subcategories_products == 1)
              {
?>
checked
<?php
              }
?>
></td><td align="left"><label for="show_subcategories_products"> &nbsp;<?php
              echo ADMIN_SHOW_PRODUCT_IN_SUBCATEGORY;
?></label></td></tr>
</table></td></tr></table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se6"></td></tr></table>
<table class="adn">
<tr class="lineb">
<td align="left"><?php
              echo STRING_ADVANCED_SEACH_TITLE;
?></td></tr>
<tr class="lins"><td align=left><a href="#" class="inl" onClick="SelectParametrsHideTable(); return false"><?php
              echo ADMIN_SELECT_PARAMETRS;
?></a>
<br><br><?php
              echo ADMIN_SELECT_PARAMETRS_PROMPT;
?></td></tr></table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se6"></td></tr></table>
<input type="hidden" name='SelectParametrsHideTable_hidden' id='SelectParametrsHideTable_hidden' value='<?php
              echo $showSelectParametrsTable;
?>'>
<script type="text/javascript">
function SelectParametrsHideTable()
{
var wspl = document.getElementById('SelectParametrsTable').style.display;
if ( wspl == 'none' )
{
document.getElementById('SelectParametrsTable').style.display = '';
document.getElementById('SelectParametrsHideTable_hidden').value = 1;
}
else
{
document.getElementById('SelectParametrsTable').style.display = 'none';
document.getElementById('SelectParametrsHideTable_hidden').value = 0;
}
}
</script>
<table class='adn' id='SelectParametrsTable'>
<tr class="lineb">
<td align="left"><?php
              echo ADMIN_PAR;
?></td></tr>
<tr><td>
<table class="adw" style="margin: 4px 5px;">
<?php
              if (count($options) >= 1)
              {
                  foreach ($options as $option)
                  {
?>
<tr><td align="center" valign="middle" class="cssel"><input type="checkbox"  name='checkbox_param_<?php
                      echo $option["optionID"];
?>' id='checkbox_param_<?php
                      echo $option["optionID"];
?>'
<?php
                      if ($option["isSet"])
                      {
?>
checked
<?php
                      }
?>
onclick='Checkbox_param_Change_<?php
                      echo $option["optionID"];
?>()'></td><td colspan="3" align="left" valign="middle" class="toph3"><?php
                      echo $option["name"];
?></td></tr>
<?php
                      if (count($option["variants"]) != 0)
                      {
?>
<tr><td></td>
<td align="center" valign="middle" class="cssel"><input type="radio" name='select_arbitrarily_<?php
                          echo $option["optionID"];
?>' id='select_arbitrarily1_<?php
                          echo $option["optionID"];
?>'
<?php
                          if ($option["set_arbitrarily"] == 1)
                          {
?>
checked
<?php
                          }
?>
value='1' onclick='Select_arbitrarily_Change_<?php
                          echo $option["optionID"];
?>()'></td><td colspan="2" align="left" valign="middle" class="toph3"><?php
                          echo ADMIN_SEARCH_IN_CATEGORY_PARAMETR_VALUE_ARBITRARILY;
?></td>
</tr>
<tr><td></td><td align="center" valign="middle" class="cssel"><input type="radio" name='select_arbitrarily_<?php
                          echo $option["optionID"];
?>' id='select_arbitrarily2_<?php
                          echo $option["optionID"];
?>'
<?php
                          if ($option["set_arbitrarily"] == 0)
                          {
?>
checked
<?php
                          }
?>
value='0' onclick='Select_arbitrarily_Change_<?php
                          echo $option["optionID"];
?>()'></td><td colspan="2" class="toph3" align="left" valign="middle"><?php
                          echo ADMIN_SEARCH_IN_CATEGORY_PARAMETR_VALUE_SELECT_FROM_VALUES;
?></td>
</tr>
<?php
                          foreach ($option["variants"] as $variant)
                          {
?>
<tr>
<td></td>
<td></td>
<td align="center" valign="middle" class="cssel"><input type="checkbox" name='checkbox_variant_<?php
                              echo $variant["variantID"];
?>' id='checkbox_variant_<?php
                              echo $variant["variantID"];
?>'
<?php
                              if ($variant["isSet"])
                              {
?>
checked
<?php
                              }
?>
></td><td class="toph3" align="left" valign="middle" width="100%"><?php
                              echo $variant["option_value"];
?></td></tr>
<?php
                          }
                      }
?><tr><td>
<script type="text/javascript">
function Checkbox_param_Change_<?php
                      echo $option["optionID"];
?>()
{
_checked = document.getElementById('checkbox_param_<?php
                      echo $option["optionID"];
?>').checked;
<?php
                      if (count($option["variants"]) != 0)
                      {
?>
document.getElementById('select_arbitrarily1_<?php
                          echo $option["optionID"];
?>').disabled =!_checked;
document.getElementById('select_arbitrarily2_<?php
                          echo $option["optionID"];
?>').disabled =!_checked;
<?php
                      }
?>
Select_arbitrarily_Change_<?php
                      echo $option["optionID"];
?>();
}
function Select_arbitrarily_Change_<?php
                      echo $option["optionID"];
?>()
{
<?php
                      if (count($option["variants"]) != 0)
                      {
?>
_enabled = document.getElementById('select_arbitrarily2_<?php
                          echo $option["optionID"];
?>').checked && document.getElementById('checkbox_param_<?php
                          echo $option["optionID"];
?>').checked;
<?php
                      }
?>
<?php
                      foreach ($option["variants"] as $variant)
                      {
?>
document.getElementById('checkbox_variant_<?php
                          echo $variant["variantID"];
?>').disabled = !_enabled;
<?php
                      }
?>
}
Checkbox_param_Change_<?php
                      echo $option["optionID"];
?>();
</script></td></tr>
<?php
                  }
              }
              else
              {
                  echo "<tr><td align=\"center\" height=\"20\">".ADMIN_NO_VALUES_CH."</td></tr>";
              }
?>
</table><table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se6"></td></tr></table></td></tr></table>

<script type="text/javascript">
<?php
              if (!$showSelectParametrsTable)
              {
?>
document.getElementById('SelectParametrsTable').style.display = 'none';
<?php
              }
?>
</script>
<table class="adn">
<tr class="linsz">
<td align="left"><span class="titlecol2"><?php
              echo STRING_DESCRIPTION;
?></span></td>
</tr>
<tr><td align="left"><textarea name="desc" class="admin" id="myarea1"><?php
              echo $d;
?></textarea></td></tr>
</table>
<?php
              if (CONF_EDITOR)
              {
?>
<script type="text/javascript" src="fckeditor/fckeditor.js"></script>
<script type="text/javascript" src="fckeditor/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
window.onload = function()
{
var oFCKeditor = new FCKeditor( 'myarea1',720,346) ;
<?php
                  $dir1 = dirname($_SERVER['PHP_SELF']);
                  $sourcessrand = array("//" => "/", "\\" => "/");
                  $dir1 = strtr($dir1, $sourcessrand);
                  if ($dir1 != "/") $dir2 = "/";
                  else  $dir2 = "";
?>
oFCKeditor.BasePath = "<?php
                  echo $dir1.$dir2;
?>fckeditor/" ;
oFCKeditor.ReplaceTextarea() ;
}
</script>
<?php
              }
?>
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adw">
<tr><td width="50%">
<table class="adn">
<tr class="linsz">
<td align="left"><span class="titlecol2"><?php
              echo ADMIN_PRODUCT_DESC2;
?></span></td>
</tr>
<tr><td align="left"><textarea name='meta_d' id="meta_d" class="adminall" style="margin-right: 38px;"><?php
              echo $meta_d;
?></textarea></td></tr>
</table>
</td>
<td width="50%">
<table class="adn">
<tr class="linsz">
<td align="left"><span class="titlecol2"><?php
              echo ADMIN_PRODUCT_DESC3;
?></span></td>
</tr>
<tr><td align="left"><textarea name='meta_k' id="meta_k" class="adminall"><?php
              echo $meta_k;
?></textarea></td></tr>
</table>
</td></tr></table>
<table class="adn"><tr><td class="se5"></td></tr></table>
<a href="#" onclick="document.getElementById('MainForm').submit(); return false" class="inl"><?php
              echo SAVE_BUTTON;
?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a
<?php
              if (isset($_GET["categoryID"]))
              {
                  echo "href=\"".ADMIN_FILE."?dpt=catalog&amp;sub=products_categories&amp;categoryID=".$_GET["categoryID"].
                      "\"";
              }
              else
              {
                  echo "href=\"".ADMIN_FILE."?dpt=catalog&amp;sub=products_categories&amp;categoryID=1\"";
              }
?>
 class="inl"><?php
              echo CANCEL_BUTTON;
?></a>
<?php
              //$must_delete indicated which query should be made: insert/update
              if (isset($_GET["categoryID"]))
              {
                  echo "<input type=\"hidden\" name=\"must_delete\" value=\"".str_replace("\"", "", $_GET["categoryID"]).
                      "\">";
                  echo "&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"#\" class=\"inl\" onClick=\"confirmDeletep('".
                      QUESTION_DELETE_CONFIRMATION."','".ADMIN_FILE."?categoryID=".str_replace("\"", "", $_GET["categoryID"]).
                      "&amp;del=1&amp;eaction=cat'); return false\">".DELETE_BUTTON."</a>";
              }
?><input type="hidden" name="save" value="yes"></form></td></tr></table>
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adn"><tr><td class="help"><span class="titlecol2"><?php
              echo USEFUL_FOR_YOU;
?></span><div class="helptext"><?php
              echo NOTE31;
?><br><br><?php
              echo NOTE34;
?></div></td>
</tr>
</table>
<table class="adn"><tr><td class="se8"></td></tr></table>
</td></tr></table></td></tr></table>
</body>
</html>
<?php
          }
          ;


          break;
      case 'prod':

          @set_time_limit(0);

          //current language

          if (isset($_SESSION["log"])) $admintempname = $_SESSION["log"];
          //get new orders count
          $q = db_query("select count(*) from ".ORDERS_TABLE." WHERE statusID=".(int)CONF_NEW_ORDER_STATUS);
          $n = db_fetch_row($q);
          $new_orders_count = $n[0];

          $past = time() - CONF_ONLINE_EXPIRE * 60;
          $result = db_query("select count(*) from ".ONLINE_TABLE." WHERE time > '".xEscSQL($past)."'");
          $u = db_fetch_row($result);
          $online_users = $u[0];

          $q = db_query("select categoryID, name, products_count, products_count_admin, parent, picture, subcount FROM ".CATEGORIES_TABLE." ORDER BY sort_order, name");
          $fc = array(); //parents
          $mc = array(); //parents
          while ($row = db_fetch_row($q)) {
                $fc[(int)$row["categoryID"]] = $row;
                $mc[(int)$row["categoryID"]] = (int)$row["parent"];
          }

          if (isset($_GET["stepback"]))
          {
              if (isset($_GET["productID"]) && $_GET["productID"] != "" && $_GET["productID"] != 0)
              {
                  $stepmb = $_GET["productID"];
                  $q = db_query("select categoryID from ".PRODUCTS_TABLE." where productID=".(int)$stepmb);
                  $r = db_fetch_row($q);
                  $categoryID = $r["categoryID"];
              }
              else
              {
                  if (isset($_GET["categoryID"]))
                  {
                      $categoryID = $_GET["categoryID"];
                  }
                  else
                  {
                      $categoryID = 1;
                  }
              }
              Redirect(ADMIN_FILE."?dpt=catalog&sub=products_categories&categoryID=".$categoryID."&expandCat=".$categoryID);
          }

          $codep = 0;
          if (isset($_GET["categoryID"]))
          {
              $codep = 1;
          }

          // Inputs
          // Remarks
          // Returns


          if (isset($_GET["delete"]))
          {
              if (isset($_GET["productID"]))
              {
                  $mpost = $_GET["productID"];
                  $q = db_query("select categoryID from ".PRODUCTS_TABLE." where productID=".(int)$mpost);
                  $r = db_fetch_row($q);
                  $categoryID = $r["categoryID"];
              }
              if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON

              {
                  Redirect(ADMIN_FILE."?safemode=yes&productID=".$_GET["productID"]."&eaction=prod");
              }
              DeleteProduct($_GET["productID"]);
              Redirect(ADMIN_FILE."?dpt=catalog&sub=products_categories&categoryID=".$categoryID."&expandCat=".$categoryID);
          }

          if (!isset($_GET["productID"])) $_GET["productID"] = 0;
          $productID = $_GET["productID"];

          if (!isset($_POST["eproduct_available_days"])) $_POST["eproduct_available_days"] = 7;
          if (!isset($_POST["eproduct_download_times"])) $_POST["eproduct_download_times"] = 5;

          if (isset($_POST["eproduct_download_times"])) $_POST["eproduct_download_times"] = (int)$_POST["eproduct_download_times"];

          // save product
          if (isset($_POST["save_product"]) || isset($_POST["save_product_for_file"]))
          {
              if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON

              {
                  Redirect(ADMIN_FILE."?safemode=yes&productID=".$_GET["productID"]."&eaction=prod");
              }
              if ($_POST["save_product"] == 1 || $_POST["save_product_for_file"] == 1)
              {
                  if ($_GET["productID"] == 0)
                  {
                      $productID = AddProduct($_POST["categoryID"], $_POST["name"], $_POST["price"],
                          $_POST["description"], $_POST["in_stock"], $_POST["brief_description"], $_POST["list_price"],
                          $_POST["product_code"], $_POST["sort_order"], isset($_POST["ProductIsProgram"]),
                          "eproduct_filename", $_POST["eproduct_available_days"], $_POST["eproduct_download_times"],
                          $_POST["weight"], $_POST["meta_description"], $_POST["meta_keywords"], isset
                          ($_POST["free_shipping"]), $_POST["min_order_amount"], $_POST["shipping_freight"],
                          $_POST["tax_class"], $_POST["title"]);
                      $_GET["productID"] = $productID;
                      $updatedValues = ScanPostVariableWithId(array("option_value", "option_radio_type"));
                      configUpdateOptionValue($productID, $updatedValues);
                  }
                  else
                  {
                      UpdateProduct($productID, $_POST["categoryID"], $_POST["name"], $_POST["price"],
                          $_POST["description"], $_POST["in_stock"], $_POST["rating"], $_POST["brief_description"],
                          $_POST["list_price"], $_POST["product_code"], $_POST["sort_order"], isset($_POST["ProductIsProgram"]),
                          "eproduct_filename", $_POST["eproduct_available_days"], $_POST["eproduct_download_times"],
                          $_POST["weight"], $_POST["meta_description"], $_POST["meta_keywords"], isset
                          ($_POST["free_shipping"]), $_POST["min_order_amount"], $_POST["shipping_freight"],
                          $_POST["tax_class"], $_POST["title"], 1, $_POST['url'], $_POST['tpl_city'], $_POST['tpl_val_1'], htmlspecialchars($_POST['tpl_val_2']));
                      $updatedValues = ScanPostVariableWithId(array("option_value", "option_radio_type"));
                      configUpdateOptionValue($productID, $updatedValues);
                  }

                  if (CONF_UPDATE_GCV == '1') update_psCount(1);

                  if (!isset($_POST["save_product_for_file"]) || $_POST["save_product_for_file"] != 1)ReLoadOpener();
              
			  }
          }

          if ($_POST["save_spwc"] == 1 && $_POST["AddProductAndOpenConfigurator"] == 0)
          {


              UpdateProduct($productID, $_POST["categoryID"], $_POST["name"], $_POST["price"], $_POST["description"],
                  $_POST["in_stock"], $_POST["rating"], $_POST["brief_description"], $_POST["list_price"],
                  $_POST["product_code"], $_POST["sort_order"], isset($_POST["ProductIsProgram"]), "eproduct_filename",
                  $_POST["eproduct_available_days"], $_POST["eproduct_download_times"], $_POST["weight"],
                  $_POST["meta_description"], $_POST["meta_keywords"], isset($_POST["free_shipping"]),
                  $_POST["min_order_amount"], $_POST["shipping_freight"], $_POST["tax_class"], $_POST["title"]);
              $updatedValues = ScanPostVariableWithId(array("option_value", "option_radio_type"));
              configUpdateOptionValue($productID, $updatedValues);

          }
          // save pictures
          if (isset($_POST["save_pictures"]))
          {
              if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON

              {
                  Redirect(ADMIN_FILE."?safemode=yes&productID=".$_GET["productID"]."&eaction=prod");
              }
              if ($_POST["save_pictures"] == 1)
              {
                  if ($_GET["productID"] == 0)
                  {
                      $productID = AddProduct($_POST["categoryID"], $_POST["name"], $_POST["price"],
                          $_POST["description"], $_POST["in_stock"], $_POST["brief_description"], $_POST["list_price"],
                          $_POST["product_code"], $_POST["sort_order"], isset($_POST["ProductIsProgram"]),
                          "eproduct_filename", $_POST["eproduct_available_days"], $_POST["eproduct_download_times"],
                          $_POST["weight"], $_POST["meta_description"], $_POST["meta_keywords"], isset
                          ($_POST["free_shipping"]), $_POST["min_order_amount"], $_POST["shipping_freight"],
                          $_POST["tax_class"], $_POST["title"]);
                      $_GET["productID"] = $productID;
                  }
                  else
                  {
                      UpdateProduct($productID, $_POST["categoryID"], $_POST["name"], $_POST["price"],
                          $_POST["description"], $_POST["in_stock"], $_POST["rating"], $_POST["brief_description"],
                          $_POST["list_price"], $_POST["product_code"], $_POST["sort_order"], isset($_POST["ProductIsProgram"]),
                          "eproduct_filename", $_POST["eproduct_available_days"], $_POST["eproduct_download_times"],
                          $_POST["weight"], $_POST["meta_description"], $_POST["meta_keywords"], isset
                          ($_POST["free_shipping"]), $_POST["min_order_amount"], $_POST["shipping_freight"],
                          $_POST["tax_class"], $_POST["title"]);
                      $updatedValues = ScanPostVariableWithId(array("option_value", "option_radio_type"));
                      configUpdateOptionValue($productID, $updatedValues);
                  }
                  AddNewPictures($_GET["productID"], "new_filename", "new_thumbnail", "new_enlarged",
                      $_POST["default_picture"]);

                  $updatedFileNames = ScanPostVariableWithId(array("filename", "thumbnail", "enlarged"));
                  UpdatePictures($_GET["productID"], $updatedFileNames, $_POST["default_picture"]);

                  $updatedFileNames = ScanFilesVariableWithId(array("ufilenameu", "uthumbnailu", "uenlargedu"));

                  UpdatePicturesUpload($_GET["productID"], $updatedFileNames, $_POST["default_picture"]);

                  ReLoadOpener3($_GET["productID"]);
              }
          }

          // delete three picture
          if (isset($_GET["delete_pictures"]))
          {
              if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON

              {
                  Redirect(ADMIN_FILE."?safemode=yes&productID=".$_GET["productID"]."&eaction=prod");
              }
              DeleteThreePictures($_GET["photoID"]);
              ReLoadOpener3($_GET["productID"]);
          }

          // delete one picture
          if (isset($_GET["delete_one_picture"]))
          {
              if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON

              {
                  Redirect(ADMIN_FILE."?safemode=yes&productID=".$_GET["productID"]."&eaction=prod");
              }
              if (isset($_GET["thumbnail"])) DeleteThumbnailPicture($_GET["thumbnail"]);
              if (isset($_GET["enlarged"])) DeleteEnlargedPicture($_GET["enlarged"]);
              ReLoadOpener3($_GET["productID"]);
          }

          // remove product from appended category
          if (isset($_GET["remove_from_app_cat"]))
          {
              if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON

              {
                  Redirect(ADMIN_FILE."?safemode=yes&productID=".$_GET["productID"]."&eaction=prod");
              }
              catRemoveProductFromAppendedCategory($_GET["productID"], $_GET["remove_from_app_cat"]);
              if (CONF_UPDATE_GCV == '1') update_psCount(1);
          }
          // add into new appended category
          if (isset($_POST["add_category"]))
          {
              if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON

              {
                  Redirect(ADMIN_FILE."?safemode=yes&productID=".$_GET["productID"]."&eaction=prod");
              }
              if ($_POST["add_category"] == 1)
              {
                  if ($_GET["productID"] == 0)
                  {
                      $productID = AddProduct($_POST["categoryID"], $_POST["name"], $_POST["price"],
                          $_POST["description"], $_POST["in_stock"], $_POST["brief_description"], $_POST["list_price"],
                          $_POST["product_code"], $_POST["sort_order"], isset($_POST["ProductIsProgram"]),
                          "eproduct_filename", $_POST["eproduct_available_days"], $_POST["eproduct_download_times"],
                          $_POST["weight"], $_POST["meta_description"], $_POST["meta_keywords"], isset
                          ($_POST["free_shipping"]), $_POST["min_order_amount"], $_POST["shipping_freight"],
                          $_POST["tax_class"], $_POST["title"]);
                      $_GET["productID"] = $productID;
                  }
                  else
                  {
                      UpdateProduct($productID, $_POST["categoryID"], $_POST["name"], $_POST["price"],
                          $_POST["description"], $_POST["in_stock"], $_POST["rating"], $_POST["brief_description"],
                          $_POST["list_price"], $_POST["product_code"], $_POST["sort_order"], isset($_POST["ProductIsProgram"]),
                          "eproduct_filename", $_POST["eproduct_available_days"], $_POST["eproduct_download_times"],
                          $_POST["weight"], $_POST["meta_description"], $_POST["meta_keywords"], isset
                          ($_POST["free_shipping"]), $_POST["min_order_amount"], $_POST["shipping_freight"],
                          $_POST["tax_class"], $_POST["title"]);
                      $updatedValues = ScanPostVariableWithId(array("option_value", "option_radio_type"));
                      configUpdateOptionValue($productID, $updatedValues);
                  }
                  catAddProductIntoAppendedCategory($_GET["productID"], $_POST["new_appended_category"]);

                  if (CONF_UPDATE_GCV == '1') update_psCount(1);

              }
          }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html class="admin">
<head>
<meta http-equiv="content-type" content="text/html; charset={$smarty.const.DEFAULT_CHARSET}">
<link rel="stylesheet" href="data/admin/style.css" type="text/css">
<link rel="icon" href="data/admin/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="data/admin/favicon.ico" type="image/x-icon">
<title><?php
          echo ADMIN_PRODUCT_TITLE;
?></title>

        <?php
          // add new product and open configurator
          // it works when user click "setting..." and new product is added
          if (isset($_POST["AddProductAndOpenConfigurator"]))
          {
              if ($_POST["AddProductAndOpenConfigurator"] == 1)
              {
                  if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON

                  {
                      Redirect(ADMIN_FILE."?safemode=yes&productID=".$_GET["productID"]."&eaction=prod");
                  }
                  $productID = AddProduct($_POST["categoryID"], $_POST["name"], $_POST["price"], $_POST["description"],
                      $_POST["in_stock"], $_POST["brief_description"], $_POST["list_price"], $_POST["product_code"],
                      $_POST["sort_order"], isset($_POST["ProductIsProgram"]), "eproduct_filename", $_POST["eproduct_available_days"],
                      $_POST["eproduct_download_times"], $_POST["weight"], $_POST["meta_description"],
                      $_POST["meta_keywords"], isset($_POST["free_shipping"]), $_POST["min_order_amount"],
                      $_POST["shipping_freight"], $_POST["tax_class"], $_POST["title"]);
                  $_GET["productID"] = $productID;
                  $updatedValues = ScanPostVariableWithId(array("option_value", "option_radio_type"));
                  configUpdateOptionValue($productID, $updatedValues);
                  OpenConfigurator($_POST["optionID"], $productID);
              }
          }

          // show product
          if ($_GET["productID"] != 0)
          {
              $product = GetProduct($_GET["productID"]);
              $product["description"] = html_spchars($product["description"]);
              $product["brief_description"] = html_spchars($product["brief_description"]);
              if (!$product["title"]) $product["title"] = "";
              $title = ADMIN_PRODUCT_EDITN;
          }
          else
          {
              $product = array();
              $title = ADMIN_PRODUCT_NEW;
              $cat = isset($_GET["categoryID"]) ? $_GET["categoryID"] : 0;
              $product["categoryID"] = $cat;
              $product["name"] = "";
              $product["title"] = "";
              $product["description"] = "";
              $product["customers_rating"] = "";
              $product["Price"] = 0;
              $product["picture"] = "";
              $product["in_stock"] = 0;
              $product["thumbnail"] = "";
              $product["big_picture"] = "";
              $product["brief_description"] = "";
              $product["list_price"] = 0;
              $product["product_code"] = "";
              $product["sort_order"] = 0;
              $product["date_added"] = null;
              $product["date_modified"] = null;
              $product["eproduct_filename"] = "";
              $product["eproduct_available_days"] = 7;
              $product["eproduct_download_times"] = 5;
              $product["weight"] = 0;
              $product["meta_description"] = "";
              $product["meta_keywords"] = "";
              $product["free_shipping"] = 0;
              $product["min_order_amount"] = 1;
              if (CONF_DEFAULT_TAX_CLASS == 0) $product["classID"] = "null";
              else  $product["classID"] = CONF_DEFAULT_TAX_CLASS;
              $product["shipping_freight"] = 0;
          }

          // gets ALL product options
          $options = configGetProductOptionValue($_GET["productID"]);

          // gets pictures
          $picturies = GetPictures($_GET["productID"]);

          // get appended categories
          $appended_categories = catGetAppendedCategoriesToProduct($_GET["productID"]);

         // $tax_classes = taxGetTaxClasses();
?>
<script type="text/javascript" src="data/admin/admin.js"></script>
<script type="text/javascript">
<!--

function confstatus(where, togo)
{
        if (where.value !== "" && where.value !== null)
        {
        document.getElementById(togo).disabled = "";
        }else{
        document.getElementById(togo).disabled = "disabled";
        }
}

function upd()
{
document.getElementById('save_product').value = 1;
}

function upd2()
{
document.getElementById('save_pictures').value = 1;
}

function upd3()
{
document.getElementById('add_category').value = 1;
}

function AddProductAndOpen_configurator(optionID)
{
document.getElementById('optionID').value = optionID;
document.getElementById('AddProductAndOpenConfigurator').value = 1;
document.getElementById('MainForm').submit();
}

function ProductIsProgramHandler()
{
                                        document.MainForm.eproduct_filename.disabled =
                                                        !document.MainForm.ProductIsProgram.checked;
                                        document.MainForm.eproduct_available_days.disabled =
                                                        !document.MainForm.ProductIsProgram.checked;
                                        document.MainForm.eproduct_download_times.disabled =
                                                        !document.MainForm.ProductIsProgram.checked;
}

//-->
</script>
</head>
<body class="ibody">
  <table class="adn">
    <tr>
      <td colspan="2">
        <table class="adn">
          <tr>
            <td class="head"><img src="data/admin/sep.gif" alt=""></td>
            <td class="head toph">&nbsp;&nbsp;<?php
          echo ADMIN_TMENU1;
?>: <b><?php
          if (CONF_BACKEND_SAFEMODE) echo "demo";
          else  echo $admintempname;
?></b></td>
            <td class="head"><img src="data/admin/sep2.gif" alt=""></td>
            <td class="head toph"><?php
          echo ADMIN_TMENU2;
?>: <b><?php
          echo $online_users;
?></b></td>
            <td class="head"><img src="data/admin/sep2.gif" alt=""></td>
            <td class="head last toph" width="100%"><a href="<?php echo ADMIN_FILE; ?>?order_search_type=SearchByStatusID&amp;checkbox_order_status_<?php
          echo CONF_NEW_ORDER_STATUS;
?>=1&amp;dpt=custord&amp;sub=new_orders&amp;search="><?php
          echo ADMIN_TMENU3;
?>: <b><?php
          echo $new_orders_count;
?></b></a></td>
            <td class="head">
              <table class="adw">
                <tr>
                <td class="head last toph"><a href="<?php echo ADMIN_FILE; ?>"><?php
          echo ADMINISTRATE_LINK;
?></a></td>
                  <td class="head"><img src="data/admin/sep2.gif" alt=""></td>
                  <td class="head last toph"><a href="index.php"><?php
          echo ADMIN_BACK_TO_SHOP;
?></a></td>
                  <td class="head"><img src="data/admin/sep2.gif" alt=""></td>
                  <td class="head last toph2 toph"><a href="index.php?logout=yes"><?php
          echo ADMIN_LOGOUT_LINK;
?></a></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td class="indexb1">
<table class="adn"><tr><td class="se"></td></tr></table>
<table width="186" class="adw" style="margin: auto;">
                               <tr>
                                   <td class="head2">
                                       <table class="adn">
                                           <tr>
                                               <td class="head4" onclick="menuresetit('menu1')"><?php
          echo ADMIN_CATALOG;
?></td>
                                               <td align="right" class="head7"><img src="data/admin/004.gif" alt="" onclick="menuresetit('menu1')" id="menu12" style="cursor: pointer;"></td>
                                           </tr>
                                       </table>
                                   </td>
                               </tr>
                               <tr id='menu13' style="display: none">
<td>
<div class="dvmenu">
<table class="adn"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=catalog&amp;sub=products_categories"><?php
          echo ADMIN_CATEGORIES_PRODUCTS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=catalog&amp;sub=dbsync"><?php
          echo ADMIN_SYNCHRONIZE_TOOLS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=catalog&amp;sub=extra"><?php
          echo ADMIN_PRODUCT_OPTIONS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=catalog&amp;sub=special"><?php
          echo ADMIN_SPECIAL_OFFERS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=catalog&amp;sub=excel_import"><?php
          echo ADMIN_IMPORT_FROM_EXCEL;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=catalog&amp;sub=excel_export"><?php
          echo ADMIN_EXPORT_TO_EXCEL;
?></a></td></tr></table>
</div></td>

                               </tr>
                               <tr>
                                   <td class="se"></td>
                               </tr>
                               <tr>
                                   <td class="head2">
                                       <table class="adn">
                                           <tr>
                                               <td class="head4" onclick="menuresetit('menu2')"><?php
          echo ADMIN_CUSTOMERS_AND_ORDERS;
?></td>
                                               <td align="right" class="head7"><img src="data/admin/004.gif" alt="" onclick="menuresetit('menu2')" id="menu22" style="cursor: pointer;"></td>
                                           </tr>
                                       </table>
                                   </td>
                               </tr>
                               <tr id='menu23' style="display: none">
<td>
<div class="dvmenu">
<table class="adn"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=new_orders"><?php
          echo ADMIN_NEW_ORDERS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=subscribers"><?php
          echo ADMIN_NEWS_SUBSCRIBERS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=order_statuses"><?php
          echo ADMIN_ORDER_STATUES;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=reg_fields"><?php
          echo ADMIN_CUSTOMER_REG_FIELDS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=discounts"><?php
          echo ADMIN_DISCOUNT_MENU;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=aux_pages"><?php
          echo ADMIN_TX7;
?></a></td></tr></table>
</div></td>
                               </tr>
                               <tr>
                                   <td class="se"></td>
                               </tr>
                               <tr>
                                   <td class="head2">
                                       <table class="adn">
                                           <tr>
                                               <td class="head4" onclick="menuresetit('menu3')"><?php
          echo ADMIN_SETTINGS;
?></td>
                                               <td align="right" class="head7"><img src="data/admin/004.gif" alt="" onclick="menuresetit('menu3')" id="menu32" style="cursor: pointer;"></td>
                                           </tr>
                                       </table>
                                   </td>
                               </tr>
                               <tr id='menu33' style="display: none">
<td>
<div class="dvmenu">
<table class="adn"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=setting&amp;settings_groupID=2"><?php
          echo ADMIN_SETTINGS_GENERAL;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=currencies"><?php
          echo ADMIN_CURRENCY_TYPES;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=shipping"><?php
          echo ADMIN_STRING_SHIPPING_TYPE;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=payment"><?php
          echo ADMIN_STRING_PAYMENT_TYPE;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=blocks_edit"><?php
          echo ADMIN_TX20;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=admin_edit"><?php
          echo ADMIN_CONF_ADMINS;
?></a></td></tr></table>
</div></td>
                               </tr>
                               <tr>
                                   <td class="se"></td>
                               </tr>
                               <tr>
                                   <td class="head2">
                                       <table class="adn">
                                           <tr>
                                               <td class="head4" onclick="menuresetit('menu4')"><?php
          echo ADMIN_MODULES;
?></td>
                                               <td align="right" class="head7"><img src="data/admin/004.gif" alt="" onclick="menuresetit('menu4')" id="menu42" style="cursor: pointer;"></td>
                                           </tr>
                                       </table>
                                   </td>
                               </tr>
                               <tr id='menu43' style="display: none">
<td>
<div class="dvmenu">
<table class="adn"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=modules&amp;sub=news"><?php
          echo ADMIN_NEWS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=modules&amp;sub=survey"><?php
          echo ADMIN_VOTING;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=modules&amp;sub=shipping"><?php
          echo ADMIN_STRING_SHIPPING_MODULES;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=modules&amp;sub=payment"><?php
          echo ADMIN_STRING_PAYMENT_MODULES;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=modules&amp;sub=linkexchange"><?php
          echo ADMIN_STRING_MODULES_LINKEXCHANGE;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=modules&amp;sub=yandex"><?php
          echo ADMIN_STRING_YANDEX;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=affiliate"><?php
          echo STRING_AFFILIATE_PROGRAM; ?></a></td></tr></table>
</div></td>

                               </tr>
                               <tr>
                                   <td class="se"></td>
                               </tr>
                                   <tr>
                                       <td class="head2">
                                           <table class="adn">
                                               <tr>
                                                   <td class="head4" onclick="menuresetit('menu5')"><?php
          echo ADMIN_REPORTS;
?></td>
                                               <td align="right" class="head7"><img src="data/admin/004.gif" alt="" onclick="menuresetit('menu5')" id="menu52" style="cursor: pointer;"></td>
                                               </tr>
                                           </table>
                                       </td>
                                   </tr>
                                   <tr id='menu53' style="display: none">
<td>
<div class="dvmenu">
<table class="adn"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=reports&amp;sub=category_viewed_times"><?php
          echo ADMIN_CATEGORY_VIEWED_TIMES;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=reports&amp;sub=customer_log"><?php
          echo ADMIN_CUSTOMER_LOG;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=reports&amp;sub=information"><?php
          echo ADMIN_INFORMATION2;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=reports&amp;sub=coming"><?php
          echo ADMIN_COMING;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=reports&amp;sub=security"><?php
          echo ADMIN_SECURITY;
?></a></td></tr></table>
</div></td>
                                   </tr>
                               <tr>
                                   <td class="se"></td>
                               </tr>
                                <tr>
                                       <td class="head2">
                                           <table class="adn">
                                               <tr>
                                                   <td class="head4" onclick="menuresetit('menu6')"><?php
          echo ADMIN_LIST_ALL;
?></td>
                                               <td align="right" class="head7"><img src="data/admin/004.gif" alt="" onclick="menuresetit('menu6')" id="menu62" style="cursor: pointer;"></td>
                                               </tr>
                                           </table>
                                       </td>
                                   </tr>
                                   <tr id='menu63' style="display: none">
<td>
<div class="dvmenu">
<table class="adn"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?login=&amp;first_name=&amp;last_name=&amp;email=&amp;groupID=0&amp;fActState=-1&amp;dpt=custord&amp;sub=custlist&amp;search=Find"><?php
          echo ADMIN_CUSTOMERS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=custord&amp;sub=custgroup"><?php
          echo ADMIN_CUSTGROUP;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=countries"><?php
          echo ADMIN_MENU_TOWNS;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=conf&amp;sub=zones"><?php
          echo ADMIN_MENU_TAXEZ;
?></a></td></tr></table>
<table class="adn topj"><tr><td><img src="data/admin/drs.gif" alt=""></td><td width="100%"><a href="<?php echo ADMIN_FILE; ?>?dpt=catalog&amp;sub=discuss"><?php
          echo ADMIN_DISCUSSIONS;
?></a></td></tr></table>
</div></td>
</tr>
</table>
<script type="text/javascript">
megamenu();
</script>
</td>
<td valign="top">
<table class="adn">
  <tr>
    <td class="zeb2 nbc">
      <table class="adn ggg">
         <tr>
           <td>
            <table class="adn">
              <tr>
              <td class="nbc2"><span class="titlecol"><?php
          echo $title;
?></span></td>
<td align="right" valign="middle" id="preproc"></td></tr>
              <tr>
               <td class="nbcl" colspan="2"></td>
              </tr>
            </table>
           </td>
         </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td valign="top" align="center" class="zeb">
      <table class="adn">
        <tr>
          <td align="left">
<form enctype="multipart/form-data" action="<?php echo ADMIN_FILE; ?>?productID=<?php
          echo $_GET["productID"];
?>&amp;eaction=prod" method="post" name="MainForm" id="MainForm">
<?php
          if (isset($_GET["safemode"]))
          {
              echo "<table class=\"adminw\"><tr><td align=\"left\"><table class=\"adn\"><tr><td><img src=\"data/admin/stop2.gif\" align=\"left\" class=\"stop\"></td><td class=\"splin\"><span class=\"error\">".
                  ERROR_MODULE_ACCESS2."</span><br><br>".ERROR_MODULE_ACCESS_DES2."</td></tr></table></td></tr></table>\n";

          }

          if (isset($_GET["couldntToDelete"]))
          {
              echo "<script type=\"text/javascript\">alert('".COULD_NOT_DELETE_THIS_PRODUCT."\\n".ADMIN_ERR5.
                  "');</script>\n";
          }
?>
<table class="adn">
<tr class="lineb">
<td align="left"><?php
          echo ADMIN_SL3;
?></td></tr>
<tr class="lins">
<td align="left"><?php
          echo ADMIN_CATEGORY_PARENT;
?>: <select name="categoryID" >
<option value="1"><?php
          echo ADMIN_CATEGORY_ROOT;
?></option>
<?php
          $cats = catGetCategoryCList($cat_id);
          for ($i = 0; $i < count($cats); $i++)
          {
              echo "<option value=\"".$cats[$i]["categoryID"]."\"";
              if ($product["categoryID"] == $cats[$i]["categoryID"]) //select category
                       echo " selected";
              echo ">";
              for ($j = 0; $j < $cats[$i]["level"]; $j++) echo "&nbsp;&nbsp;&nbsp;";
              echo $cats[$i]["name"];
              echo "</option>";
          }
?>
</select></td></tr>
<tr class="lins"><td align="left"><?php
          echo ADMIN_PRODUCT_NAME;
?>: <input type="text" name="name" value="<?php
          echo $product["name"];
?>" style="width: 590px;" class="textp"></td></tr>
<tr class="lins"><td align="left"><?php
          echo ADMIN_PRODUCT_TITLE_PAGE;
?>: <input type="text" name="title" value="<?php
          echo $product["title"];
?>" style="width: 590px;" class="textp"><br><br><input type=checkbox name='free_shipping'
                <?php
                         if ( $product["free_shipping"] )
                        {
                ?>
                        checked
                <?php
                        }
                ?>
                value='1'>&nbsp;&nbsp;<?php echo ADMIN_FREE_SHIPPING;?></td></tr>
<tr class="lins"><td align="left"><?php echo 'ЧПУ';?>: <input type="text" name="url" value="<?php echo $product["UID"]; ?>" style="width: 590px;" class="textp"></td></tr>
<tr class="lins"><td align="left"><?php echo '{city}';?>: <input type="text" name="tpl_city" value="<?php echo $product["tpl_city"]; ?>" style="width: 590px;" class="textp"></td></tr>
<tr class="lins"><td align="left"><?php echo '{tpl_val_1}'; ?>: <input type="text" name="tpl_val_1" value="<?php echo $product["tpl_val_1"];?>" style="width: 590px;" class="textp"></td></tr>
<tr class="lins"><td align="left"><span style="vertical-align:top;"><?php echo '{tpl_val_2}'; ?>: </span><textarea style="width:300px;height:13px;resize:none;" name="tpl_val_2"><?php echo $product["tpl_val_2"];?></textarea></td></tr><!--papush-->

<?php
          if (!is_null($product["date_added"]))
          {
              echo "<tr class=\"lins\"><td align=\"left\">".ADMIN_DATE_ADDED_PR.": ";
              echo "<span style=\"color: #BB0000\">".$product["date_added"]."</span>";
              if (!is_null($product["date_modified"]))
              {
                  echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".ADMIN_DATE_MODIFIED.": ";
                  echo "<span style=\"color: #BB0000\">".$product["date_modified"]."</span>";
              }
              echo "</td></tr><tr><td class=\"se5\"></td></tr>";
          }
?>
</table>

<table class="adn">
<tr class="lineb">
<td align="left" width="25%"><?php
          echo ADMIN_EDIT_PROD_MN1;
?></td>
<td align="left" width="25%"><?php
          echo ADMIN_EDIT_PROD_MN2;
?></td>
<td align="left" width="25%"><?php
          echo ADMIN_EDIT_PROD_MN3;
?></td>
<td align="left" width="25%"><?php
          echo ADMIN_EDIT_PROD_MN5;
?></td>
</tr>
<tr class="lins"><td align="left"><input type="text" name="price" value="<?php
          echo $product["Price"];
?>" style="width: 100px;" class="textp"></td>
<td align="left"><input type="text" name="list_price" value="<?php
          echo $product["list_price"];
?>" style="width: 100px;" class="textp"></td>
<td align="left"><input type="text" name="product_code" value="<?php
          echo str_replace("\"", "&quot;", $product["product_code"]);
?>" style="width: 100px;" class="textp"></td>
<td align="left"><input type="text" name="shipping_freight" value="<?php
          echo $product["shipping_freight"];
?>" style="width: 100px;" class="textp"></td>
</tr>
</table>
<table class="adn">
<tr class="lineb">
<td align="left" width="25%"><?php
          echo ADMIN_EDIT_PROD_MN4;
?></td>
<td align="left" width="25%"><?php
          echo ADMIN_EDIT_PROD_MN7;
?></td>
<td align="left" width="25%"><?php
          echo ADMIN_PRODUCT_WEIGHT;
?></td>
<?php
          if (CONF_CHECKSTOCK == 1)
          {
?>
<td align="left" width="25%"><?php
              echo ADMIN_EDIT_PROD_MN6;
?></td>
<?php
          }
          else
          {
?>
<td align="left" width="25%">&nbsp;</td>
<?php
          }
?>
</tr>
<tr class="lins"><td align="left"><input type="text" name="min_order_amount" value="<?php
          echo $product["min_order_amount"];
?>" style="width: 100px;" class="textp"></td>
<td align="left"><input type="text" name="sort_order" value="<?php
          echo $product["sort_order"];
?>" style="width: 100px;" class="textp"></td>
<td align="left"><input type="text" name="weight" value="<?php
          echo $product["weight"];
?>" style="width: 100px;" class="textp"></td>
<?php
          if (CONF_CHECKSTOCK == 1)
          {
?>
<td align="left"><input type="text" name='in_stock' value="<?php
              echo $product["in_stock"];
?>" style="width: 100px;" class="textp"></td>
<?php
          }
          else
          {
?>
<td align="left"><input type="hidden" name='in_stock' value="<?php
              echo $product["in_stock"];
?>"></td>
<?php
          }
?>
</tr></table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>
<a href="#" onclick="upd(),document.getElementById('MainForm').submit(); return false" class="inl"><?php
          echo SAVE_BUTTON;
?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<?php
          if ($codep == 1)
          {
              $wer = $_GET["categoryID"];
              echo "<a href=\"".ADMIN_FILE."?categoryID=$wer&amp;stepback=1&amp;eaction=prod\" class=\"inl\">".
                  CANCEL_BUTTON."</a>";
          }
          else
          {
              $wer = $_GET["productID"];
              echo "<a href=\"".ADMIN_FILE."?productID=$wer&amp;stepback=1&amp;eaction=prod\" class=\"inl\">".
                  CANCEL_BUTTON."</a>";
          }
?><?php
          if ($_GET["productID"]) echo "&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"#\" onClick=\"confirmDeletep('".
                  QUESTION_DELETE_CONFIRMATION."','".ADMIN_FILE."?productID=".$_GET["productID"]."&amp;delete=1&amp;eaction=prod'); return false\" class=\"inl\">".
                  DELETE_BUTTON."</a>";
?>
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adn"><tr class="lineb"><td align="left" colspan="6"><?php
          echo ADMIN_DIGIT_PRODUCTS;
?></td></tr>
<tr class="lineb">
<td align="left"><?php
          echo "&nbsp;";
?></td>
<td align="left" width="25%"><?php
          echo ADMIN_EPRODUCT_FILENAME;
?></td>
<td align="left" width="25%"><?php
          echo ADMIN_EPRODUCT_AVAILABLE_DAYS ."(".ADMIN_DAYS.")";
?></td>
<td align="left" width="25%"><?php
          echo ADMIN_EPRODUCT_DOWNLOAD_TIMES;
?></td>
<td align="left" width="25%"><?php
          echo ADMIN_UPLOAD_PRODUCT_FILE;
?></td>
<td align="left"><?php
 echo "&nbsp;";
?></td>
</tr>
<tr class="lins">
<td align="center" valign="middle"><input type=checkbox name='ProductIsProgram'
                                                        value='1'
                                                        onclick='ProductIsProgramHandler();'
                                                        <?php
                                                        if ( trim($product["eproduct_filename"]) != "" )
                                                        {
                                                        ?>
                                                                checked
                                                        <?php
                                                        }
                                                        ?>
                                                        ></td>
<td align="left" valign="middle">
                                        <?php
                                        if ( file_exists("core/files/".$product["eproduct_filename"])  &&
                                                         $product["eproduct_filename"]!=null )
                                        {
                                        ?>
                                                <?php echo $product["eproduct_filename"];?>
                                        <?php
                                        }
                                        else
                                        {
                                        ?>
                                                <?php echo ADMIN_FILE_NOT_UPLOADED;?>
                                        <?php
                                        }
                                        ?> </td>
<td align="left" valign="middle"><?php
                                                $valueArray[] = 1;
                                                $valueArray[] = 2;
                                                $valueArray[] = 3;
                                                $valueArray[] = 4;
                                                $valueArray[] = 5;
                                                $valueArray[] = 7;
                                                $valueArray[] = 14;
                                                $valueArray[] = 30;
                                                $valueArray[] = 180;
                                                $valueArray[] = 365;
                                        ?>
                                        <select name='eproduct_available_days'>
                                                <?php
                                                foreach($valueArray as $value)
                                                {
                                                ?>
                                                        <option value='<?php echo $value;?>'
                                                        <?php
                                                                if ( $product["eproduct_available_days"] == $value )
                                                                {
                                                        ?>
                                                                        selected
                                                        <?php
                                                                }
                                                        ?>
                                                        > <?php echo $value;?> </option>
                                                <?php
                                                }
                                                ?>
                                        </select>
                                </td>
<td align="left" valign="middle"><input type=text name='eproduct_download_times'
                                                value='<?php echo $product["eproduct_download_times"];?>' class="textp" >
                                </td><td align="left" valign="middle"><input type='file' name='eproduct_filename' value='<?php echo $product["eproduct_filename"];?>' class="file" size="18"></td>
                                <td align="center" valign="middle"><img src="data/admin/plus.gif" alt="" style="cursor: pointer" onclick="document.getElementById('save_product_for_file').value='1',document.getElementById('MainForm').submit()"></td>
                        </tr></table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se6"></td></tr></table>
<script type="text/javascript">ProductIsProgramHandler();</script>

<input type=hidden name='tax_class' value='null'>
<input type=hidden name='rating' value="<?php
          echo $product["customers_rating"];
?>">

<input type=hidden name='save_spwc' value='0' id='save_spwc'>
<input type=hidden name='save_product_for_file' value='0' id='save_product_for_file'>
<input type=hidden name='save_product' value='0' id='save_product'>
<input type=hidden name='save_pictures' value='0' id='save_pictures'>
<input type=hidden name='add_category' value='0' id='add_category'>
<input type=hidden name='save_product_without_closing' value='0' id='spwc'>
<input type=hidden name='AddProductAndOpenConfigurator' value='0' id='AddProductAndOpenConfigurator'>
<input type=hidden name='optionID' value='0' id='optionID'>
<table class="adn">
<tr class="linsz">
<td align="left"><span class="titlecol2"><?php
          echo ADMIN_PRODUCT_BRIEF_DESC;
?></span></td>
</tr>
<tr>
<td>
<textarea name="brief_description" class="admin" id="myarea1"><?php
          echo $product["brief_description"];
?></textarea></td></tr>
</table>
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adn">
<tr class="linsz">
<td align="left"><span class="titlecol2"><?php
          echo ADMIN_PRODUCT_DESC;
?></span></td>
</tr>
<tr>
<td><textarea name="description" id="myarea2" class="admin"><?php
          echo $product["description"];
?></textarea></td>
</tr></table>
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adw">
<tr><td width="50%">
<table class="adn">
<tr class="linsz">
<td align="left"><span class="titlecol2"><?php
          echo ADMIN_PRODUCT_DESC2;
?></span></td>
</tr>
<tr><td align="left"><textarea name='meta_description' id="meta_description" class="adminall" style="margin-right: 38px;"><?php
          echo $product["meta_description"];
?></textarea></td>
</tr>
</table>
</td>
<td width="50%">
<table class="adn">
<tr class="linsz">
<td align="left"><span class="titlecol2"><?php
          echo ADMIN_PRODUCT_DESC3;
?></span></td>
</tr>
<tr><td align="left"><textarea name='meta_keywords' id="meta_keywords" class="adminall"><?php
          echo $product["meta_keywords"];
?></textarea></td></tr>
</table>
</td></tr></table>
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adn"><tr class="lineb"><td align="left" colspan="5"><?php
          echo ADMIN_PHOTOS;
?>
<script type="text/javascript" src="data/admin/highslide.packed.js"></script>
<script type="text/javascript">
<!--
    hs.graphicsDir = 'data/admin/';
    hs.outlineType = 'rounded';

    hs.restoreTitle = '<?php
          echo STRING_HS_RESTORETITLE;
?>';
    hs.loadingText = '<?php
          echo STRING_HS_LOADINGTEXT;
?>';
    hs.loadingTitle = '<?php
          echo STRING_HS_LOADINGTITLE;
?>';
    hs.focusTitle = '<?php
          echo STRING_HS_FOCUSTITLE;
?>';
    hs.fullExpandTitle = '<?php
          echo STRING_HS_FULLEXPANDTITLE;
?>';

//-->
</script>
</td></tr>
<?php
          if (count($picturies) >= 1)
          {
?>
<tr class="lineb">
<td align="center"><?php
              echo ADMIN_ON2;
?></td>
<td align="center" width="32%"><?php
              echo ADMIN_PRODUCT_SPPICTURE;
?></td>
<td align="center" width="32%"><?php
              echo ADMIN_PRODUCT_THUMBNAIL;
?></td>
<td align="center" width="32%"><?php
              echo ADMIN_PRODUCT_BIGPICTURE;
?></td>
<td align="center"><?php
              echo ADMIN_ON3;
?></td>
</tr>
<?php
              foreach ($picturies as $picture)
              {
                  echo ("<tr class=\"linsz\">");
                  if ($picture["default_picture"] == 1)
                  {
                      $default_picture_exists = true;
                      echo "<td align=\"center\" valign=\"middle\"><input type=radio name=default_picture value='".
                          $picture["photoID"]."' checked></td>\n";
                  }
                  else  echo "<td align=\"center\" valign=\"middle\"><input type=radio name=default_picture value='".
                          $picture["photoID"]."'></td>\n";
                  echo "<td align=\"center\" valign=\"middle\">\n";
                  echo "<input type=text name=filename_".$picture["photoID"]." value='".$picture["filename"].
                      "' size=25 class=\"textp\"><br>\n";
                  if (file_exists("data/small/".$picture["filename"]) && trim($picture["filename"]) !=
                      "") echo "<a href=\"data/small/".$picture["filename"]."\" onclick=\"return hs.expand(this)\" style=\"text-decoration:none;\">".
                          ADMIN_PHOTO_PREVIEW."</a>\n";
                  else  echo ADMIN_PICTURE_NOT_UPLOADED;
                  echo "</td>\n";
                  echo "<td align=\"center\" valign=\"middle\">\n";
                  echo "<input type=text name=thumbnail_".$picture["photoID"]." value='".$picture["thumbnail"].
                      "' size=25 class=\"textp\"><br>\n";
                  if (file_exists("data/medium/".$picture["thumbnail"]) && trim($picture["thumbnail"]) !=
                      "")
                  {
                      echo "<a href=\"data/medium/".$picture["thumbnail"]."\" onclick=\"return hs.expand(this)\" style=\"text-decoration:none;\">".
                          ADMIN_PHOTO_PREVIEW."</a>";
                      echo " / <a href=\"#\" onClick=\"confirmDeletep('".QUESTION_DELETE_PICTURE."', '".ADMIN_FILE."?delete_one_picture=1&amp;thumbnail=".
                          $picture["photoID"]."&amp;productID=".$_GET["productID"]."&amp;eaction=prod'); return false\" style=\"text-decoration:none;\">".
                          DELETE_BUTTON."</a>\n";
                  }
                  else  echo ADMIN_PICTURE_NOT_UPLOADED;
                  echo "</td>\n";
                  echo "<td align=\"center\" valign=\"middle\">\n";
                  echo "<input type=text name=enlarged_".$picture["photoID"]." value='".$picture["enlarged"].
                      "' size=25 class=\"textp\"><br>\n";
                  if (file_exists("data/big/".$picture["enlarged"]) && trim($picture["enlarged"]) !=
                      "")
                  {
                      echo "<a href=\"data/big/".$picture["enlarged"]."\" onclick=\"return hs.expand(this)\" style=\"text-decoration:none;\">".
                          ADMIN_PHOTO_PREVIEW."</a>";
                      echo " / <a href=\"#\" onClick=\"confirmDeletep('".QUESTION_DELETE_PICTURE."', '".ADMIN_FILE."?delete_one_picture=1&amp;enlarged=".
                          $picture["photoID"]."&amp;productID=".$_GET["productID"]."&amp;eaction=prod'); return false\" style=\"text-decoration:none;\">".
                          DELETE_BUTTON."</a>\n";
                  }
                  else  echo ADMIN_PICTURE_NOT_UPLOADED;
                  echo "</td>\n";
                  echo "<td valign=middle align=center>\n";
?>
<img src="data/admin/mines.gif" style="cursor: pointer" alt="" onClick="confirmDeletep('<?php
                  echo QUESTION_DELETE_PICTURE
?>','<?php echo ADMIN_FILE; ?>?productID=<?php
                  echo $_GET["productID"]
?>&amp;photoID=<?php
                  echo $picture["photoID"];
?>&amp;delete_pictures=1&amp;eaction=prod')"></td>
<?php
                  echo "</tr><tr class=\"linsz\"><td></td> <td align=\"center\" valign=\"middle\"><input type=\"file\" name=\"ufilenameu_".
                      $picture["photoID"]."\" class=\"file\" size=\"13\"></td><td align=\"center\" valign=\"middle\"><input type=\"file\" name=\"uthumbnailu_".
                      $picture["photoID"]."\" class=\"file\" size=\"13\"></td><td align=\"center\" valign=\"middle\"><input type=\"file\" name=\"uenlargedu_".
                      $picture["photoID"]."\" class=\"file\" size=\"13\"></td> <td></td></tr><tr><td class=\"separ\" colspan=\"5\"><img src=\"data/admin/pixel.gif\" alt=\"\" class=\"sep\"></td></tr>\n";
              }
              echo "<tr><td class=\"se5\" colspan=\"5\"></td></tr><tr><td colspan=\"5\" align=\"left\"><a href=\"#\" onclick=\"upd2(),document.getElementById('MainForm').submit(); return false\" class=\"inl\">".
                  SAVE_BUTTON."</a></td></tr>\n";

          }
          else
          {
              echo "<tr><td height=\"20\" valign=\"middle\" align=\"center\">".ADMIN_NO_PHOTO_NEW."</td></tr><tr><td class=\"separ\" colspan=\"5\"><img src=\"data/admin/pixel.gif\" alt=\"\" class=\"sep\"></td></tr>\n";
          }
?>
</table>
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adn"><tr class="lineb"><td align="left" colspan="5"><?php
          echo ADD_BUTTON26;
?></td></tr>
<tr class="lineb">
<td align="center"><?php
          echo ADMIN_ON2;
?></td>
<td align="center" width="32%"><?php
          echo ADMIN_PRODUCT_SPPICTURE;
?></td>
<td align="center" width="32%"><?php
          echo ADMIN_PRODUCT_THUMBNAIL;
?></td>
<td align="center" width="32%"><?php
          echo ADMIN_PRODUCT_BIGPICTURE;
?></td>
<td align="center">Add</td>
</tr>
<tr class="lins">
<td align="center" valign="middle"><input type=radio name=default_picture
                                        <?php
          if (!isset($default_picture_exists))
          {
?>
                                                checked
                                        <?php
          }
?>
                                                value=-1 >

                                </td>
                                <td align="center" valign="middle"><input id="pic1" onchange="confstatus(this,'pic2'),confstatus(this,'pic3');" type="file" name="new_filename" class="file" size="13"></td>
                                <td align="center" valign="middle"><input disabled id="pic2" type="file" name="new_thumbnail" class="file" size="13"></td>
                                <td align="center" valign="middle"><input disabled id="pic3" type="file" name="new_enlarged" class="file" size="13"></td>
                                <td align="center" valign="middle"><img src="data/admin/plus.gif" alt="" style="cursor: pointer" onclick="upd2(),document.getElementById('MainForm').submit()"></td>
                        </tr></table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se6"></td></tr></table>
<?php
          if (isset($_GET["productID"]))
          {
?>
<table class="adn">
<tr class="lineb">
<td align="left"><?php
              echo ADMIN_PRODUCT_DESC4;
?></td>
</tr>
<?php
              $q = db_query("select count(*) FROM ".RELATED_PRODUCTS_TABLE." WHERE Owner=".(int)$_GET["productID"]);
              $cnt = db_fetch_row($q);
              if ($cnt[0] == 0) echo "<tr class=\"lins\"><td align=\"center\" height=\"20\">".STRING_EMPTY_CATEGORY3.
                      "</td></tr>";
              else
              {
                  $q = db_query("select productID FROM ".RELATED_PRODUCTS_TABLE." WHERE Owner=".(int)$_GET["productID"]);

                  while ($r = db_fetch_row($q))
                  {
                      $p = db_query("select productID, name FROM ".PRODUCTS_TABLE." WHERE productID=".(int)$r[0]);
                      if ($r1 = db_fetch_row($p))
                      {
                          echo "<tr class=\"liney\"><td align=\"left\">";
                          echo $r1[1];
                          echo "</td></tr>";
                      }
                  }

              }
?></table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>
<a href="#" onClick="open_window('<?php echo ADMIN_FILE; ?>?do=wishlist&owner=<?php
              echo $_GET["productID"];
?>',500,500); return false" class="inl"><?php
              echo EDIT_BUTTON;
?></a>
<table class="adn"><tr><td class="se6"></td></tr></table>
<?php
          }
?>
<script type="text/javascript">
                        function SetOptionValueTypeRadioButton( id, radioButtonState )
                        {
                                if ( radioButtonState == "UN_DEFINED" )
                                        document.getElementById('option_radio_type_'+id+'_1').click();
                                else if ( radioButtonState == "ANY_VALUE" )
                                        document.getElementById('option_radio_type_'+id+'_2').click();
                                else if ( radioButtonState == "N_VALUES" )
                                       document.getElementById('option_radio_type_'+id+'_3').click();
                        }

                        function SetEnabledStateTextValueField( id, radioButtonState )
                        {
                                if ( radioButtonState == "UN_DEFINED" ||
                                        radioButtonState == "N_VALUES" )
                                {
                                          document.getElementById('option_value_'+id).disabled=true;
                                        document.getElementById('option_value_'+id).value="";
                                }
                                else {
                                       document.getElementById('option_value_'+id).disabled=false;
                        }
                        }
</script>
<table class="adn">
<tr class="lineb">
<td align="left"colspan="3"><?php
          echo ADMIN_EDITCHAR;
?></td></tr>
<?php
          if (count($options) >= 1)
          {
              foreach ($options as $option)
              {
                  $option_row = $option["option_row"];
                  $value_row = $option["option_value"];
                  $ValueCount = $option["value_count"];
?>
<tr class="lineb"><td align="left" colspan="3"><?php
                  echo $option_row["name"]
?></td></tr>
<tr class="liney">
<td align="center" valign="middle"><input name='option_radio_type_<?php
                  echo $option_row["optionID"]
?>' id='option_radio_type_<?php
                  echo $option_row["optionID"]
?>_1' type='radio' value="UN_DEFINED" onclick="SetEnabledStateTextValueField(<?php
                  echo $option_row['optionID']
?>, 'UN_DEFINED' )"></td>
<td align="center" valign="middle">----------</td>
<td align="left" valign="middle" width="100%"><?php
                  echo ADMIN_NOT_DEFINED;
?></td>
</tr>
<tr class="liney">
<td align="center" valign="middle"><input name='option_radio_type_<?php
                  echo $option_row["optionID"]
?>' id='option_radio_type_<?php
                  echo $option_row["optionID"]
?>_2' type='radio' value="ANY_VALUE" onclick="SetEnabledStateTextValueField(<?php
                  echo $option_row['optionID']
?>, 'ANY_VALUE' )"></td>
<td align="center" valign="middle"><input type="text" name='option_value_<?php
                  echo $option_row["optionID"]
?>' id='option_value_<?php
                  echo $option_row["optionID"]
?>' value='<?php
                  echo str_replace("\"", "&quot;", $value_row["option_value"])
?>' class="textp" style="width: 150px"></td>
<td align="left" valign="middle"><?php
                  echo ADMIN_ANY_VALUE;
?></td>
</tr>
<tr class="liney">
<td align="center" valign="middle"><input name='option_radio_type_<?php
                  echo $option_row["optionID"]
?>' id='option_radio_type_<?php
                  echo $option_row["optionID"]
?>_3' type='radio' value="N_VALUES" onclick="SetEnabledStateTextValueField(<?php
                  echo $option_row['optionID']
?>, 'N_VALUES' )"></td>
<td align="center" valign="middle"><a href="#" class="inl" name="configurator_<?php
                  echo $option_row['optionID']
?>" style="cursor: pointer"
<?php
                  if ($_GET["productID"] != 0)
                  {
?>
 onclick="open_window('<?php echo ADMIN_FILE; ?>?do=configurator&optionID=<?php
                      echo $option_row["optionID"]
?>&amp;productID=<?php
                      echo $_GET["productID"]
?>',450,400); return false"
<?php
                  }
                  else
                  {
?>
 onclick="AddProductAndOpen_configurator(<?php
                      echo $option_row["optionID"]
?>); return false"
<?php
                  }
?>
><?php
                  echo ADMIN_SELECT_SETTING;
?></a></td>
<td align="left" valign="middle"><?php
                  echo ADMIN_SELECTING_FROM_VALUES;
?> (<?php
                  echo $ValueCount
?> <?php
                  echo ADMIN_VARIANTS;
?>)
<SCRIPT type="text/javascript">
<?php
                  if ((is_null($value_row["option_value"]) || $value_row["option_value"] == '') && $value_row["option_type"] ==
                      0) echo ("SetOptionValueTypeRadioButton( ".$option_row["optionID"].", 'UN_DEFINED' );");
                  else
                      if ($value_row["option_type"] == 0) echo ("SetOptionValueTypeRadioButton( ".$option_row["optionID"].
                              ", 'ANY_VALUE' );");
                      else
                          if ($value_row["option_type"] == 1) echo ("SetOptionValueTypeRadioButton( ".
                                  $option_row["optionID"].", 'N_VALUES' );");
?>
</script>
</td>
</tr>
<?php
              }
          }
          else
          {
              echo "<tr><td height=\"20\" align=\"center\" valign=\"middle\">".ADMIN_NO_CHAR_NEW."</td></tr>";
          }
?></table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se6"></td></tr></table>
<table class="adn">
<tr class="lineb">
<td align="left" colspan="2"><?php
          echo ADMIN_CATSPLIT;
?></td></tr><tr><td height="4" colspan="2"></td></tr>
<?php
          if (count($appended_categories) >= 1)
          {
              foreach ($appended_categories as $app_cat)
              {
?>
<tr>
<td colspan="2">
<table class="adn">
<tr class="liney">
<td align="left"><?php
                  echo $app_cat["category_way"];
?></td>
<td align="right"><img src="data/admin/mines.gif" alt="" style="cursor: pointer;" onclick="confirmDeletep('<?php
                  echo QUESTION_DELETE_CONFIRMATION;
?>','<?php echo ADMIN_FILE; ?>?productID=<?php
                  echo $_GET["productID"]
?>&amp;remove_from_app_cat=<?php
                  echo $app_cat["categoryID"]
?>&amp;eaction=prod')"></td>
</tr>
</table>
</td>
</tr>
<?php
              }
          }
          else
          {
              echo "<tr><td height=\"18\" align=\"center\" valign=\"middle\" colspan=\"2\">".ADMIN_NO_CAT_NEW.
                  "</td></tr>";
          }
          $cats = catGetCategoryCList($cat_id);
          if (count($cats) >= 1)
          {
?>
<tr class="liney"><td width="100%" align=right><b><?php
              echo ADD_BUTTON;
?>:</b> <select name='new_appended_category'>
<?php
              for ($i = 0; $i < count($cats); $i++)
              {
                  echo "<option value=\"".$cats[$i]["categoryID"]."\">";
                  for ($j = 0; $j < $cats[$i]["level"]; $j++) echo "&nbsp;&nbsp;";
                  echo $cats[$i]["name"];
                  echo "</option>";
              }
?></select>&nbsp;</td><td><img src="data/admin/ret.gif" alt="" style="cursor: pointer;" onclick="upd3(),document.getElementById('MainForm').submit()"></td></tr>
<?php
          }
?>
</table>
<table class="adn"><tr><td height="4"></td></tr><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>
<a href="#" onclick="upd(),document.getElementById('MainForm').submit(); return false" class="inl"><?php
          echo SAVE_BUTTON;
?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<?php
          if ($codep == 1)
          {
              $wer = $_GET["categoryID"];
              echo "<a href=\"".ADMIN_FILE."?categoryID=".$wer."&amp;stepback=1&amp;eaction=prod\" class=\"inl\">".
                  CANCEL_BUTTON."</a>";
          }
          else
          {
              $wer = $_GET["productID"];
              echo "<a href=\"".ADMIN_FILE."?productID=".$wer."&amp;stepback=1&amp;eaction=prod\" class=\"inl\">".
                  CANCEL_BUTTON."</a>";
          }
?><?php
          if ($_GET["productID"]) echo "&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"#\" onClick=\"confirmDeletep('".
                  QUESTION_DELETE_CONFIRMATION."','".ADMIN_FILE."?productID=".$_GET["productID"]."&amp;delete=1&amp;eaction=prod'); return false\" class=\"inl\">".
                  DELETE_BUTTON."</a>";
?>
</form>
<?php
          if (CONF_EDITOR)
          {
?>
<script type="text/javascript" src="fckeditor/fckeditor.js"></script>
<script type="text/javascript" src="fckeditor/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
window.onload = function()
{
var oFCKeditor = new FCKeditor( 'myarea1',720,346) ;
<?php
              $dir1 = dirname($_SERVER['PHP_SELF']);
              $sourcessrand = array("//" => "/", "\\" => "/");
              $dir1 = strtr($dir1, $sourcessrand);
              if ($dir1 != "/") $dir2 = "/";
              else  $dir2 = "";
?>
oFCKeditor.BasePath = "<?php
              echo $dir1.$dir2;
?>fckeditor/" ;
oFCKeditor.ReplaceTextarea() ;
var oFCKeditor2 = new FCKeditor( 'myarea2',720,346) ;
oFCKeditor2.BasePath = "<?php
              echo $dir1.$dir2;
?>fckeditor/" ;
oFCKeditor2.ReplaceTextarea() ;
}
</script>
<?php
          }
?>
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adn"><tr><td class="help"><span class="titlecol2"><?php
          echo USEFUL_FOR_YOU;
?></span><div class="helptext"><?php
          echo NOTE31;
?><br><?php
          echo NOTE339;
?><br><br><?php
          echo NOTE34;
?><br><br><?php
          echo NOTE342;
?></div></td>
</tr>
</table>
<table class="adn"><tr><td class="se8"></td></tr></table>
</td></tr></table></td></tr></table> </td></tr></table>
</body>
</html>






        <?php
          break;
      default:

          //init Smarty
          require ("core/smarty/smarty.class.php");
          $smarty = new Smarty; //core smarty object
	  $smarty->compile_dir .=$host.'/admin';
          //        $smarty_mail = new Smarty; //for e-mails

          if ((int)CONF_SMARTY_FORCE_COMPILE) 

          {
              $smarty->force_compile = true;
              //                $smarty_mail->force_compile = true;
          }

          // set Smarty include files dir
          $smarty->template_dir = "core/tpl/admin/{$AdminTplDir}";
          // $smarty_mail->template_dir = "core/tpl";

          //validate data to avoid SQL injections
          if (isset($_GET["customerID"])) $_GET["customerID"] = (int)$_GET["customerID"];
          if (isset($_GET["settings_groupID"])) $_GET["settings_groupID"] = (int)$_GET["settings_groupID"];
          if (isset($_GET["orderID"])) $_GET["orderID"] = (int)$_GET["orderID"];
          if (isset($_GET["answer"])) $_GET["answer"] = (int)$_GET["answer"];
          if (isset($_GET["productID"])) $_GET["productID"] = (int)$_GET["productID"];
          if (isset($_GET["categoryID"])) $_GET["categoryID"] = (int)$_GET["categoryID"];
          if (isset($_GET["countryID"])) $_GET["countryID"] = (int)$_GET["countryID"];
          if (isset($_GET["delete"])) $_GET["delete"] = (int)$_GET["delete"];
          if (isset($_GET["setting_up"])) $_GET["setting_up"] = (int)$_GET["setting_up"];


          function mark_as_selected($a, $b) //required for excel import
              //returns " selected" if $a == $b
          {
              $a = trim($a);
              return !strcmp($a, $b) ? " selected" : "";

          } //mark_as_selected


          function get_NOTempty_elements_count($arr) //required for excel import
              //gets how many NOT NULL (not empty strings) elements are there in the $arr
          {
              $n = 0;
              for ($i = 0; $i < count($arr); $i++)
                  if (trim($arr[$i]) != "") $n++;
              return $n;
          } //get_NOTempty_elements_count


          //end of functions definition

          //define department and subdepartment
          if (!isset($_GET["dpt"]))
          {
              $dpt = isset($_POST["dpt"]) ? $_POST["dpt"] : "";
          }
          else  $dpt = $_GET["dpt"];
          if (!isset($_GET["sub"]))
          {
              if (isset($_POST["sub"])) $sub = $_POST["sub"];
          }
          else  $sub = $_GET["sub"];

          if (isset($_GET["safemode"])) //show safe mode warning

          {
              $smarty->assign("safemode", ADMIN_SAFEMODE_WARNING);
          }

          $smarty->assign("AdminHtmlCharset", $AdminHtmlCharset);
          //define smarty template
          $smarty->assign("admin_main_content_template", "default.tpl.html");
          $smarty->assign("current_dpt", $dpt);

		  if(!(isset($_POST['sub']) && $_POST['sub']=='sublist')) {
			if (!isset($sub) || ($sub != 'tpl' && $sub != 'subtpl')) {$cats_vip = catGetCategoryCList($cat_id);}
			else {$cats_vip = array();}
            $smarty->assign("cats_vip", $cats_vip);
          }
		  
          if (isset($_SESSION["log"])) $smarty->assign("admintempname", $_SESSION["log"]);
/*
          $q = db_query("select categoryID, name, products_count, products_count_admin, parent, picture, subcount FROM ".CATEGORIES_TABLE." ORDER BY sort_order, name");
          $fc = array(); //parents
          $mc = array(); //parents
          while ($row = db_fetch_row($q)) {
                $fc[(int)$row["categoryID"]] = $row;
                $mc[(int)$row["categoryID"]] = (int)$row["parent"];
          }
*/
          $admin_departments = array();

          // includes all .php files from core/includes/ dir
          $includes_dir = opendir("core/includes/admin");
          $file_count = 0;
          while (($inc_file = readdir($includes_dir)) != false)
              if (strstr($inc_file, ".php"))
              {
                  include ("core/includes/admin/".$inc_file);
                  $file_count++;
              }
          closedir($includes_dir);

          if (defined("UPDATEDESIGND"))
          {
              $path = "core/cache";
              $handled = opendir($path);
              while (false !== ($file = readdir($handled)))
              {
                  if ($file != ".htaccess" && $file != "." && $file != "..") unlink($path."/".$file);
              }

              closedir($handled);
          }

          //get new orders count
          $q = db_query("select count(*) from ".ORDERS_TABLE." WHERE statusID=".(int)CONF_NEW_ORDER_STATUS);
          $n = db_fetch_row($q);
          $smarty->assign("new_orders_count", $n[0]);

          $past = time() - CONF_ONLINE_EXPIRE * 60;
          $result = db_query("select count(*) from ".ONLINE_TABLE." WHERE time > '".xEscSQL($past)."'");
          $u = db_fetch_row($result);
          $smarty->assign("online_users", $u[0]);

          if (isset($sub)) $smarty->assign("current_sub", $sub);
          $smarty->assign("admin_departments", $admin_departments);
          $smarty->assign("admin_departments_count", $file_count);

          //show Smarty output
          //$smarty->display("admin/index.tpl.html");
		  if (isset($sub) && $sub == "subtpl") {$smarty->display("index.subtpl.html");}
		  else {$smarty->display("index.tpl.html");}
  }
  }
?>