<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/7
 * Time: 10:32
 */

namespace app\index\controller;


use app\lib\exception\MissException;

class PublicController
{
    public function miss()
    {
        throw new MissException();
    }
}