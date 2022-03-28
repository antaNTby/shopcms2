function confirmDelete(id, ask, url) {
var temp = window.confirm(ask);
if (temp) {
window.location=url+id;
}
}

function confirmDeletep(question, where)
{
temp = window.confirm(question);
if (temp)
{
window.location=where;
}
}

function confirmDeletef(id, ask) {
var temp = window.confirm(ask);
if (temp) {
document.getElementById(id).submit();
}
}

function open_window(link,w,h) {
var win = "width="+w+",height="+h+",menubar=no,location=no,resizable=yes,scrollbars=yes";
var newWin = window.open(link,'newWin',win);
}

function mover(mId,set){
mId.style.backgroundColor=set;
}

function mout(mId,set){
mId.style.backgroundColor=set;
}

function moutt(id) {
var okno = document.getElementById(id);
if(okno.style.display == "none") {
okno.style.display = "";
} else {
okno.style.display = "none";
}
}

function reverimg(id) {
var imka = document.getElementById(id);
var path = imka.src.substring(0, imka.src.length-7);
var img = imka.src.substr(imka.src.length-7);
if(img == "004.gif") {
imka.src = path+'003.gif';
} else {
imka.src = path+'004.gif';
}
}

function megamenu() {
var i1 = getCookie('menu1');
var i2 = getCookie('menu2');
var i3 = getCookie('menu3');
var i4 = getCookie('menu4');
var i5 = getCookie('menu5');
var i6 = getCookie('menu6');
if ((i1 != null)&&(i1 != "")&&(i1 == "1")) {
reverimg('menu12');
moutt('menu13');
}
if ((i2 != null)&&(i2 != "")&&(i2 == "1")) {
reverimg('menu22');
moutt('menu23');
}
if ((i3 != null)&&(i3 != "")&&(i3 == "1")) {
reverimg('menu32');
moutt('menu33');
}
if ((i4 != null)&&(i4 != "")&&(i4 == "1")) {
reverimg('menu42');
moutt('menu43');
}
if ((i5 != null)&&(i5 != "")&&(i5 == "1")) {
reverimg('menu52');
moutt('menu53');
}
if ((i6 != null)&&(i6 != "")&&(i6 == "1")) {
reverimg('menu62');
moutt('menu63');
}
}

function menuresetit(ty) {
var it = getCookie(ty);
var menu2 = ty + '2';
var menu3 = ty + '3';
if ((it != null)&&(it != "")&&(it == "1")) {
setCookie(ty,'2','1',"/");
reverimg(menu2);
moutt(menu3);
}
if ((it != null)&&(it != "")&&(it == "2")) {
setCookie(ty,'1','1',"/");
reverimg(menu2);
moutt(menu3);
}else{
if (it == null) {
setCookie(ty,'1','1',"/");
reverimg(menu2);
moutt(menu3);
}
if (it == "") {
setCookie(ty,'1','1',"/");
reverimg(menu2);
moutt(menu3);
}
}
}

function setCookie(name, value, expires, path, domain, secure) {
var exp = new Date();
var oneMonthFromNow = exp.getTime() + (100*24*60*60*1000);
exp.setTime (oneMonthFromNow);
var curCookie = name + "=" + escape(value) + ((expires) ? "; expires=" + exp.toGMTString() : "") + ((path) ? "; path=" + path : "") + ((domain) ? "; domain=" + domain : "") + ((secure) ? "; secure" : "");
if ( (name + "=" + escape(value)).length <= 4000) document.cookie = curCookie;
}

function getCookie(name) {
var prefix = name + "="; var cookieStartIndex = document.cookie.indexOf(prefix);
if (cookieStartIndex == -1) return null;
var cookieEndIndex = document.cookie.indexOf(";", cookieStartIndex + prefix.length);
if (cookieEndIndex == -1) cookieEndIndex = document.cookie.length;
return unescape(document.cookie.substring(cookieStartIndex + prefix.length, cookieEndIndex));
}

function preloadImages() {
var d=document;
if(d.images){
   if(!d.massiv) d.massiv=new Array();
   var i,j=d.massiv.length,a=arguments;
   for(i=0; i<a.length; i++)
   if (a[i].indexOf("#")!=0){
       d.massiv[j]=new Image; d.massiv[j++].src=a[i];
   }
}
}
preloadImages("data/admin/003.gif","data/admin/004.gif");