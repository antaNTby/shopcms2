<?php
    /**
    * Файл отвечает за:
    * - работу с API Onpay (process CHECK&PAY-requests);
    * - генерацию ссылок для формы оплаты
    */

    require_once('core/config/tables.inc.php');
    require_once('core/config/connect.inc.php');
    require_once('core/config/onpay.inc.php');
    require_once('core/classes/class.onpay.php');

    if(!isset($_REQUEST['type'])) return;

    $type = $_REQUEST['type'];
    $valid_types = array('url','check','pay');
    if(!in_array($type,$valid_types)) return;

    define('ORDER_STATUS_NEW', 2);
    define('ORDER_STATUS_DELIVERED', 5);

    class Logger
    {
        protected $LogFilename;
        public function __construct($LogFilename)
        {
            $this->LogFilename = $LogFilename;
        }

        public function LogRequest($request)
        {
            file_put_contents($this->LogFilename,'REQ>>>'.http_build_query($request)."\n",FILE_APPEND);
        }

        public function LogResponse($response)
        {
            file_put_contents($this->LogFilename,'RESP<<<'.$response."\n",FILE_APPEND);
        }
    }

    $Logger = new Logger('logs/onpay.txt');

    if($type=='url')
    {
        $PaymentLinkParams = new OnpayPaymentUrlParameters();
        $PaymentLinkParams->pay_mode = OnPayPayMode::Fix;
        $OnPay = new Onpay($onpay_login, $onpay_secret_key, $onpay_use_md5_sign, $PaymentLinkParams);
        $response = $OnPay->getPaymentUrl($_REQUEST);
        echo $response;
        return;
    }
    else
    {
        function OnCkeck($request)
        {
            DbConnect();
            $order_status = GetOrderStatus($request['pay_for']);
            if(!$order_status)  return OnpayCheckResponse::Reject('Неизвестный код заказа.');
            if($order_status==ORDER_STATUS_DELIVERED)  return OnpayCheckResponse::Reject('Заказ уже оплачен.');
            if($order_status!=ORDER_STATUS_NEW)  return OnpayCheckResponse::Reject('Неверный статус заказа.');
            return OnpayCheckResponse::Accept();
        }

        function OnPay($request)
        {
            DbConnect();
            $order_id = $request['pay_for'];
            $order_status = GetOrderStatus($order_id);
            if(!$order_status)  return OnpayPayResponse::InvalidParams('Неизвестный код заказа.');
            if($order_status==ORDER_STATUS_DELIVERED)  return OnpayPayResponse::InvalidParams('Заказ уже оплачен.');
            if($order_status!=ORDER_STATUS_NEW)  return OnpayPayResponse::InvalidParams('Неверный статус заказа.');

            CloseOrder($order_id);
            return OnpayPayResponse::Accept();
        }

        $PaymentLinkParams = new OnpayPaymentUrlParameters();
        $PaymentLinkParams->pay_mode = OnPayPayMode::Fix;
        $CheckCallable = 'OnCkeck';
        $PayCallable = 'OnPay';

        $Logger->LogRequest($_REQUEST);
        $OnPay = new Onpay($onpay_login, $onpay_secret_key, $onpay_use_md5_sign, $PaymentLinkParams,$onpay_api_request_type, $CheckCallable,$PayCallable);

        $response = $OnPay->processApiRequest($_REQUEST);
        $Logger->LogResponse($response);
        echo $response;
        return;
    }


    function  DbConnect()
    {
        $conn = mysql_connect(DB_HOST, DB_USER, DB_PASS);
        if(!$conn) throw new Exception('Cant connect to Datebase');
        $res = mysql_select_db(DB_NAME);
        if(!$res) throw new Exception('Cant select database');

        $sql_query = 'SET NAMES utf8';
        $res = mysql_query($sql_query);
        if(!$res) throw new Exception();
    }


    function GetOrderStatus($order_id)
    {
        $sql_query = 'SELECT statusID FROM '.ORDERS_TABLE.' WHERE orderID = '.$order_id;
        $res = mysql_query($sql_query);
        $order_status = mysql_result($res,0);
        if(!$order_status) throw new Exception();
        return $order_status;
    }

    function CloseOrder($order_id)
    {
        UpdateOrderState($order_id, ORDER_STATUS_DELIVERED);
        SendNotifyMail($order_id);
    }

    function UpdateOrderState($order_id,$order_status)
    {
        $sql_query = 'UPDATE '.ORDERS_TABLE.' SET statusID = '.$order_status.' WHERE orderID = '.$order_id;
        $res = mysql_query($sql_query);
        if(!$res) throw new Exception('Cant update order status.');
    }


    function SendNotifyMail($order_id)
    {
        global $adminEmail;

        $to_email = $adminEmail;;
        $sql_query = "SELECT o.customer_email, oc.name FROM avl_orders o LEFT JOIN avl_ordered_carts oc USING(orderID) WHERE o.orderID = $order_id";
        $res = mysql_query($sql_query);
        if(!$res) throw new Exception();
        $row = mysql_fetch_assoc($res);
        if(!$row) throw new Exception();
        $email = $row['customer_email'];
        $name = $row['name'];
        if((!strstr($email, '@')) || (!strstr($email, '.'))) throw new Exception('Invalid email.');
        $text = "<strong>Название товара:</strong> $name<br />";
        $res = mail($to_email, "Спасибо за покупку карты!", $text,
            "Return-path: {$email}\n".
            "From: \"$email\" <$email>\n".
            "Content-Type: text/html; charset=utf-8\n".
            "Date: ".date("r")."\n" );
        if(!$res) throw new Exception('Cant send email to:'.$email);
    }

?>
