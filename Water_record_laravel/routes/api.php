<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/gxySign/sendEmail',[\App\Http\Controllers\GongXueYunSignServer\GongXueYunSignController::class, 'sendMail']);

// 前台路由
Route::group([], function () {

    /*
     * 用户共有的消费类型的路由
     * 前缀为 type
     * */
    Route::controller(\App\Http\Controllers\FrontServer\UserTypeController::class)->middleware('checkToken')->prefix('type')->group(function () {
        // 获取用户的类型
        Route::get('/getUserConsumeType', 'getUserType');
        // 获取展示用户类型消费数据的路由
        Route::get('/getUserConsumeTypeShowData', 'getConsumeTypeData');
        // 获取用户的父类型和子类型
        Route::get('/getUserRelatedToType', 'getParentTypeAndChildrenType');
        // 添加用户类型
        Route::post('/addUserType', 'addConsumeUserType');
        // 修改用户类型
        Route::put('/updateUserType', 'updateConsumeUserType');
        // 删除用户所属的类型
        Route::delete('/deleteUserType/{typeId}', 'deleteUserConsumeType');
    });

    /*
     * 控制器组，包含了用户登陆的一些操作
     * 前缀为 user
     * */
    Route::controller(\App\Http\Controllers\FrontServer\UserLoginController::class)->prefix('user')->group(function () {
        //账号密码登陆
        Route::post('/login', 'login');
        // 发送验证码
        Route::get('/send_mail/{email}', 'sendMail');
        //验证码登陆
        Route::post('/captcha_login', 'captchaLogin');
        // 获取用户信息
        Route::get('/getUserInfo', 'getUserInfo')->middleware('checkToken');
        //退出登陆
        Route::delete('/logout', 'logout');
    });

    /*
     * 控制器组， 消费的操作路由
     * 前缀为 consume
     * */
    Route::controller(\App\Http\Controllers\FrontServer\UserConsumePriceController::class)->middleware('checkToken')->prefix('consume')->group(function () {
        // 添加用户消费
        Route::post('/add', 'addConsume');
        // 获取用户今日消费
        Route::get('/getTodayPrice', 'getTodayPrice');
        // 获取用户所有的消费记录
        Route::get('/getUserAllConsume', 'getUserAllConsume');
        // 获取某个用户的某个记录
        Route::get('/getUserConsume/{consumeId}', 'getUserConsumeById');
        // 修改某个用户的某个记录
        Route::put('/update', 'updateUserConsume');
        // 删除用户的某个记录
        Route::delete('/delete/{consumeId}', 'deleteConsume');
    });
});


// 后台路由
Route::group([], function () {
    // 获取admin用户的路由
    Route::controller(\App\Http\Controllers\AdminServer\AdminLoginController::class)->prefix('admin')->group(function () {
        Route::post('/login', 'adminLogin');
    });

// 获取app更新的路由
    Route::controller(\App\Http\Controllers\AdminServer\AppInfoController::class)->middleware('checkToken')->prefix('app')->group(function () {
        // 获取所有app的信息
        Route::get('/getAppInfo/{page}/{limit?}', 'getAppUpdateInfo');
        // 获取指定的app信息
        Route::get('/getAppInfoById/{id}', 'getAppInfoById');
        // 获取app的最新信息
        Route::get('/getNewAppInfo', 'getNewAppInfo')->withoutMiddleware('checkToken');
        // 添加app信息
        Route::post('/addAppInfo', 'addAndUpdateAppInfo');
        // 修改app信息
        Route::put('/updateAppInfo', 'addAndUpdateAppInfo');
        // 修改app状态
        Route::put('/updateStatus/{id}/{flag}', 'updateAppStatus');
        // 删除app信息
        Route::delete('/delete/{appId}', 'deleteAppInfo');
    });

    // 后台获取的分页消费类型的路由
    Route::controller(\App\Http\Controllers\AdminServer\AdminConsumeTypeController::class)->middleware('checkToken')->prefix('admin/type')->group(function () {
       Route::get('/getConsumeType/{page}/{limit}/{parentTypeId}', 'getPaginateConsumeType');
       Route::post('/addParentType', 'addConsumeParentType');
       Route::put('/updateParentType', 'updateConsumeParentType');
       Route::delete('/deleteParentType/{parentTypeId}', 'deleteConsumeParentType');
    });

// 文件上传的路由组
    Route::controller(\App\Http\Controllers\AdminServer\UploadController::class)->group(function () {
        Route::post('/app/uploadFile', 'uploadAppFile');
        Route::post('/type/uploadParentTypeFile', 'uploadParentTypeFile');
        Route::post('/type/uploadChildrenTypeFile', 'uploadChildrenTypeFile');
    });
});


Route::get('/test', [\App\Http\Controllers\FrontServer\UserConsumePriceController::class, 'test']);
