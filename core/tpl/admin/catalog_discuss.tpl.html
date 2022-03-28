{* discussions *}
{if $answer}
<form action='{$return_url}&amp;answer={$discussion.DID}' method=post id="ansform">
            <table class="adn">
              <tr>
                <td align="left" valign="top"><span class="titlecol2">{$smarty.const.ADMIN_DISCUSSIONS_ANS}: {$discussion.product_name}</span></td>
                       </tr>
                      </table>
<table class="adn"><tr><td class="se5"></td></tr></table>
<table class="adn">
<tr class="lineb">
<td align=left>{$smarty.const.DISCUSSION_NICKNAME}</td>
<td align="left">{$smarty.const.ADMIN_DISCUSSION_ADDITION_TIME}</td>
<td align="left" class="toph3">{$smarty.const.DISCUSSION_SUBJECT}</td>
<td align="left" width="100%">{$smarty.const.DISCUSSION_BODY}</td>
</tr>
{if $admhl eq 1}
<tr><td colspan="4" class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr>
{else}{assign var="admhl" value=1}{/if}
<tr class="liney">
<td align="left">{$discussion.Author}</td>
<td align="left" class="toph3">{$discussion.add_time}</td>
<td align="left">{$discussion.Topic}</td>
<td align="left">{$discussion.Body}</td>
</tr>
</table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se6"></td></tr></table>
            <table class="adn">
              <tr>
                <td align="left" valign="top"><span class="titlecol2">{$smarty.const.ADMIN_ANSWER_TO_DISCUSSION}</span></td>
                       </tr>
                      </table>
<table class="adn"><tr><td class="se5"></td></tr></table>
<table class="adn">
<tr class="lineb">
<td align=left>{$smarty.const.DISCUSSION_NICKNAME}</td>
<td align=left width="100%">{$smarty.const.DISCUSSION_SUBJECT}</td>
</tr>
<tr class="lins">
<td align="left"><input type=text style="width: 190px;" name='newAuthor' class="textp" value='{$smarty.const.ADMIN_DISCUSSIONS_ADMIN}'></td>
<td align="left"><input type=text style="width: 380px;" name='newTopic' class="textp" value='Re: {$discussion.Topic}'></td>
</tr></table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se6"></td></tr></table>
            <table class="adn">
              <tr>
                <td align="left" valign="top"><span class="titlecol2">{$smarty.const.DISCUSSION_BODY}</span></td>
                       </tr>
                      </table>
<textarea name='newBody' class="admin"></textarea>
<table class="adn"><tr><td class="se5"></td></tr></table>
<input type=hidden value='OK' name='add'>
<a href="#" onclick="document.getElementById('ansform').submit(); return false" class="inl">{$smarty.const.ANSWER_TO_DISCUSSION}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" onclick="document.getElementById('ansform').reset(); return false" class="inl">{$smarty.const.ADMIN_CLEAR}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='{$return_url}' class="inl">{$smarty.const.ADMIN_RETURN_TO_MESSAGES}</a>
</form>
{else}
<form action='{$smarty.const.ADMIN_FILE}' method=post name=MainForm>
{if $products}
<select name='productID_Select' onchange="if (document.MainForm.productID_Select.value!=-1) window.location=document.MainForm.productID_Select.value">
{if !$discussions}
<option value='-1'
{if !$productID}
selected
{/if}
> {$smarty.const.ADMIN_PROMPT_TO_SELECT} </option>
{/if}
<option value='{$urlToFind}&amp;productID=0'> {$smarty.const.ADMIN_ALL_PRODUCTS} </option>
{section name=i loop=$products}
<option value='{$urlToFind}&amp;productID={$products[i].productID}'
{if $products[i].productID == $productID}  selected {/if}
> {$products[i].product_name} </option>
{/section}
</select>&nbsp;&nbsp;&nbsp;<b>{$smarty.const.ADMIN_DISCUSSIONS_SELECT}</b>
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adn">
<tr class="lineb">
<td align="left"><a href='{$urlToSort}&amp;sort=add_time&amp;direction=DESC' class="liv">{$smarty.const.ADMIN_DISCUSSION_ADDITION_TIME}</a></td>
<td align="left" width="40%"><a href='{$urlToSort}&amp;sort=product_name&amp;direction=ASC' class="liv">{$smarty.const.TABLE_PRODUCT_NAME}</a></td>
<td align="left"><a href='{$urlToSort}&amp;sort=Author&amp;direction=ASC' class="liv">{$smarty.const.DISCUSSION_NICKNAME}</a></td>
<td align="left" class="toph3"><a href='{$urlToSort}&amp;sort=Topic&amp;direction=ASC' class="liv">{$smarty.const.DISCUSSION_SUBJECT}</a></td>
<td align="left" width="60%">{$smarty.const.DISCUSSION_BODY}</td>
<td align="center">{$smarty.const.QUESTION_ANS_COUNT}</td>
<td align="center">Del</td>
</tr>{assign var="admhl" value=0}
{section name=i loop=$discussions}
                {if $admhl eq 1}
<tr><td colspan="7" class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr>
                  {else}{assign var="admhl" value=1}{/if}
                <tr class="liney">
                <td class="toph3" align="left">{$discussions[i].add_time}</td>
                <td align="left">{$discussions[i].product_name}</td>
                <td align="left">{$discussions[i].Author}</td>
                <td align="left">{$discussions[i].Topic}</td>
                <td align="left">{$discussions[i].Body}</td>
                <td align="center"><a href='{$fullUrl}&amp;answer={$discussions[i].DID}'>+</a></td>
                <td align="center"><a href="#" onclick="confirmDelete({$discussions[i].DID},'{$smarty.const.QUESTION_DELETE_CONFIRMATION}', '{$fullUrl}&amp;delete=');">X</a></td>
                </tr>
{sectionelse}<tr><td colspan="7" align="center" height="20">{$smarty.const.STRING_EMPTY_LIST}</td></tr>
{/section}
{if $navigator}
<tr>
<td class="navigator" colspan="7">{$navigator}</td>
</tr></table>
{else}
</table><table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr></table>
{/if}
{else}
<table class="adn">
<tr class="lineb">
<td align="left">&nbsp;</td>
</tr>
<tr>
<td align="center" valign="middle" height="24">{$smarty.const.STRING_EMPTY_LIST}</td></tr>
</table><table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr></table>
{/if}
</form>
{/if}
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adn"><tr><td class="help"><span class="titlecol2">{$smarty.const.USEFUL_FOR_YOU}</span><div class="helptext">{$smarty.const.ALERT_ADMIN2}</div></td>
        </tr>
      </table>