<form action="" method="POST" name="form_delete_linkcategory" id="form_delete_linkcategory">
<input name="fACTION" value="DELETE_LINK_CATEGORY" type="hidden">
<input name="fREDIRECT" value="{$REQUEST_URI}" type="hidden">
<input name="LINK_CATEGORY[le_cID]" type="hidden">
</form>
{section name=i loop=$le_categories}{if $le_categories[i].le_cID==$le_CategoryID}{assign var='le_CategoryName' value=$le_categories[i].le_cName}{/if}{/section}
<div class="edit_le_category" id="category_renblock"></div>
<table class="adw">
              <tr>
                <td align="left" valign="top"><span class="titlecol2">{if $le_CategoryName && $le_CategoryName != ""}{$le_CategoryName}{else}{$smarty.const.ADMIN_LE_ALL_CATEGORIES}{/if}</span></td>
                       </tr>
                      </table>
<table class="adn"><tr><td class="se5"></td></tr></table>
{if $last_page}
<!-- Links list block -->
<div id="le_links">
<form action="" method="POST" id="form_change_links">
<input name="fACTION" value="" type="hidden" id="fact1">
<input name="fREDIRECT" value="{'msg='|set_query}" type="hidden">
<table class="adn">
<tr class="lineb">
<td align="center"><input id="id_checkall" onclick="checkBoxes('form_change_links', 'id_checkall', 'id_ch');" type="checkbox" class="round"></td>
<td align="left" width="30%">{$smarty.const.ADMIN_LE_LINK_URL}</td>
<td align="left" width="30%">{$smarty.const.ADMIN_LE_LINK_TEXT}</td>
<td align="left" width="30%">{$smarty.const.STRING_DESCRIPTION}</td>
<td align="right" class="toph3">{$smarty.const.ADMIN_LE_LINK_VERIFIED}</td>
</tr>
{foreach from=$le_links item=_le_link}
<tr><td height="4" colspan="5"></td></tr>
<tr class="liney">
<td align="center"><input name="LINKS_IDS[]" value="{$_le_link.le_lID}" type="checkbox" id="id_ch" class="round"></td>
<td align="left"><input name="LINK[{$_le_link.le_lID}][le_lURL]" value="{$_le_link.le_lURL}" type="text"  class="textp" size="24"></td>
<td align="left"><input name="LINK[{$_le_link.le_lID}][le_lText]" size="24" value="{$_le_link.le_lText}" type="text" class="textp"></td>
<td align="left"><input name="LINK[{$_le_link.le_lID}][le_lDesk]" size="24" value="{$_le_link.le_lDesk}" type="text" class="textp"></td>
<td align="right" class="toph3">{if $_le_link.le_lVerified}{if $_le_link.le_lVerified!='0000-00-00 00:00:00'}{$_le_link.le_lVerified}{else}{$smarty.const.ADMIN_LE_LINK_NOT_VERIFIED}{/if}{else}{$smarty.const.ADMIN_LE_LINK_NOT_VERIFIED}{/if}</td>
</tr>
{/foreach}
{if $last_page>1}
<tr><td height="4" colspan="5"></td></tr>
<tr>
<td colspan="5" class="navigator">{if $curr_page > 1}<a href ="{"page=`$curr_page-1`&amp;show_all="|set_query}">{$smarty.const.STRING_PREVIOUS}</a>&nbsp;|{/if}{foreach from=$le_lister_range item=_page}&nbsp;{if $_page!=$curr_page or $showAllLinks}<a class="no_underline" href="{"page=`$_page`&amp;show_all="|set_query}">{$_page}</a>{else}<b>{$_page}</b>{/if}&nbsp;|{/foreach}
                                        {if $curr_page < $last_page}<a href ="{"page=`$curr_page+1`&amp;show_all="|set_query}">{$smarty.const.STRING_NEXT}</a>&nbsp;|&nbsp;{/if}{if $showAllLinks}<b>{$smarty.const.STRING_SHOWALL}</b>{else}<a class="no_underline" href ="{"show_all=yes"|set_query}">{$smarty.const.STRING_SHOWALL}</a>{/if}</td>
                        </tr>
</table><table class="adn"><tr><td class="se5"></td></tr></table>
{else}
<tr><td height="4" colspan="5"></td></tr>
</table><table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>{/if}
<table class="adw"><tr><td>{$smarty.const.STRING_LE_TODO} <select name="new_le_lCategoryID">
                <option value="0">{$smarty.const.STRING_ERROR_LE_CHOOSE_CATEGORY}</option>
                {foreach from=$le_categories item=_category}
                        <option value="{$_category.le_cID}"
                                {if $le_CategoryID==$_category.le_cID} selected="selected"
                                {elseif $pst_LINK.le_lCategoryID==$_category.le_cID} selected="selected"
                                {/if}
                                >{$_category.le_cName}</option>
                {/foreach}
                </select>&nbsp;&nbsp;<a href="#" onclick="getElementById('fact1').value = 'MOVE_LINKS';getElementById('form_change_links').submit(); return false" class="inl">{$smarty.const.ADMIN_CATEGORY_MOVE_TO}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" onclick="getElementById('fact1').value = 'SAVE_LINKS';getElementById('form_change_links').submit(); return false" class="inl">{$smarty.const.SAVE_CHANGES_BUTTON}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" onclick="getElementById('fact1').value = 'VERIFY_LINKS';getElementById('form_change_links').submit(); return false" class="inl">{$smarty.const.VERIFIED_BUTTON}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" onclick="getElementById('fact1').value = 'UNVERIFY_LINKS';getElementById('form_change_links').submit(); return false" class="inl">{$smarty.const.UNVERIFIED_BUTTON}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" onclick="if(!window.confirm('{$smarty.const.QUESTION_DELETE_CONFIRMATION}'))return false;getElementById('fact1').value = 'DELETE_LINKS';getElementById('form_change_links').submit(); return false" class="inl">{$smarty.const.DELETE_BUTTON}</a></td>
                </tr>
                </table>
<table class="adn"><tr><td class="se6"></td></tr></table>
</form>
</div>
<!-- /Links list block -->
{else}
<table class="adn">
<tr class="lineb">
<td align="left">&nbsp;</td>
</tr>
            <tr>
                <td align="center" valign="middle" height="24">{$smarty.const.ADMIN_NO_LINKS_IN}</td></tr></table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se6"></td></tr></table>
{/if}
<!-- New link form block -->
        <div class="new_link_category" id="id_new_linkcategory">
        <form action="" method="POST" id="form_new_linkcategory" name="form_new_linkcategory">
        <input name="fACTION" value="NEW_LINK_CATEGORY" type="hidden">
        <input name="fREDIRECT" value="{$REQUEST_URI}" type="hidden">
<table class="adn">
<tr class="lineb">
<td align="left">{$smarty.const.ADMIN_LE_NEW_CATEGORY_NAME2}</td>
</tr>
<tr class="lins">
<td><input name="LINK_CATEGORY[le_cName]" type="text" class="textp" size="36" id="id_new_category"></td></tr>
</table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>
<a href="#" onclick="document.getElementById('form_new_linkcategory').submit(); return false" class="inl">{$smarty.const.SAVE_BUTTON}</a>
<table class="adn"><tr><td class="se6"></td></tr></table>
</form>
</div>
<div id="le_new_link">
<form action="" id="form_new_le_link" method="POST" name="form_new_le_link">
<input name="fACTION" value="NEW_LINK" type="hidden">
<input name="fREDIRECT" value="{'msg='|set_query}" type="hidden">
<table class="adn">
<tr class="lineb">
<td align="left">{$smarty.const.ADMIN_LE_NEW_CATEGORY_NAME_LINK}</td></tr>
<tr class="lins">
<td align="left">{$smarty.const.ADMIN_LE_LINK_CATEGORY}: <select name="LINK[le_lCategoryID]">
        <option value="0">{$smarty.const.ADMIN_NOT_DEFINED}</option>
        {foreach from=$le_categories item=_category}
        <option value="{$_category.le_cID}"
                {if $le_CategoryID==$_category.le_cID} selected="selected"
                {elseif $pst_LINK.le_lCategoryID==$_category.le_cID} selected="selected"
                {/if}
                >{$_category.le_cName}</option>
        {/foreach}
        </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$smarty.const.ADMIN_LE_LINK_URL}: <input name="LINK[le_lURL]" value="{if $pst_LINK.le_lURL}{$pst_LINK.le_lURL}{else}http://{/if}" type="text" size="44" class="textp"><br><br>{$smarty.const.ADMIN_LE_LINK_TEXT}: <input name="LINK[le_lText]" value="{$pst_LINK.le_lText}" type="text" size="44" class="textp">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$smarty.const.STRING_DESCRIPTION}: <input name="LINK[le_lDesk]" value="{$pst_LINK.le_lDesk}" type="text" size="44" class="textp"></td></tr>
</table>
</form></div>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>
<a href="#" onclick="document.getElementById('form_new_le_link').submit(); return false" class="inl">{$smarty.const.SAVE_BUTTON}</a>
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adn"><tr><td class="help"><span class="titlecol2">{$smarty.const.USEFUL_FOR_YOU}</span><div class="helptext">{$smarty.const.ALERT_ADMIN_LINKEDIT}</div></td>
        </tr>
      </table>
{if $show_new_link}
{if $last_page}
<script type="text/javascript">
<!--
le_show_newlink();
//-->
</script>
{/if}
{/if}
{if $error_message}<script type="text/javascript" defer>alert('{$error_message}')</script>{/if}