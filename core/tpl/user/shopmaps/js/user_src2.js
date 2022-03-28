function confirmUnsubscribe() {
    temp = window.confirm('{/literal}{$smarty.const.QUESTION_UNSUBSCRIBE}{literal}');
    if (temp)
    {
        window.location="index.php?killuser=yes";
    }
}
function open_printable_version(link) {
var win = 'menubar=no,location=no,resizable=yes,scrollbars=yes';
newWin = window.open(link,'perintableWin',win);
newWin.focus();
} 
function open_window(link,w,h) {
var win = "width="+w+",height="+h+",menubar=no,location=no,resizable=yes,scrollbars=yes";
newWin = window.open(link,'newWin',win);
newWin.focus();
}
function validate() {
    if (document.subscription_form.email.value.length<1)
    {
        alert("{/literal}{$smarty.const.ERROR_INPUT_EMAIL}{literal}");
        return false;
    }
    if (document.subscription_form.email.value == 'Email')
    {
        alert("{/literal}{$smarty.const.ERROR_INPUT_EMAIL}{literal}");
        return false;
    }
    document.getElementById('subscription_form').submit();
    return true;
}

function validate_disc() {
    if (document.formD.nick.value.length<1)
    {
        alert("{/literal}{$smarty.const.ERROR_INPUT_NICKNAME}{literal}");
        return false;
    }
    if (document.formD.topic.value.length<1)
    {
        alert("{/literal}{$smarty.const.ERROR_INPUT_MESSAGE_SUBJECT}{literal}");
        return false;
    }
    document.getElementById('formD').submit();
    return true;
}

function validate_search() {
    if (document.AdvancedSearchInCategory.search_price_from.value != "" && ((document.AdvancedSearchInCategory.search_price_from.value < 0) || isNaN(document.AdvancedSearchInCategory.search_price_from.value)))
    {
        alert("{/literal}{$smarty.const.ERROR_INPUT_PRICE}{literal}");
        return false;
    }
    if (document.AdvancedSearchInCategory.search_price_to.value != "" && ((document.AdvancedSearchInCategory.search_price_to.value < 0) || isNaN(document.AdvancedSearchInCategory.search_price_to.value)))
    {
        alert("{/literal}{$smarty.const.ERROR_INPUT_PRICE}{literal}");
        return false;
    }
    document.getElementById('AdvancedSearchInCategory').submit();
    return true;
}

function doCart(req) {
    if(document.getElementById('cart') && req["shopping_cart_value"] > 0){
        document.getElementById('cart').innerHTML =
                  '<b>{/literal}{$smarty.const.STRING_CART_PR}{literal}:<\/b>&nbsp;&nbsp;' + req["shopping_cart_items"] +
                  '{/literal}&nbsp;{$smarty.const.CART_CONTENT_NOT_EMPTY}<div style="padding-top: 4px;"><b>{$smarty.const.STRING_CUR_PR}{literal}:<\/b>&nbsp;&nbsp;' + req["shopping_cart_value_shown"] +
                  '<\/div>{/literal}<div style="padding-top: 10px;" align="center"><a {if $smarty.const.CONF_OPEN_SHOPPING_CART_IN_NEW_WINDOW eq 1}href="#" onclick="open_window("cart.php",500,300);"{else}href="{if $smarty.const.CONF_MOD_REWRITE eq 1}cart.html{else}index.php?shopping_cart=yes{/if}"{/if}>{$smarty.const.CART_PROCEED_TO_CHECKOUT}<\/a><\/div>';
    document.getElementById('axcrt').innerHTML = '{$smarty.const.STRING_CART_OKAX}{literal}';

    }
}

function doCpr(req) {
    if(document.getElementById('cprbox') && req["cpr_value"] > 0){
        document.getElementById('cprbox').innerHTML =
                  '{/literal}{$smarty.const.STRING_COMPARISON_IN}{literal}&nbsp;' + req["cpr_value"] +
                  '{/literal}&nbsp;{$smarty.const.CART_CONTENT_NOT_EMPTY}<div style="padding-top: 10px;" align="center"><a href="{if $smarty.const.CONF_MOD_REWRITE eq 1}compare.html{else}index.php?comparison_products=yes{/if}">{$smarty.const.STRING_COMPARISON_INFOLDER}<\/a> / <a href="#" onclick="doLoadcprCL(\'clear=yes\'); return false">{$smarty.const.STRING_COMPARISON_CLEAR}<\/a><\/div>';
    document.getElementById('axcrt').innerHTML = '{$smarty.const.STRING_CART_OKAX}{literal}';

    }
}

function doCL() {
    if(document.getElementById('cprbox')){
        document.getElementById('cprbox').innerHTML =
                  '<div style="padding: 0 8 0 8;">{/literal}{$smarty.const.STRING_COMPARISON_IN} {$smarty.const.CART_CONTENT_EMPTY}<\/div>';
    document.getElementById('axcrt').innerHTML = '{$smarty.const.STRING_COMPARISON_TITLE_OK}{literal}';

    }
}

function renbox() {
    if(document.getElementById('axcrt')){
        document.getElementById('axcrt').innerHTML = '{/literal}{$smarty.const.STRING_COMPARISON_PROCESS}{literal}';
    }
}

function renboxCL() {
    if(document.getElementById('axcrt')){
        document.getElementById('axcrt').innerHTML = '{/literal}{$smarty.const.STRING_COMPARISON_TITLE_CL}{literal}';
    }
}

function doreset() {
    if(document.getElementById('axcrt')){
        document.getElementById('axcrt').innerHTML = '{/literal}{$smarty.const.STRING_CART_PROCESS}{literal}';
    }
}

function doHide() {
var agt=navigator.userAgent.toLowerCase();
var is_major = parseInt(navigator.appVersion);
var is_ie     = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
var is_ie6    = (is_ie && (is_major == 4) && (agt.indexOf("msie 6.")!=-1) );
    if(document.getElementById('axcrt')){
        document.getElementById('axcrt').style.visibility = 'hidden';
        document.getElementById('axcrt').style.display = 'none';
        setTimeout('doreset()',100);
        if ( is_ie ){
                if (document.styleSheets.length == 0) document.createStyleSheet();
                var oSheet = document.styleSheets[0];
                oSheet.addRule(".WCHhider", "visibility:visible");
        }
    }
}
function printcart() {
var agt=navigator.userAgent.toLowerCase();
var is_major = parseInt(navigator.appVersion);
var is_ie     = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
var is_ie6    = (is_ie && (is_major == 4) && (agt.indexOf("msie 6.")!=-1) );
if ( is_ie6 ){
    document.write('<div id="axcrt" class="bf vcent" style="position: absolute; display: none; visibility: hidden;">{/literal}{$smarty.const.STRING_CART_PROCESS}{literal}<\/div>');
}else{
    document.write('<div id="axcrt" class="bf vcent" style="position: fixed; display: none; visibility: hidden; left: '+Math.ceil((document.documentElement.clientWidth-300)/2)+'px; top: '+Math.ceil((document.documentElement.clientHeight-100)/2)+'px;">{/literal}{$smarty.const.STRING_CART_PROCESS}{literal}<\/div>');
}
}

//babenkoma
function showAjaxWindow(id){
    if (!document.getElementById("ajax_dialog"+id)){
        $.ajax({
            type: "POST",
            url: "index.php",
            data: "ajax_window=1&aux_page=" + id, 
            dataType: "text",
            success: function(msg){
                var arr = msg.split("|||");
                var t = document.createElement("div");
                t.id = "ajax_dialog" + id;
                t.title = arr[0];
                t.innerHTML = arr[1];
                document.body.appendChild(t);
                $( "#ajax_dialog"+id ).dialog({width:500});
            }
        });
    } else {
        $( "#ajax_dialog"+id ).dialog({width:500});
    }
}
//babenkoma\