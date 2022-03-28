<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        if ( !strcmp($sub,"shipping") )
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(20,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {


                   if (isset($_GET["save_successful"])) //update was successful
                {
                        $smarty->assign( "save_successful", ADMIN_UPDATE_SUCCESSFUL );
                }
        $moduleFiles = GetFilesInDirectory( "core/modules/shipping", "php" );

        foreach( $moduleFiles as $fileName )
                include( $fileName );

        if ( isset($_GET["setting_up"]) )
        {
                if (isset($_POST) && count($_POST)>0)
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=modules&sub=shipping&setting_up=".$_GET["setting_up"]."&safemode=yes" );
                        }
                }

                $ModuleConfig = modGetModuleConfig($_GET["setting_up"]);

                if($ModuleConfig['ModuleClassName']){

                        eval('$shipping_module = new '.$ModuleConfig['ModuleClassName'].'('.$_GET["setting_up"].');');
                }else{

                        foreach( $moduleFiles as $fileName )
                        {
                                $module = null;
                                $className = GetClassName( $fileName );
                                if(!$className)continue;
                                eval( "\$module = new ".$className."();" );

                                if ( $module->get_id() == $_GET["setting_up"] )
                                {
                                        $shipping_module = $module;
                                        break;
                                }
                        }
                }

                $constants = $shipping_module->settings_list();
                $settings = array();
                $controls = array();

                foreach( $constants as $constant )
                {
                        $settings[]         = settingGetSetting( $constant );
                        $controls[] = settingCallHtmlFunction(  $constant );
                }

                if(isset($_POST['save'])){

                        Redirect(set_query('Pustishka='));
                }

                $smarty->assign("settings", $settings );
                $smarty->assign("controls", $controls );

                $smarty->assign("shipping_module", $shipping_module );
                $smarty->assign("constant_managment", 1 );
        }
        else
        {

                $shipping_configs = modGetAllInstalledModuleObjs(SHIPPING_RATE_MODULE);
                foreach($shipping_configs as $_Ind=>$_Conf){

                        $shipping_configs[$_Ind] = array(
                                'ConfigID' => $_Conf->get_id(),
                                'ConfigName' => $_Conf->title,
                                'ConfigClassName' => get_class($_Conf),
                                );
                }
                $shipping_modules = array();
                $shipping_methods_by_modules = array();
                foreach( $moduleFiles as $fileName )
                {
                        $className = GetClassName( $fileName );
                        if(!$className)continue;

                        eval( "\$shippingModule = new ".$className."();" );
                        $shipping_modules[] = $shippingModule;
                        $shipping_methods_by_modules[] = shGetShippingMethodsByModule( $shippingModule );
                }

                function cmpShObjs($a, $b)
                {
                   return strcmp($a->title, $b->title);
                }

                usort($shipping_modules, "cmpShObjs");

                if ( isset($_GET["install"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=modules&sub=shipping&safemode=yes" );
                        }

                        $shipping_modules[ (int)$_GET["install"] ]->install();
                        Redirect(ADMIN_FILE."?dpt=modules&sub=shipping" );
                }

                if ( isset($_GET["uninstall"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=modules&sub=shipping&safemode=yes" );
                        }

                        $ModuleConfig = modGetModuleConfig($_GET["uninstall"]);
                        if($ModuleConfig['ModuleClassName']){

                                modUninstallModuleConfig($_GET["uninstall"]);
                        }else{

                                foreach ($shipping_configs as $_tModConf){

                                        if($_tModConf['ConfigID']==(int)$_GET["uninstall"]){

                                                eval('$_tModConf = new '.$_tModConf['ConfigClassName'].'();');
                                                $_tModConf->uninstall();
                                                break;
                                        }
                                }
                        }
                        Redirect(ADMIN_FILE."?dpt=modules&sub=shipping");
                }

                $smarty->assign( "shipping_modules", $shipping_modules );
                $smarty->assign( "shipping_methods_by_modules", $shipping_methods_by_modules );
                $smarty->assign ( "shipping_configs" ,  $shipping_configs);
        }

        $smarty->assign("admin_sub_dpt", "modules_shipping.tpl.html");
        }
        }
?>