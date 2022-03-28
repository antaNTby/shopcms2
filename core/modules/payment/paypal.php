<?php
// PayPal payment module
// http://www.paypal.com

/**
 * @connect_module_class_name CPayPal
 *
 */
class CPayPal extends PaymentModule {
	
	function _initVars(){
		
		$this->title 		= CPAYPAL_TTL;
		$this->description 	= CPAYPAL_DSCR;
		$this->sort_order 	= 1;
		
		$this->Settings = array( 
				"CONF_PAYMENTMODULE_PAYPAL_MERCHANT_EMAIL"
			);
	}

	function _initSettingFields(){

		$this->SettingsFields['CONF_PAYMENTMODULE_PAYPAL_MERCHANT_EMAIL'] = array(
			'settings_value' 		=> '', 
			'settings_title' 			=> CPAYPAL_CFG_MERCHANT_EMAIL_TTL, 
			'settings_description' 	=> CPAYPAL_CFG_MERCHANT_EMAIL_DSCR, 
			'settings_html_function' 	=> 'setting_TEXT_BOX(0,', 
			'sort_order' 			=> 1,
		);
	}

	function after_processing_html( $orderID ){
		
		$order = ordGetOrder( $orderID );
		$order_amount = round(100*$order["order_amount"] * $order["currency_value"])/100;

		$res = "";

		$res .= 
			"<table width='100%'>\n".
			"	<tr>\n".
			"		<td align='center'>\n".
			"<form method='POST' name='PayPalForm' action='https://www.paypal.com/cgi-bin/webscr'>\n".
			"<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">\n".
			"<input type=\"hidden\" name=\"business\" value=\"".$this->_getSettingValue('CONF_PAYMENTMODULE_PAYPAL_MERCHANT_EMAIL')."\">\n".
			"<input type=\"hidden\" name=\"item_name\" value=\"Order #".$orderID."\">\n".
			"<input type=\"hidden\" name=\"amount\" value=\"".$order_amount."\">\n".
			"<input type=\"hidden\" name=\"bn\" value=\"shopcms\">\n".
			"<input type=\"hidden\" name=\"return\" value=\"".getTransactionResultURL('success')."\">\n".
			" <input type=\"hidden\" name=\"currency_code\" value=\"".$order["currency_code"]."\">\n".
			"<input type=\"image\" name=\"submit\" src=\"http://images.paypal.com/images/x-click-but01.gif\" alt=\"".CPAYPAL_TXT_AFTER_PROCESSING_HTML_1."\">\n".
			"		</td>\n".
			"	</tr>\n".
			"</table>";

//			$res .= "<script>document.PayPalForm.submit();</script>";

		return $res;
	}
}
?>