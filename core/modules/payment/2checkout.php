<?php
        // 2checkout payment module
        // www.2checkout.com

/**
 * @connect_module_class_name C2checkout
 *
 */

class C2checkout extends PaymentModule
{

        function _initVars(){

                $this->title                 = C2CHECKOUT_TTL;
                $this->description         = C2CHECKOUT_DSCR;
                $this->sort_order         = 5;
                $this->Settings[] = "CONF_PAYMENTMODULE_2CHECKOUT_ID";
                $this->Settings[] = "CONF_PAYMENTMODULE_2CO_USD_CURRENCY";
        }

        function _initSettingFields(){

                $this->SettingsFields['CONF_PAYMENTMODULE_2CHECKOUT_ID'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => C2CHECKOUT_CFG_ID_TTL,
                        'settings_description'         => C2CHECKOUT_CFG_ID_DSCR,
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_2CO_USD_CURRENCY'] = array(
                        'settings_value'                 => '0',
                        'settings_title'                         => C2CHECKOUT_CFG_USD_CURRENCY_TTL,
                        'settings_description'         => C2CHECKOUT_CFG_USD_CURRENCY_DSCR,
                        'settings_html_function'         => 'setting_CURRENCY_SELECT(',
                        'sort_order'                         => 1,
                );
        }

        function after_processing_html( $orderID )
        {
                $order = ordGetOrder( $orderID );

                if ( $this->_getSettingValue('CONF_PAYMENTMODULE_2CO_USD_CURRENCY') > 0 )
                {
                        $TWOCOcurr = currGetCurrencyByID ( $this->_getSettingValue('CONF_PAYMENTMODULE_2CO_USD_CURRENCY') );
                        $TWOCOcurr_rate = $TWOCOcurr["currency_value"];
                }
                if (!isset($TWOCOcurr) || !$TWOCOcurr)
                {
                        $TWOCOcurr_rate = 1;
                }

                $order_amount = round( 100 * $order["order_amount"] * $TWOCOcurr_rate ) / 100;

                $res = "";

                $res .=
                        "<table width='100%'>\n".
                        "        <tr>\n".
                        "                <td align='center'>\n".
                        "<form method='POST' name='two_check_out_form' action='https://www.2checkout.com/2co/buyer/purchase'>\n".
                        "<input type=\"hidden\" name=\"sid\" value=\"".$this->_getSettingValue('CONF_PAYMENTMODULE_2CHECKOUT_ID')."\">\n".
                        "<input type=\"hidden\" name=\"total\" value=\"".$order_amount."\">\n".
                        "<input type=\"hidden\" name=\"cart_order_id\" value=\"".$orderID."\">\n".

                        "<input type=\"hidden\" name=\"card_holder_name\" value=\"".$order["billing_firstname"]." ".$order["billing_lastname"]."\">\n".
                        "<input type=\"hidden\" name=\"street_address\" value=\"".$order["billing_address"]."\">\n".
                        "<input type=\"hidden\" name=\"city\" value=\"".$order["billing_city"]."\">\n".
                        "<input type=\"hidden\" name=\"state\" value=\"".$order["billing_state"]."\">\n".
                        "<input type=\"hidden\" name=\"country\" value=\"".$order["billing_country"]."\">\n".
//                                "<input type=\"hidden\" name=\"phone\" value=\"".$order["billing_phone"]."\">\n".
                        "<input type=\"hidden\" name=\"email\" value=\"".$order["customer_email"]."\">\n".

//                                "<input type=\"hidden\" name=\"ship_name\" value=\"".$order["shipping_firstname"]." ".$order["shipping_lastname"]."\">\n".
                        "<input type=\"hidden\" name=\"ship_street_address\" value=\"".$order["shipping_address"]."\">\n".
                        "<input type=\"hidden\" name=\"ship_city\" value=\"".$order["shipping_city"]."\">\n".
                        "<input type=\"hidden\" name=\"ship_state\" value=\"".$order["shipping_state"]."\">\n".
                        "<input type=\"hidden\" name=\"ship_country\" value=\"".$order["shipping_country"]."\">\n".

//                uncomment following line to enable DEMO mode
//                                "<input type=\"hidden\" name=\"demo\" value=\"Y\">\n".

                        "<input type=\"hidden\" name=\"c_prod\" value=\"ShopCMS order\">\n".
                        "<input type=\"hidden\" name=\"id_type\" value=\"2\">\n".

                        "<input type=\"submit\" value=\"".C2CHECKOUT_TXT_1."\">\n".

                        "                </form>\n".

                        "                </td>\n".
                        "        </tr>\n".
                        "</table>";

//                                "<script>document.two_check_out_form.submit();</script>";

//echo "<pre>".str_replace("<", "&lt;", $res);

                return $res;
        }
}
?>