<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/29
 * Time: 16:53
 */

namespace app\api\repositories;


use app\api\service\BSP;

class BSPRepositories extends BSP
{
    // 顺丰接口配置
    protected $config ;

    /**
     * 加载配置文件
     * BSPRepositories constructor.
     * @param null $params
     */
    public function __construct($params = null)
    {
        $this->config = config("BSP.SF") ;
        if (null != $params) {
            $this->config = array_merge($this->config, $params);
        }
    }

    /**
     * 下单操作
     * @param array $params
     * @param array $cargoes
     * @param array $addedServices
     * @return array|mixed
     */
    public function placeOrder($params = array(), $cargoes = array(), $addedServices = array())
    {
        $order_params = $this->paramsToString($params);
        $cargoes_str  = count($cargoes) > 0 ? $this->paramsToString($cargoes, 'Cargo') : '';
        $addedServices_str = count($addedServices) > 0 ? $this->paramsToString($addedServices, 'AddedService') : '';
        $xml_string   = "<Order$order_params>$cargoes_str$addedServices_str</Order>";
        $data         = $this->postXmlBodyWithVerify($xml_string,'OrderService');
        return  $this->OrderResponse($data,'OrderResponse');
    }

    /**
     * 订单查询
     * @param $orderid
     * @return array
     */
    public function OrderSearch($orderid) {
        $OrderSearch = '<OrderSearch orderid="'.$orderid.'" />';
        $data = $this->postXmlBodyWithVerify($OrderSearch,'OrderSearchService');
        return $this->OrderResponse($data,'OrderResponse');
    }

    public function OrderSearchByMailnoOrOrderid($orderid,$type=1) {
        $RouteService = "<RouteRequest tracking_type='".$type."' method_type='1' tracking_number='".$orderid."'/>";
        $data = $this->postXmlBodyWithVerify($RouteService,'RouteService');
        return $this->OrderResponse($data,'RouteResponse');
    }

    /**
     * 确认订单
     * @param $orderid
     * @param $mailno
     * @param array $options
     * @return array|bool
     */
    public function OrderConfirm($orderid, $mailno, $options = array())
    {
        return $this->OrderConfirmRequest($orderid, $mailno, 1, $options);
    }

    /**
     * 取消订单
     * @param $orderid
     * @param string $mailno
     * @param array $options
     * @return array|bool
     */
    public function OrderCancel($orderid, $mailno = '', $options = array())
    {
        return $this->OrderConfirmRequest($orderid, $mailno, 2, $options);
    }

    public function OrderConfirmRequest($orderid, $mailno, $dealtype, $options = array())
    {
        $params = array();
        $params['dealtype'] = $dealtype;
        $params['orderid']  = $orderid;
        $params['mailno ']  = $mailno;

        $order_params = $this->paramsToString($params);
        $addedServices_str = count($options) > 0 ? $this->paramsToString($options, 'OrderConfirmOption') : '';
        $xml_string   = "<OrderConfirm$order_params>$addedServices_str</OrderConfirm>";
        $data         = $this->postXmlBodyWithVerify($xml_string,'OrderConfirmService');
        return $this->OrderResponse($data,'OrderConfirmResponse');
    }

    public function  OrderResponse($data,$type = '') {
        return $this->getResponse($data,$type);
    }
}