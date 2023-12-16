<?php
/**
 * 公共函数
 */

// 获取token的id
function getTokenId($request = null): int
{
    if ($request) {
        return $request->input('tokenId');
    }
}
