<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


    if ( isset($visit_history) && isset($_SESSION["log"]) )
        {
                $callBackParam = array( "log" => $_SESSION["log"] );
                $visits = null;
                $offset = 0;
                $count = 0;
                $navigatorHtml = GetNavigatorHtml( "index.php?visit_history=yes", 20,
                                'stGetVisitsByLogin', $callBackParam, $visits, $offset, $count );

                $smarty->assign("navigator", $navigatorHtml );
                $smarty->assign("visits", $visits );
                $smarty->assign("main_content_template", "visit_history.tpl.html");
        }

?>