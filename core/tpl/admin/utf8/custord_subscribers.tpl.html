{if $smarty.const.CONF_BACKEND_SAFEMODE eq 1}
 <table class="adminw">
              <tr>
                <td align="left">
                  <table class="adn">
                    <tr>
                      <td><img src="data/admin/stop2.gif" align="left" class="stop"></td>
                      <td class="splin"><span class="error">{$smarty.const.ERROR_MODULE_ACCESS2}</span><br><br>{$smarty.const.ERROR_MODULE_ACCESS_DES2}</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
{else}{if $Message ne ''}{$Message}<table class="adn"><tr><td class="se6"></td></tr></table>{/if}
<table class="adn">
<tr class="lineb">
<td align="left" width="100%">{$smarty.const.ADMIN_NEWS_SUBSCRIBERS}</td>
<td align="center">Del</td>
</tr>
{if $subscribers_count eq 0}
<tr><td align="center" valign="middle" height="20" colspan="2">{$smarty.const.ADMIN_SUBSCRIPTIONS_NOPOD}</td></tr>
</table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se6"></td></tr></table>
{else}{assign var="admhl" value=0}
{section name=i loop=$subscribers}
{if $admhl eq 1}<tr><td colspan="2" class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr>{else}{assign var="admhl" value=1}{/if}
<tr class="lineybig hover">
<td align="left"><a href="mailto:{$subscribers[i].Email|replace:"<":"&lt;"|replace:">":"&gt;"|replace:"'":"&amp;"|replace:'"':'&quot;'|replace:" ":"20%"}">{$subscribers[i].Email|replace:"<":"&lt;"|replace:">":"&gt;"|replace:"'":"&amp;"|replace:'"':'&quot;'}</a></td>
<td align="center"><a href="#" onclick="confirmDelete('{$subscribers[i].Email64}','{$smarty.const.QUESTION_DELETE_CONFIRMATION}','{$urlToSubscibe}&amp;unsub=');">X</a></td>
</tr>
{/section}
{if $navigator}
<tr>
<td class="navigator" colspan="2">{$navigator}</td>
</tr></table>
<table class="adn"><tr><td class="se6"></td></tr></table>
{else}
</table>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se6"></td></tr></table>
{/if}
{/if}
<form method="post" action="" enctype="multipart/form-data" name="formsubscrlist" id="formsubscrlist">
<table class="adn">
<tr class="lineb">
<td align="left">{$smarty.const.ADMIN_SUBSCRIPTIONS_STRING_UPLOAD}</td>
<td align="left">{$smarty.const.ADMIN_SUBSCRIPTIONS_STRING_EXPORT}</td>
<td align="left">{$smarty.const.ADMIN_SUBSCRIPTIONS_STRING_ERASE}</td>
</tr><tr>
<td align="left" class="vbv">
<table class="adn">
<tr>
<td align="left"><input type="radio" name="fACTION" value="fLoadSubscribersListFile" id="subscr_act_upload"></td>
<td align="left"><input type="file" name="fSubscribersListFile"  size="25" class="file"></td>
</tr>
</table>
</td>
<td align="left" class="vbv"><input type="radio" name="fACTION" value="fExportSubscribersList" id="subscr_act_export"></td>
<td align="left" class="vbv"><input type="radio" name="fACTION" value="fEraseSubscribersList" id="subscr_act_erase"></td>
</tr>
</table>
</form>
<table class="adn"><tr><td class="separ"><img src="data/admin/pixel.gif" alt="" class="sep"></td></tr><tr><td class="se5"></td></tr></table>
<a href="#" onclick="document.getElementById('formsubscrlist').submit(); return false" class="inl">{$smarty.const.SAVE_BUTTON}</a>
{/if}
<table class="adn"><tr><td class="se6"></td></tr></table>
<form action='{$smarty.const.ADMIN_FILE}?dpt=custord&amp;sub=subscribers&amp;post_sub=yes' method=post name="formaxpsub" id="formaxpsub">
<table class="adn"><tr class="linsz">
<td align="left"><span class="titlecol2">{$smarty.const.ACTION_POST_TITLE}</span></td>
</tr>
<tr>
<td align="left"><input name="title_sub" type="text" value='' size="80" class="textp"></td>
</tr></table>
<table class="adn"><tr><td class="se5"></td></tr></table>
<table class="adn">
<tr class="linsz">
<td align="left"><span class="titlecol2">{$smarty.const.ACTION_POST_TEXT}</span></td>
</tr>
<tr>
<td><textarea name="message_sub" class="admin" id="area1"></textarea></td>
</tr></table>
{if $smarty.const.CONF_EDITOR}
{literal}
<script type="text/javascript" src="fckeditor/fckeditor.js"></script>
<script type="text/javascript" src="fckeditor/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
window.onload = function()
{
var oFCKeditor = new FCKeditor( 'area1',720,300) ;
{/literal}{php}
$dir1 = dirname($_SERVER['PHP_SELF']);
$sourcessrand = array("//" => "/", "\\" => "/");
$dir1 = strtr($dir1, $sourcessrand);
if ($dir1 != "/") $dir2 = "/"; else $dir2 = "";
echo "\n";
echo "oFCKeditor.BasePath = \"".$dir1.$dir2."fckeditor/\";\n";
echo "oFCKeditor.ToolbarSet = 'Basic';\n"; 
{/php}{literal}
oFCKeditor.ReplaceTextarea() ;
}
</script>
{/literal}
{/if}
<table class="adn"><tr><td class="se5"></td></tr></table>
<a href="#" onclick="document.getElementById('formaxpsub').submit(); return false" class="inl">{$smarty.const.ACTION_POST_SUB}</a>
<input type=hidden value='{$smarty.const.SAVE_BUTTON}' name='save'>
</form>
<table class="adn"><tr><td class="se6"></td></tr></table>
<table class="adn"><tr><td class="help"><span class="titlecol2">{$smarty.const.USEFUL_FOR_YOU}</span><div class="helptext">{$smarty.const.ADMIN_SUBSCRIPTIONS_STRING_DESC}</div></td>
        </tr>
      </table>