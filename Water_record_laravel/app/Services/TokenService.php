<?php

namespace App\Services;

use App\TraitHelper\CodeMsgDataTrait;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class TokenService
{
    use CodeMsgDataTrait;

    private string $key = 'yao1314.';

    /**
     * @param int $userId 用户id
     * */
    public function setToken(int $userId): string
    {
        $payload = array(
            'iss' => 'kzy',
            'aud' => 'water',
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 60 * 60 * 24 * 7,
            'user_id' => Crypt::encryptString($userId)
        );
        return JWT::encode($payload, $this->key, 'HS512');
    }

    /**
     * @param string $token 需要破解的token
     * @return array 返回id
     * */
    public function getToken(string $token): array
    {
        $key = 'yao1314.';
        try {
            $de_token = JWT::decode($token, new Key($key, 'HS512'));
            try {
                $user_id = Crypt::decryptString($de_token->user_id);
                $this->data = $user_id;
                $this->msg = 'ok';
            } catch (DecryptException $e) {
                $this->code = 1000;
                $this->msg = $e->getMessage();
            }
        } catch (SignatureInvalidException $e) {
            $this->code = 1000;
            $this->msg = 'token错误';
        } catch (ExpiredException $e) {
            $this->code = 1001;
            $this->msg = 'token过期了';
        } catch (\Exception $e) {
            $this->code = 1002;
            $this->msg = 'token错误';
        }
        return array('code' => $this->code, 'msg' => $this->msg, 'data' => $this->data);
    }
}
