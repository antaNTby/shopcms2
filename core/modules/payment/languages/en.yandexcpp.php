<?php
	define('YANDEXCPP_TTL', 'Yandex.Money (CPP)');
	define('YANDEXCPP_DSCR', 'Integration with russian Yandex.Money payment system (http://money.yandex.ru).<br />Документация по интеграции и по значению настроек модуля: http://money.yandex.ru/doc.xml?id=459801<br />Обязательно посетите данную страницу для ознакомления с настройками, которые необходимо ввести при установке модуля, а также с порядком подключения к системе.');
	
	define('YANDEXCPP_CFG_SHOPID_TTL', 'Shop ID');
	define('YANDEXCPP_CFG_SHOPID_DSCR', 'Идентификатор магазина в ЦПП - уникальное значение, присваивается Магазину платежной ');
	
	define('YANDEXCPP_CFG_BANKID_TTL', 'Bank ID');
	define('YANDEXCPP_CFG_BANKID_DSCR', 'Идентификатор процессингового центра платежной системы');
	
	define('YANDEXCPP_CFG_TARGETBANKID_TTL', 'Target bank ID');
	define('YANDEXCPP_CFG_TARGETBANKID_DSCR', 'Идентификатор процессингового центра платежной системы');
	
	define('YANDEXCPP_CFG_MODE_TTL', 'Режим работы модуля');
	define('YANDEXCPP_CFG_MODE_DSCR', 'Определяет адрес (URL), куда будут отправлены данные о платеже');
	
	define('YANDEXCPP_TXT_TESTMODE', 'Тестовый');
	define('YANDEXCPP_TXT_LIVEMODE', 'Рабочий');
	
	define('YANDEXCPP_CFG_TARGETCURRENCY_TTL', 'Валюта платежей');
	define('YANDEXCPP_CFG_TARGETCURRENCY_DSCR', 'Выберите Рубли для рабочего режима и Деморубли для тестового');

	define('YANDEXCPP_CFG_TRANSCURRENCY_TTL', 'Валюта платежей в Вашем магазине');
	define('YANDEXCPP_CFG_TRANSCURRENCY_DSCR', 'Выберите из списка валют Вашего интернет-магазина валюту, которая соответствует Рублям или Деморублям (валюте системы Яндекс.Деньги). Необходимо для перерасчета стоимости заказа.');

	define('YANDEXCPP_TXT_RUR', 'Рубли');
	define('YANDEXCPP_TXT_DEMORUR', 'Деморубли');
	
	define('YANDEXCPP_TXT_PROCESS', 'Оплатить через Яндекс.Деньги сейчас!');
?>