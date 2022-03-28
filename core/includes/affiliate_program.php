<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

#handler for new customers by links
if(isset($_GET['refid'])){

        $_RefererLogin = regGetLoginById(intval($_GET['refid']));
        if($_RefererLogin){

                session_register('s_RefererLogin');
                $_SESSION['s_RefererLogin']         = $_RefererLogin;
                $_SESSION['refid']                         = intval($_GET['refid']);
                Redirect(set_query(''));
        }
}

if (  isset($_SESSION["log"]) && (isset($_GET["affiliate"]) || isset($_POST["affiliate"])) && CONF_AFFILIATE_PROGRAM_ENABLED ){

        $SubPage = isset($_GET['sub'])?$_GET['sub']:'balance';
        $fACTION = isset($_POST['fACTION'])?$_POST['fACTION']:'';

        $customerID                                 = regGetIdByLogin( $_SESSION["log"] );
        $affp_CustomersNum                         = affp_getCustomersNum($customerID);

        #post-requests handler
        switch ($fACTION){
                case 'SAVE_SETTINGS':
                        affp_saveSettings($customerID,
                                isset($_POST['EmailOrders']),
                                isset($_POST['EmailPayments']));
                        Redirect(set_query('save_settings=ok'));
                        break;
        }

        #loading data for subpages
        switch ($SubPage){
                case 'balance':
                        $Commissions         = affp_getCommissionsAmount($customerID);
                        $Payments                 = affp_getPaymentsAmount($customerID);
                        $smarty->assign('CommissionsNumber', count($Commissions));
                        $smarty->assign('PaymentsNumber', count($Payments));
                        $smarty->assign('CommissionsAmount', $Commissions);
                        $smarty->assign('PaymentsAmount', $Payments);
                        $smarty->assign('CurrencyISO3', currGetAllCurrencies());
                        break;
                case 'payments_history':
                        $Payments                 = affp_getPayments($customerID);
                        $smarty->assign('PaymentsNumber', count($Payments));
                        $smarty->assign('Payments', html_spchars(affp_getPayments($customerID, '', '', '', 'pID ASC')));
                        break;
                case 'settings':
                        $smarty->assign('SettingsSaved', isset($_GET['save_settings']));
                        $smarty->assign('Settings', affp_getSettings($customerID));
                        break;
                case 'attract_guide':
                        $smarty->assign('_AFFP_STRING_ATTRACT_GUIDE', str_replace(
                                array('{URL}', '{aff_percent}', '{login}'),
                                array('http://'.$_SERVER['HTTP_HOST'].set_query('').'?refid='.$customerID,
                                        CONF_AFFILIATE_AMOUNT_PERCENT, $_SESSION["log"]), AFFP_STRING_ATTRACT_GUIDE));
                        break;

        }

        $smarty->assign('affiliate_customers', $affp_CustomersNum);
        $smarty->assign('SubPage', $SubPage);
        $smarty->assign("main_content_template", "affiliate_program.tpl.html");
}
?>
