<?php

    require_once('core/config/robokassa.inc.php');
    
    class Robokassa
    {
        static function GetPaymentUrl($Cost,$Name)
        {
            global $rk_login, $rk_pass1;
            $login = $rk_login;
            $pass1 = $rk_pass1;
            
            //order properties
            $InvId    = 0;        // shop's invoice number (unique for shop's lifetime)
            $InvDesc  = $Name;   // invoice desc
            $OutSumm  = $Cost;   // invoice summ

            //build CRC value
            $crc  = self::GetCRC($login,$OutSumm,$InvId,$pass1);

            // build URL
            $url = "https://merchant.roboxchange.com/Index.aspx?MrchLogin=$login&OutSum=$OutSumm&InvId=$InvId&Desc=$InvDesc&SignatureValue=$crc";

            // print URL if you need
            //echo "<a href='/ru/$url'>Payment link</a>";
            return $url;
        }
        
        private static function GetCRC($login,$OutSumm,$InvId,$pass1)
        {
            //echo  "\n",$login,$OutSumm,$InvId,$pass1,"\n";
            $crc  = md5("$login:$OutSumm:$InvId:$pass1");
            return $crc;   
        }
        
        static function GetPaymentButton($Cost,$Name,$Type=0, $showButton = 1)
        {
            global $rk_login, $rk_pass1, $yaCounterId,$rk_useproxy,$rk_proxyurl;
            
            $TypeImgs =array('crt.gif','buy2.gif');
            
            $method = 'get';
            $sMerchantLogin = $rk_login;
            $nOutSum = $Cost;
            $nInvId = 0;
            $Desc = $Name;
            $sSignatureValue =  self::GetCRC($sMerchantLogin,$nOutSum,$nInvId,$rk_pass1);
            $template_dir = TPL_PATH;//TEMPLATE_GO; !!!need fix
            $BuyStr = ADD_TO_CART_STRING;
            $Img = $TypeImgs[$Type];
            
            $DefaultServerUrl = 'https://merchant.roboxchange.com/Index.aspx';
            if($rk_useproxy) $ServerUrl = $rk_proxyurl;
            else $ServerUrl = $DefaultServerUrl;
             
            $FormCode = <<<EOD
<form action="$ServerUrl" method="$method" name="robokassa" id="robo" onsubmit="yaCounter$yaCounterId.reachGoal('ZAKAZ'); return true;">
    <input type="hidden" name="MrchLogin" value="$sMerchantLogin">
    <input type="hidden" name="OutSum" value="$nOutSum">
    <input type="hidden" name="InvId" value="$nInvId">
    <input type="hidden" id="IncCurrLabel" name="IncCurrLabel" value="">
    <input type="hidden" name="Desc" value="$Desc">
    <input type="hidden" name="SignatureValue" value="$sSignatureValue">
EOD;
            if($showButton == 1)
                $FormCode .= '<input name="robo" type="image" src="'.$template_dir.'/images/'.$Img.'" alt="'.$BuyStr.'" />
</form>';
            else
                $FormCode .= '</form>';
            return $FormCode;
        }
    }
    
    class RobokassaHelper
    {
        static function GetInitCRC($login,$OutSumm,$InvId,$pass1,$UserParams=null)
        {
            $StandartArray = array($login,$OutSumm,$InvId,$pass1);
            $StandartStr = join(':',$StandartArray);
            
            $SourceStr = $StandartStr;
            if(is_array($UserParams))
            {
                $UserSrt = self::GetUserStr($UserParams);
                $SourceStr .= ':'.$UserSrt;
            }
            $InitCRC = md5($SourceStr);
            return $InitCRC;
        }
        
        static function GetResultCRC($OutSum,$InvId,$Pass2,$UserParams)
        {
            $StandartArray = array($OutSumm,$InvId,$Pass2);
            $StandartStr = join(':',$StandartArray);
            $SourceStr = $StandartStr;
            if(is_array($UserParams))
            {
                $UserSrt = self::GetUserStr($UserParams);
                $SourceStr .= ':'.$UserSrt;
            }
            $ResultCRC = md5($SourceStr);
            return $ResultCRC;  
        }
        
        private function GetUserStr($UserParams)
        {
            ksort($UserParams);
            $UserArray = array();
            foreach($UserParams as $key=>$value)
            {
                $UserArray[] = $key.'='.$value;
            }
            $UserSrt = join(':',$UserArray);
            return $UserSrt;
        }
    }
?>
