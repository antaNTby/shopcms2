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

                $sub_page = isset($_GET['sub_page'])?$_GET['sub_page']:'';

                $smarty->assign('CurrencyISO3', currGetAllCurrencies());

                $error_date_format = false;

                if(isset($_POST['fACTION'])){

                        switch ($_POST['fACTION']){
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

                                        $_POST['PAYMENT']['Amount'] = isset($_POST['PAYMENT']['Amount'])?round($_POST['PAYMENT']['Amount'], 2):0;

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

                                        $_POST['COMMISSION']['Amount'] = isset($_POST['COMMISSION']['Amount'])?round($_POST['COMMISSION']['Amount'], 2):0;

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
                                case 'CANCEL_CUSTOMER':
                                        affp_cancelRecruitedCustomer($_POST['CUSTOMER']['customerID']);
                                        Redirect($_POST['fREDIRECT']);
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

                                $XREQUEST_URI         = set_query('safemode=&new_commission=&delete_pay=&delete_commission=&new_pay=&till='.rawurlencode($_GET['till']).'&from='.rawurlencode($_GET['from']));

                                if(isset($show_tables)){

                                        #get payments
                                        if(!isset($_GET['OrderField']))
                                                $_GET['OrderField'] = 'pID';
                                        if(!isset($_GET['OrderDiv']))
                                                $_GET['OrderDiv'] = 'ASC';
                                        if($_GET['OrderField'] == 'Amount')
                                                $_GET['OrderField'] = ' CurrencyISO3 '.$_GET['OrderDiv'].', '.$_GET['OrderField'];
                                        $Payments = affp_getPayments(
                                                $customerID,
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
                                                $customerID,
                                                '',
                                                TransformTemplateToDATE($_GET['from'], CONF_DATE_FORMAT).' 00:00:00',
                                                TransformTemplateToDATE($_GET['till'], CONF_DATE_FORMAT).' 23:59:59',
                                                $_GET['OrderFieldC'].' '.$_GET['OrderDivC']);

                                        $smarty->assign('Payments', html_spchars($Payments));
                                        $smarty->assign('PaymentsNumber', count($Payments));
                                        $smarty->assign('Commissions', html_spchars($Commissions));
                                        $smarty->assign('CommissionsNumber', count($Commissions));
                                }

                                $RecruitedCustomers = affp_getRecruitedCustomers($customerID);
                                $smarty->assign('RecruitedCustomersNumber', count($RecruitedCustomers));
                                $smarty->assign('RecruitedCustomers', $RecruitedCustomers);
                                if(isset($_GET['delete_pay']))$smarty->assign('delete_payment', 1);
                                if(isset($_GET['delete_commission']))$smarty->assign('delete_commission', 1);
                                $smarty->assign('CurrDate', $CurrDate);
                                $smarty->assign('from', html_spchars($_GET['from']));
                                $smarty->assign('till', html_spchars($_GET['till']));
                                $smarty->assign('Error_DateFormat', $error_date_format);
                                $smarty->assign('REQUEST_URI', $XREQUEST_URI);
                                $smarty->assign('show_tables', $show_tables);
                                $smarty->assign("admin_sub_dpt", "custord_affiliate.tpl.html");
                                $smarty->assign('edCustomerID', $customerID);

                                break;
                }
?>