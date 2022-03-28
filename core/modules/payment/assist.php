<?php
/**
 * @connect_module_class_name CAssist
 *
 */
// Модуль интеграции с платежной системой Ассист
// http://www.assist.ru

class CAssist extends PaymentModule {

        function _initVars(){

                $this->title                 = "Assist";
                $this->description         = "Обработка кредитных карт через систему Assist (www.assist.ru)<br>Возможность приема платежей по кредитным картам и через платежные системы E-Port, Rapida, KreditPilot";
                $this->sort_order         = 2;

                $this->Settings = array(
                                "CONF_PAYMENTMODULE_ASSIST_MERCHANT_ID",
                                "CONF_PAYMENTMODULE_ASSIST_AUTHORIZATION_MODE",
                        );
        }

        function _initSettingFields(){

                $this->SettingsFields['CONF_PAYMENTMODULE_ASSIST_MERCHANT_ID'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => 'Shop_IDP',
                        'settings_description'         => 'Ваш ID в системе Assist',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_ASSIST_AUTHORIZATION_MODE'] = array(
                        'settings_value'                 => 0,
                        'settings_title'                         => 'Режим предварительной авторизации',
                        'settings_description'         => 'Включите эту настройку, если Вы хотите, чтобы Ваш оплата по картам производилась в режиме предварительной авторизации; чтобы работать в нормальном режиме, выключите настройку',
                        'settings_html_function'         => 'setting_CHECK_BOX(',
                        'sort_order'                         => 2,
                );
        }

        function after_processing_html( $orderID )
        {
                $order = ordGetOrder( $orderID );
                //calculate order amount
                $order_amount = round(100*$order["order_amount"] * $order["currency_value"])/100;

                $res = "";
                $res .=
                        "<table width='100%'>\n".
                        "        <tr>\n".
                        "                <td align='center'>\n".

                        "                <FORM NAME=\"form1\" ACTION=\"https://secure.assist.ru/shops/cardpayment.cfm\" METHOD=\"POST\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"Shop_IDP\" VALUE=\"".$this->_getSettingValue('CONF_PAYMENTMODULE_ASSIST_MERCHANT_ID')."\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"Order_IDP\" VALUE=\"".$orderID."\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"Subtotal_P\" VALUE=\"".$order_amount."\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"Delay\" VALUE=\"".$this->_getSettingValue('CONF_PAYMENTMODULE_ASSIST_AUTHORIZATION_MODE')."\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"Language\" VALUE=\"0\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"URL_RETURN_OK\" VALUE=\"".getTransactionResultURL('success')."\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"URL_RETURN_NO\" VALUE=\"".getTransactionResultURL('failure')."\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"Currency\" VALUE=\"".$order["currency_code"]."\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"Comment\" VALUE=\"Оплата заказа #".$orderID."\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"LastName\" VALUE=\"".str_replace("\"","&qout;",$order["billing_lastname"])."\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"FirstName\" VALUE=\"".str_replace("\"","&qout;",$order["billing_firstname"])."\">\n".
//                                "                <INPUT TYPE=\"HIDDEN\" NAME=\"MiddleName\" VALUE=\"Иванович\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"Email\" VALUE=\"".str_replace("\"","&qout;",$order["customer_email"])."\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"Address\" VALUE=\"".str_replace("\"","&qout;",$order["billing_address"])."\">\n".
//                                "                <INPUT TYPE=\"HIDDEN\" NAME=\"Phone\" VALUE=\"\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"Country\" VALUE=\"".str_replace("\"","&qout;",$order["billing_country"])."\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"State\" VALUE=\"".str_replace("\"","&qout;",$order["billing_state"])."\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"City\" VALUE=\"".str_replace("\"","&qout;",$order["billing_city"])."\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"IsFrame\" VALUE=\"1\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"f_Email\" VALUE=\"0\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"CardPayment\" VALUE=\"1\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"WalletPayment\" VALUE=\"0\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"WebMoneyPayment\" VALUE=\"0\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"RapidaPayment\" VALUE=\"1\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"PayCashPayment\" VALUE=\"0\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"EPortPayment\" VALUE=\"1\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"KreditPilotPayment\" VALUE=\"1\">\n".
                        "                <INPUT TYPE=\"HIDDEN\" NAME=\"AssistIDCCPayment\" VALUE=\"1\">\n".
                        "                <INPUT TYPE=\"SUBMIT\" NAME=\"Submit\" VALUE=\"Оплатить заказ по кредитной карте сейчас!\" onclick=\"document.all.Submit.disabled=true; document.form1.submit();\">\n".
                        "                </FORM>\n".
                        "                </td>\n".
                        "        </tr>\n".
                        "</table>";
/*
var_dump($res);
exit;*/
                return $res;



        }
}
?>