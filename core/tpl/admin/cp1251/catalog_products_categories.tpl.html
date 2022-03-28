<table class="adn">
  <tr>
    <td align="left" valign="top">
      <table class="adn">
        <tr>
          <td align="left" valign="bottom" width="100%">
            <table class="adw">
              <tr>
                <td align="left" valign="top"><span class="titlecol2">{if $searched_done}{$smarty.const.ADMIN_TEXT8}{else}{if $categoryID ne 0}{$category_name}{else}{$smarty.const.ADMIN_CATEGORY_ROOT}{/if}{/if}</span></td>
              </tr>
            </table>
          </td>
          <td align="right" valign="top">
                       <form method="POST" name="search_form" action='{$urlToSubmit}&amp;search=yes' id="search_form">
                           <table class="adw">
                                <tr class="lineys">
                                    <td width="100%" valign="top"></td>
                                    <td align="right" nowrap valign="middle"><b>{$smarty.const.ADMIN_SEARCH_PRODUCT_IN_CATEGORY}:</b>&nbsp;<select name="search_criteria" title="{$smarty.const.ADMIN_ADMIN_MENUNEW5}">
                                        <option value='name' {if $search_criteria == 'name'} selected {/if} > {$smarty.const.TABLE_PRODUCT_NAME} </option>
                                        <option value='product_code' {if $search_criteria == 'product_code'} selected {/if} > {$smarty.const.ADMIN_PRODUCT_CODE} </option></select></td>
                                    <td align="right" nowrap valign="middle"><input type="text" name="search_value" value="{$search_value}" title="{$smarty.const.ADMIN_ADMIN_MENUNEW4}" class="new"></td>
                                    <td align="right" nowrap valign="middle"><a href="#" onclick="document.getElementById('search_form').submit(); return false"><img src="data/admin/srg.gif" alt="search"></a></td>
                                </tr>
                           </table>
                        </form>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
    <tr>
        <td class="se"></td>
    </tr>
    <tr>
        <td valign="top" align="left" colspan="5">
        {if not $products && not $searched_done}
            <table class="adn">
                <tr class="lineb">
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align="center" valign="middle" height="20">{$smarty.const.STRING_EMPTY_CATEGORY}</td>
            </tr>
            <tr>
                <td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr>
            <tr>
                <td class="se5"></td>
            </tr>
            </table>
       <table class="adw">
       <tr class="link">
       <td><a href="{$smarty.const.ADMIN_FILE}?categoryID={$categoryID}&amp;eaction=prod" class="inl">{$smarty.const.ADMIN_TEXT3}</a>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
       <td><a href="{$smarty.const.ADMIN_FILE}?w=-1{if $categoryID and $categoryID!=1}&amp;catslct={$categoryID}{/if}&amp;eaction=cat" class="inl">{$smarty.const.ADMIN_TEXT4}</a></td>{if $categoryID and $categoryID!=1}<td>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{$smarty.const.ADMIN_FILE}?categoryID={$categoryID}&amp;eaction=cat" class="inl">{$smarty.const.ADMIN_ADMIN_MENUNEW1}</a></td>{/if}
       </tr>
       </table>
         {else}
         {if $couldntToDelete eq 1}<br><font color="red"><b>{$smarty.const.COULD_NOT_DELETE_THIS_PRODUCT}</b></font><br><br>{/if}
         {if $couldntToDeleteThisProducts}<br><font color="red"><b>{$smarty.const.COULD_NOT_DELETE_THESE_PRODUCT}</b></font><br><br>{/if}
         <form action='{$urlToSubmit}' method="POST" name="form" id="form">
         {if $products}
         <script type="text/javascript">
<!--
{literal}
function checkBoxes_products(_idForm, _syncID, _checkableID){

if(document.getElementById(_syncID).checked == false){
{/literal}
{section name=i loop=$products}
document.getElementById('checkbox_products_id_{$products[i].productID}').checked = false;
{/section}
{literal}
}else{
{/literal}
{section name=i loop=$products}
document.getElementById('checkbox_products_id_{$products[i].productID}').checked = true;
{/section}
{literal}
}
return true;
}
{/literal}
//-->
</script>
           <table class="adn">
                 <tr class="lineb"><td align="center" valign="middle"><input type="checkbox" class="round" id="id_checkall" onclick="checkBoxes_products('MainForm', 'id_checkall', 'id_ch');"  title="{$smarty.const.ADMIN_SEL_TITLEALL}"></td>
                  <td align="center">{$smarty.const.ADMIN_PRODUCT_ENABLED}</td>
                  <td align="left" width="100%"><a href='{$urlToSort}&amp;sort=name&amp;sort_dir=ASC'  title="{$smarty.const.ADMIN_ADMIN_MENUNEW11}" class="liv">{$smarty.const.ADMIN_PRODUCT_NAME}</a></td>
                  <td align="right"><a href='{$urlToSort}&amp;sort=product_code&amp;sort_dir=ASC'  title="{$smarty.const.ADMIN_ADMIN_MENUNEW11}" class="liv">{$smarty.const.ADMIN_PRODUCT_CODE}</a></td>
                  <td align="right"><a href='{$urlToSort}&amp;sort=Price&amp;sort_dir=ASC' title="{$smarty.const.ADMIN_ADMIN_MENUNEW11}" class="liv">{$smarty.const.ADMIN_PRODUCT_PRICE}</a></td>
                  {if $smarty.const.CONF_CHECKSTOCK eq 1}
                  <td align="right"><a href='{$urlToSort}&amp;sort=in_stock&amp;sort_dir=ASC' title="{$smarty.const.ADMIN_ADMIN_MENUNEW11}" class="liv">{$smarty.const.ADMIN_PRODUCT_INSTOCK}</a></td>
                  {/if}
                  <td align="right"><a href='{$urlToSort}&amp;sort=sort_order&amp;sort_dir=ASC' title="{$smarty.const.ADMIN_ADMIN_MENUNEW11}" class="liv">{$smarty.const.ADMIN_SORTM}</a></td>
                  <td align="center"><a href='{$urlToSort}&amp;sort=viewed_times&amp;sort_dir=DESC' title="{$smarty.const.ADMIN_ADMIN_MENUNEW10}" class="liv">VT</a></td>
                  <td align="center"><a href='{$urlToSort}&amp;sort=items_sold&amp;sort_dir=DESC' title="{$smarty.const.ADMIN_ADMIN_MENUNEW10}" class="liv">SL</a></td>
                  {if $smarty.const.CONF_USE_RATING eq 1}
                  <td align="center"><a href='{$urlToSort}&amp;sort=customers_rating&amp;sort_dir=DESC' title="{$smarty.const.ADMIN_ADMIN_MENUNEW10}" class="liv">PR</a></td>
                  {/if}
                  <td align="center">{$smarty.const.ADMIN_SPECIAL}</td>
                  <td align="center">{$smarty.const.ADMIN_ON3}</td>
                </tr>
                {assign var="admhl" value=0}
                {section name=i loop=$products}
                {if $admhl eq 1}<tr><td colspan="15" class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr>{else}{assign var="admhl" value=1}{/if}
                <tr class="liney hover">
                  <td align="center"><input type="checkbox" class="round" name="checkbox_products_id_{$products[i].productID}" id="checkbox_products_id_{$products[i].productID}" title="{$smarty.const.ADMIN_SEL_TITLE}"></td>

                  <td align="center"><input type="hidden" name="enable_{$products[i].productID}" id="enable_{$products[i].productID}" {if $products[i].enabled}value='1'{else}value='0'{/if} ><input type="checkbox" class="round" name="checkbo_en_{$products[i].productID}" id="checkbo_en_{$products[i].productID}" {if $products[i].enabled}checked{/if} onclick='CheckBoxHandler({$products[i].productID})'  title="{$smarty.const.ADMIN_ADMIN_MENUNEW6}"></td>

                  <td align="left"><a href="{$smarty.const.ADMIN_FILE}?productID={$products[i].productID}&amp;eaction=prod" title="{$smarty.const.ADMIN_ADMIN_MENUNEW9}" {if !$products[i].enabled}class="greyy"{/if}>{$products[i].name}</a></td>

                  <td align="right" {if !$products[i].enabled}class="toph3 gryy"{else}class="toph3{if !$products[i].product_code} bas{/if}"{/if}>{if $products[i].product_code}{$products[i].product_code}{else}{$smarty.const.ADMIN_NOCODE_PROD}{/if}</td>

                  <td align="right"><input type="text" name="price_{$products[i].productID}" value="{$products[i].Price}" class="prc prcs{if !$products[i].enabled} gryy{/if}"></td>

                  {if $smarty.const.CONF_CHECKSTOCK eq 1}
                  <td align="right">{if $products[i].in_stock <= 0}<input type="text" name="left_{$products[i].productID}" value="{$products[i].in_stock}" class="prc prcss{if !$products[i].enabled} gryy{/if}">{else}<input type="text" name="left_{$products[i].productID}" value="{$products[i].in_stock}" class="prc prcss {if !$products[i].enabled}gryy{/if}">{/if}</td>
                  {/if}

                  <td align="right"><input name='sort_order_{$products[i].productID}' type='text' class="prc prcss{if !$products[i].enabled} gryy{/if}" value="{$products[i].sort_order}"></td>
                  <td align="center" {if !$products[i].enabled}class="toph3 gryy"{else}class="toph3"{/if}>{$products[i].viewed_times}</td>
                  <td align="center" {if !$products[i].enabled}class="toph3 gryy"{else}class="toph3"{/if}>{$products[i].items_sold}</td>
                  {if $smarty.const.CONF_USE_RATING eq 1}
                  <td align="center" {if !$products[i].enabled}class="toph3 gryy"{else}class="toph3"{/if}>{$products[i].customers_rating}</td>
                  {/if}

                  <td align="center">{if $products[i].picture_count ne 0}<a href="{$smarty.const.ADMIN_FILE}?dpt=catalog&amp;sub=special&amp;new_offer={$products[i].productID}" title="{$smarty.const.ADMIN_ADMIN_MENUNEW7}" {if !$products[i].enabled}class="greyy"{/if}>+</a>{else}&nbsp;{/if}</td>
                  <td align="center"><a href="#" onclick="confirmDelete({$products[i].productID},'{$smarty.const.QUESTION_DELETE_CONFIRMATION_PROD}','{$urlToDelete}&amp;terminate='); return false" title="{$smarty.const.QUESTION_DELETE_CONFIRMATION_PROD}" {if !$products[i].enabled}class="greyy"{/if}>X</a></td>
                  </tr>
                  {/section}

                  {if $navigatorHtml}
                  <tr>
                    <td class="navigator" colspan="15">{$navigatorHtml}</td>
                  </tr>
                  {else}
                  <tr>
                    <td class="separ" colspan="15"><img src="data/admin/pixel.gif" alt="" class="sep"></td>
                  </tr>
                  {/if}
                  <tr>
                    <td class="se5" colspan="15"></td>
                  </tr>
                </table>
        {else}
        {if $searched_done &&  not $products}
            <table class="adn">
            <tr class="lineb">
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align="center" valign="middle" height="20">{$searched_count}</td>
            </tr>
            <tr>
                <td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr>
            <tr>
                <td class="se5"></td>
            </tr>
            </table>
        {/if}
        {/if}
        <input type="hidden" name="dpt" value="catalog">
        <input type="hidden" name="sub" value="products_categories">
        <input type="hidden" name="categoryID" value="{$categoryID}">
        <input type="hidden" name="products_update">
        <input type="hidden" name="add_command" value="off" id="add_command">

       <table class="adw">
       <tr class="link">
       {if $products}<td><a href="#" onclick="document.getElementById('form').submit(); return false" class="inl">{$smarty.const.ADMIN_TEXT7}</a>&nbsp;&nbsp;|&nbsp;&nbsp;</td>{/if}
       {if !$searched_done}<td><a href="#" class="inl" onclick="confirmDelete(0,'{$smarty.const.QUESTION_DELETE_CONFIRMATION}','{$smarty.const.ADMIN_FILE}?dpt=catalog&amp;sub=products_categories&amp;categoryID={$categoryID}&amp;delete_all_products=1'); return false">{$smarty.const.ADMIN_TEXT9}</a>&nbsp;&nbsp;|&nbsp;&nbsp;</td>{/if}
       <td><a href="{$smarty.const.ADMIN_FILE}?categoryID={$categoryID}&amp;eaction=prod" class="inl">{$smarty.const.ADMIN_TEXT3}</a>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
       <td><a href="{$smarty.const.ADMIN_FILE}?w=-1{if $categoryID and $categoryID!=1}&amp;catslct={$categoryID}{/if}&amp;eaction=cat" class="inl">{$smarty.const.ADMIN_TEXT4}</a></td>{if $categoryID and $categoryID!=1}<td>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{$smarty.const.ADMIN_FILE}?categoryID={$categoryID}&amp;eaction=cat" class="inl">{$smarty.const.ADMIN_ADMIN_MENUNEW1}</a></td>{/if}
       </tr></table>
       {if $products}<table class="adw"><tr><td class="se5"></td></tr><tr class="link"><td>C отмеченными:&nbsp;&nbsp;<a href="#" onclick="document.getElementById('add_command').value='prod_on'; document.getElementById('form').submit(); return false" class="inl">Включить</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" onclick="document.getElementById('add_command').value='prod_off'; document.getElementById('form').submit(); return false" class="inl">Выключить</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" onclick="document.getElementById('add_command').value='prod_dell'; document.getElementById('form').submit(); return false" class="inl">Удалить</a>&nbsp;&nbsp;|&nbsp;&nbsp;
<select name="prod_categoryID"><option value="1" selected>{$smarty.const.ADMIN_CATEGORY_ROOT}</option>
{section name=z loop=$cats}
<option value="{$cats[z].categoryID}">{section name=j loop=$cats[z].level}&nbsp;&nbsp;&nbsp;{/section}{$cats[z].name}</option>
{/section}
</select>&nbsp;&nbsp;<a href="#" onclick="document.getElementById('add_command').value='prod_move'; document.getElementById('form').submit(); return false" class="inl">Переместить</a></td></tr></table>{/if}
       </form>
       {/if}
<table class="adn"><tr><td class="se6"></td></tr></table>
        <table class="adn"><tr><td class="help"><span class="titlecol2">{$smarty.const.USEFUL_FOR_YOU}</span><div class="helptext">{$smarty.const.ALERT_ADMIN}</div></td></tr></table>
        </td></tr></table>
{if $products}
<script type="text/javascript">
function CheckBoxHandler(id){literal}{{/literal}
if ( document.getElementById('checkbo_en_' + id).checked )
document.getElementById('enable_' + id).value = '1';
else
document.getElementById('enable_' + id).value = '0';
{literal}}{/literal}
</script>
{/if}
{if $products_count_category}
<script type="text/javascript">
        if(document.getElementById('preproc')){literal}{{/literal}
        document.getElementById('preproc').innerHTML='<span id="axproc" style="color: #C5D2ED; font-size: 11px;">{$smarty.const.PRODUCTS_IN_CATTEK} {$products_count_category}<\/span>';
        {literal}}{/literal}
</script>
{/if}