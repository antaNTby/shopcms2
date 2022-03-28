<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        if (!strcmp($sub, "admin_edit"))
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(28,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {



                if ( isset($_GET["delete"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=conf&sub=admin_edit&safemode=yes" );
                        }
                          if ($_GET["delete"]==1) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=conf&sub=admin_edit&nomade=yes" );
                        }
                        adminpgDeleteadmin((int)$_GET["delete"]);
                        Redirect(ADMIN_FILE."?dpt=conf&sub=admin_edit" );
                }
                if ( isset($_GET["add_new"]) )
                {
                        if ( isset($_POST["save"]) )
                        {
                         if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(ADMIN_FILE."?dpt=conf&sub=admin_edit&safemode=yes");
                                }
                      $checklg = CheckLoginAdminNew($_POST["admin_login"]);
                      if ($checklg != 0 ) Redirect(ADMIN_FILE."?dpt=conf&sub=admin_edit&setlog=yes" );

                      regRegisterAdminSlave( $_POST["admin_login"], $_POST["admin_pass"], $_POST["actions"] );
                                Redirect(ADMIN_FILE."?dpt=conf&sub=admin_edit");
                        }
                        $smarty->assign( "admin_edit", array("nonepg", array("nonepg")) );
                        $smarty->assign( "add_new", 1 );
                }
                else if ( isset($_GET["edit"]) )
                {
                         if ($_GET["edit"]==1) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(ADMIN_FILE."?dpt=conf&sub=admin_edit&nomade=yes");
                                }

                      if ( isset($_POST["save"]) )
                      {
                                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(ADMIN_FILE."?dpt=conf&sub=admin_edit&safemode=yes&edit=".(int)$_GET["edit"] );
                                }

                      $edit_num = (int)$_GET["edit"];
                      UpdateAdminRights( $edit_num, $_POST["actions"] );
                      Redirect(ADMIN_FILE."?dpt=conf&sub=admin_edit");
                      }

                        $admin_edit = adminpgGetadminPage((int)$_GET["edit"]);
                        $smarty->assign( "edit_num", (int)$_GET["edit"]);
                        $smarty->assign( "admin_edit", $admin_edit );

                        $smarty->assign( "edit", 1 );
                }
                else
                {
                        $conf_admin = GetAllAdminAttributes();
                        $admin_count = count($conf_admin);
                        $smarty->assign( "admin_count", $admin_count );
                        $smarty->assign( "admin_edit", $conf_admin );
                }


                 if ( isset($_GET["nomade"]) )
                {
                  $smarty->assign("nomade", xEscSQL($_GET["nomade"]));
                }
                  if ( isset($_GET["setlog"]) )
                {
                  $smarty->assign("setlog", xEscSQL($_GET["setlog"]));
                }
                //set sub-department template
                $smarty->assign("admin_sub_dpt", "conf_admin_edit.tpl.html");
        }
        }
?>