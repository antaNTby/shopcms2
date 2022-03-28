<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################




// *****************************************************************************
// Purpose  insert predefined modules setting group into SETTINGS_GROUPS_TABLE table
// Inputs
// Remarks  this function is called in CreateTablesStructureXML, ID of this group equals to
//                result of function settingGetFreeGroupId()
// Returns  nothing
function settingInstall()
{
        db_query("insert into ".SETTINGS_GROUPS_TABLE.
                " ( settings_groupID, settings_group_name, sort_order ) ".
                " values( ".(int)settingGetFreeGroupId().", 'MODULES', 0 ) " );
}


// *****************************************************************************
// Purpose  see settingInstall() function
// Inputs
// Remarks
// Returns  group ID
function settingGetFreeGroupId()
{
        return 1;
}

function settingGetConstNameByID($_SettingID){

        $ReturnVal = '';
        $sql = 'select settings_constant_name FROM '.SETTINGS_TABLE.' WHERE settingsID='.(int)$_SettingID;
        @list($ReturnVal) = db_fetch_row(db_query($sql));
        return $ReturnVal;
}

function settingGetAllSettingGroup()
{
        $q = db_query( "select settings_groupID, settings_group_name, sort_order from ".
                        SETTINGS_GROUPS_TABLE.
                        " where settings_groupID != ".(int)settingGetFreeGroupId().
                        " order by sort_order, settings_group_name " );
        $res = array();
        while( $row = db_fetch_row($q) ) $res[] = $row;
        return $res;
}


function settingGetSetting( $constantName )
{
        $q = db_query("select settingsID, settings_groupID, settings_constant_name, ".
                " settings_value, settings_title, settings_description, ".
                " settings_html_function, sort_order ".
                " from ".SETTINGS_TABLE.
                " where settings_constant_name='".xEscSQL($constantName)."' ");
         return ( $row = db_fetch_row($q) );
}


function settingGetSettings( $settings_groupID )
{
        $q = db_query("select settingsID, settings_groupID, settings_constant_name, ".
                " settings_value, settings_title, settings_description, ".
                " settings_html_function, sort_order ".
                " from ".SETTINGS_TABLE.
                " where settings_groupID=".(int)$settings_groupID." ".
                " order by sort_order, settings_title ");
        $res = array();
        while( $row = db_fetch_row($q) ) $res[] = $row;
        return $res;
}

function _setSettingOptionValue( $settings_constant_name, $value )
{
        db_query("update ".SETTINGS_TABLE." set settings_value='".xToText(trim($value))."' ".
                " where settings_constant_name='".xEscSQL($settings_constant_name)."'" );
}

function _getSettingOptionValue( $settings_constant_name )
{
        $q = db_query("select settings_value from ".SETTINGS_TABLE.
                " where settings_constant_name='".xEscSQL($settings_constant_name)."'" );
        if ( $row = db_fetch_row( $q ) ) return $row["settings_value"];
        return null;
}

function _setSettingOptionValueByID( $settings_constant_id, $value )
{
        $sql = '
                UPDATE '.SETTINGS_TABLE.' SET settings_value="'.xToText(trim($value)).'"
                WHERE settingsID="'.(int)$settings_constant_id.'"
        ';
        db_query($sql);
}

function _getSettingOptionValueByID( $settings_constant_id )
{
        $q = db_query("select settings_value from ".SETTINGS_TABLE.
                " where settingsID=".(int)$settings_constant_id);
        if ( $row = db_fetch_row( $q ) ) return $row["settings_value"];
        return null;
}


function settingCallHtmlFunction( $constantName )
{
        $q = db_query("select settings_html_function, settingsID, settings_constant_name from ".
                SETTINGS_TABLE." where settings_constant_name='".xEscSQL($constantName)."' " );
        if( $row = db_fetch_row($q) )
        {
                $function         =  $row["settings_html_function"];
                $settingsID        =  $row["settingsID"];
                $str = "";
                if ( preg_match('/,[ ]*$|\([ ]*$/',$function))
                        eval( "\$str=".$function."$settingsID);" );
                else
                        eval( "\$str=".$function.";" );
                return $str;
        }
        return false;
}


function settingCallHtmlFunctions( $settings_groupID )
{
        $q = db_query("select settings_html_function, settingsID from ".SETTINGS_TABLE.
                " where settings_groupID=".(int)$settings_groupID." ".
                " order by sort_order, settings_title " );
        $controls = array();
        while( $row = db_fetch_row($q) )
        {
                $function         =  $row["settings_html_function"];
                $settingsID        =  $row["settingsID"];
                $str = "";
                if ( is_bool(strpos($function,")")) )
                        eval( "\$str=".$function."$settingsID);" );
                else
                        eval( "\$str=".$function.";" );
                $controls[] = $str;
        }
        return $controls;
}



// *****************************************************************************
// Purpose        generate define directive withhelp eval function
// Inputs   nothing
// Remarks
// Returns        nothing
function settingDefineConstants()
{
        $dird = dirname($_SERVER['PHP_SELF']);
        $sourcessrandd = array("//" => "/", "\\" => "/");
        $dird = strtr($dird, $sourcessrandd);
        if ($dird != "/") $dirf = "/"; else $dirf = "";
        $url = "http://".$_SERVER["HTTP_HOST"].$dird.$dirf;

        define('CONF_FULL_SHOP_URL', trim($url));

        $q = db_query("select settings_constant_name, settings_value from ".SETTINGS_TABLE);
        while( $row = db_fetch_row($q) ) define($row["settings_constant_name"], $row["settings_value"] );
}


function setting_CHECK_BOX($settingsID)
{
        $q = db_query("select settings_constant_name from ".
                        SETTINGS_TABLE." where settingsID=".(int)$settingsID);
        $row = db_fetch_row( $q );
        $settings_constant_name = $row["settings_constant_name"];

        if ( isset($_POST["save"]) )
                _setSettingOptionValue( $settings_constant_name,
                                isset($_POST["setting".$settings_constant_name])?1:0 );
        $res = "<input type=checkbox name='setting".$settings_constant_name."' value=1 ";
        if ( _getSettingOptionValue($settings_constant_name) )
                $res .= " checked ";
        $res .= ">";
        return $res;
}

// *****************************************************************************
// Purpose
// Inputs
//                        $dataType = 0        - string
//                        $dataType = 1        - float
//                        $dataType = 2        - int
// Remarks
// Returns
function setting_TEXT_BOX($dataType, $settingsID, $BlockInSafeMode = null){

        if(isset($BlockInSafeMode)){

                if($settingsID && CONF_BACKEND_SAFEMODE)return ADMIN_SAFEMODE_BLOCKED;
                else{
                        $settingsID = $BlockInSafeMode;
                }
        }
        $q = db_query("select settings_constant_name from ".
                        SETTINGS_TABLE." where settingsID=".(int)$settingsID);
        $row = db_fetch_row( $q );
        $settings_constant_name = $row["settings_constant_name"];

        if ( isset($_POST["save"]) && isset($_POST["setting".$settings_constant_name]) )
        {
                 if ( $dataType == 0 )
                        $value = $_POST["setting".$settings_constant_name];
                else if ( $dataType == 1 )
                        $value = (float)$_POST["setting".$settings_constant_name];
                else if ( $dataType == 2 )
                        $value = (int)$_POST["setting".$settings_constant_name];
                _setSettingOptionValue( $settings_constant_name, $value );
        }
        return "<input type=text value='"._getSettingOptionValue( $settings_constant_name ).
                        "' name='setting".$settings_constant_name."' >";
}

// *****************************************************************************
// Purpose        same as setting_TEXT_BOX() except for it stores data in encrypted way
// Inputs
//                        $dataType = 0        - string
//                        $dataType = 1        - float
//                        $dataType = 2        - int
// Remarks
// Returns
function setting_TEXT_BOX_SECURE($dataType, $settingsID)
{
        $q = db_query("select settings_constant_name from ".
                        SETTINGS_TABLE." where settingsID=".(int)$settingsID);
        $row = db_fetch_row( $q );
        $settings_constant_name = $row["settings_constant_name"];

        if ( isset($_POST["save"]) && isset($_POST["setting".$settings_constant_name]) )
        {
                 if ( $dataType == 0 )
                        $value = $_POST["setting".$settings_constant_name];
                else if ( $dataType == 1 )
                        $value = (float)$_POST["setting".$settings_constant_name];
                else if ( $dataType == 2 )
                        $value = (int)$_POST["setting".$settings_constant_name];
                _setSettingOptionValue( $settings_constant_name, cryptCCNumberCrypt ( $value , NULL ) );
        }
        return "<input type=text value='".cryptCCNumberDeCrypt( _getSettingOptionValue( $settings_constant_name ) , NULL ).
                        "' name='setting".$settings_constant_name."' >";
}


function setting_DATEFORMAT()
{
        if ( isset($_POST["save"]) )
        {
                if ( isset($_POST["setting_DATEFORMAT"]) )
                {
                        _setSettingOptionValue( "CONF_DATE_FORMAT",
                                $_POST["setting_DATEFORMAT"] );
                }
        }

        $res = "";
        $currencies = currGetAllCurrencies();
        $res = "<select name='setting_DATEFORMAT'>";
        $current_format = _getSettingOptionValue("CONF_DATE_FORMAT");
        if (!$current_format) $current_format = "MM/DD/YYYY";

        //first option  - MM/DD/YYYY - US style
        $res .= "<option value='MM/DD/YYYY'";
        if (!strcmp($current_format,"MM/DD/YYYY")) $res .= " selected";
        $res .= ">MM/DD/YYYY</option>";

        //second option - DD.MM.YYYY - European style
        $res .= "<option value='DD.MM.YYYY'";
        if (!strcmp($current_format,"DD.MM.YYYY")) $res .= " selected";
        $res .= ">DD.MM.YYYY</option>";

        $res .= "</select>";
        return $res;
}


function setting_WEIGHT_UNIT($settingsID)
{
        if ( isset($_POST["save"]) )
                _setSettingOptionValue( "CONF_WEIGHT_UNIT",
                                $_POST["setting_WEIGHT_UNIT"] );
        $res = "<select name='setting_WEIGHT_UNIT'>";

        $units = array(
                                "lbs" => STRING_LBS,
                                "kg" => STRING_KG,
                                "g" => STRING_GRAM
                        );

        foreach( $units as $key => $val )
        {
                $res .= "<option value='".$key."'";
                if ( !strcmp(_getSettingOptionValue("CONF_WEIGHT_UNIT"),$key) )$res .= " selected ";
                $res .= ">";
                $res .= "        ".$val;
                $res .= "</option>";
        }
        $res .= "</select>";
        return $res;
}


function settingCONF_DEFAULT_CURRENCY()
{
        if ( isset($_POST["save"]) )
        {
                if ( isset($_POST["settingCONF_DEFAULT_CURRENCY"]) )
                {
                        _setSettingOptionValue( "CONF_DEFAULT_CURRENCY",
                                $_POST["settingCONF_DEFAULT_CURRENCY"] );
                }
        }

        $res = "";
        $currencies = currGetAllCurrencies();
        $res = "<select name='settingCONF_DEFAULT_CURRENCY'>";
        $res .= "<option value='0'>".ADMIN_NOT_DEFINED."</option>";
        $selectedID = _getSettingOptionValue("CONF_DEFAULT_CURRENCY");
        foreach( $currencies as $currency )
        {
                $res .= "<option value='".$currency["CID"]."' ";
                if ( $selectedID == $currency["CID"] )
                        $res .= " selected ";
                $res .= ">";
                $res .= $currency["Name"];
                $res .= "</option>";
        }
        $res .= "</select>";
        return $res;
}

function settingCONF_MAIL_METHOD()
{
        if ( isset($_POST["save"]) )
        {
                if ( isset($_POST["settingCONF_MAIL_METHOD"]) )
                {
                        _setSettingOptionValue( "CONF_MAIL_METHOD",
                                $_POST["settingCONF_MAIL_METHOD"] );
                }
        }
        $selectedID = _getSettingOptionValue("CONF_MAIL_METHOD");
        $res = "";
        $res = "<select name='settingCONF_MAIL_METHOD'>";
        $res .= "<option value='0'";
        if ( $selectedID == 0 ) $res .= " selected ";
        $res .= ">Smtp</option>";
        $res .= "<option value='1'";
        if ( $selectedID == 1 ) $res .= " selected ";
        $res .= ">Mail</option>";
        $res .= "</select>";
        return $res;
}

function settingCONF_USER_SYSTEM()
{
        if ( isset($_POST["save"]) )
        {
                if ( isset($_POST["settingCONF_USER_SYSTEM"]) )
                {
                        _setSettingOptionValue( "CONF_USER_SYSTEM",
                                $_POST["settingCONF_USER_SYSTEM"] );
                }
        }
        $selectedID = _getSettingOptionValue("CONF_USER_SYSTEM");
        $res = "";
        $res = "<select name='settingCONF_USER_SYSTEM'>";
        $res .= "<option value='0'";
        if ( $selectedID == 0 ) $res .= " selected ";
        $res .= ">".ADMIN_USER_SYST_OFF."</option>";
        $res .= "<option value='1'";
        if ( $selectedID == 1 ) $res .= " selected ";
        $res .= ">".ADMIN_USER_SYST_OFFON."</option>";
        $res .= "<option value='2'";
        if ( $selectedID == 2 ) $res .= " selected ";
        $res .= ">".ADMIN_USER_SYST_ON."</option>";
        $res .= "</select>";
        return $res;
}

function settingCONF_TIMEZONE()
{
        if ( isset($_POST["save"]) )
        {
                if ( isset($_POST["settingCONF_TIMEZONE"]) )
                {
                        _setSettingOptionValue( "CONF_TIMEZONE",
                                $_POST["settingCONF_TIMEZONE"] );
                }
        }
        $selectedID = _getSettingOptionValue("CONF_TIMEZONE");
        $res = "";
        $res = "<select name='settingCONF_TIMEZONE'>";
        $res .= "<option value='-12'";
        if ( $selectedID == -12 ) $res .= " selected ";
        $res .= ">GMT-12</option>";
        $res .= "<option value='-11'";
        if ( $selectedID == -11 ) $res .= " selected ";
        $res .= ">GMT-11</option>";
        $res .= "<option value='-10'";
        if ( $selectedID == -10 ) $res .= " selected ";
        $res .= ">GMT-10</option>";
        $res .= "<option value='-9'";
        if ( $selectedID == -9 ) $res .= " selected ";
        $res .= ">GMT-9</option>";
        $res .= "<option value='-8'";
        if ( $selectedID == -8 ) $res .= " selected ";
        $res .= ">GMT-8</option>";
        $res .= "<option value='-7'";
        if ( $selectedID == -7 ) $res .= " selected ";
        $res .= ">GMT-7</option>";
        $res .= "<option value='-6'";
        if ( $selectedID == -6 ) $res .= " selected ";
        $res .= ">GMT-6</option>";
        $res .= "<option value='-5'";
        if ( $selectedID == -5 ) $res .= " selected ";
        $res .= ">GMT-5</option>";
        $res .= "<option value='-4'";
        if ( $selectedID == -4 ) $res .= " selected ";
        $res .= ">GMT-4</option>";
        $res .= "<option value='-3'";
        if ( $selectedID == -3 ) $res .= " selected ";
        $res .= ">GMT-3</option>";
        $res .= "<option value='-2'";
        if ( $selectedID == -2 ) $res .= " selected ";
        $res .= ">GMT-2</option>";
        $res .= "<option value='-1'";
        if ( $selectedID == -1 ) $res .= " selected ";
        $res .= ">GMT-1</option>";
        $res .= "<option value='0'";
        if ( $selectedID == 0 ) $res .= " selected ";
        $res .= ">GMT+0</option>";
        $res .= "<option value='1'";
        if ( $selectedID == 1 ) $res .= " selected ";
        $res .= ">GMT+1</option>";
        $res .= "<option value='2'";
        if ( $selectedID == 2 ) $res .= " selected ";
        $res .= ">GMT+2</option>";
        $res .= "<option value='3'";
        if ( $selectedID == 3 ) $res .= " selected ";
        $res .= ">GMT+3</option>";
        $res .= "<option value='4'";
        if ( $selectedID == 4 ) $res .= " selected ";
        $res .= ">GMT+4</option>";
        $res .= "<option value='5'";
        if ( $selectedID == 5 ) $res .= " selected ";
        $res .= ">GMT+5</option>";
        $res .= "<option value='6'";
        if ( $selectedID == 6 ) $res .= " selected ";
        $res .= ">GMT+6</option>";
        $res .= "<option value='7'";
        if ( $selectedID == 7 ) $res .= " selected ";
        $res .= ">GMT+7</option>";
        $res .= "<option value='8'";
        if ( $selectedID == 8 ) $res .= " selected ";
        $res .= ">GMT+8</option>";
        $res .= "<option value='9'";
        if ( $selectedID == 9 ) $res .= " selected ";
        $res .= ">GMT+9</option>";
        $res .= "<option value='10'";
        if ( $selectedID == 10 ) $res .= " selected ";
        $res .= ">GMT+10</option>";
        $res .= "<option value='11'";
        if ( $selectedID == 11 ) $res .= " selected ";
        $res .= ">GMT+11</option>";
        $res .= "<option value='12'";
        if ( $selectedID == 12 ) $res .= " selected ";
        $res .= ">GMT+12</option>";


        $res .= "</select>";
        return $res;
}


function settingCONF_DEFAULT_COUNTRY()
{
        if ( isset($_POST["save"]) )
                _setSettingOptionValue( "CONF_DEFAULT_COUNTRY",
                                $_POST["settingCONF_DEFAULT_COUNTRY"] );
        $res = "<select name='settingCONF_DEFAULT_COUNTRY'>";
        $res .= "<option value='0'>".ADMIN_NOT_DEFINED."</option>";
        $selectedID = _getSettingOptionValue("CONF_DEFAULT_COUNTRY");
        $count_row = 0;
        $countries = cnGetCountries( array(), $count_row );

        foreach( $countries as $country )
        {
                $res .= "<option value='".$country["countryID"]."'";
                if ( $selectedID == $country["countryID"] )
                        $res .= " selected ";
                $res .= ">";
              $res .= "        ".$country["country_name"];
                $res .= "</option>";
        }
        $res .= "</select>";
        return $res;
}


function settingCONF_DEFAULT_TAX_CLASS()
{
        if ( isset($_POST["save"]) ) {
        _setSettingOptionValue( "CONF_DEFAULT_TAX_CLASS", $_POST["settingCONF_DEFAULT_TAX_CLASS"] );
        db_query( "update ".PRODUCTS_TABLE." set classID=".(int)$_POST["settingCONF_DEFAULT_TAX_CLASS"]." ");
        }
        $res  = "<select name='settingCONF_DEFAULT_TAX_CLASS'>";
        $res .= "        <option value='0'>".ADMIN_NOT_DEFINED."</option>";
        $selectedID = _getSettingOptionValue("CONF_DEFAULT_TAX_CLASS");
        $count_row = 0;
        $taxClasses = taxGetTaxClasses();
        foreach( $taxClasses as $taxClass )
        {
                $res .= "        <option value='".$taxClass["classID"]."'";
                if ( $selectedID == $taxClass["classID"] )
                        $res .= " selected ";
                $res .= ">";
                $res .= "        ".$taxClass["name"];
                $res .= "</option>";
        }
        $res .= "</select>";
        return $res;
}

function settingCONF_DEFAULT_TEMPLATE()
{
        if ( isset($_POST["save"]) ) {
        _setSettingOptionValue( "CONF_DEFAULT_TEMPLATE", $_POST["settingCONF_DEFAULT_TEMPLATE"] );
        eval( " define('UPDATEDESIGND', 1);" );
        }
        $res  = "<select name='settingCONF_DEFAULT_TEMPLATE'>";
        $selectedID = _getSettingOptionValue("CONF_DEFAULT_TEMPLATE");
        $themelist = array();
        $handle = opendir('core/tpl/user/');
        while ($file = readdir($handle)) {
        if ((!preg_match("/[.]/",$file))) {
        $themelist[] = $file;
        }
        }
        closedir($handle);

        for ($i = 0; $i < count($themelist); $i++) {
        if ($themelist[$i] != "") {
                      $res .= "<option value='".$themelist[$i]."' ";
                                          if ($themelist[$i] == $selectedID) $res .= "selected";
                      $res .= ">".$themelist[$i]."</option>";
                }
        }
        $res .= "</select>";
        return $res;
}

function settingCONF_SELECT_CART_METHOD()
{
        if ( isset($_POST["save"]) ) {
        _setSettingOptionValue( "CONF_CART_METHOD", $_POST["settingCONF_SELECT_CART_METHOD"] );
        if ($_POST["settingCONF_SELECT_CART_METHOD"] == 1){
        _setSettingOptionValue( "CONF_OPEN_SHOPPING_CART_IN_NEW_WINDOW", 1);
        }else{
        _setSettingOptionValue( "CONF_OPEN_SHOPPING_CART_IN_NEW_WINDOW", 0);
        }
        }
        $res  = "<select name='settingCONF_SELECT_CART_METHOD'>";
        $selectedID = _getSettingOptionValue("CONF_CART_METHOD");
        $methodlist = array();
        $methodlist[] = array("title"=>STRING_CART_ID1, "value"=>0);
        $methodlist[] = array("title"=>STRING_CART_ID2, "value"=>1);
        $methodlist[] = array("title"=>STRING_CART_ID3, "value"=>2);

        for ($i = 0; $i < count($methodlist); $i++) {
          if ($methodlist[$i] != "") {
                      $res .= "<option value='".$methodlist[$i]["value"]."' ";
                                          if ($methodlist[$i]["value"] == $selectedID) $res .= "selected";
                      $res .= ">".$methodlist[$i]["title"]."</option>";
                }
        }
        $res .= "</select>";
        return $res;
}

function settingSELECT_USERTEMPLATE()
{
      $res  = "";

      if ( isset($_SESSION["CUSTOM_DESIGN"])){
      $selectedID = $_SESSION["CUSTOM_DESIGN"];
      }else{
      $selectedID = _getSettingOptionValue("CONF_DEFAULT_TEMPLATE");
      }

      $themelist = array();
      $handle = opendir('core/tpl/user/');

      while ($file = readdir($handle)) {
        if ((!ereg("[.]",$file))) {
                        $themelist[] = $file;
                }
      }
      closedir($handle);

      for ($i = 0; $i < count($themelist); $i++) {
        if ($themelist[$i] != "") {
                      $res .= "<option value='".$themelist[$i]."' ";
                                          if ($themelist[$i] == $selectedID) $res .= "selected";
                      $res .= ">".$themelist[$i]."</option>";
                }
      }
        return $res;
}


function settingCONF_DEFAULT_SORT_ORDER()
{
        if ( isset($_POST["save"]) ) {

        _setSettingOptionValue( "CONF_DEFAULT_SORT_ORDER", $_POST["settingCONF_DEFAULT_SORT_ORDER"] );

        }
        $res  = "<select name='settingCONF_DEFAULT_SORT_ORDER'>";
        $selectedsID = _getSettingOptionValue("CONF_DEFAULT_SORT_ORDER");
        $sortlist = array();
        $sortlist[] = array("title"=>STRING_SET_ID3, "value"=>"sort_order, name");
        $sortlist[] = array("title"=>STRING_SET_ID1, "value"=>"Price ASC");
        $sortlist[] = array("title"=>STRING_SET_ID2, "value"=>"Price DESC");

        for ($i = 0; $i < count($sortlist); $i++) {
          if ($sortlist[$i] != "") {
                      $res .= "<option value='".$sortlist[$i]["value"]."' ";
                                          if ($sortlist[$i]["value"] == $selectedsID) $res .= "selected";
                      $res .= ">".$sortlist[$i]["title"]."</option>";
                }
        }
        $res .= "</select>";
        return $res;
}

function settingCONF_DISPLAY_FOTO()
{
        if ( isset($_POST["save"]) ) {

        _setSettingOptionValue( "CONF_DISPLAY_FOTO", $_POST["settingCONF_DISPLAY_FOTO"] );

        }
        $res  = "<select name='settingCONF_DISPLAY_FOTO'>";
        $selectedsID = _getSettingOptionValue("CONF_DISPLAY_FOTO");
        $sortlist = array();
        $sortlist[] = array("title"=>BLOCK_EDIT_9, "value"=>0);
        $sortlist[] = array("title"=>BLOCK_EDIT_6, "value"=>1);

        for ($i = 0; $i < count($sortlist); $i++) {
          if ($sortlist[$i] != "") {
                      $res .= "<option value='".$sortlist[$i]["value"]."' ";
                                          if ($sortlist[$i]["value"] == $selectedsID) $res .= "selected";
                      $res .= ">".$sortlist[$i]["title"]."</option>";
                }
        }
        $res .= "</select>";
        return $res;
}

function settingCONF_DEFAULT_CUSTOMER_GROUP()
{
        if ( isset($_POST["save"]) )
                _setSettingOptionValue( "CONF_DEFAULT_CUSTOMER_GROUP",
                                $_POST["settingCONF_DEFAULT_CUSTOMER_GROUP"] );

        $res = "<select name='settingCONF_DEFAULT_CUSTOMER_GROUP'>";
        $selectedID = _getSettingOptionValue("CONF_DEFAULT_CUSTOMER_GROUP");

        $res .= "<option value='0'>".ADMIN_NOT_DEFINED."</option>";

        $custGroups = GetAllCustGroups();
        foreach( $custGroups as $custGroup )
        {
                $res .= "<option value='".$custGroup["custgroupID"]."'";
                if ( $selectedID == $custGroup["custgroupID"] )
                        $res .= " selected ";
                $res .= ">";
                $res .= "        ".$custGroup["custgroup_name"];
                $res .= "</option>";
        }
        $res .= "</select>";
        return $res;
}


function _CONF_DISCOUNT_TYPE_radio_button( $value, $caption, $checked, $href )
{
        if ( $checked == 1 )
                $checked = "checked";
        else
                $checked = "";
        if ( $href )
        {
                $href1 = "<a href='".ADMIN_FILE."?dpt=custord&sub=custgroup' class=inl>";
                $href2 = "</a>";
        }
        else
        {
                $href1 = "";
                $href2 = "";
        }
        $res  = "";
        $res .= "<tr class=liney>";
        $res .= "<td valign=middle>";
        $res .= "<input class='round' name='settingCONF_DISCOUNT_TYPE' type=radio $checked value='".$value."' id=\"disstatus_".$value."\">";
        $res .= "</td>";
        $res .= "<td valign=middle align=left width=\"100%\"><label for=\"disstatus_".$value."\"> &nbsp;";
        $res .= $caption;
        $res .= "</label></td>";
        $res .= "</tr>";
        return $res;
}


function settingCONF_DISCOUNT_TYPE()
{
        if ( isset($_POST["save"]) )
                _setSettingOptionValue( "CONF_DISCOUNT_TYPE", $_POST["settingCONF_DISCOUNT_TYPE"] );
        $value = _getSettingOptionValue("CONF_DISCOUNT_TYPE");
        $value_go = _getSettingOptionValue("CONF_USER_SYSTEM");
        if ($value_go > 0){
        $res = "";
        $res .= "<table class=and>";
        $res .= _CONF_DISCOUNT_TYPE_radio_button( "1", ADMIN_DISCOUNT_IS_SWITCHED_OFF,  $value=="1"?1:0, 0 );
        $res .= _CONF_DISCOUNT_TYPE_radio_button( "2", ADMIN_DISCOUNT_CUSTOMER_GROUP,         $value=="2"?1:0, 1 );
        $res .= _CONF_DISCOUNT_TYPE_radio_button( "3", ADMIN_DISCOUNT_GENERAL_ORDER_PRICE, $value=="3"?1:0, 0 );
        $res .= _CONF_DISCOUNT_TYPE_radio_button( "4", ADMIN_DISCOUNT_CUSTOMER_GROUP_PLUS_GENERAL_ORDER_PRICE,         $value=="4"?1:0, 0 );
        $res .= _CONF_DISCOUNT_TYPE_radio_button( "5", ADMIN_DISCOUNT_MAX_CUSTOMER_GROUP_GENERAL_ORDER_PRICE,         $value=="5"?1:0, 0 );
        $res .= "</table>";
        }else{
            $res = "";
        $res .= "<table class=and>";
        $res .= _CONF_DISCOUNT_TYPE_radio_button( "1", ADMIN_DISCOUNT_IS_SWITCHED_OFF,  $value=="1"?1:0, 0 );
        $res .= _CONF_DISCOUNT_TYPE_radio_button( "3", ADMIN_DISCOUNT_GENERAL_ORDER_PRICE, $value=="3"?1:0, 0 );
        $res .= "</table>";
        }
        return $res;
}


function settingCONF_NEW_ORDER_STATUS()
{
        if ( isset($_POST["save"]) && isset($_POST["settingCONF_NEW_ORDER_STATUS"]) )
                _setSettingOptionValue( "CONF_NEW_ORDER_STATUS",
                                $_POST["settingCONF_NEW_ORDER_STATUS"] );
        $orders = ostGetOrderStatues( false );

        $res = "";
        if ( count($orders)<2 )
                $res .= "<b>".ADMIN_STATUSES_COUNT_PROMPT_ERROR."<b>";
        else
        {
                $selectedID = _getSettingOptionValue("CONF_NEW_ORDER_STATUS");
                if ( $selectedID == "" )
                        $res .= "<b>".ADMIN_STATUS_IS_NOT_DEFINED."</b>&nbsp;";
                $res .= "<select name='settingCONF_NEW_ORDER_STATUS'>\n";
                foreach( $orders as $order )
                {
                        $res .= "<option value='".$order["statusID"]."' ";
                        if ( $selectedID == $order["statusID"] )
                                $res .= "selected";
                        $res .= ">\n";
                        $res .= "                ".$order["status_name"]."\n";
                        $res .= "</option>\n";
                }
                $res .= "</select>";
        }
        return $res;
}

function settingCONF_COMPLETED_ORDER_STATUS()
{
        $equal_prompt_error = "";
        if ( isset($_POST["save"]) && isset($_POST["settingCONF_COMPLETED_ORDER_STATUS"]) )
        {
                if ( $_POST["settingCONF_NEW_ORDER_STATUS"] ==
                                $_POST["settingCONF_COMPLETED_ORDER_STATUS"] )
                {
                        $equal_prompt_error = ADMIN_STATUSES_EQUAL_PROMPT_ERROR;
                        $_POST["settingCONF_COMPLETED_ORDER_STATUS"] = ostGetOtherStatus(
                                                                        $_POST["settingCONF_COMPLETED_ORDER_STATUS"] );
                        $_POST["settingCONF_COMPLETED_ORDER_STATUS"] =
                                $_POST["settingCONF_COMPLETED_ORDER_STATUS"]["statusID"];
                }
                _setSettingOptionValue( "CONF_COMPLETED_ORDER_STATUS",
                                $_POST["settingCONF_COMPLETED_ORDER_STATUS"] );
        }
        $orders = ostGetOrderStatues( false );
        $res = "";
        if ( count($orders)<2 )
                $res = "<b>".ADMIN_STATUSES_COUNT_PROMPT_ERROR."<b>";
        else
        {
                $selectedID = _getSettingOptionValue("CONF_COMPLETED_ORDER_STATUS");
                if ( $selectedID == "" )
                        $res .= "&nbsp;<b>".ADMIN_STATUS_IS_NOT_DEFINED."</b>";
                $res .= "<select name='settingCONF_COMPLETED_ORDER_STATUS'>\n";
                foreach( $orders as $order )
                {
                        $res .= "<option value='".$order["statusID"]."' ";
                        if ( $selectedID == $order["statusID"] )
                                $res .= "selected";
                        $res .= ">";
                        $res .= "                ".$order["status_name"]."\n";
                        $res .= "</option>\n";
                }
                $res .= "</select>";
        }
        return $res;
}

function setting_ORDER_STATUS_SELECT( $_SettingID ){

        $Options = array(array('title'=>ADMIN_NOT_DEFINED, 'value'=>0,));
        $statuses = ostGetOrderStatues( false );
        foreach ($statuses as $_statuses){

                $Options[] = array(
                        'title'                 => $_statuses['status_name'],
                        'value'         => $_statuses['statusID'],
                        );
        }

        return setting_SELECT_BOX($Options, $_SettingID);
}

function setting_CURRENCY_SELECT( $_SettingID ){

        $Options = array(array('title'=>ADMIN_NOT_DEFINED, 'value'=>0,));
        $Currencies = currGetAllCurrencies();
        foreach ($Currencies as $_Currency){

                $Options[] = array(
                        'title'                 => $_Currency['Name'],
                        'value'         => $_Currency['CID'],
                        );
        }

        return setting_SELECT_BOX($Options, $_SettingID);
}
function settingCONF_COLOR( $settingsID )
{
        $q = db_query("select settingsID, settings_constant_name from ".
                                SETTINGS_TABLE." where settingsID=$settingsID");
        $row = db_fetch_row($q);
        $constant_name = $row["settings_constant_name"];


        if ( isset($_POST["save"]) && isset($_POST["settingCONF_COLOR_".$settingsID])  )
                _setSettingOptionValue( $constant_name,
                                $_POST["settingCONF_COLOR_".$settingsID]  );

        $value = _getSettingOptionValue( $constant_name );
        $value = strtoupper($value);
        $res = "<table><tr><td><table bgcolor=black cellspacing=1><tr><td bgcolor=#".$value.">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table></td>";
        $res .= "<td><input type=text value='".$value."' name='settingCONF_COLOR_$settingsID' ></td></tr></table>";
        return $res;
}


function settingCONF_COUNTRY()
{
        if ( isset($_POST["save"]) )
                _setSettingOptionValue( "CONF_COUNTRY",
                                $_POST["settingCONF_COUNTRY"] );

        $count_row = 0;
        $countries = cnGetCountries( array(), $count_row );

        $res = "";

        $selectedID = _getSettingOptionValue("CONF_COUNTRY");
        if ( isset( $_GET["countryID"] ) )
                $selectedID = $_GET["countryID"];
        //if ( $selectedID == "0" )
        //        $res .= "<b>".ADMIN_CONF_COUNTRY_IS_NOT_DEFINED."</b>&nbsp;";
        $onChange = "JavaScript:window.location=\"".ADMIN_FILE."?dpt=conf&sub=setting&settings_groupID=".$_GET["settings_groupID"]."&countryID=\" + document.MainForm.settingCONF_COUNTRY.value";
        // onchange='$onChange'
        $res .= "<select name='settingCONF_COUNTRY' >\n";
        $res .= "        <option value='0'>".ADMIN_NOT_DEFINED."</option>";
        foreach( $countries as $country )
        {
                $res .= "<option value='".$country["countryID"]."' ";
                if ( $selectedID == $country["countryID"] )
                        $res .= "selected";
                $res .= ">\n";
                 $res .= "                ".$country["country_name"]."\n";
                $res .= "</option>\n";
        }
        $res .= "</select>";
        return $res;
}

function settingCONF_ZONE()
{
        if ( isset($_POST["save"]) )
                if ( isset($_POST["settingCONF_ZONE"]) )
                        _setSettingOptionValue( "CONF_ZONE", $_POST["settingCONF_ZONE"] );

        $countries = cnGetCountries( array(), $count_row );
        if ( count($countries) != 0 )
        {

                $countryID = _getSettingOptionValue("CONF_COUNTRY");
                $zones = znGetZones( _getSettingOptionValue("CONF_COUNTRY") );

                $selectedID = _getSettingOptionValue("CONF_ZONE");
                $res = "";
                if ( !ZoneBelongsToCountry($selectedID, $countryID) )
                        $res .= ERROR_ZONE_DOES_NOT_CONTAIN_TO_COUNTRY."<br>";
                if ( count($zones) > 0 )
                {
                        $res .= "<select name='settingCONF_ZONE'>\n";
                        foreach( $zones as $zone )
                        {
                                $res .= "<option value='".$zone["zoneID"]."' ";
                                if ( $selectedID == $zone["zoneID"] )
                                        $res .= "selected";
                                $res .= ">\n";
                             $res .= "                ".$zone["zone_name"]."\n";
                                $res .= "</option>\n";
                        }
                        $res .= "</select>";
                }
                else
                {
                        if ( trim($selectedID) != (string)((int)$selectedID) )
                                $res .= "<input type=text name='settingCONF_ZONE' value='$selectedID'>";
                        else
                                $res .= "<input type=text name='settingCONF_ZONE' value=''>";
                }
                return $res;
        }
        else
                return "-";
}

function settingCONF_CALCULATE_TAX_ON_SHIPPING()
{
        if ( isset($_POST["save"]) )
                _setSettingOptionValue( "CONF_CALCULATE_TAX_ON_SHIPPING", $_POST["settingCONF_CALCULATE_TAX_ON_SHIPPING"] );

        $res = "<select name='settingCONF_CALCULATE_TAX_ON_SHIPPING'>";
        $res .= "        <option value='0'>".ADMIN_NOT_DEFINED."</option>";
        $selectedID = _getSettingOptionValue("CONF_CALCULATE_TAX_ON_SHIPPING");
        $count_row = 0;
        $taxClasses = taxGetTaxClasses();
        foreach( $taxClasses as $taxClass )
        {
                $res .= "<option value='".$taxClass["classID"]."'";
                if ( $selectedID == $taxClass["classID"] )
                        $res .= " selected ";
                $res .= ">";
                $res .= "        ".$taxClass["name"];
                $res .= "</option>";
        }
        $res .= "</select>";
        return $res;
}
 function setting_SELECT_BOX($_Options, $_SettingID){

        if(!is_array($_Options)){

                $_Options = explode(',',$_Options);
                $TC = count($_Options)-1;
                for(;$TC>=0;$TC--){

                        $_Options[$TC] = explode(':', $_Options[$TC]);
                        $_Options[$TC]['title'] = $_Options[$TC][0];
                        if(!isset($_Options[$TC][1])){
                                $_Options[$TC]['value'] = '';
                        }else{
                                $_Options[$TC]['value'] = $_Options[$TC][1];
                        }
                }
        }
        $sql = "select settings_constant_name
                FROM ".SETTINGS_TABLE."
                WHERE settingsID=".(int)$_SettingID;

        $row = db_fetch_row( db_query($sql) );
        $settings_constant_name = $row["settings_constant_name"];

        if ( isset($_POST["save"]) )
                _setSettingOptionValue( $settings_constant_name,         $_POST["setting_".$settings_constant_name] );

        $html = '<select name="setting_'.$settings_constant_name.'">';
        $SettingConstantValue = _getSettingOptionValue($settings_constant_name);
        foreach ($_Options as $_Option){

                $html .= '<option value="'.$_Option['value'].'"'.($SettingConstantValue==$_Option['value']?' selected="selected"':'').'>'.$_Option['title'].'</option>';
        }
        $html .= '</select>';
        return $html;
}

function setting_CHECKBOX_LIST($_boxDescriptions, $_SettingID){

        $sql = "select settings_constant_name
                FROM ".SETTINGS_TABLE."
                WHERE settingsID=".$_SettingID;
        $row = db_fetch_row( db_query($sql) );
        $settings_constant_name = $row["settings_constant_name"];

        if ( isset($_POST["save"]) ){

                $newValues = '';
                $_POST['setting_'.$settings_constant_name] = isset($_POST['setting_'.$settings_constant_name])?$_POST['setting_'.$settings_constant_name]:array();

                $maxOffset = max(array_keys($_boxDescriptions));

                for(; $maxOffset>=0; $maxOffset-- ){

                        $newValues .= (int)in_array($maxOffset, $_POST['setting_'.$settings_constant_name]);
                }
                _setSettingOptionValue( $settings_constant_name,         bindec($newValues) );
        }

        $Value = _getSettingOptionValue($settings_constant_name);
        $html = '';


        foreach ($_boxDescriptions as $_offset=>$_boxDescr){

                $html .= '<div style="padding:2px;"><input'.($Value&pow(2, $_offset)?' checked="checked"':'').' name="setting_'.$settings_constant_name.'[]" value="'.$_offset.'" type="checkbox" style="margin:0px;padding:0px;" />&nbsp;'.$_boxDescr.'</div>';
        }
        return $html;
}

function setting_COUNTRY_SELECT($_ShowButton, $_SettingID = null){

        if(!isset($_SettingID)){

                $_SettingID = $_ShowButton;
                $_ShowButton = false;
        }

        $Options = array(
                array("title"=>'-', "value"=>0)
                );
        $CountriesNum = 0;
        $Countries = cnGetCountries(array('raw data'=>true), $CountriesNum );
        foreach ($Countries as $_Country){

                $Options[] = array("title"=>$_Country['country_name'], "value"=>$_Country['countryID']);
        }
        return '<nobr>'.setting_SELECT_BOX($Options, $_SettingID).($_ShowButton?'&nbsp;&nbsp;<input type="button" name="save55" onclick="document.getElementById(\'save\').name=\'save\';document.getElementById(\'formmodule\').submit(); return false" value=" '.SELECT_BUTTON.' "  style="font-size: 11px; font-family: Tahoma, Arial; border: 1px solid #80A2D9; background-color: #E1ECFD;" />':'').'</nobr>';
}

function setting_ZONE_SELECT($_CountryID, $_Params ,$_SettingID = null){

        $Mode = '';
        if(!isset($_SettingID)){

                $_SettingID = $_Params;
                $Mode = 'simple';
        }elseif(isset($_Params['mode'])) {

                $Mode = $_Params['mode'];
        }
        $Zones = znGetZones($_CountryID);
        $Options = array(
                array("title"=>'-', "value"=>0)
                );
        switch ($Mode){
                default:
                case 'simple':
                        break;
                case 'notdef':
                        if(!count($Zones))return STR_ZONES_NOTDEFINED;
                        break;
        }
        foreach ($Zones as $_Zone){

                $Options[] = array("title"=>$_Zone['zone_name'], "value"=>$_Zone['zoneID']);
        }
        return setting_SELECT_BOX($Options, $_SettingID);
}

function setting_RADIOGROUP($_Options, $_SettingID){

        if(!is_array($_Options)){

                $_Options = explode(',',$_Options);
                $TC = count($_Options)-1;
                for(;$TC>=0;$TC--){

                        $_Options[$TC] = explode(':', $_Options[$TC]);
                        $_Options[$TC]['title'] = $_Options[$TC][0];
                        if(!isset($_Options[$TC][1])){
                                $_Options[$TC]['value'] = '';
                        }else{
                                $_Options[$TC]['value'] = $_Options[$TC][1];
                        }
                }
        }
        $sql = "select settings_constant_name
                FROM ".SETTINGS_TABLE."
                WHERE settingsID=".(int)$_SettingID;
        $row = db_fetch_row( db_query($sql) );
        $settings_constant_name = $row["settings_constant_name"];

        if ( isset($_POST["save"]) )
                _setSettingOptionValue( $settings_constant_name,         $_POST["setting_".$settings_constant_name] );

        $html = '';
        $TC = 0;
        $SettingConstantValue = _getSettingOptionValue($settings_constant_name);
        foreach ($_Options as $_Option){

                $html .= '<input class="inlradio" type="radio" name="setting_'.$settings_constant_name.'" value="'.$_Option['value'].'"'.($SettingConstantValue==$_Option['value']?' checked="checked"':'').' id="id_'.$settings_constant_name.$TC.'" />&nbsp;<label for="id_'.$settings_constant_name.$TC.'">'.$_Option['title'].'</label><br />';
                $TC++;
        }
        return $html;
}

function setting_SINGLE_FILE($_Path, $_SettingID){

        $Error = 0;
        $ConstantName = settingGetConstNameByID($_SettingID);
        if(isset($_POST['save']) && isset($_FILES['setting_'.$ConstantName])){

                if($_FILES['setting_'.$ConstantName]['name']){
                        if(@copy($_FILES['setting_'.$ConstantName]['tmp_name'], $_Path.'/'.$_FILES['setting_'.$ConstantName]['name'])){
                                _setSettingOptionValue($ConstantName, $_FILES['setting_'.$ConstantName]['name']);
                        }else{
                                $Error = 1;
                        }
                }
        }

        $ConstantValue = _getSettingOptionValue($ConstantName);
        return ($Error?'<div>'.ERROR_FAILED_TO_UPLOAD_FILE.'</div>':'').'<input type="file" name="setting_'.$ConstantName.'" /><br />'.($ConstantValue?$ConstantValue:'&nbsp;');
}
?>