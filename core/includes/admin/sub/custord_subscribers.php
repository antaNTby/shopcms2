<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

if (!strcmp($sub, "subscribers")) //show news subscribers
{
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(8,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {
        if(isset($_POST['fACTION'])){

                $xREQUEST_URI = set_query('&QWERTY=');
                /**
                 * this action is forbidden when SAFE MODE is ON
                 */
                if (CONF_BACKEND_SAFEMODE)Redirect(_getUrlToUnsub()."&safemode=yes");

                if(!session_is_registered('SUBSCRIBE_MESSAGE')){

                        session_register('SUBSCRIBE_MESSAGE');
                }
                switch ($_POST['fACTION']){
                        case 'fLoadSubscribersListFile':
                                $UploadError = false;
                                do{
                                        if (!isset($_FILES['fSubscribersListFile']['tmp_name'])){
                                                $UploadError=true;
                                                break;
                                        }
                                        if (!$_FILES['fSubscribersListFile']['tmp_name']){
                                                $UploadError=true;
                                                break;
                                        }
                                        if (!$_FILES['fSubscribersListFile']['size']){
                                                $UploadError=true;
                                                break;
                                        }
                                        if (!file_exists($_FILES['fSubscribersListFile']['tmp_name'])){
                                                $UploadError=true;
                                                break;
                                        }
                                }while (0);
                                if($UploadError){

                                        $_SESSION['SUBSCRIBE_MESSAGE'] = array(
                                                'Message' => ADMIN_SUBSCRIPTIONS_ERROR_UPLOAD_SUBSCRLIST,
                                                'MessageCode' => 2,
                                        );
                                        break;
                                }
                                $FileContents = file ($_FILES['fSubscribersListFile']['tmp_name']);
                                $emailCounter = 0;
                                foreach ($FileContents as $_email){

                                        $_email = trim($_email);
                                        if(subscrVerifyEmailAddress($_email) == ''){

                                                subscrAddUnRegisteredCustomerEmail($_email);
                                                $emailCounter++;
                                        }
                                }
                                if(!$emailCounter){

                                        $_SESSION['SUBSCRIBE_MESSAGE'] = array(
                                                'Message' => ADMIN_SUBSCRIPTIONS_ERROR_UPLOAD_NO_EMAILS,
                                                'MessageCode' => 2,
                                        );
                                        break;
                                }else {

                                        $_SESSION['SUBSCRIBE_MESSAGE'] = array(
                                                'Message' => str_replace('{*EMAILS_NUMBER*}', $emailCounter, ADMIN_SUBSCRIPTIONS_OK_UPLOAD_SUBSCRLIST),
                                                'MessageCode' => 1,
                                        );
                                }
                                break;
                        case 'fEraseSubscribersList':
                                $CountRow = 0;
                                $Subscriptions = subscrGetAllSubscriber('', $CountRow);

                                foreach ($Subscriptions as $_Subscription){

                                        subscrUnsubscribeSubscriberByEmail(base64_encode($_Subscription['Email']));
                                }
                                if(!count($Subscriptions))break;
                                $_SESSION['SUBSCRIBE_MESSAGE'] = array(
                                        'Message' => str_replace('{*EMAILS_NUMBER*}', count($Subscriptions),ADMIN_SUBSCRIPTIONS_OK_ERASE_SUBSCRLIST),
                                        'MessageCode' => 1,
                                );
                                break;
                        case 'fExportSubscribersList':
                                $CountRow = 0;
                                $Subscriptions = subscrGetAllSubscriber('', $CountRow);
                                $ExportBuffer = '';
                                if(!count($Subscriptions))break;
                                $fp = @fopen('core/temp/subscribers.txt', 'w');
                                if(!$fp){

                                        $_SESSION['SUBSCRIBE_MESSAGE'] = array(
                                                'Message' => ADMIN_SUBSCRIPTIONS_ERROR_FILE_CREATION,
                                                'MessageCode' => 2,
                                        );
                                        break;
                                }

                                foreach ($Subscriptions as $_Subscription){

                                        fwrite($fp, $_Subscription['Email']."\r\n");
                                }

                                $getFileParam = cryptFileParamCrypt( "GetSubscriptionsList", null );
                                $smarty->assign( "getFileParam", $getFileParam );

                                $_SESSION['SUBSCRIBE_MESSAGE'] = array(
                                        'Message' => str_replace('{*URL*}', ADMIN_FILE.'?do=get_file&getFileParam='.$getFileParam, ADMIN_SUBSCRIPTIONS_OK_EXPORT_SUBSCRLIST),
                                        'MessageCode' => 1,
                                );

                                fclose($fp);
                                break;
                }
                Redirect($xREQUEST_URI);
        }
        function _getUrlToNavigate()
        {
                $res = ADMIN_FILE."?dpt=custord&sub=subscribers";
                return $res;
        }

        function _getUrlToUnsub()
        {
                $res = ADMIN_FILE."?dpt=custord&sub=subscribers";
                if ( isset($_GET["offset"]) )
                        $res .= "&offset=".$_GET["offset"];
                if ( isset($_GET["show_all"]) )
                        $res .= "&show_all=".$_GET["show_all"];
                return $res;
        }

        if (isset($_GET["unsub"])) // unsubscribe registered user
        {
                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                {
                        Redirect(_getUrlToUnsub()."&safemode=yes");
                }

                subscrUnsubscribeSubscriberByEmail( ($_GET["unsub"]) );

                if(!session_is_registered('SUBSCRIBE_MESSAGE')){

                        session_register('SUBSCRIBE_MESSAGE');
                }
                $_SESSION['SUBSCRIBE_MESSAGE'] = array(
                        'Message' => str_replace('{*EMAIL*}',base64_decode($_GET["unsub"]), ADMIN_SUBSCRIPTIONS_OK_EMAIL_DELETED),
                        'MessageCode' => 1,
                );
                Redirect( _getUrlToUnsub() );
        }

        if (isset($_GET["post_sub"]))
        {
           if (CONF_BACKEND_SAFEMODE)Redirect(_getUrlToNavigate()."&safemode=yes");
           SendNewsMessage($_POST["title_sub"], $_POST["message_sub"]);
        }

        $callBackParam = array();
        $subscribers = array();

        $count = 0;
        $htmlNavigator = GetNavigatorHtml( _getUrlToNavigate(), 15,
                'subscrGetAllSubscriber', $callBackParam,
                $subscribers, $offset, $count );

        $smarty->hassign( "urlToSubscibe", _getUrlToUnsub());

        foreach($subscribers as $key => $val)
        {
                $subscribers[$key]["Email64"] = base64_encode( $subscribers[$key]["Email" ]);
        }

        /**
         * Messages handler
         */
        if(isset($_SESSION['SUBSCRIBE_MESSAGE'])){

                if(isset($_SESSION['SUBSCRIBE_MESSAGE']['Message']) && isset($_SESSION['SUBSCRIBE_MESSAGE']['MessageCode'])){

                        $smarty->assign('Message', $_SESSION['SUBSCRIBE_MESSAGE']['Message']);
                        $smarty->assign('MessageCode', $_SESSION['SUBSCRIBE_MESSAGE']['MessageCode']);
                        unset($_SESSION['SUBSCRIBE_MESSAGE']['Message']);
                }
        }

        $smarty->assign( "navigator", $htmlNavigator );
        $smarty->assign( "subscribers", $subscribers );
        $smarty->assign( "subscribers_count", count($subscribers) );

        $smarty->assign("admin_sub_dpt", "custord_subscribers.tpl.html");
}
}
?>