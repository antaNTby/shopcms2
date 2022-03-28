<?php
    include("core/config/tables.inc.php");
    include("core/config/connect.inc.php");

    mysql_connect(DB_HOST, DB_USER, DB_PASS);
    mysql_select_db(DB_NAME);

    $WMI_ORDER_STATE = $_POST['WMI_ORDER_STATE'];
    $user_id = intval($_POST['user_id']);
    $order_id = intval($_POST['order_id']);

    if($WMI_ORDER_STATE == 'Accepted'){
        $sql = "UPDATE avl_orders SET statusID = 5 WHERE orderID = $order_id";
        mysql_query($sql) or die(mysql_error().'. Ошибка в строке '.__LINE__);

        $q = mysql_query("SELECT o.customer_email, oc.name
                          FROM avl_orders o
                          LEFT JOIN avl_ordered_carts oc USING(orderID)
                          WHERE o.orderID = $order_id") or die(mysql_error().'. Ошибка в строке '.__LINE__);
        $email = @mysql_result($q, 0);
        $name = @mysql_result($q, 0, 1);

        if(strstr($email, '@') && strstr($email, '.')){
        $text = "<strong>Название товара:</strong> $name<br />";

        $res = mail($adminEmail, "Спасибо за покупку карты!", $text,
        		"Return-path: {$email}\n".
        		"From: < $email >\n".
        		"Content-Type: text/html; charset=Windows-1251\n".
        		"Date: ".date("r")."\n" );
        }

        echo 'WMI_RESULT=OK';
    }
?>
