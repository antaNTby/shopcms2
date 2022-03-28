<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        if (!strcmp($sub, "blocks_edit"))
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(17,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {

           $cats = catGetCategoryCList($cat_id);//Min();
           $smarty->assign( "cats", $cats );

           if ( isset($_GET["block_switch_on"]) )
           {
            if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(ADMIN_FILE."?dpt=conf&sub=blocks_edit&safemode=yes");
                                }
           Powerblocks( 1, $_GET["block_switch_on"]);
           }
           if ( isset($_GET["block_switch_off"]) )
           {
            if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(ADMIN_FILE."?dpt=conf&sub=blocks_edit&safemode=yes");
                                }
           Powerblocks( 0, $_GET["block_switch_off"]);
           }

             if ( isset($_POST["savel"]) )
                        {

                                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(ADMIN_FILE."?dpt=conf&sub=blocks_edit&safemode=yes");
                                }

                      SortBlocks();


                      }
                if ( isset($_GET["delete"]) )
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=conf&sub=blocks_edit&safemode=yes" );
                        }
                        blockspgDeleteblocks($_GET["delete"]);
                        Redirect(ADMIN_FILE."?dpt=conf&sub=blocks_edit" );
                }
                if ( isset($_GET["add_new"]) )
                {
                        if ( isset($_POST["save"]) )
                        {
                        if ( isset($_GET["file"]) )
                        {
                         if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(ADMIN_FILE."?dpt=conf&sub=blocks_edit&safemode=yes");
                                }

                              blockspgAddblocksPageFile( $_POST["block_name"], $_POST["block_select_file"], $_POST["block_select_where"], $_POST["block_select_line"], $_POST["block_select_active"], $_POST["block_select_admin"], $_POST["spage_select"], $_POST["dpage_select"], $_POST["categories_select"], $_POST["products_select"] );
                                Redirect(ADMIN_FILE."?dpt=conf&sub=blocks_edit");

                        }else{

                         if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(ADMIN_FILE."?dpt=conf&sub=blocks_edit&safemode=yes");
                                }
                             blockspgAddblocksPage( $_POST["block_name"], $_POST["block_content"], $_POST["block_select_where"], $_POST["block_select_line"], $_POST["block_select_active"], $_POST["block_select_admin"], $_POST["spage_select"], $_POST["dpage_select"], $_POST["categories_select"], $_POST["products_select"] );
                                Redirect(ADMIN_FILE."?dpt=conf&sub=blocks_edit");

                        }


                        }
                             if ( isset($_GET["file"]) )
                        {

                             $blocklist = array();
                             $handle = opendir("core/tpl/user/".CONF_DEFAULT_TEMPLATE."/blocks/");
                             while ($file = readdir($handle)) {
                             if ((ereg("[html]",$file))) {
                                $blocklist[] = $file;
                             }
                             }
                             closedir($handle);

                                $smarty->assign( "blocklist", $blocklist );
                                $smarty->assign( "add_new_file", 1 );
                        }
                        $smarty->assign( "add_new", 1 );
                }
                else if ( isset($_GET["edit"]) )
                {

                        if ( isset($_POST["save"]) )
                        {
                                if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                                {
                                        Redirect(ADMIN_FILE."?dpt=conf&sub=blocks_edit&safemode=yes&edit=".$_GET["edit"] );
                                }
                                blockspgUpdateblocksPage( $_GET["edit"], $_POST["block_name"], $_POST["block_content"], $_POST["block_select_where"], $_POST["block_select_line"], $_POST["block_select_active"], $_POST["block_select_admin"], $_POST["spage_select"], $_POST["dpage_select"], $_POST["categories_select"], $_POST["products_select"] );
                                Redirect(ADMIN_FILE."?dpt=conf&sub=blocks_edit");
                        }

                        $blocks_edit = blockspgGetblocksPage($_GET["edit"]);
                        $blocks_edit["content"] = html_spchars($blocks_edit["content"]);
                        $smarty->assign( "blocks_edit", $blocks_edit );
                        $smarty->assign( "edit", 1 );
                }
                else
                {
                        $conf_blocks = GetAllBlocksAttributes();
                        $blocks_count = count($conf_blocks);
                        $smarty->assign( "blocks_count", $blocks_count );
                        $smarty->assign( "blocks_edit", $conf_blocks );
                }


                        $aux_pages = auxpgGetAllPageAttributes();
                        $smarty->assign( "aux_pages", $aux_pages );

                //set sub-department template
                $smarty->assign("admin_sub_dpt", "conf_blocks_edit.tpl.html");

        }
        }
?>