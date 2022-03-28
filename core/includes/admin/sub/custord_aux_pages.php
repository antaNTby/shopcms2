<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

    if (!strcmp($sub, "aux_pages"))
    {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(16,$relaccess))) //unauthorized
        {
            $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
        } else {


                if ( isset($_GET["delete"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=custord&sub=aux_pages&safemode=yes" );
                        }
						
                        auxpgDeleteAuxPage( $_GET["delete"] );
                        Redirect(ADMIN_FILE."?dpt=custord&sub=aux_pages" );
                }
                if ( isset($_GET["add_new"]) )
                {
                        if ( isset($_POST["save"]) )
                        {
                                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
								{
									Redirect(ADMIN_FILE."?dpt=custord&sub=aux_pages&safemode=yes" );
								}
								
                                $aux_page_text_type = 0;
                                if ( isset($_POST["aux_page_text_type"]) )
                                        $aux_page_text_type = 1;
                                auxpgAddAuxPage( $_POST["aux_page_name"],
                                        $_POST["aux_page_text"], $aux_page_text_type,
                                        $_POST["meta_keywords"], $_POST["meta_description"], $_POST["aux_page_title"], $_POST["is_ajax_window"] );
                                Redirect(ADMIN_FILE."?dpt=custord&sub=aux_pages");
                        }
                        $smarty->assign( "add_new", 1 );
                }
                else if ( isset($_GET["edit"]) )
                {

                        if ( isset($_POST["save"]) )
                        {
                                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(ADMIN_FILE."?dpt=custord&sub=aux_pages&safemode=yes&edit=".$_GET["edit"] );
                                }

                                $aux_page_text_type = 0;
                                if ( isset($_POST["aux_page_text_type"]) )
                                        $aux_page_text_type = 1;
                                auxpgUpdateAuxPage( $_GET["edit"], $_POST["aux_page_name"],
                                        $_POST["aux_page_text"], $aux_page_text_type,
                                        $_POST["meta_keywords"], $_POST["meta_description"], $_POST["aux_page_title"], $_POST["is_ajax_window"]   );
                                Redirect(ADMIN_FILE."?dpt=custord&sub=aux_pages");
                        }

                        $aux_page = auxpgGetAuxPage( $_GET["edit"] );
                        if($aux_page["aux_page_text_type"] == 1){
                        $aux_page["aux_page_text"] = html_spchars($aux_page["aux_page_text"]);
                        }
                        $smarty->assign( "aux_page", $aux_page );
                        $smarty->assign( "edit", 1 );
                }
                else
                {
                        $aux_pages = auxpgGetAllPageAttributes();
                        $smarty->assign( "aux_pages", $aux_pages );
                }

                //set sub-department template
                $smarty->assign("admin_sub_dpt", "custord_aux_pages.tpl.html");
        }
        }
?>