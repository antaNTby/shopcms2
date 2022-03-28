{* Шаблон вывода заказа в отдельном окне на печать *}
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel=stylesheet href="data/admin/style.css" type="text/css" media="all">
<meta http-equiv="Content-Type" content="text/html; charset={$smarty.const.DEFAULT_CHARSET}">
<title>{$smarty.const.STRING_ORDER} #{$order.orderID}</title>
</head>
<body onLoad="window.print();" style="padding: 8px;">
{if $error ne ""}
<br><div align="center">{$error}</div><br>
{else}
<div align="left"><span style="padding-left: 1px;"><b>{$smarty.const.CONF_SHOP_NAME}</b></span></div>
<br>
<table class="adn">
<tr class="lineb">
<td align="left">{$smarty.const.STRING_ORDER} #{$order.orderID}</td>
</tr><tr><td height="4"></td></tr>
<tr valign="top">
<td>
<table class="adn">
<tr class="liney">
<td>{$smarty.const.TABLE_ORDER_TIME}: <b>{$order.order_time}</b></td>
</tr>
<tr class="liney">
<td>{$smarty.const.CUSTOMER_FIRST_NAME}: <b>{$order.customer_firstname}</b></td>
</tr>
<tr class="liney">
<td>{$smarty.const.CUSTOMER_LAST_NAME}: <b>{$order.customer_lastname}</b></td>
</tr>
                                        {section name=i loop=$order.reg_fields_values}
                                        <tr class="liney">
                                                <td>{$order.reg_fields_values[i].reg_field_name}: <b>{$order.reg_fields_values[i].reg_field_value}</b></td>
                                        </tr>
                                        {/section}
<tr class="liney">
<td>{$smarty.const.CUSTOMER_ADRESL}: <b>{if $order.shipping_address ne ""}{$order.shipping_address}{/if}{if $order.shipping_city ne ""}, {$order.shipping_city}{/if}{if $order.shipping_state ne ""}, {$order.shipping_state}{/if}{if $order.shipping_country ne ""}, {$order.shipping_country}{/if}</b></td>
</tr>           
                                        {if $order.shipping_type}
                                        <tr class="liney">
                                                <td>{$smarty.const.STRING_SHIPPING_TYPE2}: <b>{$order.shipping_type}{if $order.shippingServiceInfo} ({$order.shippingServiceInfo}){/if}</b></td>
                                        </tr>
                                        {/if}
			                            {if $order.payment_type}
                                        <tr class="liney">
                                                <td>{$smarty.const.STRING_PAYMENT_TYPE2}: <b>{$order.payment_type}</b></td>
                                        </tr>
                                        {/if}
</table>
{if $order.customers_comment}
<table class="adn"><tr><td height="6"></td></tr></table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se6"></td></tr></table>
<table class="adn">
<tr class="lineb">
<td align="left">{$smarty.const.TABLE_ORDER_COMMENTS}</td></tr>
<tr class="liney">
<td align="left">{$order.customers_comment|replace:"<":"&lt;"}</td>
</tr>
</table>
{/if}
</td>
</tr><tr><td height="6"></td></tr></table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>
<table class="adn">
<tr class="lineb">
<td align="left">{$smarty.const.ADMIN_PRODUCT_NAME}</td>
<td align="left">{$smarty.const.TABLE_PRODUCT_QUANTITY}</td>
<td align="right">{$smarty.const.TABLE_PRODUCT_COST_WITHOUT_TAX}&nbsp;</td>
</tr><tr><td height="3" colspan="3"></td></tr>
{section name=i loop=$orderContent}
<tr class="liney">
<td align="left" style="padding-right: 4px;">{$orderContent[i].name}
                                                        {if $orderContent[i].eproduct_filename}
                                                                {if $completed_order_status}
                                                                        {if $completed_order_status == $order.statusID}

                                                                                <br><a href='{$smarty.const.ADMIN_FILE}?do=get_file&amp;getFileParam={$orderContent[i].getFileParam}' class="sin">{$smarty.const.ADMIN_DOWN_LOAD} ({$orderContent[i].file_size} MB)</a>

                                                                                {if $orderContent[i].day_count_remainder > 0}
                                                                                        - {$smarty.const.ADMIN_EPRODUCT_AVAILABLE_DAYS}
                                                                                                {$orderContent[i].day_count_remainder}
                                                                                        {$smarty.const.ADMIN_DAYS}
                                                                                        {if $orderContent[i].load_counter_remainder != 0}
                                                                                                ,
                                                                                                {$smarty.const.ADMIN_REMANDER_EPRODUCT_DOWNLOAD_TIMES}
                                                                                                        {$orderContent[i].load_counter_remainder}
                                                                                                {$smarty.const.ADMIN_DOWNLOAD_TIMES}
                                                                                        {/if}
                                                                                {/if}
                                                                        {/if}
                                                                {/if}
                                                        {/if}</td>
<td align="left" style="padding-right: 4px;">{$orderContent[i].Quantity}</td>
<td align="right" nowrap="nowrap">{$orderContent[i].PriceToShow}</td>
</tr><tr><td colspan="3" class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr>
{/section}
</table>
<table class="adn">
<tr class="liney"><td width="99%" align="right">{$smarty.const.STRING_PRED_TOTAL}:</td>
                                        <td align="right" nowrap="nowrap">&nbsp;&nbsp;{$order.clear_total_priceToShow}</td>
                                </tr>
                                {if $order.order_discount > 0}
								<tr class="liney">
								        <td align="right">{$smarty.const.ADMIN_DISCOUNT} {$order.order_discount}%:</td>
								        <td align="right" nowrap="nowrap">{$order.order_discount_ToShow}</td>
								</tr>
								{/if}
                                <tr class="liney">
                                        <td align="right">{$smarty.const.ADMIN_SHIPPING_COST}:</td>
                                        <td align="right" nowrap="nowrap">&nbsp;&nbsp;{$order.shipping_costToShow}</td>
                                </tr>
                                <tr class="liney">
                                        <td align="right"><b>{$smarty.const.TABLE_TOTAL}:</b></td>
                                        <td align="right" nowrap="nowrap">&nbsp;&nbsp;<b>{$order.order_amountToShow}</b></td>
                                </tr>
                        </table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>
<div align="right"><b>{$smarty.const.GUSTOMER_PLEASURE}</b>&nbsp;&nbsp;</div>
{/if}
</body>
</html>