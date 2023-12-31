<?php

namespace App\TraitHelper;

use Symfony\Component\HttpFoundation\Response;

trait CodeMsgDataTrait
{
    /**
     * @param int $code 状态码
     * @param string $msg 信息
     * @param mixed $data 数据
     * */
    public function __construct(
        protected int $code = 200,
        protected string $msg = '',
        protected mixed $data = null
    )
    {}

    public function sendJsonData():Response
    {
        $array = array("code"=>$this->code, "msg"=>$this->msg, "data"=>$this->data);
        return response($array)->header('Content-Type','application/json;charset=utf-8');
    }
    public function __clone(): void
    {
    }
}
