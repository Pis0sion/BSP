<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/29
 * Time: 16:41
 */

namespace app\api\controller\v1;


use app\api\repositories\BSPRepositories;
use app\api\request\OrderRequest;

class BSPController
{

    protected $Bsp;

    public function __construct(BSPRepositories $BSPRepositories)
    {
        $this->Bsp = $BSPRepositories ;
    }

    // 下单接口
    public function placeOrder(OrderRequest $orderRequest)
    {
        $params = $orderRequest->getBizVars();

        $cargoes = [
            'name'  =>  'iphone 7 plus',
        ];

        return $this->Bsp->placeOrder($params,$cargoes);
    }

}