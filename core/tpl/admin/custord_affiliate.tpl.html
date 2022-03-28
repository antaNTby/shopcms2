{* Affiliate program settings and info *}
{if $smarty.const.CONF_AFFILIATE_PROGRAM_ENABLED}
<script language="javascript" type="text/javascript">
<!--
window.reloadURL = '{$REQUEST_URI}';
//-->
</script>


{if !$show_tables}{$smarty.const.MSG_PERIOD_ISNT_SPECIFIED}<table class="adn"><tr><td class="se6"></td></tr></table>{/if}
{if $show_tables}

{* commissions table *}

{if $CommissionsNumber}
{if $delete_commission}<div style="color: green;">{$smarty.const.AFFP_MSG_COMMISSION_DELETED}</div><br>{/if}
<form action="{$REQUEST_URI}" method="POST" name="form_delete_commission" style="display:none;">
<input name="fACTION" value="DELETE_COMMISSION" type="hidden">
<input name="fREDIRECT" value="{$REQUEST_URI}" type="hidden">
<input name="COMMISSION[cID]" value="" type="hidden">
</form>
<table class="adn">
<tr class="lineb">
<td align="left" colspan="6">{$smarty.const.AFFP_STRING_CUSTOMER_COMMISSIONS} ({$smarty.const.STRING_FROM} {$from} {$smarty.const.STRING_TILL} {$till})</td>
</tr>
<tr class="lineb">
<td align=left><a href="{'OrderFieldC=cID&OrderDivC=DESC'|set_query:$REQUEST_URI}" class="liv">ID</a></td>
<td align=left><a href="{'OrderFieldC=xDateTime&OrderDivC=DESC'|set_query:$REQUEST_URI}" class="liv">{$smarty.const.ADMIN_CURRENT_DATE}</a></td>
<td align=left>{$smarty.const.TABLE_CUSTOMER}</td>
<td align=left width="80%"><a href="{'OrderFieldC=Description&OrderDivC=ASC'|set_query:$REQUEST_URI}" class="liv">{$smarty.const.STRING_DESCRIPTION}</a></td>
<td align=left><a href="{'OrderFieldC=Amount&OrderDivC=ASC'|set_query:$REQUEST_URI}" class="liv">{$smarty.const.STRING_SUM}</a></td>
<td align=left>&nbsp;</td>
</tr>{assign var="admhl" value=0}
{foreach from=$Commissions item=_Commission}
{if $admhl eq 1}
<tr><td colspan="6" class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr>
{else}{assign var="admhl" value=1}{/if}
                {assign_array var="TotalCommissionsAmount" index=$_Commission.CurrencyISO3 value=$TotalCommissionsAmount[$_Commission.CurrencyISO3]+$_Commission.Amount}
<tr class="liney hover">
                <td align=left class="toph3">C-{$_Commission.cID}</td>
                <td align=left class="toph3">{$_Commission.xDateTime}</td>
                <td align=left class="toph3"><a href="{''|set_query}?dpt=custord&sub=custlist&customer_details=contact_info&customerID={$_Commission.customerID}" class="inl">{$_Commission.CustomerLogin}</a></td>
                <td align=left>{$_Commission.Description}</td>
                <td align=left class="toph3">{$_Commission.Amount} {$_Commission.CurrencyISO3}</td>
                <td align=left><a href="javascript:open_window('{"sub_page=edit_commission&cID=`$_Commission.cID`"|set_query:$REQUEST_URI}',500,400);">{$smarty.const.EDIT_BUTTON}</a>&nbsp;|&nbsp;<a href="javascript:void(0)" onclick="
                                if(!confirm('{$smarty.const.STRING_LE_CONFIRM}'))return false;
                                document.form_delete_commission.elements['COMMISSION[cID]'].value = {$_Commission.cID};
                                document.form_delete_commission.submit();
                                return false;
                                "  title="{$smarty.const.DELETE_BUTTON}">X</a></td>
        </tr>{/foreach}
                          <tr>
                    <td class="navigator" colspan="6">      <table width="100%">


                <tr>                     <td alihn=right width="100%" class="toph3">{$smarty.const.STRING_SUM}:&nbsp;</td>
                        <td align="right">
                                <table align="right">
                                {foreach from=$TotalCommissionsAmount key=_key item=_total}
                                        <tr style="font-weight: bold"><td align="right" class="toph3">{$_total|string_format:"%.2f"}</td><td class="toph3">{$_key}</td></tr>
                                {/foreach}
                                </table>
                        </td></tr>
        </table></td>
                  </tr>
</table>
</form>
<table class="adn"><tr><td class="se6"></td></tr></table>
{else}
{$smarty.const.AFFP_MSG_NOCOMMISISONS_FOUND}<table class="adn"><tr><td class="se6"></td></tr></table>
{/if}{* if $CommissionsNumber *}


{* payments table *}

{if $PaymentsNumber}
{if $delete_payment}<div style="color: green;">{$smarty.const.AFFP_MSG_PAYMENT_DELETED}</div><br>{/if}
        <form action="{$REQUEST_URI}" method="POST" name="form_delete_payment" style="display:none;">
        <input name="fACTION" value="DELETE_PAYMENT" type="hidden">
        <input name="fREDIRECT" value="{$REQUEST_URI}" type="hidden">
        <input name="PAYMENT[pID]" value="" type="hidden">
        </form>
<table class="adn">
<tr class="lineb">
<td align="left" colspan="6">{$smarty.const.AFFP_STRING_PAYMENTS_TO_CUSTOMERS} ({$smarty.const.STRING_FROM} {$from} {$smarty.const.STRING_TILL} {$till})</td>
</tr>
<tr class="lineb">
<td align=left><a href="{'OrderField=pID&OrderDiv=DESC'|set_query:$REQUEST_URI}" class="liv">ID</a></td>
<td align=left><a href="{'OrderField=xDate&OrderDiv=DESC'|set_query:$REQUEST_URI}" class="liv">{$smarty.const.ADMIN_CURRENT_DATE}</a></td>
<td align=left>{$smarty.const.TABLE_CUSTOMER}</td>
<td align=left width="80%"><a href="{'OrderField=Description&OrderDiv=ASC'|set_query:$REQUEST_URI}" class="liv">{$smarty.const.STRING_DESCRIPTION}</a></td>
<td align=left><a href="{'OrderField=Amount&OrderDiv=ASC'|set_query:$REQUEST_URI}" class="liv">{$smarty.const.STRING_SUM}</a></td>
<td align=left>&nbsp;</td>
</tr>{assign var="admhl" value=0}
   {foreach from=$Payments item=_Payment}
{if $admhl eq 1}
<tr><td colspan="6" class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr>
{else}{assign var="admhl" value=1}{/if}
{assign_array var="TotalPaymentsAmount" index=$_Payment.CurrencyISO3 value=$TotalPaymentsAmount[$_Payment.CurrencyISO3]+$_Payment.Amount}
<tr class="liney hover">
                <td align=left class="toph3">P-{$_Payment.pID}</td>
                <td align=left class="toph3">{$_Payment.xDate}</td>
                <td align=left class="toph3"><a href="{''|set_query}?dpt=custord&sub=custlist&customer_details=contact_info&customerID={$_Payment.customerID}" class="inl">{$_Payment.CustomerLogin}</a></td>
                <td align=left>{$_Payment.Description}</td>
                <td align=left class="toph3">{$_Payment.Amount} {$_Payment.CurrencyISO3}</td>
                <td align=left><a href="javascript:open_window('{"sub_page=edit_payment&pID=`$_Payment.pID`"|set_query:$REQUEST_URI}',500,400);">{$smarty.const.EDIT_BUTTON}</a>&nbsp;|&nbsp;<a href="javascript:void(0)" onclick="
                                if(!confirm('{$smarty.const.STRING_LE_CONFIRM}'))return false;
                                document.form_delete_payment.elements['PAYMENT[pID]'].value = {$_Payment.pID};
                                document.form_delete_payment.submit();
                                return false;
                                " title="{$smarty.const.DELETE_BUTTON}">X</a></td>
        </tr>{/foreach}
                          <tr>
                    <td class="navigator" colspan="6">      <table width="100%">


                <tr>                     <td alihn=right width="100%" class="toph3">{$smarty.const.STRING_SUM}:&nbsp;</td>
                        <td align="right">
                                <table align="right">
                                {foreach from=$TotalPaymentsAmount key=_key item=_total}
                                        <tr style="font-weight: bold"><td align="right" class="toph3">{$_total|string_format:"%.2f"}</td><td class="toph3">{$_key}</td></tr>
                                {/foreach}
                                </table>
                        </td></tr>
        </table></td>
                  </tr>
</table>
</form>
<table class="adn"><tr><td class="se6"></td></tr></table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                        <td style="font-weight: bold;"  align="right" width="100%" class="toph3">{$smarty.const.AFFP_USER_BALANCE}:&nbsp;
                        </td>
                        <td align="right">
                               <table align="right">
                                {foreach from=$CurrencyISO3 item=_currency}
                                {if $TotalCommissionsAmount[$_currency.currency_iso_3] or $TotalPaymentsAmount[$_currency.currency_iso_3]}
                                        <tr>
                                                <td align="right" style="font-weight: bold;" class="toph3">{"`$TotalCommissionsAmount[$_currency.currency_iso_3]-$TotalPaymentsAmount[$_currency.currency_iso_3]`"|string_format:"%.2f"}
                                                </td>
                                                <td style="font-weight: bold;" class="toph3">{$_currency.currency_iso_3}
                                                </td>
                                        </tr>
                                {/if}
                                {/foreach}
                                </table></td></tr></table> <table class="adn"><tr><td class="se6"></td></tr></table>
{else}
{$smarty.const.AFFP_MSG_NOPAYMENTS_FOUND}<table class="adn"><tr><td class="se6"></td></tr></table>
{/if}
{/if}






{if $error_new_commission}<div style="color: red">{$error_new_commission}</div><br>{/if}
{if $newCommissionStatus}<div style="color: green">{$smarty.const.AFFP_MSG_NEW_COMMISSION_OK}</div><br>{/if}
<form method="POST" action="{'#new_commission_anchor'|set_query:$REQUEST_URI}" id="newcommform">
<input name="fACTION" value="NEW_COMMISSION" type="hidden">
<input name="fREDIRECT" value="{'#new_commission_anchor'|set_query:$REQUEST_URI}" type="hidden">
<table class="adn">
<tr class="lineb">
<td align="left" colspan="4">{$smarty.const.AFFP_SUBMIT_NEW_COMMISSION}</td>
</tr>
<tr class="lineb">
<td align=left>{$smarty.const.ADMIN_CURRENT_DATE}</td>
<td align=left>{$smarty.const.STRING_SUM}</td>
<td align=left>{$smarty.const.CUSTOMER_LOGIN}</td>
<td align=left>{$smarty.const.STRING_DESCRIPTION}</td>
</tr>
<tr class="lins">
          <td align=left><input name="NEW_COMMISSION[xDate]" value="{$NEW_COMMISSION.xDate}" type="text" size="20" class="prc"></td>
                <td align=left><input name="NEW_COMMISSION[Amount]" value="{$NEW_COMMISSION.Amount}" type="text" size="20" class="prc">&nbsp;
                        <select name="NEW_COMMISSION[CurrencyISO3]">
                        {foreach from=$CurrencyISO3 item=_currency}
                                <option
                                {if $_currency.currency_iso_3==$NEW_COMMISSION.CurrencyISO3}
                                        selected="selected"
                                {/if}
                                >{$_currency.currency_iso_3}</option>
                        {/foreach}
                        </select></td>
                <td align=left><input name="NEW_COMMISSION[customerLogin]" class="prc" value="{if $NEW_COMMISSION.customerLogin}{$NEW_COMMISSION.customerLogin}{elseif $edCustomerLogin}{$edCustomerLogin}{/if}" type="text" size="20"></td>  <td align=left><textarea name="NEW_COMMISSION[Description]" style="height: 44px; width: 170px;">{$NEW_COMMISSION.Description}</textarea></td>
        </tr>
</table>
</form>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>
<a href="#" onclick="document.getElementById('newcommform').submit(); return false" class="inl">{$smarty.const.ADD_BUTTON}</a>
<table class="adn"><tr><td class="se6"></td></tr></table>




{if $error_new_payment}<div style="color: red">{$error_new_payment}</div><br>{/if}
{if $newPayStatus}<div style="color: green">{$smarty.const.AFFP_MSG_NEW_PAY_OK}</div><br>{/if}
<form method="POST" action="{'#new_payment_anchor'|set_query:$REQUEST_URI}" id="newpayyform">
<input name="fACTION" value="NEW_PAYMENT" type="hidden">
<input name="fREDIRECT" value="{'#new_payment_anchor'|set_query:$REQUEST_URI}" type="hidden">
<table class="adn">
<tr class="lineb">
<td align="left" colspan="4">{$smarty.const.AFFP_SUBMIT_NEW_PAYMENT}</td>
</tr>
<tr class="lineb">
<td align=left>{$smarty.const.ADMIN_CURRENT_DATE}</td>
<td align=left>{$smarty.const.STRING_SUM}</td>
<td align=left>{$smarty.const.CUSTOMER_LOGIN}</td>
<td align=left>{$smarty.const.STRING_DESCRIPTION}</td>
</tr>
<tr class="lins">
          <td align=left><input name="NEW_PAYMENT[xDate]" value="{$NEW_PAYMENT.xDate}" type="text" size="20" class="prc"></td>
                <td align=left><input name="NEW_PAYMENT[Amount]" value="{$NEW_PAYMENT.Amount}" type="text" size="20" class="prc">&nbsp;
                        <select name="NEW_PAYMENT[CurrencyISO3]">
                        {foreach from=$CurrencyISO3 item=_currency}
                                <option
                                {if $_currency.currency_iso_3==$NEW_PAYMENT.CurrencyISO3}
                                        selected="selected"
                                {/if}
                                >{$_currency.currency_iso_3}</option>
                        {/foreach}
                        </select></td>
                <td align=left><input name="NEW_PAYMENT[customerID]" class="prc" value="{if $NEW_PAYMENT.customerID}{$NEW_PAYMENT.customerID}{elseif $edCustomerLogin}{$edCustomerLogin}{/if}" type="text" size="20"></td>  <td align=left><textarea name="NEW_PAYMENT[Description]" style="height: 44px; width: 170px;">{$NEW_PAYMENT.Description}</textarea></td>
        </tr>
</table>
</form>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>
<a href="#" onclick="document.getElementById('newpayyform').submit(); return false" class="inl">{$smarty.const.ADD_BUTTON}</a>
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adn"><tr><td class="help"><span class="titlecol2">{$smarty.const.USEFUL_FOR_YOU}</span><div class="helptext">{$smarty.const.ALERT_ADMIN2}</div></td>
        </tr>
      </table>
{else}
{$smarty.const.AFFP_MSG_PROGRAM_DISABLED}. {$smarty.const.AFF_GO_SETTINGS}
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adn"><tr><td class="help"><span class="titlecol2">{$smarty.const.USEFUL_FOR_YOU}</span><div class="helptext">{$smarty.const.ALERT_ADMIN2}</div></td>
        </tr>
      </table>
{/if}
