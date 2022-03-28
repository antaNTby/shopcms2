{* product tree for froogle-module and yandexmarket-module *}
{literal}
<script type="text/javascript">
<!--
function checkAllProducts(_ProdNum){

        for(var i=1; i<=_ProdNum; i++){

                document.getElementById('prod'+i).checked = document.getElementById('id_checkallprod').checked
        }
}
//-->
</script>
{/literal}
<input name="expandID" type="hidden">
<input name="unexpandID" type="hidden">
<input name="showProducts" type="hidden">
<input name="updateCategory" value="" type="hidden">

{foreach from=$ProductCategories item=_ProductCategory}<table class="adn">
<tr>
<td style="padding-left: {$_ProductCategory.level*20}px"  valign="top">
{if $_ProductCategory.ExistSubCategories}
{if $_ProductCategory.ExpandedCategory}
<a href="#" onclick="document.form_export.elements['unexpandID'].value = {$_ProductCategory.categoryID};
                                                        document.form_export.submit();
                                                        return false;"><img src="data/admin/minus.gif" alt=""></a>
{else}<a href="#" onclick="document.form_export.elements['expandID'].value = {$_ProductCategory.categoryID};
                                                        document.form_export.submit();
                                                        return false;"><img src="data/admin/mplus.gif" alt=""></a>
{/if}{else}<img src="data/admin/dr.gif" alt="">{/if}</td><td valign="top">
{count array=$smarty.session.selectedProducts[$_ProductCategory.categoryID] item=_ProductsNum}
<input name="CHECKED_CATEGORIES[{$_ProductCategory.categoryID}]"
                                                type="checkbox" class="round" value="1"
                                                onclick="
                                                        document.form_export.elements['updateCategory'].value = {$_ProductCategory.categoryID};
                                                        document.form_export.submit();
                                                        return false;"
                                                id="{$_ProductCategory.categoryID}"
{if $smarty.session.checkedCategories[$_ProductCategory.categoryID] or $_ProductsNum or $smarty.session.selectedProductsIncSub[$_ProductCategory.categoryID]} checked="checked"{/if}>
</td><td width="100%" style="padding: 0 0 4px 4px;">
{if $_ProductCategory.products_count_category}
<a class="inl" href="#" onclick="document.form_export.elements['showProducts'].value = {$_ProductCategory.categoryID};
                                        document.form_export.submit();
                                        return false;
                                        ">{$_ProductCategory.name}</a>
                        {else}
                                {$_ProductCategory.name}
                        {/if}
                        {if $_ProductCategory.ExpandedCategory or !$_ProductCategory.ExistSubCategories}
                                (
                                {if $_ProductsNum}
                                        {$_ProductsNum}
                                {elseif $smarty.session.checkedCategories[$_ProductCategory.categoryID]}
                                        {$_ProductCategory.products_count_category}
                                {else}
                                0
                                {/if}
                                /
                                {$_ProductCategory.products_count_category}
                                )
                        {else}
                                (
                                {if $smarty.session.selectedProductsIncSub[$_ProductCategory.categoryID]}
                                        {$smarty.session.selectedProductsIncSub[$_ProductCategory.categoryID]}
                                {elseif $smarty.session.selectedProductsIncSub[$_ProductCategory.categoryID]}
                                        {$_ProductCategory.products_count}
                                {else}0
                                {/if}
                                /
                                {$_ProductCategory.products_count}
                                )
                        {/if}
                        {if $showProducts==$_ProductCategory.categoryID and $ProductsNum}
                        <br><br><input name="cIDForProducts" value="{$showProducts}" type="hidden">
<table class="adn">
<tr class="lineb">
<td align="center"><input type="checkbox" onclick="checkAllProducts({$ProductsNum})" id="id_checkallprod"
                                        {if $smarty.session.checkedCategories[$_ProductCategory.categoryID]}
                                                checked="checked"
                                        {/if}
                                          class="round"></td>
<td align="left">{$smarty.const.ADMIN_PRODUCT_NAME}</td>
<td align="right">{$smarty.const.ADMIN_PRODUCT_PRICE},&nbsp;{$smarty.const.STRING_UNIVERSAL_CURRENCY}</td>
 </tr>
                                {counter start=0 skip=1 print=false}
                                {foreach from=$Products item=_Product}
                {if $admhl eq 1}
<tr><td colspan="3" class="separ"></td></tr>
                  {else}{assign var="admhl" value=1}{/if}
<tr class="liney">
      <td align="center"><input class="round" name="PRODUCTS[{$_Product.productID}]" value="1" id="prod{counter}" type="checkbox"  {if $smarty.session.selectedProducts[$showProducts][$_Product.productID]} checked="checked"  {/if}></td>
      <td align="left" width="100%">{$_Product.name}</td>
      <td align="right">{if $_Product.Price}{$_Product.Price}{else}{$smarty.const.MSG_COST_DOESNT_EXIST}{/if}</td>
     </tr>
   {/foreach}
</table>
<table class="adn"><tr><td class="separ"></td></tr><tr><td class="se5"></td></tr></table>
<input name="save_products" value="1" type="hidden"><a href="#" onclick="document.getElementById('form_export').submit(); return false;" class="inl">{$smarty.const.SELECT_BUTTON}</a>
<table class="adn"><tr><td class="se6"></td></tr></table>
{/if}
</td>
</tr></table>
{/foreach}
