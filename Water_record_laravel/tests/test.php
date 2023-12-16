<?php

class test
{
    public static function __construct(
        private int    $code,
        private string $msg,
        private        $data,
    ){}

    public function SengData()
    {
        $array = array(self::code, self::msg, self::data);
        return $array;
    }
}
