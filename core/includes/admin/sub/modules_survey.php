<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

        //customer survey module

        if (!strcmp($sub, "survey"))
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(19,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {


                if (isset($_GET["save_successful"])) //show successful save confirmation message
                {
                        $smarty->assign("configuration_saved", 1);
                }

                if (isset($_GET["del_poll"])) //show successful save confirmation message
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=modules&sub=survey&safemode=yes" );
                        }
                        db_query("delete from ".SURVEY_TABLE." WHERE poll_id=".(int)$_GET["del_poll"]);
                        Redirect(ADMIN_FILE."?dpt=modules&sub=survey&save_successful=yes");
                }

                if (isset($_GET["edit_poll"])) //show successful save confirmation message
                {
                        $result = db_query("SELECT poll_title, poll_ans FROM ".SURVEY_TABLE." WHERE poll_id=".(int)$_GET["edit_poll"]);
                        $data = db_fetch_row($result);
                        $current_survey_answers = unserialize($data["poll_ans"]);
                        $current_survey_question = $data[0];
                        $smarty->assign("current_survey_answers", $current_survey_answers);
                        $smarty->assign("current_survey_question", $current_survey_question);
                        $smarty->assign("current_survey", (int)$_GET["edit_poll"]);
                        $smarty->assign("edit_poll", "yes");
                }

                if (isset($_POST["save_current_voting"]) && isset($_POST["question"]) && isset($_POST["answers"])) // save new survey
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=modules&sub=survey&safemode=yes" );
                        }

                        $answers = explode("\n",chop($_POST["answers"]));
                        $answers_cm = array();
                        for ($i=0; $i<count($answers); $i++) {
                                if(rtrim($answers[$i]) !== "" && $i<10) $answers_cm[] = xToText(rtrim($answers[$i]));
                                }
                        $answers = serialize($answers_cm);
                        db_query("update ".SURVEY_TABLE." set poll_title='".xToText(trim($_POST["question"]))."', poll_ans='".$answers."' WHERE poll_id=".(int)$_POST["save_current_voting"]);
                        Redirect(ADMIN_FILE."?dpt=modules&sub=survey&save_successful=yes");
                }

                if (isset($_GET["active_poll"])) //show successful save confirmation message
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=modules&sub=survey&safemode=yes" );
                        }
                        db_query("UPDATE ".SURVEY_TABLE." SET active=0 WHERE active=1");
                        db_query("UPDATE ".SURVEY_TABLE." SET active=1 WHERE poll_id=".(int)$_GET["active_poll"]);
                        Redirect(ADMIN_FILE."?dpt=modules&sub=survey&save_successful=yes");
                }

                if (isset($_POST["save_voting"]) && isset($_POST["question"]) && isset($_POST["answers"])) // save new survey
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=modules&sub=survey&safemode=yes" );
                        }

                        $answers = explode("\n",chop($_POST["answers"]));
                        $answers_cm = array();
                        for ($i=0; $i<count($answers); $i++) {
                                if(rtrim($answers[$i]) !== "" && $i<10) $answers_cm[] = xToText(rtrim($answers[$i]));
                                }
                        $answers = serialize($answers_cm);
                        db_query("UPDATE ".SURVEY_TABLE." SET active=0 WHERE active=1");
                        db_query("insert into ".SURVEY_TABLE." (poll_date, poll_title, poll_ans, active) values('".xEscSQL(get_current_time())."','".xToText(trim($_POST["question"]))."','".$answers."',1)");
                        Redirect(ADMIN_FILE."?dpt=modules&sub=survey&save_successful=yes");
                }

                if (isset($_GET["start_new_poll"])) //show new customer survey form
                {
                        $smarty->assign("start_new_poll", "yes");
                }
                else //show existing survey results
                {
                        $result = db_query("SELECT poll_title, all_poll, active, poll_id, poll_date FROM ".SURVEY_TABLE." ORDER BY poll_id DESC");
                        $surveys = array();
                        while($data = db_fetch_row($result)){
                        $data["poll_ans"] = unserialize($data["poll_ans"]);
                        $surveys[] = $data;
                        $smarty->assign("surveys", $surveys);
                        }
                }

                //set sub-department template
                $smarty->assign("admin_sub_dpt", "modules_survey.tpl.html");
        }
        }
?>