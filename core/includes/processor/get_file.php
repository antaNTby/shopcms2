<?php
  #####################################
  # ShopCMS: Скрипт интернет-магазина
  # Copyright (c) by ADGroup
  # http://shopcms.ru
  #####################################

  header ("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
  header ("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
  header ("Pragma: no-cache");

  //get order data
  if (isset($_GET["getFileParam"]))
  {
      $getFileParam = cryptFileParamDeCrypt($_GET["getFileParam"], null); //echo $getFileParam;
      $params = explode("&", $getFileParam);
      foreach ($params as $param)
      {
          $param_value = explode("=", $param);

          if (count($param_value) >= 2)
          {
              if ($param_value[0] == "orderID") $orderID = (int)$param_value[1];
              else
                  if ($param_value[0] == "productID") $productID = (int)$param_value[1];
                  else
                      if ($param_value[0] == "customerID") $customerID = (int)$param_value[1];
                      else
                          if ($param_value[0] == "order_time") $order_time = base64_decode($param_value[1]);
          }
      }

  }


  if (isset($_POST["remind_password"]))
  {
      regSendPasswordToUser($_POST["login_to_remind_password"], $smarty_mail);
  }

  $authenticateError = false;

  if (isset($_POST["submitLoginAndPassword"]))
  {
      $authenticateError = !regAuthenticate($_POST["login"], $_POST["password"]);
  }

  //authorized login check
  $relaccess = checklogin();

  if (!isset($customerID)) $customerID = 0;

  if (!isset($_SESSION["log"]) && $customerID != -1) //unauthorized

  {
?>
                <form name='MainForm' method=POST>
                        <table>
        <?php
      if ($authenticateError)
      {
?>
                                <tr>
                                        <td colspan=2>
                                                <font color=red>
                                                        <b><?php
          echo ERROR_WRONG_PASSWORD;
?></b>
                                                </font>
                                        </td>
                                </tr>
        <?php
      }
?>

                                <tr>
                                        <td colspan=2>
                                                <font class=middle>
                                                        <b><?php
      echo STRING_AUTHORIZATION;
?></b>
                                                </font>
                                        </td>
                                </tr>
                                <tr>
                                        <td>
                                                <?php
      echo CUSTOMER_LOGIN;
?>
                                        </td>
                                        <td>
                                                <input type=text name='login'>
                                        </td>
                                </tr>
                                <tr>
                                        <td>
                                                <?php
      echo CUSTOMER_PASSWORD;
?>
                                        </td>
                                        <td>
                                                <input type=password name='password'>
                                        </td>
                                </tr>
                                <tr>
                                        <td>
                                                <input type=submit name='submitLoginAndPassword' value='<?php
      echo OK_BUTTON;
?>'>
                                        </td>
                                        <td>
                                                &nbsp;
                                        </td>
                                </tr>
                <?php
      if (isset($_POST["remind_password"]))
      {
?>
                                        <tr>
                                                <td colspan=2>
                                                        <b><?php
          echo STRING_PASSWORD_SENT;
?></b>
                                                </td>
                                        </tr>
                <?php
      }
?>
                                <?php
      if ($authenticateError)
      {
?>
                                                <tr>
                                                        <td colspan=2>
                                                                &nbsp;
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td>
                                                                <?php
          echo STRING_FORGOT_PASSWORD_FIX;
?>
                                                        </td>
                                                        <td>
                                                                <input type=text name='login_to_remind_password' value=''>
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td>
                                                                <input type=submit name='remind_password' value='<?php
          echo OK_BUTTON;
?>'>
                                                        </td>
                                                </tr>
                                <?php
      }
?>
                        </table>
                </form>
<?php
  }
  else
  {

      $fileToDownLoad = "";
      $fileToDownLoadShortName = "";
      $res = 0;

      if (!isset($_GET["getFileParam"])) die(ERROR_FORBIDDEN);
      else
      {
          $getFileParam = cryptFileParamDeCrypt($_GET["getFileParam"], null);
          if ($getFileParam == "GetDataBaseSqlScript")
          {
              if (CONF_BACKEND_SAFEMODE != 1 && !in_array(2, $relaccess)) die(ERROR_FORBIDDEN);
              else
              {
                  $fileToDownLoad = "core/temp/database.sql.gz";
                  $fileToDownLoadShortName = "database.sql.gz";
              }
          }
          else
              if ($getFileParam == "GetCustomerExcelSqlScript")
              {
                  if (!in_array(33, $relaccess)) die(ERROR_FORBIDDEN);
                  else
                  {
                      $fileToDownLoad = "core/temp/customers.csv";
                      $fileToDownLoadShortName = "customers.csv";
                  }
              }
              else
                  if ($getFileParam == "GetCSVCatalog")
                  {
                      if (CONF_BACKEND_SAFEMODE != 1 && !in_array(6, $relaccess)) die(ERROR_FORBIDDEN);
                      else
                      {
                          $fileToDownLoad = "core/temp/catalog.csv.gz";
                          $fileToDownLoadShortName = "catalog.csv.gz";
                      }
                  }
                  else
                      if ($getFileParam == "GetFullFileDb")
                      {
                          if (!in_array(2, $relaccess)) die(ERROR_FORBIDDEN);
                          else
                          {
                              $fileToDownLoad = "core/temp/fulldump.sql.gz";
                              $fileToDownLoadShortName = "fulldump.sql.gz";
                          }
                      }
                      else
                          if ($getFileParam == "GetSubscriptionsList")
                          {
                              if (CONF_BACKEND_SAFEMODE != 1 && !in_array(8, $relaccess)) die(ERROR_FORBIDDEN);
                              else
                              {
                                  $fileToDownLoad = "core/temp/subscribers.txt";
                                  $fileToDownLoadShortName = "subscribers.txt";
                              }
                          }
                          else //download ordered products

                          {
                              $params = explode("&", $getFileParam);
                              foreach ($params as $param)
                              {
                                  $param_value = explode("=", $param);

                                  if (count($param_value) >= 2)
                                  {

                                      if ($param_value[0] == "orderID") $orderID = (int)$param_value[1];
                                      else
                                          if ($param_value[0] == "productID") $productID = (int)$param_value[1];
                                          else
                                              if ($param_value[0] == "customerID") $customerID = (int)
                                                      $param_value[1];
                                              else
                                                  if ($param_value[0] == "order_time")
                                                  {
                                                      for ($k = 2; $k < count($param_value); $k++)
                                                      {
                                                          $param_value[1] .= $param_value[$k]."=";

                                                      }
                                                      $order_time = base64_decode($param_value[1]);
                                                  }

                                  }

                              }

                              if (isset($orderID) && isset($productID))
                              {
                                  $res = ordAccessToLoadFile($orderID, $productID, $pathToProductFile, $fileToDownLoadShortName);
                              }
                              else  $res = 4;

                              if ($customerID == -1 && isset($order_time)) //verify order time

                              {
                                  $q = db_query("select order_time from ".ORDERS_TABLE." where orderID=".orderID);
                                  $row = db_fetch_row($q);
                                  if (!$row || $row[0]!=$order_time) $res = 4;
                              }
                              else
                                  if ($customerID == -1) $res = 4;

                              if ($res == 0 && $orderID>0) $fileToDownLoad = $pathToProductFile;
                          }
      }

      if ($res == 0 && strlen($fileToDownLoad) > 0 && file_exists($fileToDownLoad))
      {
          header('Content-type: application/force-download');
          header('Content-Transfer-Encoding: Binary');
          header('Content-length: '.filesize($fileToDownLoad));
          header('Content-disposition: attachment; filename='.basename($fileToDownLoad));
          readfile($fileToDownLoad);
      }
      else
          if ($res == 1) echo ("<font color=red><b>".STRING_COUNT_DOWNLOAD_IS_EXCEEDED_EPRODUCT_DOWNLOAD_TIMES.
                  "</b></font>");
          else
              if ($res == 2) echo ("<font color=red><b>".STRING_AVAILABLE_DAYS_ARE_EXHAUSTED_TO_DOWNLOAD_PRODUCT.
                      "</b></font>");
              else
                  if ($res == 3) echo ("<font color=red><b>".ERROR_FORBIDDEN_TO_ACCESS_FILE_ORDER_IS_NOT_PAYED.
                          "</b></font>");
                  else //if ( $res == 4 )
                           echo ("<font color=red><b>".ERROR_FORBIDDEN."</b></font>");


  }
?>