<?php

if(isset($_REQUEST['transaction_result']))
    $transaction_result=$_REQUEST['transaction_result'];
    else $transaction_result = null;

                $orderID = null;
                $order = null;
                if(isset($_REQUEST["InvId"])) $orderID = (int)$_REQUEST["InvId"];
                if(isset($_REQUEST["LMI_PAYMENT_NO"])) $orderID = (int)$_REQUEST["LMI_PAYMENT_NO"];
                $order = ordGetOrder( $orderID );
if ($order!=null && $orderID>0){
switch ($transaction_result){

        case 'success':
                $smarty->assign('orderID', $orderID);
                $smarty->assign('TransactionResult', $transaction_result);
                $smarty->assign( "main_content_template", "transaction_result.tpl.html");
                if ($orderID != "" && $order["customerID"] == regGetIdByLogin($_SESSION["log"]))header('Refresh: 6; url=index.php?p_order_detailed='.$orderID);
                break;

        case 'failure':
                $smarty->assign('TransactionResult', $transaction_result);
                $smarty->assign( "main_content_template", "transaction_result.tpl.html");
                break;

        default:  break;
}
}
?>