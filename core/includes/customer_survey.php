<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        // customer survey processing

        if ((isset($_GET["save_voting_results"]) || isset($_GET["view_voting_results"])) && isset($_SESSION)) //save survey results
        {

                $result = db_query("select poll_id, poll_title, poll_ans, ans_0, ans_1, ans_2, ans_3, ans_4,
                ans_5, ans_6, ans_7, ans_8, ans_9, iplog, tdate FROM ".SURVEY_TABLE." WHERE active=1");
                $data = db_fetch_row($result);
				if($data["tdate"] != date('Y-m-d',time())){ 
				db_query("UPDATE ".SURVEY_TABLE." SET iplog='', tdate='".xEscSQL(get_current_time())."'  WHERE active=1");
				$data["iplog"] = "";
				}
                $answers_results = unserialize($data["poll_ans"]);
				if($data["iplog"]!=""){$iplogs = unserialize($data["iplog"]);}else{$iplogs = array();}
				$ipaddr = stGetCustomerIP_Address();
				if(!isset($iplogs[$ipaddr]))$iplogs[$ipaddr]=0;
   
                //increase voters count for current option
                if ((!isset($_SESSION["vote_completed"][$data[0]]) || $_SESSION["vote_completed"][$data[0]] != 1)
                        && isset($_GET["answer"]) && isset($answers_results[$_GET["answer"]]) && $iplogs[$ipaddr]<3) {						
                $anscol = (int)$_GET["answer"];
				$iplogs[$ipaddr]++;
				$iplogs = serialize($iplogs);
                db_query("UPDATE ".SURVEY_TABLE." SET ans_".$anscol."=ans_".$anscol."+1, all_poll=all_poll+1, iplog='".xEscSQL($iplogs)."'  WHERE active=1");
                $data["ans_".$anscol]++;
                //don't allow user to vote more than 1 time
                $_SESSION["vote_completed"][$data[0]] = 1;
                }else{
                if(!isset($_GET["view_voting_results"]))$smarty->assign("user_voted", 1);
                }
                $survey_results = array();
                for ($i=0; $i<count($answers_results); $i++) $survey_results[$i] = $data["ans_".$i];
                $smarty->assign("survey_results", $survey_results);
                $smarty->assign("show_survey_results", 1);
                $smarty->assign("main_content_template", "customer_survey_result.tpl.html");
        }


        $result = db_query("select poll_id, poll_title, poll_ans, all_poll FROM ".SURVEY_TABLE." WHERE active=1");
        $data = db_fetch_row($result);
        $answers = unserialize($data["poll_ans"]);
        $smarty->assign("survey_id", $data[0]);
        $smarty->assign("survey_question", $data[1]);
        $smarty->assign("survey_answers", $answers);
        $smarty->assign("voters_count", $data[3]);


?>