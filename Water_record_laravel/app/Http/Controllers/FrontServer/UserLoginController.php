<?php

namespace App\Http\Controllers\FrontServer;

use App\Facade\TokenFacade;
use App\Http\Requests\UserVali;
use App\Models\w_user;
use App\TraitHelper\CodeMsgDataTrait;
use App\Utils\SendJson;
use App\Utils\SendMail;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;


class UserLoginController extends Controller
{
    use CodeMsgDataTrait;

    public function login(UserVali $request)
    {

        $email = $request->input('email');
        $password = $request->input('password');
        $request->validated();
        $user = w_user::where(['email' => $email, 'password' => $password, 'role' => 1]);
        if ($user->count()) {
            $token = TokenFacade::setToken((int)$user->first()->id);
            $this->code = 200;
            $this->msg = 'ok';
            $this->data = $token;
        } else {
            $this->code = 401;
            $this->msg = '账号或密码错误';
        }
        return $this->sendJsonData();
    }

    // 发送邮箱
    public function sendMail(Request $request, $email)
    {
        $Cache_email = 'login' . $email;
        if (Cache::has($Cache_email)) {
            $cache_data = Cache::get($Cache_email);
            $old_time = $cache_data['datetime'];
            $new_time = time();
            $time = $new_time - $old_time;
            // 如果时间小于30秒，则表示验证码频繁
            if ($time <= 30) {
                $this->code = 201;
                $this->msg = '验证码频繁';
                return $this->sendJsonData();
            }
        }
        $sendMailClass = new SendMail($email);
        $res = $sendMailClass->sendToMail();
        if ($res == 200) {
            $this->code = 200;
            $this->msg = '验证码发送成功';
        } else if ($res == 400) {
            $this->code = 550;
            $this->msg = '存储失败';
        } else {
            $this->code = 551;
            $this->msg = '发送失败';
        }
        return $this->sendJsonData();
    }

    // 邮箱登陆
    public function captchaLogin(Request $request)
    {
        $email = $request->input('email');
        $captcha = $request->input('captcha');
        $res = SendMail::getIns($email)->validateCaptcha($captcha);
        switch ($res) {
            case 200:
                $exits_user = w_user::where('email', $email);
                if ($exits_user->count() < 1) {
                    $user = new w_user();
                    $user->email = $email;
                    $user->u_name = $email;
                    if ($user->save()) {
                        $this->code = 200;
                        $this->msg = 'ok';
                    } else {
                        $this->code = 501;
                        $this->msg = '用户保存失败';
                    }
                } else {
                    $this->code = 200;
                    $this->msg = 'ok';
                }
                $user_id = (int)$exits_user->first()->id;
                $token = TokenFacade::setToken($user_id);
                $this->data = $token;
                break;
            case 404:
                $this->code = $res;
                $this->msg = '验证码不存在';
                break;
            case 402:
                $this->code = $res;
                $this->msg = '验证码错误';
                break;
        }
        return $this->sendJsonData();
    }

    // 获取用户信息
    public function getUserInfo(Request $request)
    {
        $id = getTokenId($request);
        $userInfo = w_user::find($id);
        $array = array("name" => $userInfo->u_name, "email" => $userInfo->email, 'avatar' => $userInfo->avatar, 'created_time' => $userInfo->created_at);
        $this->data = $array;
        return $this->sendJsonData();
    }

//    退出登陆
    public function logout(Request $request)
    {
        $this->msg = 'ok';
        return $this->sendJsonData();
    }
}
