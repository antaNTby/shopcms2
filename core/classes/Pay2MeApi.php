<?php

/**
 * Class Pay2MeApi
 */
class Pay2MeApi
{
    const API_URL = 'https://api.pay2me.world';
    const API_VERSION = 'v3';

    public $apiKey;
    public $secretKey;

    /**
     * Pay2MeApi constructor.
     * @param string $apiKey
     * @param string $secretKey
     */
    public function __construct($apiKey, $secretKey)
    {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
    }

    /**
     * @param string $order_id
     * @param $order_return
     * @param $order_desc
     * @param $order_amount
     * @return mixed
     */
    public function dealCreate($order_id, $order_desc, $order_amount, $order_return) {
        return $this->transport(
            '/api/'. self::API_VERSION .'/deals',
            array(
                "order_id" => $order_id,
                "order_return" => $order_return,
                "order_desc" => $order_desc,
                "order_amount" => $order_amount
            )
        );
    }

    /**
     * @param string $object_id
     * @return mixed
     */
    public function dealStatus($object_id) {
        return $this->transport('/api/'. self::API_VERSION .'/deals/status/'.$object_id, array(),'GET');
    }

    /**
     * @param string $object_id
     * @return mixed
     */
    public function dealComplete($object_id) {
        return $this->transport('/api/'. self::API_VERSION .'/deals/complete/'.$object_id,array(),'PUT');
    }

    /**
     * @param array $object_ids
     * @return mixed
     */
    public function dealsComplete($object_ids=array()) {
        return $this->transport('/api/'. self::API_VERSION .'/deals/complete',array('deals'=>$object_ids),'PUT');
    }

    /**
     * @param string $object_id
     * @return mixed
     */
    public function dealCancel($object_id) {
        return $this->transport('/api/'. self::API_VERSION .'/deals/cancel/'.$object_id,array(),'PUT');
    }

    /**
     * @param string $path
     * @param array $params
     * @param string $method
     * @return mixed
     */
    public function transport($path, $params=array(), $method='POST') {
        $params['signature'] = $this->getSignature($params);
        $data_string = json_encode($params);
        $curl = curl_init( self::API_URL . $path);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'X-API-KEY: '.$this->apiKey,
            'Accept: application/json',
        ));

        curl_setopt($curl, CURLOPT_SSLVERSION, 4);
        //curl_setopt($curl, CURLOPT_SSLVERSION, 6);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $json = curl_exec($curl);
        var_dump($json);

        var_dump(curl_error($curl), curl_errno($curl));

        $result = json_decode($json);

        return $result;
    }

    /**
     * @param array $params
     * @return string
     */
    private function getSignature($params=array()) {
        ksort($params);
        $a = array();
        foreach ($params as $key => $val) {
            $a[]=$val;
        }
        return md5(implode("", $a).$this->secretKey);
    }
}
