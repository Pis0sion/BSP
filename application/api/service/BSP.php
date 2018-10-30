<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/29
 * Time: 16:47
 */

namespace app\api\service;


abstract class BSP
{
    protected $ret = array(
        'head' => "ERR",
        'message' => '系统错误',
        'code' => -1
    );
    /**
     * 顺丰BSP下订单接口（含筛选）
     * @param array $params
     * @param array $cargoes
     * @param array $addedServices
     * @return mixed
     */
    abstract function placeOrder($params = array(), $cargoes = array(), $addedServices = array());
    /**
     * 顺丰BSP查单接口
     * Created by PhpStorm.
     */
    abstract function OrderSearch($orderid);
    /**
     * 确认订单
     * @param $orderid
     * @param $mailno
     * @param array $options
     * @return array|bool
     */
    abstract function OrderConfirm($orderid, $mailno, $options = array());
    /**
     * 取消订单
     * @param $orderid
     * @param string $mailno
     * @param array $options
     * @return array|bool
     */
    abstract function OrderCancel($orderid, $mailno = '', $options = array());
    /**
     * 订单确认与取消发送
     * @param $orderid 客户订单号
     * @param $mailno  运单号
     * @param $dealtype  类型【1：确认；2：取消】
     * @param array $options 其他参数
     * @return array
     */
    abstract function OrderConfirmRequest($orderid, $mailno, $dealtype, $options = array());
    /**
     * 返回结果
     * @param $data
     * @return array
     */
    abstract function OrderResponse($data,$type = '');


    /**
     * 顺丰BSP接口主程序 已经已经集成验证
     * @param $xml
     * @return bool|mixed
     */
    public function postXmlBodyWithVerify($xml,$server){
        $xml       = $this->buildXml($xml,$server);
        $verifyCode= $this->sign($xml, $this->config['checkword']);
        $post_data = "xml=$xml&verifyCode=$verifyCode";
        $response  = $this->postXmlCurl($post_data,$this->getPostUrl());
        return $response;
    }
    /**
     * 拼接XML字符串
     * @param $bodyData
     * @return string
     */
    public function buildXml($bodyData,$server){
        $xml = '<Request service="'.$server.'" lang="zh-CN">' .
            '<Head>'.$this->config['accesscode'].'</Head>' .
            '<Body>' . $bodyData . '</Body>' .
            '</Request>';
        return $xml;
    }
    /**
     * 获取POSTURL地址
     * @return string
     */
    protected function getPostUrl(){
        if($this->config['ssl']){
            return   $this->config['server_ssl'].$this->config['uri'];
        } else {
            return   $this->config['server'].$this->config['uri'];
        }
    }
    /**
     * get request service name
     * 获取请求服务器名称
     * @param null $class
     * @return string
     */
    public function getServiceName($class=null) {
        if (empty($class)) {
            return basename(str_replace('\\', '/', get_called_class()),'.php');
        }
        return basename(str_replace('\\', '/', $class),'.php');;
    }
    /**
     * 计算验证码
     * data 是拼接完整的报文XML
     * check_word 是顺丰给的接入码
     * @param string $data
     * @param string $check_word
     * @return string
     */
    public static function sign($data, $check_word) {
        $string = trim($data).trim($check_word);
        $md5    = md5(mb_convert_encoding($string, 'UTF-8', mb_detect_encoding($string)), true);
        $sign   = base64_encode($md5);
        return $sign;
    }


    /**
     * XML to 数组.
     * @param string $xml XML string
     * @return array|\SimpleXMLElement
     */
    public static function parse($xml)
    {
        $data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        if (is_object($data) && get_class($data) === 'SimpleXMLElement') {
            $data = self::arrarval($data);
        }
        return $data;
    }


    /**
     * XML to 对象
     * @param $xml
     * @return \SimpleXMLElement
     */
    public static function parseRaw($xml)
    {
        $data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        return $data;
    }


    /**
     * 对象 to 数组.
     * @param string $data
     * @return array
     */
    private static function arrarval($data)
    {
        if (is_object($data) && get_class($data) === 'SimpleXMLElement') {
            $data = (array) $data;
        }
        if (is_array($data)) {
            foreach ($data as $index => $value) {
                $data[$index] = self::arrarval($value);
            }
        }
        return $data;
    }


    /**
     * 转换顺丰返回XML
     * @param $data
     * @param $name
     * @return array
     */
    public function getResponse($data, $name) {
        $ret = array();
        $xml = @simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        if ($xml){
            $ret['head'] = (string)$xml->Head;
            if ($xml->Head == 'OK'){
                $ret = array_merge($ret , $this->getData($xml, $name));
            }
            if ($xml->Head == 'ERR'){
                $ret = array_merge($ret , $this->getErrorMessage($xml));
            }
        }
        return $ret;
    }


    /**
     * 获取错误信息
     * @param $xml
     * @return array
     */
    public function getErrorMessage($xml) {
        $ret = array();
        $ret['message'] = (string)$xml->ERROR;
        if (isset($xml->ERROR[0])) {
            foreach ($xml->ERROR[0]->attributes() as $key => $val) {
                $ret[$key] = (string)$val;
            }
        }
        return $ret;
    }

    /**
     * 获取xml字段
     * @param $xml
     * @param $name
     * @return array
     */
    public function getData($xml, $name) {
        $ret = array();
        if (isset($xml->Body->$name)){
            foreach ($xml->Body->$name as $v) {
                foreach ($v->attributes() as $key => $val) {
                    $ret[$key] = (string)$val;
                }
            }
        }
        return $ret;
    }


    /**
     * 转换属性为XML字符串
     * @param array $params
     * @param string $xml_Name
     * @return string
     */
    protected function paramsToString($params = [], $xml_Name = '')
    {
        $string = '';
        $return_string = '';
        if ($xml_Name && is_array($params)) {
            foreach ($params as $key => $value) {
                if ( is_array($value)){
                    $string = $this->paramsToString($value);
                }else{
                    $string .= " $key=\"$value\"";
                }
                $return_string .= "<$xml_Name$string></$xml_Name>";
            }
        } elseif (!$xml_Name && is_array($params)) {
            foreach ($params as $k => $v) {
                $string .= " $k=\"$v\"";
            }
            $return_string = $string;
        }

        return $return_string;
    }


    /**
     * 作用：以post方式提交xml到对应的接口url
     * @param $data
     * @param $url
     * @param int $second
     * @return bool|mixed
     */
    public function postXmlCurl($data,$url,$second=60)
    {
        try{
            header("Content-type: text/html; charset=utf-8");
            $ch = curl_init();//初始化curl
            curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
            curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
            curl_setopt($ch, CURLOPT_TIMEOUT, $second);//超时设置
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $data = curl_exec($ch);//运行curl
            curl_close($ch);
            return $data;
        }catch (\Exception $e) {
            return false;
        }
    }
}