<?php
#Обратный звонок на сайте
/*
error:
1 - empty phone
2 - error on sent email
*/

$mod_name = 'callback';
if( (!isset($_GET[$mod_name])) && (!isset($_POST[$mod_name])) ) return;

if(!isset($_POST[$mod_name]))
{
	$Phone = '';
	$Name = '';
	$Info = '';
}
else//isset($_POST[$mod_name])
{
	$Phone = $_POST['phone'];
	$Name = $_POST['name'];
	$Info = $_POST['info'];

    if(trim($Phone)=='')
    {
        $smarty->assign("error",1);
    }
    else
    {
        $EmailDest = CONF_GENERAL_EMAIL;
        $EmailSubject = "Запрос обратного звонка";
        $EmailContent =  "Тел.:".$Phone."\nИмя:".$Name."\nДоп.инфо.:".$Info;
        if (xMailTxtHTML($EmailDest, $EmailSubject, $EmailContent )) $smarty->assign("send_success",true);
        else $smarty->assign("error", 2);
    }
}

//extract input to Smarty
//$smarty->hassign("customer_name",$customer_name);
$smarty->hassign("phone",$Phone);
$smarty->hassign("name",$Name);
$smarty->hassign("info",$Info);
$smarty->assign("main_content_template", "callback.tpl.html");

?>