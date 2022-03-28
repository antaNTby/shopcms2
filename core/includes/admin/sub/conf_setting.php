<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


        if ( !strcmp($sub,"setting") )
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(12,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {

                if (isset($_GET["resizestart"]))Renderimages();
                if (isset($_GET["watermarkstart"]))Renderwatermarks();

                $setting_groups = settingGetAllSettingGroup();
                $smarty->assign("setting_groups", $setting_groups );

                if ( isset($_POST) && count($_POST)>0 )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                if ( isset($_GET["settings_groupID"]) )
                                        Redirect(ADMIN_FILE."?dpt=conf&sub=setting&settings_groupID=".(int)$_GET["settings_groupID"]."&safemode=yes" );
                                else
                                        Redirect(ADMIN_FILE."?dpt=conf&sub=setting&safemode=yes" );
                        }
                }

                if ( isset($_GET["settings_groupID"]) )
                {
                        $settings = settingGetSettings( (int)$_GET["settings_groupID"] );
                        $smarty->assign("settings", $settings );

                        $smarty->assign("controls", settingCallHtmlFunctions((int)$_GET["settings_groupID"]) );
                        $smarty->assign("settings_groupID", (int)$_GET["settings_groupID"] );
                }

                if ( !isset($_GET["settings_groupID"]) && count($setting_groups) > 0 )
                        Redirect(ADMIN_FILE."?dpt=conf&sub=setting&settings_groupID=".
                                        $setting_groups[0]["settings_groupID"] );



        // set sub-department template
        $smarty->assign("admin_sub_dpt", "conf_setting.tpl.html");
        }
        }
?>