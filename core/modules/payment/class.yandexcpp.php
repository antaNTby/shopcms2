<?php
        /**
         * @connect_module_class_name YandexCPP
         *
         */
        class YandexCPP extends PaymentModule {

                function _initVars(){

                        $this->title = YANDEXCPP_TTL;
                        $this->description = YANDEXCPP_DSCR;
                        $this->sort_order = 1;

                        $this->Settings = array(
                                        'CONF_YANDEXCPP_SHOPID',
                                        'CONF_YANDEXCPP_BANKID',
                                        'CONF_YANDEXCPP_TARGETBANKID',
                                        'CONF_YANDEXCPP_MODE',
                                        'CONF_YANDEXCPP_TARGETCURRENCY',
                                        'CONF_YANDEXCPP_TRANSCURRENCY',
                                );
                }

                function _initSettingFields(){

                        $this->SettingsFields['CONF_YANDEXCPP_SHOPID'] = array(
                                'settings_value'                 => '',
                                'settings_title'                         => YANDEXCPP_CFG_SHOPID_TTL,
                                'settings_description'         => YANDEXCPP_CFG_SHOPID_DSCR,
                                'settings_html_function'         => 'setting_TEXT_BOX(0,',
                                'sort_order'                         => 1,
                        );
                        $this->SettingsFields['CONF_YANDEXCPP_BANKID'] = array(
                                'settings_value'                 => '1001',
                                'settings_title'                         => YANDEXCPP_CFG_BANKID_TTL,
                                'settings_description'         => YANDEXCPP_CFG_BANKID_DSCR,
                                'settings_html_function'         => 'setting_TEXT_BOX(0,',
                                'sort_order'                         => 1,
                        );
                        $this->SettingsFields['CONF_YANDEXCPP_TARGETBANKID'] = array(
                                'settings_value'                 => '1001',
                                'settings_title'                         => YANDEXCPP_CFG_TARGETBANKID_TTL,
                                'settings_description'         => YANDEXCPP_CFG_TARGETBANKID_DSCR,
                                'settings_html_function'         => 'setting_TEXT_BOX(0,',
                                'sort_order'                         => 1,
                        );
                        $this->SettingsFields['CONF_YANDEXCPP_MODE'] = array(
                                'settings_value'                 => 'live',
                                'settings_title'                         => YANDEXCPP_CFG_MODE_TTL,
                                'settings_description'         => YANDEXCPP_CFG_MODE_DSCR,
                                'settings_html_function'         => 'setting_SELECT_BOX(YandexCPP::_getModes(),',
                                'sort_order'                         => 1,
                        );
                        $this->SettingsFields['CONF_YANDEXCPP_TARGETCURRENCY'] = array(
                                'settings_value'                 => '643',
                                'settings_title'                         => YANDEXCPP_CFG_TARGETCURRENCY_TTL,
                                'settings_description'         => YANDEXCPP_CFG_TARGETCURRENCY_DSCR,
                                'settings_html_function'         => 'setting_SELECT_BOX(YandexCPP::_getTargetCurrencies(),',
                                'sort_order'                         => 1,
                        );
                        $this->SettingsFields['CONF_YANDEXCPP_TRANSCURRENCY'] = array(
                                'settings_value'                 => '',
                                'settings_title'                         => YANDEXCPP_CFG_TRANSCURRENCY_TTL,
                                'settings_description'         => YANDEXCPP_CFG_TRANSCURRENCY_DSCR,
                                'settings_html_function'         => 'setting_CURRENCY_SELECT(',
                                'sort_order'                         => 1,
                        );
                }

                function after_processing_html( $orderID ){

                        $res = '';

                        $order = ordGetOrder( $orderID );

                        $order_amount = roundf(PaymentModule::_convertCurrency($order['order_amount'],0,$this->_getSettingValue('CONF_YANDEXCPP_TRANSCURRENCY')));

                        $post_1=array(
                                'TargetCurrency' => $this->_getSettingValue('CONF_YANDEXCPP_TARGETCURRENCY'),
                                'currencyID' => $this->_getSettingValue('CONF_YANDEXCPP_TARGETCURRENCY'),
                                'wbp_InactivityPeriod' => '2',
                                'wbp_ShopAddress' => 'wn1.paycash.ru:8828',
                                'wbp_ShopEncryptionKey' => 'hAAAEicBAHV6wr3pySqE3thhKHbjvyf4XCMxKc2nSj2u8K46i0dMIP8Wd2KJHkZuhGMWZGmYAp6wsb3XqZW5HKVpamQt+t9rwGNsSaVfeZb9DM5aodCpIMHhLA8gGPDIiG4+Q15X/7Zm3MJNGavZ8+eWAnlvS1M7c6eeLTNJ0CKIYd1yHXfU',
                                'wbp_ShopKeyID' => '4060341894',
                                'wbp_Version' => '1.0',
                                'wbp_CorrespondentID' => '8994748E663DE6B3C68D2D9931B079C74789D4B4',
                                'BankID' => $this->_getSettingValue('CONF_YANDEXCPP_BANKID'),
                                'TargetBankID' => $this->_getSettingValue('CONF_YANDEXCPP_TARGETBANKID'),
                                'PaymentTypeCD' => 'PC',
                                'ShopID' => $this->_getSettingValue('CONF_YANDEXCPP_SHOPID'),

                                'CustomerNumber' => $orderID,
                                'Sum' => $order_amount,
                                'CustName' => $order['shipping_firstname'].' '.$order['shipping_lastname'],
                                'CustAddr' => '',
                                'CustEMail' => $order['customer_email'],

                                'OrderDetails' => '',
                        );

                        $order_content = ordGetOrderContent( $orderID );
                        foreach ($order_content as $item){

                                $post_1['OrderDetails'] .= $item['name']."\r\n";
                        }


                        $implAddress = array('shipping_country', 'shipping_state', 'shipping_city', 'shipping_address');

                        foreach ($implAddress as $k){

                                if($order[$k]){
                                        $post_1['CustAddr'] .= ', '.$order[$k];
                                }
                        }
                        $post_1['CustAddr'] = substr($post_1['CustAddr'], 1);

      $hidden_fields_html = '';
      reset($post_1);

      while(list($k,$v)=each($post_1)){

                                $hidden_fields_html .= '<input type="hidden" name="'.$k.'" value="'.$v.'" />'."\n";
      }

      $processing_url = $this->_getSettingValue('CONF_YANDEXCPP_MODE')=='test'?'http://demomoney.yandex.ru/select-wallet.xml':'http://money.yandex.ru/select-wallet.xml';

                        $res = '
                                <form method="post" action="'.xHtmlSpecialChars($processing_url).'" style="text-align:center;" id="payform">
                                        '.$hidden_fields_html.'
                                </form><table cellspacing="0" cellpadding="0" class="fsttab"><tr><td><table cellspacing="0" cellpadding="0" class="sectb"><tr><td><a href="#" onclick="document.getElementById(\'payform\').submit(); return false">'.STRING_PAY_NOW.'</a></td></tr></table></td></tr></table>';

                        return $res;
                }

                function _getModes(){
                        return YANDEXCPP_TXT_TESTMODE.':test,'.YANDEXCPP_TXT_LIVEMODE.':live';
                }

                function _getTargetCurrencies(){
                        return YANDEXCPP_TXT_RUR.':643,'.YANDEXCPP_TXT_DEMORUR.':10643';
                }
        }
?>
