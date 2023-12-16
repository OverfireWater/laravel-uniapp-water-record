<?php

namespace App\TraitHelper;

use App\Utils\SendJson;
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

    public function sendJsonData(){
        return SendJson::getIns($this->code, $this->msg, $this->data)();
    }
    public function __clone(): void
    {
    }
}
