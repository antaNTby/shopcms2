<?
if (count($_POST)>0||count($_GET)>0){

$_GET['order4_confirmation_quick']="yes";
$_GET['order_success']="yes";
$_GET['payment_status']=1;
if (isset($_POST['inv_id'])){$_GET['paymentMethodID']=8;$_GET['orderID']=$_POST['inv_id'];}
elseif(isset($_POST['LMI_PAYMENT_NO'])){$_GET['paymentMethodID']=8;$_GET['orderID']=$_POST['LMI_PAYMENT_NO'];}
require_once("index.php");
if (isset($_GET['orderID'])){
$orderID=$_GET['orderID'];
$order = ordGetOrder( $orderID );

$mySmarty = new Smarty; 
$mySmarty->template_dir = "design/email/";
$invoice = $mySmarty->fetch("success_pay.txt");

}
}
else
echo "Yes";?>