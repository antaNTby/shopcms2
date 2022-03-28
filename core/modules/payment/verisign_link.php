<?php
// VeriSign Link payment module
// http://www.verisign.com

/**
 * @connect_module_class_name CVeriSignLink
 *
 */
class CVeriSignLink extends PaymentModule {

        function _initVars(){

                $this->title                 = CVERISIGNLINK_TTL;
                $this->description         = CVERISIGNLINK_DSCR;
                $this->sort_order         = 5;

                $this->Settings = array(
                                "CONF_PAYMENTMODULE_VERISIGNLINK_LOGIN",
                                "CONF_PAYMENTMODULE_VERISIGNLINK_PARTNER",
                                "CONF_PAYMENTMODULE_VERISIGNLINK_TRANSTYPE",
                                "CONF_PAYMENTMODULE_VERISIGNLINK_USD_CURRENCY"
                        );
        }

        function _initSettingFields(){

                $this->SettingsFields['CONF_PAYMENTMODULE_VERISIGNLINK_LOGIN'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => CVERISIGNLINK_CFG_LOGIN_TTL,
                        'settings_description'         => CVERISIGNLINK_CFG_LOGIN_DSCR,
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_VERISIGNLINK_PARTNER'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => CVERISIGNLINK_CFG_PARTNER_TTL,
                        'settings_description'         => CVERISIGNLINK_CFG_PARTNER_DSCR,
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_VERISIGNLINK_TRANSTYPE'] = array(
                        'settings_value'                 => 'S',
                        'settings_title'                         => CVERISIGNLINK_CFG_TRANSTYPE_TTL,
                        'settings_description'         => CVERISIGNLINK_CFG_TRANSTYPE_DSCR,
                        'settings_html_function'         => 'setting_SELECT_BOX(CVeriSignLink::getTranstypeOptions(),',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_VERISIGNLINK_USD_CURRENCY'] = array(
                        'settings_value'                 => '0',
                        'settings_title'                         => CVERISIGNLINK_CFG_USD_CURRENCY_TTL,
                        'settings_description'         => CVERISIGNLINK_CFG_USD_CURRENCY_DSCR,
                        'settings_html_function'         => 'setting_CURRENCY_SELECT(',
                        'sort_order'                         => 1,
                );
        }

        function getTranstypeOptions(){

                return array(
                        array(
                                'title' => CVERISIGNLINK_TXT_GETTRANSTYPEOPTIONS_1,
                                'value' => 'S',
                                ),
                        array(
                                'title' => CVERISIGNLINK_TXT_GETTRANSTYPEOPTIONS_2,
                                'value' => 'A',
                                ),
                        );
        }

        function after_processing_html( $orderID )
        {
                $order = ordGetOrder( $orderID );

                //get order amount
                if ( $this->_getSettingValue('CONF_PAYMENTMODULE_VERISIGNLINK_USD_CURRENCY') > 0 )
                {
                        $curr = currGetCurrencyByID ( $this->_getSettingValue('CONF_PAYMENTMODULE_VERISIGNLINK_USD_CURRENCY') );
                        $curr_rate = $curr["currency_value"];
                }
                if (!isset($curr) || !$curr)
                {
                        $curr_rate = 1;
                }

                $order_amount = roundf( $order["order_amount"] * $curr_rate );

                //get billing country ISO 2-chars code
                $q = db_query("select country_iso_3 from ".COUNTRIES_TABLE." where country_name = '".$order["billing_country"]."';");
                $row = db_fetch_row($q);
                if ($row)
                {
                        $bcountry = $row[0];
                }
                else
                {
                        $bcountry = "";
                }

                $res = "";

                $res .=
                        "<table width='100%'>\n".
                        "        <tr>\n".
                        "                <td align='center'>\n".
                        "<form method='POST' name='verisignLINKform' action='https://payments.verisign.com/payflowlink'>\n".
                        "<input type=\"hidden\" name=\"LOGIN\" value=\"".$this->_getSettingValue('CONF_PAYMENTMODULE_VERISIGNLINK_LOGIN')."\">\n".
                        "<input type=\"hidden\" name=\"PARTNER\" value=\"".$this->_getSettingValue('CONF_PAYMENTMODULE_VERISIGNLINK_PARTNER')."\">\n".
                        "<input type=\"hidden\" name=\"AMOUNT\" value=\"".$order_amount."\">".
                        "<input type=\"hidden\" name=\"TYPE\" value=\"".$this->_getSettingValue('CONF_PAYMENTMODULE_VERISIGNLINK_TRANSTYPE')."\">\n".
                        "<input type=\"hidden\" name=\"DESCRIPTION\" value=\"Order #$orderID\">\n".
                        "<input type=\"hidden\" name=\"NAME\" value=\"".$order["billing_firstname"]." ".$order["billing_lastname"]."\">\n".
                        "<input type=\"hidden\" name=\"ADDRESS\" value=\"".str_replace("\n","",$order["billing_address"])."\">\n".
                        "<input type=\"hidden\" name=\"CITY\" value=\"".$order["billing_city"]."\">\n".
                        "<input type=\"hidden\" name=\"STATE\" value=\"".$order["billing_state"]."\">\n".
                        "<input type=\"hidden\" name=\"COUNTRY\" value=\"".$bcountry."\">\n".
                        "<input type=\"hidden\" name=\"EMAIL\" value=\"".$order["customer_email"]."\">\n".
//                                "<input type=\"hidden\" name=\"PHONE\" value=\"".$order["billing_city"]."\">\n".
//                                "<input type=\"hidden\" name=\"FAX\" value=\"".$order["billing_state"]."\">\n".

                        "<input type=\"submit\" value=\"".CVERISIGNLINK_TXT_AFTER_PROCESSING_HTML_1."\">\n".

                        "                </form>\n".

                        "                </td>\n".
                        "        </tr>\n".
                        "</table>";

                        // xMailTxtHTMLDATA($order["customer_email"], "VeriSign payment", $res);


                return $res;
        }
}
?>