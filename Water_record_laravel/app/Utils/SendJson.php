<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Response;
class SendJson
{
    private static SendJson $ins;

    public function __construct(
        private int   $code = 200,
        private string $msg = '',
        private mixed $data = null,
    ){}
    /**
     * @param int $code 状态码
     * @param string $msg 信息
     * @param mixed $data 数据
     * */
    public static function getIns(int $code,string $msg,mixed $data=null): SendJson
    {
        if (!isset(self::$ins)){
            self::$ins = new self($code,$msg,$data);
        }
        return self::$ins;
    }
    public function __invoke()
    {
        $array = array("code"=>$this->code, "msg"=>$this->msg, "data"=>$this->data);
        return response($array)->header('Content-Type','application/json;charset=utf-8');
    }
}
