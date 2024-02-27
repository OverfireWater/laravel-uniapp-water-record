<?php

namespace App\Http\Controllers\GongXueYunSignServer;

use App\TraitHelper\CodeMsgDataTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;

class GongXueYunSignController extends Controller
{
    use CodeMsgDataTrait;

    public function sendMail(Request $request)
    {
        $email = $request->input('email');
        $msg = $request->input('msg');
        $code = $request->input('code');
        $Cache_email = 'GongXueYunSign' . $email;
        $now_date = date("Y-m-d H:i:s");
        if ($code == 200) {
            $text = "您好，本次工学云打卡 成功, 打卡时间为$now_date";
        } else if ($code == 202) {
            $text = "$msg, 时间为：$now_date";
        } else {
            $text = "您好，本次工学云打卡 失败，失败原因：$msg, 打卡时间为$now_date";
        }
        try {
            $res = Mail::raw($text, function ($message) use ($email) {
                $message->To($email);
                $message->subject('工学云自助打卡');
            });
        } catch (\Exception $e) {
            $err_code = $e->getCode();
            if ($err_code) {
                $this->code = 551;
                $this->msg = "发送失败";
            }
        }
        return $this->sendJsonData();
    }
}
