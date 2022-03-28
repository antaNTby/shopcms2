<?

if (count($_POST)>0||count($_GET)>0){


$_GET['order4_confirmation_quick']="yes";
$_GET['order_success']="yes";
$_GET['payment_status']=2;
if (isset($_POST['inv_id'])){$_GET['paymentMethodID']=7;$_GET['orderID']=$_POST['inv_id'];}
elseif(isset($_POST['LMI_PAYMENT_NO'])){$_GET['paymentMethodID']=8;$_GET['orderID']=$_POST['LMI_PAYMENT_NO'];}
require_once("index.php");

}
else
echo "Yes";?>