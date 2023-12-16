<?php

namespace App\Utils;

use Illuminate\Support\Facades\Cache;

class RedisUtil
{
    private static RedisUtil $ins;
    public function __construct(
        private string $key = ''
    )
    {}
    /**
     * @param string $key 键值

     * */
    public static function getIns(string $key){
        if (!isset(self::$ins)){
            self::$ins = new self($key);
        }
        return self::$ins;
    }
    /**
     * @param mixed $val 闭包函数。在查询不到该键值时，才执行$val闭包函数，需要一个存储的数据
     * */
    public function setRedis(mixed $val)
    {
        Cache::rememberForever($this->key, $val);
    }

    public function getRedis()
    {
        if (Cache::has($this->key)) {
            $data = Cache::get($this->key);
            return json_decode($data, true);
        }
    }

    public function destoryRedis(): bool
    {
        if (Cache::has($this->key)) {
            Cache::forget($this->key);
            return true;
        } else {
            return false;
        }
    }
}
