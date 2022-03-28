<form action="{$smarty.const.ADMIN_FILE}" method=post name="payform" id="payform">
{if $payment_types}
<table class="adn">
<tr class="lineb">
<td align="center">{$smarty.const.ADMIN_ON2}</td>
<td align="left" width="100%">{$smarty.const.STRING_NAME}</td>
<td align="left">{$smarty.const.STRING_DESCRIPTION}</td>
<td align="left">{$smarty.const.STRING_MODULE_EMAIL_COMMENTS}</td>
<td align="left" class="toph3">{$smarty.const.STRING_SHIPPING_METHODS_ARE_ALLOWED}</td>
<td align="left">{$smarty.const.STRING_MODULE_NAME}</td>
<td align="left">{$smarty.const.ADMIN_SORT_ORDER}</td>
<td align="center">Del</td>
</tr>
{section name=i loop=$payment_types}
<tr><td height="4" colspan="8"></td></tr>
<tr class="liney">
<td align="center"><input type=checkbox name=Enabled_{$payment_types[i].PID} {if $payment_types[i].Enabled} checked{/if}></td>
<td align="left"><input type=text name=name_{$payment_types[i].PID} value="{$payment_types[i].Name|replace:'"':'&quot;'}" class="textp" size="24"></td>
<td align="left"><textarea name="description_{$payment_types[i].PID}" style="height: 44px; width: 170px;">{$payment_types[i].description|replace:'"':'&quot;'}</textarea></td>
<td align="left"><textarea name="email_comments_text_{$payment_types[i].PID}" style="height: 44px; width: 170px;">{$payment_types[i].email_comments_text|replace:'"':'&quot;'}</textarea></td>
<td align="left"><table border="0" cellpadding="0" cellspacing="0">
{section name=j loop=$payment_types[i].ShippingMethodsToAllow}
<tr class="lineys">
<td><input type=checkbox name='ShippingMethodsToAllow_{$payment_types[i].PID}_{$payment_types[i].ShippingMethodsToAllow[j].SID}'
                                                                {if $payment_types[i].ShippingMethodsToAllow[j].allow == 1}
                                                                        checked
                                                                {/if} ></td>
<td>&nbsp;{$payment_types[i].ShippingMethodsToAllow[j].name}</td>
</tr>
{/section}
</table>
</td>
<td align="left"><select name='module_{$payment_types[i].PID}' >
                                        <option value='null'> -- </option>
                                        {section name=j loop=$payment_modules}
                                        <option value='{$payment_modules[j]->get_id()}'
                                        {if $payment_modules[j]->get_id() == $payment_types[i].module_id}
                                                selected
                                        {/if}
                                        > {$payment_modules[j]->title} </option>
                                        {/section}
                                </select><input type=hidden name=calculate_tax_{$payment_types[i].PID} value=0></td>
<td align="left"><input type=text name=sort_order_{$payment_types[i].PID} value="{$payment_types[i].sort_order|replace:'"':'&quot;'}" class="textp" size="4"></td>
<td align="center"><a href="#" onclick="confirmDelete({$payment_types[i].PID},'{$smarty.const.QUESTION_DELETE_CONFIRMATION}','{$smarty.const.ADMIN_FILE}?dpt=conf&amp;sub=payment&amp;delete=');">X</a></td>
</tr>
<tr><td height="4"></td></tr><tr><td colspan="8" class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr>
{/section}
</table>
<table class="adn"><tr><td height="4"></td></tr><tr><td class="se5"></td></tr></table>
<a href="#" onclick="document.getElementById('payform').submit(); return false" class="inl">{$smarty.const.SAVE_BUTTON}</a>
<table class="adn"><tr><td class="se6"></td></tr></table>
{/if}
<table class="adn">
<tr class="lineb">
<td align="center">{$smarty.const.ADMIN_ON2}</td>
<td align="left" width="100%">{$smarty.const.STRING_NAME}</td>
<td align="left">{$smarty.const.STRING_DESCRIPTION}</td>
<td align="left">{$smarty.const.STRING_MODULE_EMAIL_COMMENTS}</td>
<td align="left" class="toph3">{$smarty.const.STRING_SHIPPING_METHODS_ARE_ALLOWED}</td>
<td align="right">{$smarty.const.STRING_MODULE_NAME}</td>
<td align="right">{$smarty.const.ADMIN_SORT_ORDER}</td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr>
<tr><td height="4" colspan="8"></td></tr>
<tr class="liney">
<td align="center"><input type=checkbox name="new_Enabled" checked></td>
<td align="left"><input type=text name="new_name" class="textp" size="24"></td>
<td align="left"><textarea name="new_description" style="height: 44px; width: 170px;"></textarea></td>
<td align="left"><textarea name="new_email_comments_text" style="height: 44px; width: 170px;"></textarea></td>
<td align="left"><table border="0" cellpadding="0" cellspacing="0">
{section name=j loop=$shipping_methods}
<tr class="lineys">
<td><input type=checkbox name='new_ShippingMethodsToAllow_{$shipping_methods[j].SID}'></td>
<td>{$shipping_methods[j].Name}</td>
</tr>
{/section}
</table></td>
<td align="left"><select name='new_module' >
                                        <option value='null'> -- </option>
                                        {section name=j loop=$payment_modules}
                                        <option value='{$payment_modules[j]->get_id()}'> {$payment_modules[j]->title} </option>
                                        {/section}
</select><input type="hidden" name="new_calculate_tax" value="0"></td>

<td align="left"><input type=text name="new_sort_order" class="textp" size="4"></td>
<td></td>
</tr><tr><td height="4" colspan="8"></td></tr>
</table>
<input type=hidden name=dpt value=conf>
                <input type=hidden name=sub value=payment>
                <input type=hidden name=save_payment value=yes>
</form>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>
<a href="#" onclick="document.getElementById('payform').submit(); return false" class="inl">{$smarty.const.ADD_BUTTON}</a>
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adn"><tr><td class="help"><span class="titlecol2">{$smarty.const.USEFUL_FOR_YOU}</span><div class="helptext">{$smarty.const.STRING_MODULE_EMAIL_COMMENTS_TEXT}<br><br>{$smarty.const.ALERT_ADMIN2}</div></td>
        </tr>
      </table>