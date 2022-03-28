<?php

    class OnPayPayMode
    {
        const Free = 'free';
        const Fix = 'fix';
    }


    class OnpayPaymentUrlParameters
    {
        public $pay_mode;
        public $price;
        public $ticker;
        public $pay_for;
        public $md5;
        public $convert;
        public $url_success;
        public $url_success_enc;
        public $url_fail;
        public $url_fail_enc;
        public $user_email;
        public $note;
        public $ln;
        public $f;
        public $one_way;
        public $price_final;
        public $direct_no;
    }

    class OnpayResponseCode
    {
        const OK = '0';
        const ACCEPT = '0';
        const REJECT = '2';
        const INVALID_PARAMS = '3';
        const INVALID_SIGN = '7';
        const TEMPORARY_ERROR = '10';
    }

    define('OnpayResponseCodeOk','0');

    /**
    * Класс объект которого должна вернуть пользовательская функция-обработчик CHEK-запросов сервера OnPay
    */
    class OnpayResponseBase
    {
        /**
        * Код ответа
        * 
        * @var mixed
        */
        public $code;

        /**
        * Коментарий к коду
        * 
        * @var mixed
        */
        public $comment;

        /**
        * Code=0 ОК – означает:
        * - для “check” - “может быть принято”
        * - для “pay” - “уведомление о платеже принято” 
        * 
        * @param mixed $comment
        * @return OnpayCheckResponse
        */
        public static function Accept($comment='OK')
        {
            $resp = new OnpayCheckResponse();
            $resp->code = OnpayResponseCode::OK;
            $resp->comment = $comment;
            return $resp;    
        }

        public static function InvalidParams($comment='INVALID_PARAMS')
        {
            $resp = new OnpayCheckResponse();
            $resp->code = OnpayResponseCode::INVALID_PARAMS;
            $resp->comment = $comment;
            return $resp;    
        }
        
        public static function InvalidSign($comment='INVALID_SIGN')
        {
            $resp = new OnpayCheckResponse();
            $resp->code = OnpayResponseCode::INVALID_SIGN;
            $resp->comment = $comment;
            return $resp;    
        }
        
        public static function TemporaryError($comment='TEMPORARY_ERROR')
        {
            $resp = new OnpayCheckResponse();
            $resp->code = OnpayResponseCode::TEMPORARY_ERROR;
            $resp->comment = $comment;
            return $resp;    
        }
    }

    class OnpayCheckResponse extends OnpayResponseBase
    {
        public static function Reject($comment='Reject')
        {
            $resp = new OnpayCheckResponse();
            $resp->code = OnpayResponseCode::REJECT;
            $resp->comment = $comment;
            return $resp;    
        }
    }

    
    class OnpayPayResponse extends OnpayResponseBase
    {
        /**
        * ID транзакции (заказа) сохранённый в системе Продавца (опциональный, для отслеживания платежей на стороне Продавца).
        * 
        * @var mixed
        */
        public $order_id;
        
        public static function Accept($comment='OK',$order_id=null)
        {
            $resp = new OnpayPayResponse();
            $resp->code = OnpayResponseCode::OK;
            $resp->comment = $comment;
            $resp->order_id = $order_id;
            return $resp;    
        } 
    }

    /**
    * Класс взаимодействия с API Onpay
    *
    * @author likemusic
    * @version 1.0
    * @namespace Onpay
    */

    class Onpay
    {
        const BaseUrl = ' http://secure.onpay.ru/pay/';

        /**
        * Пользовательский ключ API IN.
        *
        * Задается в настройках сайта в личном кабинете на сайте Onpay.
        *
        * @var string
        */
        protected $api_key;

        /**
        * Адрес платежной формы Onpay. По умолчанию это логин пользователя на сайте onpay.
        * @var string
        */
        protected $login;

        /**
        * Необходимо ли использовать md5 подпись при создании ссылки
        * 
        * @var bool
        */
        protected $use_md5;

        /**
        * Значения по умолчанию параметров ссылки на форму оплаты
        * 
        * @var OnpayPaymentUrlParameters
        */
        protected  $defaults;

        /**
        * Спосо отправки запросов  - 'post' или 'get'
        * 
        * @var string
        */
        protected $api_request_type;

        /**
        * Ф-ция вызываемая при CHECK-запросе
        * 
        * @var callable
        */
        protected $OnCheckCallable;
        protected $OnPayCallable;

        /**
        * Известные параметры для ссылки на форму оплаты
        * 
        * @var array
        */
        protected $KnownUrlParameters;

        /**
        * Конструктор
        * 
        * @param string $login - Адрес платежной формы Onpay. По умолчанию это логин пользователя на сайте onpay.
        * @param string $api_key - Пароль для API (secret_key).
        * @param OnpayPaymentUrlParameters|array|null $defaults - параметры по-умолчанию для конструктора ссылок.
        * @param Callable $OnCheck - функция вызываемая при получении CHECK-запроса. 
        * @param Callable $OnPay - функция вызываемая при получении PAY-запроса.
        * @return Onpay
        */
        public function __construct($login, $api_key=null, $use_md5=false, $defaults, $api_request_type, $OnCheck=null,$OnPay=null)
        {
            $this->KnownUrlParameters = (array) new OnpayPaymentUrlParameters();

            $this->login = $login;
            $this->api_key = $api_key;
            $this->use_md5 = $use_md5;

            $this->setDefaults($defaults);

            $this->api_request_type = $api_request_type ? $api_request_type: 'post';
            $this->OnCheckCallable = $OnCheck;
            $this->OnPayCallable = $OnPay;
        }

        protected function setDefaults($defaults)
        {
            $params = $this->getUrlParamsArray($defaults);
            $this->defaults = $params; 
        }

        /**
        * Формирование ссылки на платежную форму
        */
        public function getPaymentUrl($params)
        {
            $params = $this->getUrlParamsArray($params);

            //объединяем со значениями по умолчанию
            $result_params = array_merge($this->defaults, $params);

            if($this->use_md5)
            {
                $md5_sign = $this->getUrlMd5Sign($result_params);
                $result_params['md5']= $md5_sign;
            }

            //формируем строку параметров
            $http_query_str = http_build_query($result_params);
            $Url = self::BaseUrl.$this->login.'?'.$http_query_str;
            return $Url;
        }

        /**
        * Получить подпись для входящих параметров
        * 
        * @param mixed $params
        */
        protected function getUrlMd5Sign($params)
        {
            /*«pay_mode;price;ticker;pay_for;convert; secret_key» Например: fix;100.0;WMR;123;yes;secret_key*/
            $arr=array();
            $arr[]=$params['pay_mode'];

            //Сумма в формате #.# - один знак после точки
            $arr[]=sprintf("%.2f",$params['price']);
            //number_format($params['price'],1,'.','');

            $arr[]=$params['ticker'];
            $arr[]=$params['pay_for'];

            //Даже если вы не указываете параметр “convert” в ссылке, то в строке для формирования подписи md5 необходимо указать “yes” (без кавычек) на месте этого параметра.            
            $arr[]= (isset($params['convert'])) ? $params['convert'] : 'yes';
            $arr[]=$this->api_key;
            $str = join(';',$arr);
            $sign = md5($str);
            return $sign;
        }

        /**
        * Возвращает массив соостоящий из известных непустых параметров.
        * 
        * @param mixed $params
        */
        protected function getUrlParamsArray($params)
        {
            //если объект - приводим к массиву
            if(!is_array($params)) $params = (array) $params;

            //извлекаем все известные парамеры
            $params = array_intersect_key($params,$this->KnownUrlParameters);

            //удаляем пустые элементы
            $params = array_filter($params);
            return $params;            
        }

        /**
        * Обработка check и pay-запроса
        */
        public function processApiRequest($request=null)
        {
            if(!$request) $request = ($this->api_request_type=='post') ? $_POST : $_GET;
            if(!isset($request['type'])) throw new Exception();
            $type = $request['type'];
            if(!in_array($type, array('check','pay'))) throw new Exception();


            if($type=='check')
            {
                //check all reqiures params
                if($this->IsValidCheckRequestMd5Sign($request)) 
                    $ret = call_user_func($this->OnCheckCallable,$request);
                else 
                    $ret = OnpayResponseBase::InvalidSign();

                $responce = $this->GetCheckResponse($request,$ret);
            } 
            else
            {
                if($this->IsValidPayRequestMd5Sign($request)) 
                    $ret = call_user_func($this->OnPayCallable,$request);
                else 
                    $ret = OnpayResponseBase::InvalidSign();
                $responce = $this->GetPayResponse($request,$ret);
            } 
            return $responce;
        }

        protected function IsValidCheckRequestMd5Sign($request)
        {
            $keys = array('type','pay_for','order_amount','order_currency');
            $ret = $this->IsValidRequestMd5Sign($request, $keys);
            return $ret;
        }
        
        protected function IsValidPayRequestMd5Sign($request)
        {
            $keys = array('type','pay_for','onpay_id','order_amount','order_currency');
            $ret = $this->IsValidRequestMd5Sign($request, $keys);
            return $ret;
        }

        protected function IsValidRequestMd5Sign($request,$keys)
        {
            $vals = array();
            foreach($keys as $key)$vals[]=$request[$key];
            $vals[]=$this->api_key;
            $str = join(';',$vals);
            $sign = strtoupper(md5($str));
            if($sign==$request['md5']) return true;
            else return false;
        }

        protected function GetCheckResponse($request,$ret)
        {
            if(!is_array($ret)) $ret = (array) $ret;
            $md5 = $this->getCheckResponceMd5Sign($request,$ret);
            $resp = <<<RESP
<?xml version="1.0" encoding="UTF-8"?>
<result>
    <code>{$ret['code']}</code>
    <pay_for>{$request['pay_for']}</pay_for>
    <comment>{$ret['comment']}</comment>
    <md5>{$md5}</md5>
</result>
RESP;
            return $resp;       
        }

        protected function GetPayResponse($request,$ret)
        {
            if(!is_array($ret)) $ret = (array) $ret;
            $md5 = $this->getPayResponceMd5Sign($request,$ret);
            $order_node = (isset($ret['order_id']) && $ret['order_id']) ? "<order_id>{$ret['order_id']}</order_id>\n    " : '';

            $resp = <<<RESP
<?xml version="1.0" encoding="UTF-8"?>
<result>
    <code>{$ret['code']}</code>
    <comment>{$ret['comment']}</comment>
    <onpay_id>{$request['onpay_id']}</onpay_id>
    <pay_for>{$request['pay_for']}</pay_for>
    {$order_node}<md5>{$md5}</md5>
</result>
RESP;
            return $resp;       
        }

        protected function getCheckResponceMd5Sign($request,$ret)
        {
            // “type;pay_for;order_amount;order_ticker;code;secret_key_api_in” (без кавычек)
            $keys = array('type','pay_for','order_amount','order_currency');
            $vals = array();
            foreach($keys as $key) $vals[] = $request[$key];
            $vals[] = $ret['code'];
            $vals[] = $this->api_key;
            $str = join(';',$vals);
            $sign = strtoupper(md5($str));
            return $sign;
        }

        protected function getPayResponceMd5Sign($request,$ret)
        {
            // “type;pay_for;onpay_id;order_id;order_amount;order_ticker;code;secret_key_api_in” (без кавычек)
            $keys = array('type','pay_for','onpay_id','order_amount','order_ticker');
            $vals = array();
            foreach($keys as $key) $vals[] = $request[$key];
            $vals[] = $ret['code'];
            $vals[] = $this->api_key;
            $str = join(';',$vals);
            $sign = strtoupper(md5($str));
            return $sign;
        }
    }

?>