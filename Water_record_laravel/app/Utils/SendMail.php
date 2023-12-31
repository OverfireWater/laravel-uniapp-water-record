<?php

namespace App\Utils;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendMail
{

    public function __construct(
        private string $email = '',
    )
    {}

    public function sendToMail()
    {
        $code = $this->random();
        $text = '您好，欢迎登陆零钱漂流记app，验证码为 ' . $code . ' ,如非本人操作，请忽略！';
        try {
            $res = Mail::raw($text, function ($message) {
                $message->To($this->email);
                $message->subject('零钱漂流记验证码');
            });
            return $this->setCaptchaToRedis($code);
        } catch (\Exception $e) {
            return $e->getCode();
        }
    }

    /**
     * 存储验证码到redis
     * @param int $code 验证码
     * @return int number 200:success | 400:error 存储失败
     * */
    private function setCaptchaToRedis(int $code): int
    {
        $arr = array('msg' => 'ok', 'code' => $code, 'datetime' => time());
        //                                                  秒
        Cache::put('login' . $this->email, $arr, 60 * 2);
        if (Cache::has('login' . $this->email)) {
            return 200;
        } else {
            return 400;
        }
    }

    /**
     * 校验验证码是否正确
     * @param int $user_code 用户输入的验证码
     * */
    public function validateCaptcha(int $user_code): int
    {
        $cache_data = Cache::get('login' . $this->email);
        if (!$cache_data) {
            return 404;
        }
        $code = $cache_data['code'];
        if ($code === $user_code){
            Cache::forget('login'.$this->email);
            return 200;
        }else{
            return 402;
        }
    }

    private function random(): int
    {
        return mt_rand(10000, 99999);
    }
}
