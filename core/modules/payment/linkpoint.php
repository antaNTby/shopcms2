<?php
	// LinkPoint payment module
	// http://www.linkpoint.com

function settingCONF_LINKPOINT_INTEGRATION($_SettingID)
{
	$SettingConstName = settingGetConstNameByID($_SettingID);
	
	if ( isset($_POST["save"]) )
	{
		if ( isset($_POST['setting'.$SettingConstName]) )
		{
			_setSettingOptionValueByID( $_SettingID, 
				$_POST['setting'.$SettingConstName] );
		}
	}

	$res = "";
	$currencies = currGetAllCurrencies();
	$res = "<select name='setting".$SettingConstName."'>";
	$selectedID = _getSettingOptionValueByID($_SettingID);
	$res .= "<option value='1'"; if ((int)$selectedID==1) $res .= " selected"; $res .= ">".CLINKPOINT_TXT_1."</option>";
	$res .= "<option value='2'"; if ((int)$selectedID==2) $res .= " selected"; $res .= ">".CLINKPOINT_TXT_2."</option>";
	$res .= "<option value='3'"; if ((int)$selectedID==3) $res .= " selected"; $res .= ">".CLINKPOINT_TXT_3."</option>";
	$res .= "</select>";
	return $res;
}

/**
 * @connect_module_class_name CLinkPoint
 *
 */

class CLinkPoint extends PaymentModule {

	var $replSettings;
	
	function _initVars(){
		
		$this->title 		= CLINKPOINT_TTL;
		$this->description 	= CLINKPOINT_DSCR;
		$this->sort_order 	= 1;
		
		$this->Settings = 	array( 
			"CONF_PAYMENTMODULE_LINKPOINT_STORENAME",
			"CONF_PAYMENTMODULE_LINKPOINT_INTEGRATION_TYPE",
			"CONF_PAYMENTMODULE_LINKPOINT_USD_CURRENCY"
		);
		$this->replSettings = $this->Settings;
	}

	function _initSettingFields(){

		$this->SettingsFields['CONF_PAYMENTMODULE_LINKPOINT_STORENAME'] = array(
			'settings_value' 		=> '1234567890', 
			'settings_title' 			=> CLINKPOINT_CFG_STORENAME_TTL, 
			'settings_description' 	=> CLINKPOINT_CFG_STORENAME_DSCR, 
			'settings_html_function' 	=> 'setting_TEXT_BOX(0,', 
			'sort_order' 			=> 1,
		);
		$this->SettingsFields['CONF_PAYMENTMODULE_LINKPOINT_INTEGRATION_TYPE'] = array(
			'settings_value' 		=> '1', 
			'settings_title' 			=> CLINKPOINT_CFG_INTEGRATION_TYPE_TTL, 
			'settings_description' 	=> CLINKPOINT_CFG_INTEGRATION_TYPE_DSCR, 
			'settings_html_function' 	=> 'settingCONF_LINKPOINT_INTEGRATION(', 
			'sort_order' 			=> 1,
		);
		$this->SettingsFields['CONF_PAYMENTMODULE_LINKPOINT_USD_CURRENCY'] = array(
			'settings_value' 		=> '0', 
			'settings_title' 			=> CLINKPOINT_CFG_USD_CURRENCY_TTL, 
			'settings_description' 	=> CLINKPOINT_CFG_USD_CURRENCY_DSCR, 
			'settings_html_function' 	=> 'setting_CURRENCY_SELECT(', 
			'sort_order' 			=> 1,
		);
	}

	function payment_form_html()
	{
		if ((int)$this->_getSettingValue('CONF_PAYMENTMODULE_LINKPOINT_INTEGRATION_TYPE') > 1)
		{
			$ccnumber = isset($_POST["lp_cc_number"]) ? str_replace("\"","&quot;",$_POST["lp_cc_number"]) : "";
			$ccholder = isset($_POST["lp_cc_holder"]) ? str_replace("\"","&quot;",$_POST["lp_cc_holder"]) : "";
			$cvv = isset($_POST["lp_cvv"]) ? str_replace("\"","&quot;",$_POST["lp_cvv"]) : "";
			$ccmonth = isset($_POST["lp_exp_month"]) ? (int) $_POST["lp_exp_month"] : 0;
			$ccyear = isset($_POST["lp_exp_year"]) ? (int) $_POST["lp_exp_year"] : 0;

			$exp_months = "";
			for ($i=1; $i<=12; $i++)
			{
				$m = (string)$i;
				if ($i<10) $m = "0".$m;
				$exp_months .= "<option value=\"$m\"";
				if ($ccmonth == $i) $exp_months .= " selected";
				$exp_months .= ">$m</option>\n";
			}

			$curr_year = (int) strftime("%y",time());
			$exp_years = "";
			for ($i=$curr_year; $i<$curr_year+10; $i++)
			{
				$y = (string)$i;
				if ($i<10) $y = "0".$y;
				$exp_years .= "<option value=\"$y\"";
				if ($ccyear == $i) $exp_years .= " selected";
				$exp_years .= ">20$y</option>\n";
			}

			$text = "<table>
			<tr><td>".CLINKPOINT_TXT_PAYMENT_FORM_HTML_1.":</td><td><input type=text name=lp_cc_number value=\"$ccnumber\"></td></tr>
			<tr><td>".CLINKPOINT_TXT_PAYMENT_FORM_HTML_2.":</td><td><input type=text name=lp_cc_holder value=\"$ccholder\"></td></tr>
			<tr><td>".CLINKPOINT_TXT_PAYMENT_FORM_HTML_3.":</td><td>
				<select name=lp_exp_month>
				<option value=\"0\">".CLINKPOINT_TXT_PAYMENT_FORM_HTML_4."</option>
				$exp_months
				</select> /
				<select name=lp_exp_year>
				<option value=\"0\">".CLINKPOINT_TXT_PAYMENT_FORM_HTML_5."</option>
				$exp_years
				</select>
			</td></tr>";

			$text .= "<tr><td>CVV:</td><td><input type=text name=lp_cvv value=\"$cvv\"></td></tr>";

			$text .= "<tr><td>Card type:</td><td>\n".
				"<select name=lp_cardtype>\n".
				"	<option value=\"V\">Visa</option>\n".
				"	<option value=\"M\">MasterCard</option>\n".
				"	<option value=\"D\">Discover</option>\n".
				"	<option value=\"A\">American Express</option>\n".
				"	<option value=\"J\">JCB</option>\n".
				"	<option value=\"C\">Diner's Club</option>\n".
				"</select>".
				"</td></tr>";

			$text .= "</table>";

			return $text;
		}
		else
		{
			return "";
		}
	}

	function payment_process($order)
	{

		//verify input

		if ((int)$this->_getSettingValue('CONF_PAYMENTMODULE_LINKPOINT_INTEGRATION_TYPE') > 1)
		{

			if (!isset($_POST["lp_cc_number"]) || strlen( trim($_POST["lp_cc_number"]) ) == 0)
			{
				return CLINKPOINT_TXT_PAYMENT_PROCESS_1;
			}

			if (!isset($_POST["lp_cc_holder"]) || strlen( trim($_POST["lp_cc_holder"]) ) == 0)
			{
				return CLINKPOINT_TXT_PAYMENT_PROCESS_2;
			}

			if (!isset($_POST["lp_cvv"]) || strlen( trim($_POST["lp_cvv"]) ) == 0)
			{
				return CLINKPOINT_TXT_PAYMENT_PROCESS_3;
			}

			if (!isset($_POST["lp_exp_month"]) || ((int) $_POST["lp_exp_month"]) == 0)
			{
				return CLINKPOINT_TXT_PAYMENT_PROCESS_4;
			}

			if (!isset($_POST["lp_exp_year"]) || ((int) $_POST["lp_exp_year"]) == 0)
			{
				return CLINKPOINT_TXT_PAYMENT_PROCESS_5;
			}

			return 1;

		}
		else
		{
			return 1;
		}

	}

	function after_processing_php($orderID)
	{

		if ((int)$this->_getSettingValue('CONF_PAYMENTMODULE_LINKPOINT_INTEGRATION_TYPE') > 1)
		{

			$orderID = (int)$orderID;
			if ($orderID)
			{
				$expires = (string) $_POST["lp_exp_month"];
				$expires.= (string) $_POST["lp_exp_year"];
				$cvv = isset($_POST["lp_cvv"]) ? $_POST["lp_cvv"] : "";

				if ((int)$this->_getSettingValue('CONF_PAYMENTMODULE_LINKPOINT_INTEGRATION_TYPE') == 3)
				{

					db_query("update ".ORDERS_TABLE." set cc_number = '".cryptCCNumberCrypt($_POST["lp_cc_number"],null)."', cc_holdername = '".cryptCCHoldernameCrypt($_POST["lp_cc_holder"],null)."', cc_expires = '".cryptCCExpiresCrypt($expires,null)."', cc_cvv = '".cryptCCNumberCrypt($cvv,null)."' where orderID=$orderID");
				}
				else
				{

					$_SESSION["lp_cc_number"] = cryptCCNumberCrypt($_POST["lp_cc_number"],null);
					$_SESSION["lp_cc_holdername"] = cryptCCHoldernameCrypt($_POST["lp_cc_holder"],null);
					$_SESSION["lp_cc_expires"] = cryptCCExpiresCrypt($expires,null);
					$_SESSION["lp_cc_cvv"] = cryptCCNumberCrypt($cvv,null);

				}
			}

			$_SESSION["lp_cardtype"] = $_POST["lp_cardtype"];

		}

		return "";

	}

	function after_processing_html( $orderID ) 
	{
		$orderID = (int) $orderID;
		$order = ordGetOrder( $orderID );
		if ( $this->_getSettingValue('CONF_PAYMENTMODULE_LINKPOINT_USD_CURRENCY') > 0 )
		{
			$LPcurr = currGetCurrencyByID ( $this->_getSettingValue('CONF_PAYMENTMODULE_LINKPOINT_USD_CURRENCY') );
			$LPcurr_rate = $LPcurr["currency_value"];
		}
		if (!isset($LPcurr) || !$LPcurr)
		{
			$LPcurr_rate = 1;
		}

		$order_amount = round(100*$order["order_amount"] * $LPcurr_rate)/100;

		$res = "";

		$res .= 

			"<table width='100%'>\n".
			"	<tr>\n".
			"		<td align='center'>\n".
			"<form method='POST' action='linkpoint.php'>\n".
			"<input type=\"hidden\" name=\"chargetotal\" value=\"".$order_amount."\">\n";
		foreach($this->replSettings as $_Sett){
		
			$res .="<input type=\"hidden\" name=\"pSettingsAccordance[".$_Sett."]\" value=\"".$this->_getSettingRealName($_Sett)."\">\n";
		}
		$res .= "<input type=\"hidden\" name=\"oid\" value=\"".$orderID."\">\n".

			"<input type=\"submit\" value=\"".CLINKPOINT_TXT_AFTER_PROCESSING_HTML_1."\">\n".
			"		</form></td>\n".
			"	</tr>\n".
			"</table>\n\n";

		return $res;

	}
}

?>