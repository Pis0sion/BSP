<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/29
 * Time: 17:41
 */

namespace app\api\request;


use app\api\utils\tools\Utils;
use app\common\validate\OrderValidate;
use think\facade\Config;
use think\Request;

class OrderRequest extends Request
{
    public function getBizVars()
    {
        $params['custid'] = Config::get("BSP.SF.custid");
        return array_merge($params,$this->getRequestVars());
    }

    protected function getRequestVars()
    {
        (new OrderValidate())->goCheck();
        return $this->only([
            'orderid',
            'express_type', 'j_province', 'j_city', 'j_company', 'j_contact', 'j_tel','j_address',
            'd_province', 'd_city', 'd_county', 'd_company', 'd_contact', 'd_tel', 'd_address',
            'parcel_quantity', 'pay_method', 'customs_batchs',
        ]);
    }
    // TODO： 商品描述
    public function getSubjectInfo()
    {
        return "";
    }
}