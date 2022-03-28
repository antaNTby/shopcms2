{if $smarty.const.CONF_AFFILIATE_PROGRAM_ENABLED}
<script language="javascript" type="text/javascript">
<!--
window.reloadURL = '{$REQUEST_URI}';
//-->
</script>
{if $safemode}<div style="color: red;">{$smarty.const.ADMIN_SAFEMODE_WARNING}</div><br>{/if}
{if $smarty.const.CONF_USER_SYSTEM eq 2}{$smarty.const.STRING_AFFILIATE_CUSTOMERS}: {$RecruitedCustomersNumber}<br><br>{/if}
{* recruited customers table *}
<form action="{$REQUEST_URI}" method="POST" name="form_cancel_customer" style="display:none;">
<input name="fACTION" value="CANCEL_CUSTOMER" type="hidden">
<input name="fREDIRECT" value="{$REQUEST_URI}" type="hidden">
<input name="CUSTOMER[customerID]" value="" type="hidden">
</form>
{if $RecruitedCustomersNumber}
<table class="adn">
<tr class="lineb">
<td align=left>{$smarty.const.ADMIN_CUSTOMER_LOGIN}</td>
<td align=left>{$smarty.const.STR_ACTSTATE}</td>
<td align=left>{$smarty.const.ADMIN_REGISTRATION_TIME}</td>
<td align=right>&nbsp;</td>
</tr>
                 {assign var="admhl" value=0}
        {foreach from=$RecruitedCustomers item=_Customer name=i}
        {if $admhl eq 1}
<tr><td colspan="4" class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr>
{else}{assign var="admhl" value=1}{/if}
<tr class="liney hover">
<td align=left><a href="{''|set_query}?dpt=custord&sub=custlist&customer_details=contact_info&customerID={$_Customer.customerID}">{$_Customer.Login}</a>
                        </td>
<td align=left>{if $_Customer.ActivationCode}{$smarty.const.STR_NOTACTIVATED}
                                {else}{$smarty.const.STR_ACTIVATED}
                                {/if}
                        </td>
<td align=left>{$_Customer.reg_datetime}</td>
<td align=right width="1%"><a href="javascript:void(0)" onclick="
                                        if(!confirm('{$smarty.const.STRING_LE_CONFIRM}'))return false;
                                        document.form_cancel_customer.elements['CUSTOMER[customerID]'].value = {$_Customer.customerID};
                                        document.form_cancel_customer.submit();
                                        return false;
                                        " title="{$smarty.const.AFFP_REMOVE_USER}">X</a></td>
                </tr>
        {/foreach}

</table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>

{/if}{* if $RecruitedCustomersNumber *}

{* calendar form *}
<b style="font-size:110%">{$smarty.const.AFFP_COMMISSION_PAYMENTS}</b>
<table class="adn"><tr><td class="se5"></td></tr></table><a href="?dpt=custord&amp;sub=affiliate&amp;edCustomerID={$edCustomerID}#new_commission_anchor" class="inl">{$smarty.const.AFFP_SUBMIT_NEW_COMMISSION}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="?dpt=custord&amp;sub=affiliate&amp;edCustomerID={$edCustomerID}#new_payment_anchor" class="inl">{$smarty.const.AFFP_SUBMIT_NEW_PAYMENT}</a>
<br><br>
<b>{$smarty.const.STRING_CALENDAR}</b><br><br>
<form method="POST" action="{$REQUEST_URI}">
{if $Error_DateFormat}
<div style="color: red;">{$smarty.const.AFFP_MSG_ERROR_DATE_FORMAT}</div><br>
{/if}
{$smarty.const.STRING_FROM}: <input type="text" name="from" value="{$from}" size="12"> {$smarty.const.STRING_TILL}: <input name="till" value="{$till}" type="text" size="12" /> <input value="{$smarty.const.VIEW_BUTTON}" type="submit" style="font-size: 11px; font-family: Tahoma, Arial; border: 1px solid #80A2D9; background-color: #E1ECFD;">
</form>
{if !$show_tables}
<br>{$smarty.const.MSG_PERIOD_ISNT_SPECIFIED}
{/if}
<table class="adn"><tr><td class="se6"></td></tr></table>
{* commissions table *}
{if $delete_commission}<div style="color: green;">{$smarty.const.AFFP_MSG_COMMISSION_DELETED}</div><br>{/if}

{if $show_tables}
<table class="adn">
<tr class="lineb">
<td align="left">{$smarty.const.AFFP_STRING_CUSTOMER_COMMISSIONS} ({$smarty.const.STRING_FROM} {$from} {$smarty.const.STRING_TILL} {$till})</td>
</tr>
</table>
{/if}



{if $CommissionsNumber and $show_tables}
        <form action="{$REQUEST_URI}" method="POST" name="form_delete_commission" style="display:none;">
        <input name="fACTION" value="DELETE_COMMISSION" type="hidden">
        <input name="fREDIRECT" value="{$REQUEST_URI}" type="hidden">
        <input name="COMMISSION[cID]" value="" type="hidden">
        </form>

<table class="adn">
<tr class="lineb">
<td align=left><a href="{'OrderFieldC=cID&OrderDivC=DESC'|set_query:$REQUEST_URI}" class="liv">ID</a></td>
<td align=left><a href="{'OrderFieldC=xDateTime&OrderDivC=DESC'|set_query:$REQUEST_URI}" class="liv">{$smarty.const.ADMIN_CURRENT_DATE}</a></td>
<td align=left><a href="{'OrderFieldC=Description&OrderDivC=ASC'|set_query:$REQUEST_URI}" class="liv">{$smarty.const.STRING_DESCRIPTION}</a></td>
<td align=left><a href="{'OrderFieldC=Amount&OrderDivC=ASC'|set_query:$REQUEST_URI}" class="liv">{$smarty.const.STRING_SUM}</a></td>
<td width="1%">&nbsp;</td></tr>   {assign var="admhl" value=0}
                {foreach from=$Commissions item=_Commission}
{if $admhl eq 1}
<tr><td colspan="5" class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr>
{else}{assign var="admhl" value=1}{/if}
                        {assign_array var="TotalCommissionsAmount" index=$_Commission.CurrencyISO3 value=$TotalCommissionsAmount[$_Commission.CurrencyISO3]+$_Commission.Amount}
<tr class="liney hover">
                        <td align=left>C-{$_Commission.cID}</td>
                        <td align=left>{$_Commission.xDateTime}</td>
                        <td align=left>{$_Commission.Description}</td>
                        <td align=left nowrap="nowrap">{$_Commission.Amount} {$_Commission.CurrencyISO3}</td>
                        <td align=left><a href="javascript:open_window('{"sub_page=edit_commission&cID=`$_Commission.cID`"|set_query}',500,400);">{$smarty.const.EDIT_BUTTON}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0)" onclick="
                                if(!confirm('{$smarty.const.STRING_LE_CONFIRM}'))return false;
                                document.form_delete_commission.elements['COMMISSION[cID]'].value = {$_Commission.cID};
                                document.form_delete_commission.submit();
                                return false;
                                " title="{$smarty.const.DELETE_BUTTON}">X</a></td>
                </tr>
                {/foreach}

                          <tr>
                    <td class="navigator" colspan="7">      <table width="100%">


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
{elseif $show_tables}
<div align="left" style="padding: 4px 6px;">{$smarty.const.AFFP_MSG_NOCOMMISISONS_FOUND}</div><table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr></table>
{/if}

{* payments table *}
<table class="adn"><tr><td class="se6"></td></tr></table>
{if $delete_payment}<div style="color: green;">{$smarty.const.AFFP_MSG_PAYMENT_DELETED}</div><br>{/if}
{if $show_tables}
<table class="adn">
<tr class="lineb">
<td align="left">{$smarty.const.AFFP_STRING_PAYMENTS_TO_CUSTOMERS} ({$smarty.const.STRING_FROM} {$from} {$smarty.const.STRING_TILL} {$till})</td>
</tr>
</table>
{/if}

{if $PaymentsNumber and $show_tables}
        <form action="{$REQUEST_URI}" method="POST" name="form_delete_payment" style="display:none;">
        <input name="fACTION" value="DELETE_PAYMENT" type="hidden">
        <input name="fREDIRECT" value="{$REQUEST_URI}" type="hidden">
        <input name="PAYMENT[pID]" value="" type="hidden">
        </form>


<table class="adn">
<tr class="lineb">
<td align=left><a href="{'OrderField=pID&OrderDiv=DESC'|set_query:$REQUEST_URI}" class="liv">ID</a></td>
<td align=left><a href="{'OrderField=xDate&OrderDiv=DESC'|set_query:$REQUEST_URI}" class="liv">{$smarty.const.ADMIN_CURRENT_DATE}</a></td>
<td align=left><a href="{'OrderField=Description&OrderDiv=ASC'|set_query:$REQUEST_URI}" class="liv">{$smarty.const.STRING_DESCRIPTION}</a></td>
<td align=left><a href="{'OrderField=Amount&OrderDiv=ASC'|set_query:$REQUEST_URI}" class="liv">{$smarty.const.STRING_SUM}</a></td>
<td width="1%">&nbsp;</td></tr>   {assign var="admhl" value=0}

                {foreach from=$Payments item=_Payment}
                {if $admhl eq 1}
<tr><td colspan="5" class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr>
{else}{assign var="admhl" value=1}{/if}
                        {assign_array var="TotalPaymentsAmount" index=$_Payment.CurrencyISO3 value=$TotalPaymentsAmount[$_Payment.CurrencyISO3]+$_Payment.Amount}
<tr class="liney hover">
                        <td align=left>P-{$_Payment.pID}</td>
                        <td align=left>{$_Payment.xDate}</td>
                        <td align=left>{$_Payment.Description}</td>
                        <td align=left nowrap="nowrap">{$_Payment.Amount} {$_Payment.CurrencyISO3}</td>
                        <td align=left><a href="javascript:open_window('{"sub_page=edit_payment&pID=`$_Payment.pID`"|set_query}',500,400);">{$smarty.const.EDIT_BUTTON}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0)" onclick="
                                if(!confirm('{$smarty.const.STRING_LE_CONFIRM}'))return false;
                                document.form_delete_payment.elements['PAYMENT[pID]'].value = {$_Payment.pID};
                                document.form_delete_payment.submit();
                                return false;
                                " title="{$smarty.const.DELETE_BUTTON}">X</a></td>
                </tr>
                {/foreach}
                          <tr>
                    <td class="navigator" colspan="7">      <table width="100%">


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
{elseif $show_tables}
<div align="left" style="padding: 4px 6px;">{$smarty.const.AFFP_MSG_NOPAYMENTS_FOUND}</div><table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr></table>
{/if}
{if $PaymentsNumber and $CommissionsNumber and $show_tables}
<table class="adn"><tr><td class="se5"></td></tr></table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                        <td style="font-weight: bold;"  align="right" width="100%" class="toph3">{$smarty.const.AFFP_USER_BALANCE}:&nbsp;
                        </td>
                        <td align="right">
                               <table align="right">
                                {foreach from=$CurrencyISO3 item=_currency}
                                {if $TotalCommissionsAmount[$_currency.currency_iso_3] or $TotalPaymentsAmount[$_currency.currency_iso_3]}
                                        <tr>
                                         <td align="right" style="font-weight: bold;" class="toph3">
                                                {"`$TotalCommissionsAmount[$_currency.currency_iso_3]-$TotalPaymentsAmount[$_currency.currency_iso_3]`"|string_format:"%.2f"}
                                        </td>
                                                <td style="font-weight: bold;" class="toph3">{$_currency.currency_iso_3}
                                                </td>
                                        </tr>
                                {/if}
                                {/foreach}
                    </table></td></tr></table>
{/if}
{else}{$smarty.const.AFFP_MSG_PROGRAM_DISABLED}{/if}