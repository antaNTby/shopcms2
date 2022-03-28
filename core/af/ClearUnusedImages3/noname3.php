<?

// регистрационная информация (логин, пароль #1)
// registration info (login, password #1)

$mrh_login = "mapiki";
$mrh_pass1 = "ab1578602";
//$mrh_pass1 = "mapss4198Shop";


/*$mrh_login = "demo";
$mrh_pass1 = "password_1";*/

/*
$mrh_login = "demo";
$mrh_pass1 = "Morbid11";    */

// номер заказа
// number of order
$inv_id = 0;

// описание заказа
// order description
$inv_desc = "TestTovat";

// сумма заказа
// sum of order
$out_summ = "1.00";

// тип товара
// code of goods
$shp_item = "2";

// предлагаемая валюта платежа
// default payment e-currency
$in_curr = "";

// язык
// language
$culture = "ru";

// формирование подписи
// generate signature
$crc  = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item");

// форма оплаты товара
// payment form
/*
print
   "<html>".
   "<form action='https://merchant.roboxchange.com/Index.aspx' method=POST>".
   "<input type=hidden name=MrchLogin value=$mrh_login>".
   "<input type=hidden name=OutSum value=$out_summ>".
   "<input type=hidden name=InvId value=$inv_id>".
   "<input type=hidden name=Desc value='$inv_desc'>".
   "<input type=hidden name=SignatureValue value=$crc>".
   "<input type=hidden name=Shp_item value='$shp_item'>".
   "<input type=hidden name=IncCurrLabel value=$in_curr>".
   "<input type=hidden name=Culture value=$culture>".
   "<input type=submit value='Оплатить'>".
   "</form></html>";
   */
   
//http://test.robokassa.ru/Index.aspx
print
   "<html>".
   "<form action='https://merchant.roboxchange.com/Index.aspx' method=GET>".
   "<input type=hidden name=MrchLogin value=$mrh_login>".
   "<input type=hidden name=OutSum value=$out_summ>".
   "<input type=hidden name=InvId value=$inv_id>".
   "<input type=hidden name=Desc value='$inv_desc'>".
   "<input type=hidden name=SignatureValue value=$crc>".
   "<input type=hidden name=Shp_item value='$shp_item'>".
   "<input type=hidden name=IncCurrLabel value=$in_curr>".
   "<input type=hidden name=Culture value=$culture>".
   "<input type=submit value='Оплатить'>".
   "</form></html>";

?>