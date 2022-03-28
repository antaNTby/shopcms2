<?php
        /**
         * @connect_module_class_name ROBOXchange
         *
         */
        class ROBOXchange extends PaymentModule {

                function _initVars(){

                        $this->title = ROBOXCHANGE_TTL;
                        $this->description = ROBOXCHANGE_DSCR;
                        $this->sort_order = 1;

                        $this->Settings = array(
                                        'CONF_ROBOXCHANGE_MERCHANTLOGIN',
                                        'CONF_ROBOXCHANGE_MERCHANTPASS1',
                                        'CONF_ROBOXCHANGE_MERCHANTPASS2',
                                        'CONF_ROBOXCHANGE_LANG',
              //                          'CONF_ROBOXCHANGE_ROBOXCURRENCY',
                                        'CONF_ROBOXCHANGE_SHOPCURRENCY',
                                        'CONF_ROBOXCHANGE_STATUS_AFTER_PAY'
                                );
                }

                function _initSettingFields(){

                        $this->SettingsFields['CONF_ROBOXCHANGE_MERCHANTLOGIN'] = array(
                                'settings_value'                 => '',
                                'settings_title'                         => ROBOXCHANGE_CFG_MERCHANTLOGIN_TTL,
                                'settings_description'         => ROBOXCHANGE_CFG_MERCHANTLOGIN_DSCR,
                                'settings_html_function'         => 'setting_TEXT_BOX(0,',
                                'sort_order'                         => 1,
                        );
                        $this->SettingsFields['CONF_ROBOXCHANGE_MERCHANTPASS1'] = array(
                                'settings_value'                 => '',
                                'settings_title'                         => ROBOXCHANGE_CFG_MERCHANTPASS1_TTL,
                                'settings_description'         => ROBOXCHANGE_CFG_MERCHANTPASS1_DSCR,
                                'settings_html_function'         => 'setting_TEXT_BOX(0,',
                                'sort_order'                         => 1,
                        );
                        $this->SettingsFields['CONF_ROBOXCHANGE_MERCHANTPASS2'] = array(
                                'settings_value'                 => '',
                                'settings_title'                         => ROBOXCHANGE_CFG_MERCHANTPASS2_TTL,
                                'settings_description'         => ROBOXCHANGE_CFG_MERCHANTPASS2_DSCR,
                                'settings_html_function'         => 'setting_TEXT_BOX(0,',
                                'sort_order'                         => 1,
                        );
                        $this->SettingsFields['CONF_ROBOXCHANGE_LANG'] = array(
                                'settings_value'                 => '',
                                'settings_title'                         => ROBOXCHANGE_CFG_LANG_TTL,
                                'settings_description'         => ROBOXCHANGE_CFG_LANG_DSCR,
                                'settings_html_function'         => 'setting_SELECT_BOX(ROBOXchange::_getLanguages(),',
                                'sort_order'                         => 1,
                        );
/*                        $this->SettingsFields['CONF_ROBOXCHANGE_ROBOXCURRENCY'] = array(
                                'settings_value'                 => '',
                                'settings_title'                         => ROBOXCHANGE_CFG_ROBOXCURRENCY_TTL,
                                'settings_description'         => ROBOXCHANGE_CFG_ROBOXCURRENCY_DSCR,
                                'settings_html_function'         => 'setting_SELECT_BOX(ROBOXchange::_getRoboxCurrencies(),',
                                'sort_order'                         => 1,
                        );*/
                        $this->SettingsFields['CONF_ROBOXCHANGE_SHOPCURRENCY'] = array(
                                'settings_value'                 => '',
                                'settings_title'                         => ROBOXCHANGE_CFG_SHOPCURRENCY_TTL,
                                'settings_description'         => ROBOXCHANGE_CFG_SHOPCURRENCY_DSCR,
                                'settings_html_function'         => 'setting_CURRENCY_SELECT(',
                                'sort_order'                         => 1,
                        );
                        $this->SettingsFields['CONF_ROBOXCHANGE_STATUS_AFTER_PAY'] = array(
                                'settings_value'                 => '',
                                'settings_title'                         => ROBOXCHANGE_STATUS_AFTER_PAY_TTL,
                                'settings_description'         => ROBOXCHANGE_STATUS_AFTER_PAY_DSCR,
                                'settings_html_function'         => 'setting_ORDER_STATUS_SELECT(',
                                'sort_order'                         => 1,
                        );
                }

                function after_processing_html( $orderID ){

                        $res = '';

                        $order = ordGetOrder( $orderID );

		            if ($this->_getSettingValue('CONF_ROBOXCHANGE_SHOPCURRENCY') > 0 )
		            {
			            $exhange_curr = currGetCurrencyByID ( $this->_getSettingValue('CONF_ROBOXCHANGE_SHOPCURRENCY') );
			            $exhange_rate = $exhange_curr["currency_value"];
			            $exhange_round = $exhange_curr["roundval"]; 
			
		            }
	             	else
                    {		
		                $exhange_rate = 1;
			            $exhange_round = 2;
                    }
		
		            if ( (float)$exhange_rate == 0 ) $exhange_rate = 1;
			
		                $order_amount = _formatPrice(roundf($order["order_amount"]*$exhange_rate), $exhange_round, ".", "");

                        $post_1 = array(
                                'mrh' => $this->_getSettingValue('CONF_ROBOXCHANGE_MERCHANTLOGIN'),
                                'out_summ' => $order_amount,
                                'inv_id' => $orderID,
                        );

                        $post_1['crc'] = md5(implode(':',$post_1).':'.$this->_getSettingValue('CONF_ROBOXCHANGE_MERCHANTPASS1'));

                        $post_1['lang'] = $this->_getSettingValue('CONF_ROBOXCHANGE_LANG');
                        $post_1['in_curr'] = "WMZ";
                        $post_1['inv_desc'] = CONF_SHOP_NAME;

                        reset($post_1);
                        $roboxlink = "http://merchant.roboxchange.com/Index.aspx?MrchLogin=".$post_1['mrh']."&amp;OutSum=".$post_1['out_summ']."&amp;InvId=".$post_1['inv_id']."&amp;SignatureValue=".$post_1['crc']."&amp;Culture=".$post_1['lang']."&amp;IncCurrLabel=".$post_1['in_curr']."&amp;Desc=".rawurlencode($post_1['inv_desc']);

                        $res = "<table cellspacing='0' cellpadding='0' class='fsttab'><tr><td><table cellspacing='0' cellpadding='0' class='sectb'><tr><td><a href='".$roboxlink."'>".STRING_PAY_NOW."</a></td></tr></table></td></tr></table>";

                        return $res;
                }

                function after_payment_php( $orderID, $OutSum, $SignatureValue, $flag){

                        $res = '';

                        $order = ordGetOrder( $orderID );

		            if ($this->_getSettingValue('CONF_ROBOXCHANGE_SHOPCURRENCY') > 0 )
		            {
			            $exhange_curr = currGetCurrencyByID ( $this->_getSettingValue('CONF_ROBOXCHANGE_SHOPCURRENCY') );
			            $exhange_rate = $exhange_curr["currency_value"];
			            $exhange_round = $exhange_curr["roundval"]; 
			
		            }
	             	else
                    {		
		                $exhange_rate = 1;
			            $exhange_round = 2;
                    }
		
		            if ( (float)$exhange_rate == 0 ) $exhange_rate = 1;
			
		                $order_amount = _formatPrice(roundf($order["order_amount"]*$exhange_rate), $exhange_round, ".", "");
						
                        if($flag=="result") { $mrh_pass = $this->_getSettingValue('CONF_ROBOXCHANGE_MERCHANTPASS2');
                        }else{  $mrh_pass = $this->_getSettingValue('CONF_ROBOXCHANGE_MERCHANTPASS1');  }

                        $OutSum_x = _formatPrice($OutSum, $exhange_round, ".", "");
                        $my_crc = strtoupper(md5($OutSum.":".$orderID.":".$mrh_pass));

                        if ($order_amount > 0 && $my_crc == strtoupper($SignatureValue) && $OutSum_x == $order_amount) {
                            ostSetOrderStatusToOrder($order["orderID"],$this->_getSettingValue('CONF_ROBOXCHANGE_STATUS_AFTER_PAY'));
                            $res = "OK".$orderID;
                        }


                       return $res;
                }


                function _getLanguages(){
                        return ROBOXCHANGE_TXT_LANGRU.':ru,'.ROBOXCHANGE_TXT_LANGEN.':en';
                }

                function _getRoboxCurrencies(){
                        /*
                        http://www.roboxchange.com/xml/currlist.asp
                        */
                        $options = array();

                        $error_options = $options;

                        $error_options[] = array(
                                'title' => ROBOXCHANGE_TXT_NOCURR,
                                'value' => ''
                                );
                        $options[] = array(
                                        'title' => "WMZ",
                                        'value' => "WMZ",
                                        );


                        return $options;
                }
        }
?>
