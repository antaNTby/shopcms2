<?php
        /**
         * @connect_module_class_name Chronopay
         *
         */
        class Chronopay extends PaymentModule {

                var $processing_url = 'https://secure.chronopay.com/index_shop.cgi';

                function _initVars(){

                        $this->title = CHRONOPAY_TTL;
                        $this->description = CHRONOPAY_DSCR;
                        $this->sort_order = 1;

                        $this->Settings = array(
                                        'CONF_CHRONOPAY_PRODUCT_ID',
                                        'CONF_CHRONOPAY_CURCODE',
                                );
                }

                function _initSettingFields(){

                        $this->SettingsFields['CONF_CHRONOPAY_PRODUCT_ID'] = array(
                                'settings_value'                 => '',
                                'settings_title'                         => CHRONOPAY_CFG_PRODUCT_ID_TTL,
                                'settings_description'         => CHRONOPAY_CFG_PRODUCT_ID_DSCR,
                                'settings_html_function'         => 'setting_TEXT_BOX(0,',
                                'sort_order'                         => 1,
                        );
                        $this->SettingsFields['CONF_CHRONOPAY_CURCODE'] = array(
                                'settings_value'                 => '',
                                'settings_title'                         => CHRONOPAY_CFG_CURCODE_TTL,
                                'settings_description'         => CHRONOPAY_CFG_CURCODE_DSCR,
                                'settings_html_function'         => 'setting_CURRENCY_SELECT(',
                                'sort_order'                         => 1,
                        );
                }

                function after_processing_html( $orderID ){

                        $res = '';

                        $order = ordGetOrder( $orderID );
                        $order_amount = roundf(PaymentModule::_convertCurrency($order['order_amount'],0,$this->_getSettingValue('CONF_CHRONOPAY_CURCODE')));

                        $currency = currGetCurrencyByID($this->_getSettingValue('CONF_CHRONOPAY_CURCODE'));

                        $zone_iso2 = $order['billing_state'];

                        $countries = cnGetCountries(array('offset'=>0,'CountRowOnPage'=>1000000), $count_row);

                        foreach ($countries as $country){

                                if($country['country_name'] == $order['billing_country']){

                                        $country_iso3 = $country['country_iso_3'];
                                        $zones = znGetZones($country['countryID']);

                                        foreach ($zones as $zone){

                                                if($zone['zone_name']==$zone_iso2){

                                                        $zone_iso2 = $zone['zone_code'];
                                                        break;
                                                }
                                        }
                                        break;
                                }
                        }

                        $post_1=array(
                                'product_id' => $this->_getSettingValue('CONF_CHRONOPAY_PRODUCT_ID'),
                                'product_name' => CONF_SHOP_NAME,
                                'product_price' => $order_amount,
                                'product_price_currency' => $currency['currency_iso_3'],

                                'f_name' => $order['billing_firstname'],
                                's_name' => $order['billing_lastname'],
                                'street' => $order['billing_address'],
                                'city' => $order['billing_city'],
                                'state' => $zone_iso2,
                                'country' => $country_iso3,
                                'email' => $order['customer_email'],

                                'cb_url' => getTransactionResultURL('success'),
                                'cb_type' => 'P',
                                'decline_url' => getTransactionResultURL('failure'),
                        );

      $hidden_fields_html = '';
      reset($post_1);

      while(list($k,$v)=each($post_1)){

                                $hidden_fields_html .= '<input type="hidden" name="'.$k.'" value="'.$v.'" />'."\n";
      }

                        $res = '
                                <form method="post" action="'.xHtmlSpecialChars($this->processing_url).'" style="text-align:center;">
                                        '.$hidden_fields_html.'
                                        <input type="submit" value="'.CHRONOPAY_TXT_SUBMIT.'" />
                                </form>
                                ';

                        return $res;
                }
        }
?>
