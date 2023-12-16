<?php

namespace App\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string setToken(int $userId)
 * @method static string getToken(string $token)
 */
class TokenFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TokenFacade';
    }
}
