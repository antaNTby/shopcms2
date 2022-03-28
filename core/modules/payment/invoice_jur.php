<?php
/**
 * @connect_module_class_name CInvoiceJur
 *
 */

// Модуль формирования счетов на оплату для юридических лиц

// этот модуль позволяет формировать счет для оплаты.
// в реквизитах счета указыва
// ВАЖНО:
//        1.                в счете никак не фигурирует система налогов, которая определяется в администрировании
//                        магазина в разделе "Настройки" - "Налоги".
//                        Этот модуль позволяет отображать или не отображать ставку НДС, а также определять,
//                        включен налог в стоимость или нет
//                        Ставка НДС фиксирована для этого модуля и никак не связана с налогами, которые Вы
//                        определяете в администрировании (общая система налогообложения).
//        2.                счета выписываются только в рублях

define('CINVOICEJUR_DB_TABLE', DB_PRFX.'_module_payment_invoice_jur');

class CInvoiceJur extends PaymentModule {

        function _initVars(){

                $this->title                 = "Выставление счетов";
                $this->description         = "Модуль формирования счетов на оплату для юридических лиц";
                $this->sort_order         = 3;

                $this->Settings = array(
                                "CONF_PAYMENTMODULE_INVOICE_JUR_CURRENCY",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_NDS",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_NDS_IS_INCLUDED_IN_PRICE",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_EMAIL_HTML_INVOICE",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_COMPANYNAME",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_COMPANYADDRESS",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_COMPANYPHONE",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_BANK_ACCOUNT_NUMBER",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_INN",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_KPP",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_BANKNAME",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_BANK_KOR_NUMBER",
                                "CONF_PAYMENTMODULE_INVOICE_JUR_BIK"
                        );
        }

        function _initSettingFields(){

                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_CURRENCY'] = array(
                        'settings_value'                 => '0',
                        'settings_title'                         => 'Валюта - рубли',
                        'settings_description'         => 'Счета на оплату выписываются в рублях. Выберите из списка валют магазина рубль. При формировании счета будет использоваться значение курса рубля. Если валюта не определена, будет использован курс выбранной пользователем валюты',
                        'settings_html_function'         => 'setting_CURRENCY_SELECT(',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_NDS'] = array(
                        'settings_value'                 => '0',
                        'settings_title'                         => 'Ставка НДС (%)',
                        'settings_description'         => 'Укажите ставку НДС в процентах. Если Вы работаете по упрощенной системе налогообложения, укажите 0',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_NDS_IS_INCLUDED_IN_PRICE'] = array(
                        'settings_value'                 => '1',
                        'settings_title'                         => 'НДС уже включен в стоимость товаров',
                        'settings_description'         => 'Включите эту опцию, если налог уже включен в стоимость товаров в Вашем магазине. Если же НДС не включен в стоимость и должен прибавляться дополнительно, выключите эту опцию',
                        'settings_html_function'         => 'setting_CHECK_BOX(',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_EMAIL_HTML_INVOICE'] = array(
                        'settings_value'                 => '1',
                        'settings_title'                         => 'Отправлять покупателю HTML-счет',
                        'settings_description'         => 'Включите эту опцию, если хотите, чтобы покупателю автоматически отправлялся счет в HTML-формате. Если опция выключена, то покупателю будет отправлена ссылка на счет на сайте магазина',
                        'settings_html_function'         => 'setting_CHECK_BOX(',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_COMPANYNAME'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => 'Название компании',
                        'settings_description'         => 'Укажите название организации, от имени которой выписывается счет',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_COMPANYADDRESS'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => 'Адрес компании',
                        'settings_description'         => 'Укажите адрес организации, от имени которой выписывается счет',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_COMPANYPHONE'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => 'Телефон компании',
                        'settings_description'         => 'Укажите телефон организации',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_BANK_ACCOUNT_NUMBER'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => 'Расчетный счет',
                        'settings_description'         => 'Номер расчетного счета организации',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_INN'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => 'ИНН',
                        'settings_description'         => 'ИНН организации',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_KPP'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => 'КПП',
                        'settings_description'         => '',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_BANKNAME'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => 'Наименование банка',
                        'settings_description'         => '',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_BANK_KOR_NUMBER'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => 'Корреспондентский счет',
                        'settings_description'         => '',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );
                $this->SettingsFields['CONF_PAYMENTMODULE_INVOICE_JUR_BIK'] = array(
                        'settings_value'                 => '',
                        'settings_title'                         => 'БИК',
                        'settings_description'         => '',
                        'settings_html_function'         => 'setting_TEXT_BOX(0,',
                        'sort_order'                         => 1,
                );


                //создать таблицу, в которую будет записывать информацию для счета
                // - сумма к оплате в выбранной валюте

                if(!in_array(strtolower(CINVOICEJUR_DB_TABLE), db_get_all_tables())){

                        $sql = '
                                CREATE TABLE '.CINVOICEJUR_DB_TABLE.'
                                (module_id INT UNSIGNED, orderID INT, company_name varchar(64), company_inn varchar(64), nds_included int default 0, nds_rate float default 0, RUR_rate float default 1)
                        ';
                        db_query($sql);
                }
        }

        function payment_form_html()
        {
                $text = "";

                $text.= "
                        <table>
                        <tr><td>Название организации:</td><td><input type=text name=minvoicejur_company_name value=\"\"></td></tr>
                        <tr><td>ИНН:</td><td><input type=text name=minvoicejur_inn value=\"\"></td></tr>
                        </table>
                ";

                return $text;
        }

        function payment_process($order)
        {
                //проверить правильность ввода

                if (!isset($_POST["minvoicejur_company_name"]) || strlen( trim($_POST["minvoicejur_company_name"]) ) == 0)
                {
                        return "Пожалуйста, введите название организации, на имя которой будет выставляться счет";
                }

                if (!isset($_POST["minvoicejur_inn"]) || strlen( trim($_POST["minvoicejur_inn"]) ) == 0)
                {
                        return "Пожалуйста, введите ИНН организации, на имя которой будет выставляться счет";
                }

                return 1;
        }

        function after_processing_php( $orderID )
        {
                //сохранить сумму счета
                $orderID = (int) $orderID;
                $order = ordGetOrder( $orderID );
                if ($order)
                {
                        $q = db_query("select count(*) from ".CINVOICEJUR_DB_TABLE."  where orderID=".(int)$orderID." AND module_id=".(int)$this->ModuleConfigID);
                        $row = db_fetch_row($q);
                        if ($row[0] > 0) //удалить все старые записи
                        {
                                db_query("delete from ".CINVOICEJUR_DB_TABLE." where orderID=".(int)$orderID." AND module_id=".(int)$this->ModuleConfigID);
                        }

                        $q = db_query("select currency_value from ".CURRENCY_TYPES_TABLE." where CID=".( (int)$this->_getSettingValue('CONF_PAYMENTMODULE_INVOICE_JUR_CURRENCY') ) );
                        $row = db_fetch_row($q);
                        $RUR_rate = $row ? (float)$row[0] : 1;

                        //добавить новую запись
                        $sql = "insert into ".CINVOICEJUR_DB_TABLE." (module_id, orderID, company_name, company_inn, nds_included, nds_rate, RUR_rate) values (".(int)$this->ModuleConfigID.", ".(int)$orderID.", '".xToText(trim($_POST["minvoicejur_company_name"]))."', '".xToText(trim($_POST["minvoicejur_inn"]))."', '".$this->_getSettingValue('CONF_PAYMENTMODULE_INVOICE_JUR_NDS_IS_INCLUDED_IN_PRICE')."', ".(float)$this->_getSettingValue('CONF_PAYMENTMODULE_INVOICE_JUR_NDS').", ".$RUR_rate." )";
                        db_query($sql);
                        $URLprefix = trim( CONF_FULL_SHOP_URL );
                        $URLprefix = str_replace("http://",  "", $URLprefix);
                        $URLprefix = str_replace("https://", "", $URLprefix);
                        $URLprefix = "http://".$URLprefix;
                        if ($URLprefix[ strlen($URLprefix)-1 ] != '/')
                        {
                                $URLprefix .= "/";
                        }

                        //отправить счет покупателю по электронной почте
                        if ($this->_getSettingValue('CONF_PAYMENTMODULE_INVOICE_JUR_EMAIL_HTML_INVOICE') == 1) //html
                        {

                                $mySmarty = new Smarty; //core smarty object
                                $mySmarty->template_dir = "core/modules/tpl/";
                                //define smarty vars
                                $mySmarty->assign( "billing_lastname", $order["billing_lastname"] );
                                $mySmarty->assign( "billing_firstname", $order["billing_firstname"] );
                                $mySmarty->assign( "billing_city", $order["billing_city"] );
                                $mySmarty->assign( "billing_address", $order["billing_address"] );
                                $mySmarty->assign( "orderID", $orderID );
                                $mySmarty->assign( "order_time", $order["order_time_mysql"] );

                                //сумма счета
                                $q = db_query("select company_name, company_inn, nds_included, nds_rate, RUR_rate from ".CINVOICEJUR_DB_TABLE." where orderID=".(int)$orderID." AND module_id=".(int)$this->ModuleConfigID);
                                $row = db_fetch_row($q);
                                if ($row) //сумма найдена в файле с описанием счета
                                {
                                        $mySmarty->assign( "customer_companyname", $row["company_name"] );
                                        $mySmarty->assign( "customer_inn",  $row["company_inn"] );
                                        $nds_rate = (float) $row["nds_rate"];
                                        $RUR_rate = (float) $row["RUR_rate"];
                                        $nds_included = !strcmp((string)$row["nds_included"],"1") ? 1 : 0;
                                }
                                else //информация о счет не найдена
                                {
                                        die ("Счет не найден в базе данных");
                                }

                                //заказанные товары
                                $order_content = ordGetOrderContent( $orderID );
                                $amount = 0;
                                foreach( $order_content as $key => $val)
                                {
                                        $order_content[$key]["Price"] = $this->_my_formatprice ( $order_content[$key]["Price"] * $RUR_rate );
                                        $order_content[$key]["Price_x_Quantity"] = $this->_my_formatprice ( $val["Quantity"] * $val["Price"] * $RUR_rate );
                                        $amount += (double)strtr($order_content[$key]["Price_x_Quantity"], array("," => "", " " => ""));
                                }

                                $shipping_rate = $order["shipping_cost"]*$RUR_rate;

                                $order["discount_value"] = round((double)$order["order_discount"] * $amount)/100;

                                $mySmarty->assign( "order_discount", $order["order_discount"] );
                                $mySmarty->assign( "order_discount_value", $this->_my_formatprice($order["discount_value"]) );

                                $amount += $shipping_rate; //+стоимость доставки

                                $mySmarty->assign( "order_content", $order_content);
                                $mySmarty->assign( "order_content_items_count", count($order_content) + 1 );
                                $mySmarty->assign( "order_subtotal", $this->_my_formatprice($amount) );

                                $amount -= $order["discount_value"];

                                if ($nds_rate <= 0) //показать НДС
                                {
                                        $mySmarty->assign( "order_tax_amount", "нет" );
                                        $mySmarty->assign( "order_tax_amount_string", "нет" );
                                }
                                else
                                {
                                        //налог не расчитывается на стоимость доставки
                                        //если вы хотите, чтобы налог расчитывался и на стоимость доставки замените ниже
                                        // '($amount-$shipping_rate)' на '$amount'

                                        if (!$nds_included) //налог включен
                                        {
                                                $tax_amount = round ( ($amount-$shipping_rate) * $nds_rate ) / 100;

                                                $amount += $tax_amount;
                                        }
                                        else //прибавить налог
                                        {
                                                $tax_amount = round ( 100 * ($amount-$shipping_rate) * $nds_rate / ($nds_rate+100) ) / 100;
                                        }
                                        $mySmarty->assign( "order_tax_amount", $this->_my_formatprice($tax_amount) );
                                        $mySmarty->assign( "order_tax_amount_string", $this->create_string_representation_of_a_number($tax_amount) );

                                }

                                $mySmarty->assign( "order_total", $this->_my_formatprice($amount) );
                                $mySmarty->assign( "order_total_string", $this->create_string_representation_of_a_number($amount) );

                                //доставка
                                if ($shipping_rate > 0)
                                {
                                        $mySmarty->assign( "shipping_type", $order["shipping_type"] );
                                        $mySmarty->assign( "shipping_rate", $this->_my_formatprice($shipping_rate) );
                                }

                                $mySmarty->assign("shopping_cart_url", $URLprefix); //путь к файлу логотипа
                                $mySmarty->assign('InvoiceModule', $this);

                                $invoice = $mySmarty->fetch("invoice_jur.tpl.html");

                                //отправить счет покупателю
                                xMailTxtHTMLDATA($order["customer_email"], "Счет на оплату", $invoice);

                                // xMailTxtHTMLDATA(CONF_GENERAL_EMAIL, "Счет на оплату - заказ #".$orderID, $invoice);

                        }
                        else //ссылка на счет
                        {

                                $invoice_url = $URLprefix . "index.php?do=invoice_jur&moduleID=".(int)$this->ModuleConfigID."&orderID=".(int)$orderID."&order_time=".base64_encode( $order["order_time_mysql"] ) . "&customer_email=" . base64_encode( $order["customer_email"] );

                                xMailTxtHTMLDATA($order["customer_email"], "Счет на оплату", "Здравствуйте!<br><br>Спасибо за Ваш заказ.<br>Счет на оплату Вы можете посмотреть и распечатать по адресу:<br><a href=\"" . $invoice_url . "\">" . $invoice_url . "</a><br><br>С уважением,<br>".CONF_SHOP_NAME);

                        }

                }

                return "";
        }

        function after_processing_html( $orderID )
        {
                //открыть окно со счетом
                $order = ordGetOrder( $orderID );

                if(!$this->ModuleConfigID){

                        $sql = '
                                SELECT module_id FROM '.MODULES_TABLE.' WHERE module_name="'.xEscSQL($this->title).'"
                        ';
                        @list($this->ModuleConfigID) = db_fetch_row(db_query($sql));
                }

                $res = "";

                $res .=
                        "<script>\n".
                        "        open_window('index.php?do=invoice_jur&moduleID=".(int)$this->ModuleConfigID."&orderID=".(int)$orderID."&order_time=".base64_encode( $order["order_time_mysql"] )."&customer_email=".base64_encode( $order["customer_email"] )."',700,600);\n".
                        "</script>\n";

                return $res;
        }

        function uninstall($_ModuleConfigID = 0){

                PaymentModule::uninstall($_ModuleConfigID);

                if(!count(modGetModuleConfigs(get_class($this)))){

                        //удалить таблицу с информацией о счетах
                        db_query("DROP TABLE IF EXISTS ".CINVOICEJUR_DB_TABLE);
                }else {

                        $sql = 'DELETE FROM '.CINVOICEJUR_DB_TABLE.' WHERE module_id='.(int)$this->ModuleConfigID;
                }
        }

        //следующие функции дублируют функции из invoice_jur.php, лежащего в папке core/includes/processor/. Функции одинаковые, было принято решение не выносить их в отдельный файл.

        function _my_formatPrice($price)
        {
                return _formatPrice(roundf($price));
        }

        function number2string($n,$rod) //перевести число $n в строку. Число обязательно должно быть 0 < $n < 1000. $rod указывает на род суффикса (0 - женский, 1 - мужской; например, "рубль" - 1, "тысяча" - 0).
        {
                $a = floor($n / 100);
                $b = floor(($n - $a*100) / 10);
                $c = $n % 10;

                $s = "";
                switch($a)
                {
                        case 1: $s = "сто";
                        break;
                        case 2: $s = "двести";
                        break;
                        case 3: $s = "триста";
                        break;
                        case 4: $s = "четыреста";
                        break;
                        case 5: $s = "пятьсот";
                        break;
                        case 6: $s = "шестьсот";
                        break;
                        case 7: $s = "семьсот";
                        break;
                        case 8: $s = "восемьсот";
                        break;
                        case 9: $s = "девятьсот";
                        break;
                }
                $s .= " ";
                if ($b != 1)
                {
                   switch($b)
                   {
                        case 1: $s .= "десять";
                        break;
                        case 2: $s .= "двадцать";
                        break;
                        case 3: $s .= "тридцать";
                        break;
                        case 4: $s .= "сорок";
                        break;
                        case 5: $s .= "пятьдесят";
                        break;
                        case 6: $s .= "шестьдесят";
                        break;
                        case 7: $s .= "семьдесят";
                        break;
                        case 8: $s .= "восемьдесят";
                        break;
                        case 9: $s .= "девяносто";
                        break;
                   }
                   $s .= " ";
                   switch($c)
                   {
                        case 1: $s .= $rod ? "один" : "одна";
                        break;
                        case 2: $s .= $rod ? "два" : "две";
                        break;
                        case 3: $s .= "три";
                        break;
                        case 4: $s .= "четыре";
                        break;
                        case 5: $s .= "пять";
                        break;
                        case 6: $s .= "шесть";
                        break;
                        case 7: $s .= "семь";
                        break;
                        case 8: $s .= "восемь";
                        break;
                        case 9: $s .= "девять";
                        break;
                   }
                }
                else //...дцать
                {
                   switch($c)
                   {
                        case 0: $s .= "десять";
                        break;
                        case 1: $s .= "одиннадцать";
                        break;
                        case 2: $s .= "двенадцать";
                        break;
                        case 3: $s .= "тринадцать";
                        break;
                        case 4: $s .= "четырнадцать";
                        break;
                        case 5: $s .= "пятьнадцать";
                        break;
                        case 6: $s .= "шестьнадцать";
                        break;
                        case 7: $s .= "семьнадцать";
                        break;
                        case 8: $s .= "восемьнадцать";
                        break;
                        case 9: $s .= "девятьнадцать";
                        break;
                   }
                }
                return $s;
        }

        function create_string_representation_of_a_number( $n )
                // создает строковое представление суммы. Например $n = 123.
                // результат будет "Сто двадцать три рубля 00 копеек"
        {
                //разделить сумма на разряды: единицы, тысячи, миллионы, миллиарды (больше миллиардов не проверять :) )

                $billions = floor($n / 1000000000);
                $millions = floor( ($n-$billions*1000000000) / 1000000);
                $grands = floor( ($n-$billions*1000000000-$millions*1000000) / 1000);
                $roubles = floor( ($n-$billions*1000000000-$millions*1000000-$grands*1000) );//$n % 1000;

                //копейки
                $kop = round ( $n*100 - round( floor($n)*100 ) );
                if ($kop < 10) $kop = "0".(string)$kop;

                $s = "";
                if ($billions > 0)
                {
                        $t = "ов";
                        $temp = $billions % 10;
                        if (floor(($billions % 100)/10) != 1)
                        {
                                if ($temp == 1) $t = "";
                                else if ($temp >=2 && $temp <= 4) $t = "а";
                        }
                        $s .= $this->number2string($billions,1)." миллиард$t ";
                }
                if ($millions > 0)
                {
                        $t = "ов";
                        $temp = $millions % 10;
                        if (floor(($millions % 100)/10) != 1)
                        {
                                if ($temp == 1) $t = "";
                                else if ($temp >=2 && $temp <= 4) $t = "а";
                        }
                        $s .= $this->number2string($millions,1)." миллион$t ";
                }
                if ($grands > 0)
                {
                        $t = "";
                        $temp = $grands % 10;
                        if (floor(($grands % 100)/10) != 1)
                        {
                                if ($temp == 1) $t = "а";
                                else if ($temp >=2 && $temp <= 4) $t = "и";
                        }
                        $s .= $this->number2string($grands,0)." тысяч$t ";
                }
                if ($roubles > 0)
                {
                        $rub = "ей";
                        $temp = $roubles % 10;
                        if (floor(($roubles % 100)/10) != 1)
                        {
                                if ($temp == 1) $rub = "ь";
                                else if ($temp >=2 && $temp <= 4) $rub = "я";
                        }
                        $s .=  $this->number2string($roubles,1)." рубл$rub ";
                }

                {
                        $kp = "ек";
                        $temp = $kop % 10;
                        if (floor(($kop % 100)/10) != 1)
                        {
                                if ($temp == 1) $kp = "йка";
                                else if ($temp >=2 && $temp <= 4) $kp = "йки";
                        }

                        $s .= "$kop копе$kp";
                }

                //теперь сделать первую букву заглавной
                if ($roubles>0 || $grands>0 || $millions>0 || $billions>0)
                {
                        $cnt=0; while($s[$cnt]==" ") $cnt++;
                        $s[$cnt] = chr( ord($s[$cnt])- 32 );
                }

                return $s;
        }
}
?>