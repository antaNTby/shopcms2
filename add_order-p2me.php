<?php

include("core/config/tables.inc.php");
include("core/config/connect.inc.php");
include("core/config/pay2me.inc.php");
include("core/functions/registration_functions.php" );
include("core/functions/functions.php" );
include("core/functions/crypto_functions.php");
include("core/functions/datetime_functions.php");
include("core/includes/database/".DBMS.".php");
include("core/classes/Pay2MeApi.php");

header('Access-Control-Allow-Origin: *');
$email = $_POST['email'];
$itemID = $_POST['itemID'];
$name = $_POST['name'];
$price = $_POST['price'];

$tmp = explode('@', $email);
$login = $tmp[0];

if($itemID > 0 && $name != '' && $email != ''){
    db_connect(DB_HOST,DB_USER,DB_PASS) or die (ERROR_DB_INIT);
    mysql_query("set names {$UserDbCharset}");
    db_select_db(DB_NAME) or die (db_error());

    $sql = mysql_query("SELECT customerID, Login FROM ".CUSTOMERS_TABLE." WHERE Email = '".xEscSQL($email)."'");
    $customerID = @mysql_result($sql, 0);
    $login = @mysql_result($sql, 0, 1);


    if ($customerID < 1){
        $login = $login.'_'.uniqid();
        //regRegisterCustomer($login, uniqid(), $email, $login, $login, 0, array());
        $sql = "insert into ".CUSTOMERS_TABLE.
                    "( Login, cust_password, Email, first_name, last_name, subscribed4news, reg_datetime, CID, custgroupID, affiliateID )".
                    "values( '".$login."', '', '".$email."', ".
                    " '".$first_name."', '".$last_name."', '".$subscribed4news."', '".get_current_time()."', 1, 1, 0 )";
        mysql_query($sql) or die($sql);
        //regAddAddress($login, '', 1, 50, '', '', '', '', $login, '');

        mysql_query("insert into ".CUSTOMER_ADDRESSES_TABLE.
                    " ( first_name, last_name, countryID, zoneID, zip, state, city, ".
                                    " address, customerID ) ".
                    " values( '$login', '$login', 1, NULL, '', '', ".
                            " '', '', $customerID )");
        $customerID = regGetIdByLogin( $login );
    }

    $sql = "INSERT INTO ".ORDERS_TABLE." SET
            customerID = $customerID,
            order_time = NOW(),".
            "statusID = 2, ".
            "shipping_type = 'Электронный товар',".
            "currency_code = 'RUR', ".
            "currency_value = 1,".
            "shipping_cost = '0',".
            "order_amount = '$price',".
            "customer_email = '".xEscSQL($email)."'";
    mysql_query($sql) or die(mysql_error().'. Ошибка в строке '.__LINE__);
    $orderID = mysql_insert_id();
$itemID = xEscSQL($itemID);
    $sql = "INSERT INTO ".ORDERED_CARTS_TABLE." SET
                                       itemID = '$itemID',
                                       orderID = $orderID,
                                       name = '".xEscSQL(iconv('UTF-8', 'Windows-1251',$name))."',
                                       Price = '".$price."',
                                       Quantity = '1'";
                                mysql_query("$sql") or die(mysql_error().'. Ошибка в строке '.__LINE__);

    $q = mysql_query("SELECT UID FROM avl_products WHERE productID = '$itemID'") or die(mysql_error().'. Ошибка в строке '.__LINE__);
    $url = @mysql_result($q);

    $orderReturn = 'http://mapsshop.ru/';

    $redirectUrl = getPaymentUrl($orderID, $name, $price, $orderReturn, $pay2MeApiKey, $pay2MeSecretKey);

    $name = iconv('UTF-8', 'Windows-1251', $name); if(strstr($email, '@') && strstr($email, '.')){
        $text = <<<TEXT
Ваш заказ: <strong>$name</strong> стоимостью: <strong>$price р.</strong> 
<br>
<br>Выберите самый удобный способ оплаты и оплатите карту. 
<br>
<br>После оплаты мы вышлем Вам ссылку на архив с картой и ссылки на видео-инструкции по работе с картой на ваш e-mail. 
<br>
<br>Если у Вас есть вопросы по оплате или  по работе с картой: 
<br>напишите нам на этот <strong>e-mail: <a href="mailto:$adminEmail">$adminEmail</a></strong>
<br>Мы будем рады Вам помочь! 
<br>
<br>
<br>__
<br>С уважением, компания ООО 'МапИнвест'.
<br><a href="https://mapsshop.ru/">https://mapsshop.ru/</a>
<br>Топографические, cпутниковые и векторные GPS карты городов и областей России и мира."
TEXT;

        $res = mail($email, '=?windows-1251?B?'.base64_encode('Спасибо за Ваш заказ в интернет-магазине карт - mapsshop.ru!').'?=', $text,
        		"Return-path: $adminEmail\n".
        		"From: $adminEmail  >\n".
        		"Content-Type: text/html; charset=Windows-1251\n".
        		"Date: ".date("r")."\n" );
    }
}

function getPaymentUrl($orderId, $orderDescription, $orderAmount, $orderReturn, $apiKey, $secretKey)
{
    $payToMeApi = new pay2MeApi($apiKey, $secretKey);
    $response = $payToMeApi->dealCreate($orderId, $orderDescription, $orderAmount, $orderReturn);

    return $response['redirect'];
}

?>
{
    "user_id": "<?= $customerID ?>",
    "order_id": "<?= $orderID ?>",
    "redirect_url": <?= $redirectUrl ?>
}
