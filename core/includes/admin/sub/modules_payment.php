<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        if ( !strcmp($sub,"payment") )
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(21,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {


                   if (isset($_GET["save_successful"])) //update was successful
                {
                        $smarty->assign( "save_successful", ADMIN_UPDATE_SUCCESSFUL );
                }


        $moduleFiles = GetFilesInDirectory( "core/modules/payment", "php" );

        foreach( $moduleFiles as $fileName )
                include( $fileName );


        if ( isset($_GET["setting_up"]) )
        {
                if (isset($_POST) && count($_POST)>0)
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=modules&sub=payment&setting_up=".$_GET["setting_up"]."&safemode=yes" );
                        }
                }

                $payment_module = null;

                $ModuleConfig = modGetModuleConfig($_GET["setting_up"]);

                if($ModuleConfig['ModuleClassName']){

                        eval('$payment_module = new '.$ModuleConfig['ModuleClassName'].'('.$_GET["setting_up"].');');
                }else{

                        foreach( $moduleFiles as $fileName )
                        {
                                $module = null;
                                $className = GetClassName( $fileName );
                                if(!$className)continue;
                                eval( "\$module = new ".$className."();" );
                                if ( $module->get_id() == $_GET["setting_up"] )
                                {
                                        $payment_module = $module;
                                        break;
                                }
                        }
                }

                $constants = $payment_module->settings_list();
                $settings = array();
                $controls = array();

                foreach( $constants as $constant )
                {
                        $settings[]        = settingGetSetting( $constant );
                        $controls[]        = settingCallHtmlFunction(  $constant );
                        $smarty->assign("settings", $settings );
                        $smarty->assign("controls", $controls );
                }

                $smarty->assign("payment_module", $payment_module );
                $smarty->assign("constant_managment", 1);
        }
        else
        {

                $payment_modules = array();
                $payment_methods_by_modules = array();
                $payment_configs = modGetAllInstalledModuleObjs(PAYMENT_MODULE);
                foreach($payment_configs as $_Ind=>$_Conf){

                        $payment_configs[$_Ind] = array(
                                'ConfigID' => $_Conf->get_id(),
                                'ConfigName' => $_Conf->title,
                                'ConfigClassName' => get_class($_Conf),
                                );
                }
                foreach( $moduleFiles as $fileName )
                {
                        $className = GetClassName( $fileName );
                        if(!$className)continue;

                        eval( '$paymentModule = new '.$className.'();' );
                        $payment_modules[] = $paymentModule;
                        $payment_methods_by_modules[] = payGetPaymentMethodsByModule( $paymentModule );
                }

                function cmpPObjs($a, $b)
                {
                   return strcmp($a->title, $b->title);
                }

                usort($payment_modules, "cmpPObjs");

                if ( isset($_GET["install"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=modules&sub=payment&safemode=yes" );
                        }

                        $payment_modules[ (int)$_GET["install"] ]->install();
                        Redirect(ADMIN_FILE."?dpt=modules&sub=payment" );
                }

                if ( isset($_GET["uninstall"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=modules&sub=payment&safemode=yes" );
                        }


                        $ModuleConfig = modGetModuleConfig($_GET["uninstall"]);
                        if($ModuleConfig['ModuleClassName']){

                                modUninstallModuleConfig($_GET["uninstall"]);
                        }else{

                                foreach ($payment_configs as $_tModConf){

                                        if($_tModConf['ConfigID']==(int)$_GET["uninstall"]){

                                                eval('$_tModConf = new '.$_tModConf['ConfigClassName'].'();');
                                                $_tModConf->uninstall();
                                                break;
                                        }
                                }
                        }
                        Redirect(ADMIN_FILE."?dpt=modules&sub=payment" );
                }

                $smarty->assign("payment_modules", $payment_modules );
                $smarty->assign("payment_methods_by_modules", $payment_methods_by_modules );
                $smarty->assign ( "payment_configs" ,  $payment_configs);
        }

        $smarty->assign("admin_sub_dpt", "modules_payment.tpl.html");
        }
        }
?>