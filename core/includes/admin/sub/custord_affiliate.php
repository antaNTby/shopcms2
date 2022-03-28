<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        if (CONF_BACKEND_SAFEMODE && isset($_POST['fACTION'])) //this action is forbidden when SAFE MODE is ON
        {
                Redirect( isset($_POST['fREDIRECT'])?set_query('&safemode=yes', $_POST['fREDIRECT']):set_query('&safemode=yes') );
        }

        if (!strcmp($sub, "affiliate")) //show registered customers list
        {
                $sub_page = isset($_GET['sub_page'])?$_GET['sub_page']:'';

                $smarty->assign('CurrencyISO3', currGetAllCurrencies());

                $error_date_format = false;

                if(isset($_POST['fACTION'])){

                        switch ($_POST['fACTION']){
                                case 'ENABLE_AFFILIATE':
                                        settingCallHtmlFunction( 'CONF_AFFILIATE_PROGRAM_ENABLED' );
                                        Redirect($_POST['fREDIRECT']);
                                        break;
                                case 'SAVE_SETTINGS':
                                        $_POST['settingCONF_AFFILIATE_AMOUNT_PERCENT'] = floatval($_POST['settingCONF_AFFILIATE_AMOUNT_PERCENT']);
                                        if(!($_POST['settingCONF_AFFILIATE_AMOUNT_PERCENT']>=0 && $_POST['settingCONF_AFFILIATE_AMOUNT_PERCENT']<=100)){

                                                $smarty->assign('ErrorPercent', true);
                                                unset($_POST['save']);
                                                break;
                                        }
                                        settingCallHtmlFunction( 'CONF_AFFILIATE_EMAIL_NEW_COMMISSION' );
                                        settingCallHtmlFunction( 'CONF_AFFILIATE_EMAIL_NEW_PAYMENT' );
                                        settingCallHtmlFunction( 'CONF_AFFILIATE_AMOUNT_PERCENT' );
                                        Redirect($_POST['fREDIRECT']);
                                        break;
                                case 'NEW_PAYMENT':
                                        #check date
                                        if(!isTemplateDate($_POST['NEW_PAYMENT']['xDate'], CONF_DATE_FORMAT)){

                                                $smarty->assign('NEW_PAYMENT', html_spchars($_POST['NEW_PAYMENT']));
                                                $smarty->assign('error_new_payment', AFFP_MSG_ERROR_DATE_FORMAT);
                                                break;
                                        }else {

                                                $xDate = TransformTemplateToDATE($_POST['NEW_PAYMENT']['xDate'], CONF_DATE_FORMAT).date(" H:i:s");
                                        }

                                        #check user
                                        $p_customerID = regGetIdByLogin($_POST['NEW_PAYMENT']['customerID']);
                                        if(!$p_customerID){

                                                $smarty->assign('error_new_payment', ERROR_INPUT_LOGIN);
                                                $smarty->assign('NEW_PAYMENT', html_spchars($_POST['NEW_PAYMENT']));
                                                break;
                                        }
                                        $_POST['NEW_PAYMENT']['xDate']                         = $xDate;
                                        $_POST['NEW_PAYMENT']['customerID']         = $p_customerID;
                                        affp_addPayment($_POST['NEW_PAYMENT']);

                                        Redirect(set_query('new_pay=ok', $_POST['fREDIRECT']));
                                        break;
                                case 'DELETE_PAYMENT':
                                        if(!isset($_POST['PAYMENT']['pID']))break;
                                        affp_deletePayment($_POST['PAYMENT']['pID']);
                                        Redirect(set_query('delete_pay=ok', $_POST['fREDIRECT']));
                                        break;
                                case 'SAVE_PAYMENT':
                                        #check date
                                        if(!isTemplateDate($_POST['PAYMENT']['xDate'], CONF_DATE_FORMAT)){

                                                $error_message = AFFP_MSG_ERROR_DATE_FORMAT;
                                                break;
                                        }else
                                                $xDate = TransformTemplateToDATE($_POST['PAYMENT']['xDate'], CONF_DATE_FORMAT);

                                        #check user
                                        if(!regGetIdByLogin($_POST['PAYMENT']['customerLogin'])){

                                                $error_message = ERROR_INPUT_LOGIN;
                                                break;
                                        }else {

                                                $_POST['PAYMENT']['customerID'] = regGetIdByLogin($_POST['PAYMENT']['customerLogin']);
                                                unset($_POST['PAYMENT']['customerLogin']);
                                        }

                                        $_POST['PAYMENT']['Amount'] = isset($_POST['PAYMENT']['Amount'])?round($_POST['PAYMENT']['Amount'],2):0;

                                        $_POST['PAYMENT']['xDate'] = $xDate;
                                        affp_savePayment($_POST['PAYMENT']);
                                        print '
                                                <script language="javascript" type="text/javascript">
                                                <!--
                                                window.opener.document.location.href = window.opener.reloadURL;
                                                window.opener.focus();
                                                window.close();
                                                //-->
                                                </script>
                                                ';
                                        exit(1);
                                        break;
                                case 'NEW_COMMISSION':
                                        #check date
                                        if(!isTemplateDate($_POST['NEW_COMMISSION']['xDate'], CONF_DATE_FORMAT)){

                                                $smarty->assign('NEW_COMMISSION', html_spchars($_POST['NEW_COMMISSION']));
                                                $smarty->assign('error_new_commission', AFFP_MSG_ERROR_DATE_FORMAT);
                                                break;
                                        }else {

                                                $xDateTime = TransformTemplateToDATE($_POST['NEW_COMMISSION']['xDate'], CONF_DATE_FORMAT).date(" H:i:s");
                                        }

                                        #check user
                                        if(!regGetIdByLogin($_POST['NEW_COMMISSION']['customerLogin'])){

                                                $smarty->assign('NEW_COMMISSION', html_spchars($_POST['NEW_COMMISSION']));
                                                $smarty->assign('error_new_commission', ERROR_INPUT_LOGIN);
                                                break;
                                        }else {

                                                $_POST['NEW_COMMISSION']['customerID'] = regGetIdByLogin($_POST['NEW_COMMISSION']['customerLogin']);
                                                $tLogin = $_POST['NEW_COMMISSION']['customerLogin'];
                                                unset($_POST['NEW_COMMISSION']['customerLogin']);
                                        }

                                        $_POST['NEW_COMMISSION']['Amount'] = isset($_POST['NEW_COMMISSION']['Amount'])?sprintf("%.2f", $_POST['NEW_COMMISSION']['Amount']):0;
                                        $_POST['NEW_COMMISSION']['xDateTime'] = $xDateTime;
                                        unset($_POST['NEW_COMMISSION']['xDate']);

                                        #email to customer
                                        $t = '';
                                        $Email = '';
                                        $FirstName = '';
                                        regGetContactInfo($tLogin, $t, $Email, $FirstName, $t, $t, $t);

                                        xMailTxt($Email, AFFP_NEW_COMMISSION, 'customer.affiliate.commission_notifi.tpl.html',
                                                array(
                                                        'customer_firstname' => $FirstName,
                                                        '_AFFP_MAIL_NEW_COMMISSION' => str_replace('{MONEY}', $_POST['NEW_COMMISSION']['Amount'].' '.$_POST['NEW_COMMISSION']['CurrencyISO3'],AFFP_MAIL_NEW_COMMISSION)
                                                        ));

                                        affp_addCommission($_POST['NEW_COMMISSION']);

                                        Redirect(set_query('new_commission=ok', $_POST['fREDIRECT']));
                                        break;
                                case 'DELETE_COMMISSION':
                                        if(!isset($_POST['COMMISSION']['cID']))break;
                                        affp_deleteCommission($_POST['COMMISSION']['cID']);
                                        Redirect(set_query('delete_commission=ok', $_POST['fREDIRECT']));
                                        break;
                                case 'SAVE_COMMISSION':
                                        #check date
                                        if(!isTemplateDate($_POST['COMMISSION']['xDateTime'], CONF_DATE_FORMAT)){

                                                $error_message = AFFP_MSG_ERROR_DATE_FORMAT;
                                                break;
                                        }else
                                                $xDateTime = TransformTemplateToDATE($_POST['COMMISSION']['xDateTime'], CONF_DATE_FORMAT).date(" H:i:s");

                                        #check user
                                        if(!regGetIdByLogin($_POST['COMMISSION']['customerLogin'])){

                                                $error_message = ERROR_INPUT_LOGIN;
                                                break;
                                        }else {

                                                $_POST['COMMISSION']['customerID'] = regGetIdByLogin($_POST['COMMISSION']['customerLogin']);
                                                unset($_POST['COMMISSION']['customerLogin']);
                                        }

                                        $_POST['COMMISSION']['Amount'] = isset($_POST['COMMISSION']['Amount'])?round($_POST['COMMISSION']['Amount'],2):0;

                                        $_POST['COMMISSION']['xDateTime'] = $xDateTime;
                                        affp_saveCommission($_POST['COMMISSION']);
                                        print '
                                                <script language="javascript" type="text/javascript">
                                                <!--
                                                window.opener.document.location.href = window.opener.reloadURL;
                                                window.opener.focus();
                                                window.close();
                                                //-->
                                                </script>
                                                ';
                                        exit(1);
                                        break;
                        }
                }

                switch ($sub_page) {
                        case 'edit_payment':
                                #this part for edit payment
                                if(isset($error_message)){

                                        $smarty->assign('Payment', html_spchars($_POST['PAYMENT']));
                                        $smarty->assign('error_message', $error_message);
                                }else {

                                        $Payment = affp_getPayments('', $_GET['pID']);
                                        $Payment[0]['xDate'] = $Payment[0]['xDate'];
                                        $Payment[0]['customerLogin'] = regGetLoginById($Payment[0]['customerID']);

                                        $smarty->assign('Payment', html_spchars($Payment[0]));
                                }

                                $smarty->display("admin/custord_edit_payment.tpl.html");
                                exit(1);
                                break;

                        case 'edit_commission':
                                #this part for edit commission
                                if(isset($error_message)){

                                        $smarty->assign('Commission', html_spchars($_POST['COMMISSION']));
                                        $smarty->assign('error_message', $error_message);
                                }else {

                                        $Commission = affp_getCommissions('', $_GET['cID']);
                                        $Commission[0]['xDateTime'] = $Commission[0]['xDateTime'];
                                        $Commission[0]['customerLogin'] = regGetLoginById($Commission[0]['customerID']);

                                        $smarty->assign('Commission', html_spchars($Commission[0]));
                                }

                                $smarty->display("admin/custord_edit_commission.tpl.html");
                                exit(1);
                                break;

                        default:
                                #this part will display all tables
                                /**
                                 * check from-date and till-date
                                 */
                                if (isset($_POST['from']))$_GET['from'] = $_POST['from'];
                                if (isset($_POST['till']))$_GET['till'] = $_POST['till'];
                                if (!isset($_GET['from']))$_GET['from'] = '';
                                else $_GET['from'] = rawurldecode($_GET['from']);
                                if (!isset($_GET['till']))$_GET['till']='';
                                else $_GET['till'] = rawurldecode($_GET['till']);

                                $show_tables = false;
                                $CurrDate = TransformDATEToTemplate(date("Y-m-d"));

                                if ($_GET['from']){

                                        if(isTemplateDate($_GET['from']))
                                                $show_tables = true;
                                        else
                                                $error_date_format = true;
                                }elseif(!isset($_POST['from'])){
                                        $_GET['from'] = TransformDATEToTemplate(date("Y-m-01"));
                                }else {
                                        $error_date_format = true;
                                }
                                if ($_GET['till']){

                                        if(isTemplateDate($_GET['till']))
                                                $show_tables = ($show_tables && true);
                                        else {

                                                $show_tables = false;
                                                $error_date_format = true;
                                        }
                                }elseif(!isset($_POST['till'])) {
                                        $_GET['till'] = $CurrDate;
                                        $show_tables = false;
                                }else {

                                        $show_tables = false;
                                        $error_date_format = true;
                                }

                                $XREQUEST_URI         = set_query('&edCustomerID=&safemode=&new_commission=&delete_pay=&delete_commission=&new_pay=&till='.rawurlencode($_GET['till']).'&from='.rawurlencode($_GET['from']));

                                if(isset($show_tables)){

                                        #get payments
                                        if(!isset($_GET['OrderField']))
                                                $_GET['OrderField'] = 'pID';
                                        if(!isset($_GET['OrderDiv']))
                                                $_GET['OrderDiv'] = 'ASC';
                                        if($_GET['OrderField'] == 'Amount')
                                                $_GET['OrderField'] = ' CurrencyISO3 '.$_GET['OrderDiv'].', '.$_GET['OrderField'];
                                        $Payments = affp_getPayments(
                                                '',
                                                '',
                                                TransformTemplateToDATE($_GET['from'], CONF_DATE_FORMAT),
                                                TransformTemplateToDATE($_GET['till'], CONF_DATE_FORMAT),
                                                $_GET['OrderField'].' '.$_GET['OrderDiv']);

                                        #get commissions
                                        if(!isset($_GET['OrderFieldC']))
                                                $_GET['OrderFieldC'] = 'cID';
                                        if(!isset($_GET['OrderDivC']))
                                                $_GET['OrderDivC'] = 'ASC';
                                        if($_GET['OrderFieldC'] == 'Amount')
                                                $_GET['OrderFieldC'] = ' CurrencyISO3 '.$_GET['OrderDivC'].', '.$_GET['OrderFieldC'];
                                        $Commissions = affp_getCommissions(
                                                '',
                                                '',
                                                TransformTemplateToDATE($_GET['from'], CONF_DATE_FORMAT).' 00:00:00',
                                                TransformTemplateToDATE($_GET['till'], CONF_DATE_FORMAT).' 23:59:59',
                                                $_GET['OrderFieldC'].' '.$_GET['OrderDivC']);

                                        $smarty->assign('Payments', html_spchars($Payments));
                                        $smarty->assign('PaymentsNumber', count($Payments));
                                        $smarty->assign('Commissions', html_spchars($Commissions));
                                        $smarty->assign('CommissionsNumber', count($Commissions));
                                }

                                if(isset($_GET['new_pay']))$smarty->assign('newPayStatus', '1');
                                if(isset($_GET['new_commission']))$smarty->assign('newCommissionStatus', '1');
                                if(isset($_GET['delete_pay']))$smarty->assign('delete_payment', 1);
                                if(isset($_GET['delete_commission']))$smarty->assign('delete_commission', 1);
                                $smarty->assign('CurrDate', $CurrDate);
                                $smarty->assign('show_tables', $show_tables);
                                $smarty->assign('from', html_spchars($_GET['from']));
                                $smarty->assign('till', html_spchars($_GET['till']));
                                $smarty->assign('Error_DateFormat', $error_date_format);
                                $smarty->assign('REQUEST_URI', $XREQUEST_URI);
                                $smarty->assign('htmlEmailNewCommission', settingCallHtmlFunction( 'CONF_AFFILIATE_EMAIL_NEW_COMMISSION' ));
                                $smarty->assign('htmlEmailNewPayment', settingCallHtmlFunction( 'CONF_AFFILIATE_EMAIL_NEW_PAYMENT' ));
                                $smarty->assign('htmlEnabledSettings', settingCallHtmlFunction( 'CONF_AFFILIATE_PROGRAM_ENABLED' ));
                                $smarty->assign('htmlAmountPercent', settingCallHtmlFunction( 'CONF_AFFILIATE_AMOUNT_PERCENT' ));
                                $smarty->assign("admin_sub_dpt", "custord_affiliate.tpl.html");

                                if(!isset($_POST['NEW_PAYMENT']))
                                        $smarty->assign('NEW_PAYMENT', array('xDate'=>$CurrDate));
                                if(!isset($_POST['NEW_COMMISSION']))
                                        $smarty->assign('NEW_COMMISSION', array('xDate'=>$CurrDate));
                                if(isset($_GET['edCustomerID']))
                                        $smarty->assign('edCustomerLogin', regGetLoginById(intval($_GET['edCustomerID'])));

                                break;
                }
        }
?>