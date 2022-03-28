<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################


function modGetModules( $moduleFiles )
{
        $modules        = array();
        foreach( $moduleFiles as $fileName )
        {
                $className        = GetClassName( $fileName );
                if(!$className) continue;
                eval( "\$objectModule = new ".$className."();" );
                if ( $objectModule->is_installed() )
                        $modules[] = $objectModule;
        }
        return $modules;
}

function modGetModuleObjects( $moduleFiles )
{
        $modules        = array();
        foreach( $moduleFiles as $fileName )
        {
                $className        = GetClassName( $fileName );
                if(!$className) continue;
                eval( "\$objectModule = new ".$className."();" );
                $modules[] = $objectModule;
        }
        return $modules;
}

function modGetModuleConfigs($_ModuleClassName){

        $ModuleConfigs = array();

        $sql = "select * FROM ".MODULES_TABLE." WHERE ModuleClassName='".xEscSQL($_ModuleClassName)."' ORDER BY module_name ASC
        ";
        $Result = db_query($sql);
        while ($_Row = db_fetch_row($Result)) {

                $ModuleConfigs[] = array(
                        'ConfigID'                 => $_Row['module_id'],
                        'ConfigName'         => $_Row['module_name'],
                        'ConfigClass'         => $_ModuleClassName,
                        );
        }

        return $ModuleConfigs;
}

function modGetModuleConfig($_ConfigID){

        $sql = "select * FROM ".MODULES_TABLE." WHERE module_id=".(int)$_ConfigID;
        return db_fetch_row(db_query($sql));
}

function modUninstallModuleConfig($_ConfigID){

        $ModuleConfig = modGetModuleConfig($_ConfigID);
        eval('$_tClass = new '.$ModuleConfig['ModuleClassName'].'();');
        $_tClass->uninstall($ModuleConfig['module_id']);
}

function modGetAllInstalledModuleObjs($_ModuleType = 0){

        $ModuleObjs = array();
        $sql = 'select module_id FROM '.MODULES_TABLE.' ORDER BY module_name ASC, module_id ASC';
        $Result = db_query($sql);
        while ($_Row = db_fetch_row($Result)) {

                $_TObj = modGetModuleObj($_Row['module_id'], $_ModuleType);
                if($_TObj && $_TObj->get_id() && $_TObj->is_installed())        $ModuleObjs[] = $_TObj;
        }
        return $ModuleObjs;
}

function modGetModuleObj($_ID, $_ModuleType = 0){

        $ModuleConfig = modGetModuleConfig($_ID);
        $objectModule = null;

        if(!$_ID) return $objectModule;

        if ($ModuleConfig['ModuleClassName']) {

                if(class_exists($ModuleConfig['ModuleClassName'])){

                        eval('$objectModule = new '.$ModuleConfig['ModuleClassName'].'('.$_ID.');');
                        if($_ModuleType && $objectModule->getModuleType()!=$_ModuleType)
                                $objectModule = null;
                }else{

                        $moduleFiles = array();
                        $IncludeDir = '';
                        switch ($_ModuleType){

                                case SHIPPING_RATE_MODULE:
                                        $IncludeDir = "core/modules/shipping";
                                        break;
                                case PAYMENT_MODULE:
                                        $IncludeDir = "core/modules/payment";
                                        break;
                                case SMSMAIL_MODULE:
                                        $IncludeDir = "core/modules/smsmail";
                                        break;
                        }
                        $moduleFiles = GetFilesInDirectory( $IncludeDir, "php" );

                        foreach( $moduleFiles as $fileName )
                        {
                                $className = GetClassName( $fileName );
                                if(strtolower($className) != strtolower($ModuleConfig['ModuleClassName'])) continue;

                                require_once($fileName);
                                eval( '$objectModule = new '.$className.'('.$_ID.');' );
                                return $objectModule;
                        }
                }
        }else {

                $moduleFiles = array();
                switch ($_ModuleType){

                        case SHIPPING_RATE_MODULE:
                                $moduleFiles = GetFilesInDirectory( "core/modules/shipping", "php" );
                                break;
                        case PAYMENT_MODULE:
                                $moduleFiles = GetFilesInDirectory( "core/modules/payment", "php" );
                                break;
                        case SMSMAIL_MODULE:
                                $IncludeDir = "core/modules/smsmail";
                                break;
                }

                foreach( $moduleFiles as $fileName )
                {
                        $className        = GetClassName( $fileName );
                        if(!$className) continue;
                        if(!class_exists($className))require_once($fileName);
                        eval( '$objectModule = new '.$className.'();' );

                        if ( $objectModule->get_id() == $_ID && $objectModule->title==$ModuleConfig['module_name'])
                                return $objectModule;
                        else $objectModule = null;
                }
        }
        return $objectModule;
}
?>