<?php
  #####################################
  # ShopCMS: Скрипт интернет-магазина
  # Copyright (c) by ADGroup
  # http://shopcms.ru
  #####################################

  $relaccess = checklogin();

  if (CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(1, $relaccess))) //unauthorized

  {
      die(ERROR_FORBIDDEN);
  }

  if (isset($_GET["optionID"]))
  {
      $optionID = (int)$_GET["optionID"];
      $productID = (int)$_GET["productID"];
  }
  else //_POST

  {
      $optionID = (int)$_POST["optionID"];
      $productID = (int)$_POST["productID"];
  }

  if (isset($_POST["save"]))
  {
      if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON

      {
          Redirect(ADMIN_FILE."?do=configurator&safemode=yes&productID=".$productID."&optionID=".$optionID);
      }

      $variantID_default = "null";
      foreach ($_POST as $key => $value)
      {
          if (strstr($key, "default_radiobutton_"))
          {
              $key = str_replace("default_radiobutton_", "", $key);
              $variantID_default = (int)$key;
          }
      }

      $option_show_times = (int)$_POST["option_show_times"];
      if ($option_show_times <= 0) $option_show_times = 1;

      $data = ScanPostVariableWithId(array("switchOn", "price_surplus"));
      UpdateConfiguriableProductOption($optionID, $productID, $option_show_times, $variantID_default, $data);
  }


  if (isset($_POST["save"]) || isset($_POST["close"]))
  {

      if (isset($_POST["save"]))
      {
          // save values on opener window
          echo "<script type='text/javascript'>";
          echo "window.opener.document.getElementById('spwc').value='1';";
          echo "window.opener.document.getElementById('option_radio_type_".$optionID."_3').click();";
          echo "window.opener.document.getElementById('save_spwc').value='1';";
          echo "window.opener.document.getElementById('MainForm').submit();";
          echo "</script>";
      }

      echo ("<script type='text/javascript'>");
      echo ("window.close();");
      echo ("</script>");
      exit;
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel=STYLESHEET href="data/admin/style.css" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo DEFAULT_CHARSET; ?>">
<title><?php echo ADMIN_CONFIGURATOR_TITLE; ?></title>
<script type="text/javascript">
<!--
function checkboxGroup(_GroupBoxID){

        this.GroupBoxID = _GroupBoxID
        this.BoxCollection = Array();

        this.addBox = function(_ID, _Settings){

                _Obj = document.getElementById(_ID)
                this.BoxCollection.push(_Obj)
                _Obj.spNum = _Settings["spNum"]
                _Obj.GroupObj = this
                eval(_Settings["evalCode"])
        }

        this.changeState = function(){

                var pObj  = document.getElementById(this.GroupBoxID)
                for(var i=0; i<this.BoxCollection.length; i++){

                        this.BoxCollection[i].checked = !pObj.checked
                        this.BoxCollection[i].click()
                }
        }

        this.checkState = function(){

                var noChecked = true
                for(var i=0; i<this.BoxCollection.length; i++){

                        if(this.BoxCollection[i].checked){
                                noChecked = false
                                break
                        }
                }
                if(noChecked){

                        var pObj  = document.getElementById(this.GroupBoxID)
                        pObj.checked = false
                }
        }
}

var chbCol = new checkboxGroup('id_chbGroup')
//-->
</script>
</head>
<body>
<table class="adn">
<tr class="lineb"><td align="left" colspan="4"><?php
  $optionName = db_query("select name from ".PRODUCT_OPTIONS_TABLE." where optionID=".$optionID);
  $optionNameRow = db_fetch_row($optionName);
  echo ADMIN_CONFIGURATOR_TITLE;
?> "<?php echo $optionNameRow["name"]; ?>"</td></tr>
<?php
  if (isset($_GET["safemode"]))
  {
      echo "<table class=\"adminw\"><tr><td align=\"left\"><table class=\"adn\"><tr>
          <td><img src=\"data/admin/stop2.gif\" align=\"left\" class=\"stop\"></td>
          <td class=\"splin\"><span class=\"error\">".ERROR_MODULE_ACCESS2."</span><br><br>".ERROR_MODULE_ACCESS_DES2."</td>
          </tr></table></td></tr></table>\n";

  }
?>
<form action="<?php echo ADMIN_FILE; ?>?do=configurator" method=post name="configurator_form" id="configurator_form">
<script type="text/javascript">

                function OnClickRadioButton( numberRadio )
                {
                        <?php
  $q = db_query("select count(*) from ".PRODUCTS_OPTIONS_VALUES_VARIANTS_TABLE." where optionID=".$optionID);
  $r = db_fetch_row($q);
  $variant_count = $r[0];

  $q = db_query("select variantID from ".PRODUCTS_OPTIONS_VALUES_VARIANTS_TABLE." where optionID=".$optionID);
  while ($r = db_fetch_row($q))
  {
?>
                                 document.getElementById('default_radiobutton_<?php
      echo $r["variantID"]
?>').checked=
                                                        false;
                        <?php
  }
?>
                        document.getElementById('default_radiobutton_'+numberRadio).checked = true;
                }

                function OnClickCheckButton( numberCheck )
                {
                         document.getElementById('default_radiobutton_'+numberCheck).disabled=
                                !document.getElementById('switchOn_'+numberCheck).checked;
                        document.getElementById('price_surplus_'+numberCheck).disabled=
                                !document.getElementById('switchOn_'+numberCheck).checked;



                        if ( document.getElementById('default_radiobutton_'+numberCheck).disabled )
                                document.getElementById('default_radiobutton_'+numberCheck).checked=false;
                }

</script>

        <?php
  //  if ( document.getElementById('price_surplus_'+numberCheck).disabled )
  //                  document.getElementById('price_surplus_'+numberCheck).value='';
  if ($variant_count != 0)
  {
?>

<tr class="lineb">
<td align="center" valign=middle><input type="checkbox" class="round" id="id_chbGroup" onclick="chbCol.changeState()"></td>
                        <td align="center"><?php
      echo ADMIN_BY_DEF;
?></td>
                        <td align="center"><?php
      echo ADMIN_VALUE;
?></td>
                        <td align="center"><?php
      echo ADMIN_PRICE_SURPLUS;
?></td>
                </tr>


        <?php
      $values = db_query("select option_value, variantID from ".PRODUCTS_OPTIONS_VALUES_VARIANTS_TABLE.
          " where optionID=".$optionID." order by sort_order");

      $q = db_query("select option_show_times, variantID from ".PRODUCT_OPTIONS_VALUES_TABLE." where optionID=".
          $optionID." AND productID=".$productID);

      if ($r = db_fetch_row($q))
      {
          $option_show_times = $r["option_show_times"];
          $variantID_default = $r["variantID"];
      }
      else
      {
          $option_show_times = 1;
          $variantID_default = null;
      }

      $first_row_bool = true;
      while ($value_row = db_fetch_row($values))
      {

          $q = db_query("select price_surplus from ".PRODUCTS_OPTIONS_SET_TABLE." where productID=".
              $productID." AND optionID=".$optionID." AND variantID=".$value_row["variantID"]);
          if ($r = db_fetch_row($q)) $price_surplus = $r["price_surplus"];
          else  $price_surplus = null;

          $q = db_query("select COUNT(*) from ".PRODUCTS_OPTIONS_SET_TABLE." where productID=".$productID.
              " AND optionID=".$optionID." AND variantID=".$value_row["variantID"]);
          $r = db_fetch_row($q);
          $check = ($r[0] != 0);
?>

                <tr>
                        <td align="center" height="26"><input name="switchOn_<?php
          echo $value_row["variantID"]
?>"
                                        type="checkbox" class="round"
                               id="switchOn_<?php
          echo $value_row["variantID"]
?>"
                                >
                        </td>
                        <td align="center"><input name="default_radiobutton_<?php
          echo $value_row["variantID"]
?>" id="default_radiobutton_<?php
          echo $value_row["variantID"]
?>"
                                                        type="radio" class="round" value="<?php
          echo $value_row["variantID"]
?>"
                                <?php
          if ((string )$variantID_default == (string )$value_row["variantID"] // || ($first_row_bool && $variantID_default==null)
              )
          {
?>
                                                checked
                                <?php
              $first_row_bool = false;
          }
?>
                                                onclick='OnClickRadioButton(<?php
          echo $value_row["variantID"]
?>)'

                                                disabled=true
                                        >
                        </td>
                        <td align="center">
                                <?php
          echo $value_row["option_value"]
?>
                        </td>
                        <td align="center">
                                <input name="price_surplus_<?php
          echo $value_row["variantID"]
?>" id="price_surplus_<?php
          echo $value_row["variantID"]
?>"
                                        type="text" value="<?php
          echo $price_surplus
?>" disabled=true class="textp">
                <script type="text/javascript">
                <!--
                chbCol.addBox("switchOn_<?php
          echo $value_row["variantID"]
?>", {spNum:"<?php
          echo $value_row["variantID"]
?>", evalCode: "_Obj.onclick = function(){OnClickCheckButton(this.spNum);        this.GroupObj.checkState()}"});
                <?php
          if ($check)
          {
?>
                document.getElementById('switchOn_<?php
              echo $value_row["variantID"]
?>').click();
                <?php
          }
?>
                //-->
                </script>
                        </td>
                </tr>


        <?php
      }
?>
        </table><table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>
        <div align="left" style="padding: 0 5px;"><?php
      echo ADMIN_OFFER_TO_SELECT;
?> <input name="option_show_times" type="text" value="<?php
      echo $option_show_times
?>" class="textp" size="5"> <?php
      echo ADMIN_OFFER_TIMES;
?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="document.getElementById('configurator_form').submit(); return false" class="inl"><?php
      echo SAVE_BUTTON;
?></a></div>
        <input name="save" type=hidden value=""><input name="close" type=hidden value="">
<input type=hidden name="optionID" value="<?php
      echo $optionID
?>">
<input type=hidden name="productID" value="<?php
      echo $productID
?>">

        <?php
  }
  else
  {
?>
                </table><br><div align="center"><?php
      echo ADMIN_NO_VARIANTS
?>...</div>
        <?php
  }
?>


        </form>
</body>

</html>