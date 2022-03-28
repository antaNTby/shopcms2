<?php
#######################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#######################################

        //catalog database synchronization

        //show new orders page if selected
        if (!strcmp($sub, "dbsync"))
        {
        if ( CONF_BACKEND_SAFEMODE != 1 && (!isset($_SESSION["log"]) || !in_array(2,$relaccess))) //unauthorized
        {
                          $smarty->assign("admin_sub_dpt", "error_forbidden.tpl.html");
                        } else {
                //database synchronization
                //affects only products and categories database! doesn't touch customers and orders tables
                function updatebases()
                {

                $tot_data = 0;
                $tot_idx = 0;
                $tot_all = 0;
                $total_gain = 0;
                $local_query = "SHOW TABLE STATUS";
                $result = db_query($local_query);
                if (db_num_rows($result["resource"])) {
                  while ($row = db_fetch_row($result)) {
                  $local_query = 'OPTIMIZE TABLE '.$row[0];
                  $resultat = db_query($local_query);
                                                        }
                return true;
                }
                }

                if (isset($_POST["export_db"])) //export database to SQL-file
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=catalog&sub=dbsync&safemode=yes");
                        }
                        @set_time_limit(0);
                        $xdb = "core/temp/database.sql.gz";
                        if (file_exists($xdb)) unlink($xdb);

                        // write SQL insert statements to file
                        serProductAndCategoriesSerialization( $xdb );

                        $getFileParam = cryptFileParamCrypt( "GetDataBaseSqlScript", null );
                        $smarty->assign( "getFileParam", $getFileParam );
                        $smarty->assign( "filenamegz", "database.sql.gz" );
                        $smarty->assign( "sync_action", "export");
                        $smarty->assign( "database_filesize", round(filesize($xdb) / 1048576, 3));

                }
                elseif (isset($_POST["import_db"])) //execute sql-file
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=catalog&sub=dbsync&safemode=yes");
                        }

                        @set_time_limit(0);

                        //upload file
                        if (isset($_FILES["db"]) && $_FILES["db"]["name"])
                        {
                        if(preg_match("/^(.+?)\.sql(\.(bz2|gz))?$/", $_FILES["db"]["name"], $matches))
                        {

                                $db_name = "core/temp/file.db";
                                if (file_exists($db_name)) unlink($db_name);
                                $res = @move_uploaded_file($_FILES["db"]["tmp_name"], $db_name);
                                if ( $res )
                                {

                                        SetRightsToUploadedFile( $db_name );

                                        DestroyReferConstraintsXML( DATABASE_STRUCTURE_XML_PATH );

                                        //clear products&categories database
                                        serDeleteProductAndCategories();

                                        $f = implode("",gzfile($db_name));
                                        $f = str_replace("DBPRFX_", DB_PRFX, $f);
                                        $f = explode("INSERT INTO ".DB_PRFX,$f);
                                        for ($i=0; $i<count($f); $i++){
                                                if (strlen($f[$i])>0)
                                                {
                                                        $f[$i] = str_replace(");",")",$f[$i]);
                                                        db_query( "INSERT INTO ".DB_PRFX.$f[$i] );
                                                }

                                        }

                                        // _serImport($db_name);
                                        // serImportWithConstantNameReplacing($db_name);

                                        CreateReferConstraintsXML( DATABASE_STRUCTURE_XML_PATH );

                                        //update products count value if defined
                                        if (CONF_UPDATE_GCV == 1)  update_psCount(1);
                                        unlink($db_name);
                                        $smarty->assign("sync_successful", 1);
                                }
                                else
                                        $smarty->assign("sync_successful", 0);

                        }} else $smarty->assign("sync_successful", 0);

                        $smarty->assign("sync_action", "import");

                        //update products count value if defined
                        if (CONF_UPDATE_GCV == 1)
                        {
                                update_psCount(1);
                        }
                }elseif (isset($_POST["import_db_file"])) //execute sql-file
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=catalog&sub=dbsync&safemode=yes");
                        }

                        @set_time_limit(0);

                        //upload file
                        if (isset($_FILES["db_file"]) && $_FILES["db_file"]["name"])
                        {
                                $db_name = "core/temp/".$_FILES["db_file"]["name"];
                                if (file_exists($db_name)) unlink($db_name);
                                $res = @move_uploaded_file($_FILES["db_file"]["tmp_name"], $db_name);
                                if ( $res )
                                {

                                        SetRightsToUploadedFile( $db_name );

                        $masterfef = $_FILES["db_file"]["name"];
                        include_once('core/classes/class.dump.php');
                        $SK = new dumper();
                        $SK->SET['masterfef'] = $_FILES["db_file"]["name"];
                        $SK->restore();
                                        unlink($db_name);
                                        $smarty->assign("sync_successful", 1);
                                }
                                else
                                        $smarty->assign("sync_successful", 0);

                        } else $smarty->assign("sync_successful", 0);

                        $smarty->assign("sync_action", "import");

                }elseif (isset($_POST["full_export"]))
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=catalog&sub=dbsync&safemode=yes");
                        }

                        if (file_exists("core/temp/fulldump.sql.gz")) unlink("core/temp/fulldump.sql.gz");
                        $masterfef = "core/temp";
                        include_once('core/classes/class.dump.php');
                        $SK = new dumper();
                        $SK->SET['masterfef'] = "core/temp";
                        $SK->backup();
                        $getFileParam = cryptFileParamCrypt( "GetFullFileDb", null );
                        $smarty->assign( "getFileParam", $getFileParam );
                        $smarty->assign("filenameffe", "fulldump.sql.gz");
                        $smarty->assign( "database_filesizef", round(filesize("core/temp/fulldump.sql.gz") / 1048576, 3));
                }

                if (isset($_GET["optimize"]))
                {
                        if (CONF_BACKEND_SAFEMODE) //this action is forbidden when SAFE MODE is ON
                        {
                                Redirect(ADMIN_FILE."?dpt=catalog&sub=dbsync&safemode=yes");
                        }
                       @set_time_limit(0);
                       if(updatebases()) $smarty->assign("gain", "nots");
                }

                //set sub-department template
                $smarty->assign("admin_sub_dpt", "catalog_dbsync.tpl.html");
        }
        }
?>