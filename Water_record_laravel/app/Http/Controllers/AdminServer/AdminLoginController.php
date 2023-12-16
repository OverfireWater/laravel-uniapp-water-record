<?php

namespace App\Http\Controllers\AdminServer;

use App\Facade\TokenFacade;
use App\Models\w_user;
use App\TraitHelper\CodeMsgDataTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminLoginController extends Controller
{
    use CodeMsgDataTrait;

    public function adminLogin(Request $request)
    {
        $email = $request->input('account');
        $password = $request->input('password');
        $user = w_user::where(['email' => $email, 'password' => $password, 'role' => 0]);
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
}
