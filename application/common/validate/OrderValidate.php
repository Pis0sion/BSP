<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/29
 * Time: 17:45
 */

namespace app\common\validate;


class OrderValidate extends BaseValidate
{
    protected $rule = [
        "express_type" => "require",
        "j_province" => "require",
        "j_city" => "require",
        "j_company" => "require",
        "j_contact" => "require",
        "j_tel" => "require",
        "j_address" => "require",
        "d_province" => "require",
        "d_city" => "require",
        "d_county" => "require",
        "d_company" => "require",
        "d_contact" => "require",
        "d_tel" => "require",
        "d_address" => "require",
        "parcel_quantity" => "require",
        "pay_method" => "require",
        "customs_batchs" => "require",
    ];

}