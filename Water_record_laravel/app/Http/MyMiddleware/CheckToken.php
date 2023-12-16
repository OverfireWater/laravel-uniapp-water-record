<?php

namespace App\Http\MyMiddleware;

use App\Facade\TokenFacade;
use App\TraitHelper\CodeMsgDataTrait;
use App\Utils\SendJson;
use Closure;
use Illuminate\Http\Request;

class CheckToken
{
    use CodeMsgDataTrait;

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if ($token) {
            $res = TokenFacade::getToken($token);
            switch ($res['code']) {
                case 200:
                    $request->merge(['tokenId' => $res['data']]);
                    return $next($request);
                default:
                    $this->code = $res['code'];
                    $this->msg = $res['msg'];
            }
        } else {
            $this->code = 1000;
            $this->msg = 'token不存在';
        }
        return $this->sendJsonData();
    }
}
