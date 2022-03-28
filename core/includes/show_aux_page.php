<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        if ( isset($show_aux_page) )
        {
                $aux_page = auxpgGetAuxPage( $show_aux_page );

                if ( $aux_page )
                {
                        $smarty->assign("page_body", $aux_page["aux_page_text"] );
                        $smarty->assign("aux_page_name", $aux_page["aux_page_name"] );
                        $smarty->assign("show_aux_page", $aux_page["aux_page_ID"] );
                        $smarty->assign("main_content_template", "show_aux_page.tpl.html" );
                }
                else
                {
                        header("HTTP/1.0 404 Not Found");
                        header("HTTP/1.1 404 Not Found");
                        header("Status: 404 Not Found");
                        die(ERROR_404_HTML);
                }
        }
?>