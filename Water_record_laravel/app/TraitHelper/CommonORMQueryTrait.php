<?php

namespace App\TraitHelper;
use App\Models\w_user;

trait CommonORMQueryTrait
{
    public function getUserInfo($request)
    {
        $userId = getTokenId($request);
        return w_user::find($userId);
    }
}
