<?php
require_once('core/config/robokassa.inc.php');

echo GetPaymentButton($_POST['Cost'], $_POST['Name'], $_POST['order_id'], $_POST['IncCurrLabel']);

function GetCRC($login,$OutSumm,$InvId,$pass1)
{
    //echo  "\n",$login,$OutSumm,$InvId,$pass1,"\n";
    $crc  = md5("$login:$OutSumm:$InvId:$pass1");
    return $crc;   
}

function GetPaymentButton($Cost,$Name, $order_id, $IncCurrLabel)
{
    global $rk_login, $rk_pass1, $yaCounterId,$rk_useproxy,$rk_proxyurl;
    
    
    $method = 'get';
    $sMerchantLogin = $rk_login;
    $nOutSum = $Cost;
    $nInvId = $order_id;
    $Desc = $Name;
    $sSignatureValue =  GetCRC($sMerchantLogin,$nOutSum,$nInvId,$rk_pass1 );
    
    $DefaultServerUrl = 'https://merchant.roboxchange.com/Index.aspx';
    if($rk_useproxy) $ServerUrl = $rk_proxyurl;
    else $ServerUrl = $DefaultServerUrl;
     
    $FormCode = <<<EOD
<form action="$ServerUrl" method="$method" name="robokassa" id="robokassa" onsubmit="yaCounter$yaCounterId.reachGoal('ZAKAZ'); return true;">
<input type="hidden" name="MrchLogin" value="$sMerchantLogin">
<input type="hidden" name="OutSum" value="$nOutSum">
<input type="hidden" name="InvId" value="$nInvId">
<input type="hidden" id="IncCurrLabel" name="IncCurrLabel" value="$IncCurrLabel">
<input type="hidden" name="Desc" value="$Desc">
<input type="hidden" name="SignatureValue" value="$sSignatureValue">
</form>
EOD;
    return $FormCode;
}
?>