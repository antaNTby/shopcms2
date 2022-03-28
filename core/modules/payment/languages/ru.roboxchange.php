<?php
        define('ROBOXCHANGE_TTL', 'Robokassa (ROBOXchange)');
        define('ROBOXCHANGE_DSCR', 'Интеграция с платежной системой Robokassa (www.robokassa.ru). Модуль работает в режиме автоматической оплаты. Этот модуль можно использовать для автоматической продажи цифровых товаров. Настройки:<br>Result Url - http(s)://адрес_магазина/index.php?robokassa=result (POST method)<br>Success Url - http(s)://адрес_магазина/index.php?robokassa=success&transaction_result=success (POST method)<br>Fail Url - http(s)://адрес_магазина/index.php?transaction_result=failure (POST method)');

        define('ROBOXCHANGE_CFG_LANG_TTL', 'Язык интерфейса');
        define('ROBOXCHANGE_CFG_LANG_DSCR', 'Выберите язык интерфейса на сервере ROBOXchange, который увидит покупатель при оплате');

        define('ROBOXCHANGE_TXT_LANGRU', 'Русский');
        define('ROBOXCHANGE_TXT_LANGEN', 'Английский');

        define('ROBOXCHANGE_CFG_MERCHANTLOGIN_TTL', 'Login магазина в обменном пункте');
        define('ROBOXCHANGE_CFG_MERCHANTLOGIN_DSCR', 'Информация о Вашем аккаунте продавца в платежной системе ROBOXchange');

        define('ROBOXCHANGE_CFG_ROBOXCURRENCY_TTL', 'Выберите валюту обменного пункта, в которой будет происходить оплата клиентом');
        define('ROBOXCHANGE_CFG_ROBOXCURRENCY_DSCR', '');

        define('ROBOXCHANGE_CFG_SHOPCURRENCY_TTL', 'Выберите валюту магазина, которой соответствует выбранная Вами валюта обменного пункта');
        define('ROBOXCHANGE_CFG_SHOPCURRENCY_DSCR', 'Выберите из списка валют Вашего интернет-магазина');

        define('ROBOXCHANGE_CFG_MERCHANTPASS1_TTL', 'Пароль №1');
        define('ROBOXCHANGE_CFG_MERCHANTPASS1_DSCR', '');
        define('ROBOXCHANGE_CFG_MERCHANTPASS2_TTL', 'Пароль №2');
        define('ROBOXCHANGE_CFG_MERCHANTPASS2_DSCR', '');
        define('ROBOXCHANGE_STATUS_AFTER_PAY_TTL', 'Статус заказа после оплаты');
        define('ROBOXCHANGE_STATUS_AFTER_PAY_DSCR', 'Укажите, какой статус присваивать заказу после совершения платежа. Рекомендуется установить тот же статус что установлен в настройках магазина в качестве статуса завершенного заказа. Это позволит работать мгновенной доставке цифрового товара.');
        define('ROBOXCHANGE_TXT_NOCURR', 'ОШИБКА: Не удалось получить список валют с сервера ROBOXchange');

        define('ROBOXCHANGE_TXT_PROCESS', 'Оплатить заказ сейчас!');
?>
