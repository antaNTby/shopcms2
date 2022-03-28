{* шаблон формы расширенного поиска *}

{if $categories_to_select}
<form name='AdvancedSearchInCategory' method="GET" action="index.php" id="AdvancedSearchInCategory">
  <input type=hidden name='categoryID' value='{$categoryID}'>
  <input type=hidden name='search_with_change_category_ability' value='yes'>
  <table cellspacing="1" cellpadding="0" width="100%" class="gre">
    <tr>
      <td class="lt pad" align="right" style="white-space: nowrap">{$smarty.const.STRING_CATEGORY}:</td>
      <td class="padt" align="left" width="100%"><select name='categorySelect' onchange='_categoryChangedHandler()'>          
          {if !$categoryID}
          <option value='0'>{$smarty.const.ADMIN_PROMPT_TO_SELECT}</option>
          {/if}
          {section name=i loop=$categories_to_select}
          <option value='{$categories_to_select[i].categoryID}'{if $categories_to_select[i].categoryID == $categoryID} selected {/if} >{section name=j loop=$categories_to_select[i].level}&nbsp;&nbsp;&nbsp;{/section}{$categories_to_select[i].name}</option>
          {/section}

        </select>
        {literal}
        <script type="text/javascript">
          function _categoryChangedHandler()
          {
          if (document.AdvancedSearchInCategory.categorySelect.value != 0)
          window.location = 'index.php?categoryID=' +
          document.AdvancedSearchInCategory.categorySelect.value +
          '&search_with_change_category_ability=yes';
          }
        </script>
        {/literal}
      </td>
    </tr>
    
	{if $categoryID}
    <tr>
      <td class="lt pad" align="right" style="white-space: nowrap">{$smarty.const.STRING_NAME}:</td>
      <td class="padt" align="left" width="100%"><input type="text" name="search_name" size="50" value="{$search_name}"></td>
    </tr>
    <tr>
      <td class="lt pad" align="right" style="white-space: nowrap">{$smarty.const.STRING_PRODUCT_PRICE}:</td>
      <td class="padt" align="left" width="100%">{$smarty.const.STRING_PRICE_FROM} <input name="search_price_from" type="text" size="10" value="{$search_price_from}" > {$smarty.const.STRING_PRICE_TO} <input name="search_price_to" type="text" size="10" value="{$search_price_to}" > {$priceUnit}</td>
    </tr>
    {/if}
    
	{if $params}
    {section name=i loop=$params}
    <tr>
      <td class="lt pad" align="right" style="white-space: nowrap">{$params[i].name}:</td>
      <td class="padt" align="left" width="100%">{if $params[i].controlIsTextField eq 1}
        <input type=text size="50" name='param_{$params[i].optionID}' value='{$params[i].value}'>
        {else}
        <select name='param_{$params[i].optionID}'>
          <option value='0'>{$smarty.const.STRING_UNIMPORTANT}</option>          
          {section name=j loop=$params[i].variants}
          <option value='{$params[i].variants[j].variantID}' {if $params[i].value == $params[i].variants[j].variantID} selected {/if}>{$params[i].variants[j].value}</option>
		  {/section}
        </select>
        {/if}
	  </td>
    </tr>
    {/section}
    {/if}
  </table>
  
  {if $categoryID}
  <input type="hidden" value="yes" name="advanced_search_in_category">
  {if $show_subcategory_checkbox}
  {if $show_subcategories_products}
  <input type=hidden value='1' name='search_in_subcategory'>
  {else}
  <div class="fil1"></div>
  <table cellspacing="0" cellpadding="0">
    <tr>
      <td class="padt" style="padding-left: 0px;"><input type=checkbox value='1' name='search_in_subcategory' {if $search_in_subcategory} checked  {/if}></td>
      <td valign="middle" align="left">{$smarty.const.STRING_SEARCH_IN_SUBCATEGORIES}</td>
    </tr>
  </table>
  {/if}
  {/if}
  
  <div class="fil1"></div>
  <table cellspacing="0" cellpadding="0">
    <tr>
      <td><table cellspacing="0" cellpadding="0" class="fsttab">
          <tr>
            <td>
			  <table cellspacing="0" cellpadding="0" class="sectb">
                <tr>
                  <td><a href="#" onclick="validate_search(this); return false">{$smarty.const.FIND_BUTTON}</a></td>
                </tr>
              </table>
			</td>
          </tr>
        </table>
        <input type=hidden name='advanced_search_in_category' value="on"></td>
    </tr>
  </table>
  {/if}
</form>
{else}
<div align="center">{$smarty.const.STRING_NO_CATEGORIES}</div>
{/if}