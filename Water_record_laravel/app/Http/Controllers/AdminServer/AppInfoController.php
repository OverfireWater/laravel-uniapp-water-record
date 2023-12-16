<?php

namespace App\Http\Controllers\AdminServer;

use App\Models\app_update_info;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\TraitHelper\CodeMsgDataTrait;
use Illuminate\Support\Facades\Storage;

class AppInfoController extends Controller
{
    use CodeMsgDataTrait;

    // 获取app的所有信息列表
    public function getAppUpdateInfo(Request $request, $page, $limit = 10)
    {
        $columns = ['*'];
        $pageName = 'page';
        $paginateApp = app_update_info::orderByDesc('id')->paginate($limit, $columns, $pageName, $page);
        $this->msg = 'ok';
        $array = array('total' => $paginateApp->total(), 'currentPage' => $paginateApp->currentPage(),
            'limit' => $paginateApp->perPage(), 'lastPage' => $paginateApp->lastPage(), 'data' => $paginateApp->items());
        $this->data = $array;
        return $this->sendJsonData();
    }

    // 通过id获取app的信息
    public function getAppInfoById(Request $request, $id)
    {
        $appInfo = app_update_info::find($id);
        if ($appInfo) {
            $this->msg = 'ok';
            $this->data = $appInfo;
        } else {
            $this->msg = '获取错误';
            $this->code = 404;
        }
        return $this->sendJsonData();
    }

    // 添加app信息 | 修改app信息
    public function addAndUpdateAppInfo(Request $request)
    {
        // 版本
        $version = $request->input('version');
        // 更新链接
        $update_url = $request->input('update_url');
        // 静默更新
        $silent = $request->input('silent') ? 1 : 0;
        // 强制更新
        $force = $request->input('force') ? 1 : 0;
        // 网络检查
        $net_check = $request->input('net_check') ? 1 : 0;
        // 强制更新
        $issue = $request->input('issue') ? 1 : 0;
        // 更新内容
        $note = $request->input('note');
        // 判断是否为修改链接
        if (preg_match('/^(http:\/\/|https:\/\/)/', $update_url)) {
            $new_path = $update_url;
        } else {
            $new_path = preg_replace('/^(app\/temp)/', 'app', $update_url);
            Storage::move($update_url, $new_path);
            Storage::delete($update_url);
            $new_path = config('app.url') . $new_path;
            if ($appInfoId = $request->input('id')){
                $appInfo_data = app_update_info::find($appInfoId);
                $original_url = $appInfo_data->update_url;
                $original_file_name = substr(strrchr($original_url, "/"), 1);
                Storage::delete("app/$original_file_name");
            }
        }
        if ($appId = $request->input('id')) {
            $appInfo = app_update_info::find($appId);
        } else {
            $appInfo = new app_update_info();
        }
        try {
            $appInfo->version = $version;
            $appInfo->update_url = $new_path;
            $appInfo->silent = $silent;
            $appInfo->force = $force;
            $appInfo->net_check = $net_check;
            $appInfo->issue = $issue;
            $appInfo->note = $note;
            $appInfo->save();
            $this->msg = 'ok';
        } catch (\PDOException $exception) {
            $this->code = 403;
            $this->msg = $exception->getMessage();
        }
        return $this->sendJsonData();
    }

    // 修改app的状态
    public function updateAppStatus(Request $request, $id, $flag)
    {
        $flag = $flag === 'true' ? 1 : 0;
        // 发布
        if ($flag) {
            $appCount = app_update_info::where('issue', 1)->count();
            if ($appCount) {
                $this->code = 501;
                $this->msg = '当前有正在发布的app更新，暂不能更改';
                return $this->sendJsonData();
            }
        }
        try {
            $app = app_update_info::find($id);
            $app->issue = $flag;
            $app->save();
            $this->msg = 'ok';
        } catch (\PDOException $exception) {
            $this->code = 403;
            $this->msg = '修改失败';
        }
        return $this->sendJsonData();
    }

    // 获取app最新的信息
    public function getNewAppInfo()
    {
        $newAppInfo = app_update_info::where('issue', 1)->orderByDesc('id')->first();
        if ($newAppInfo) {
            $newAppInfo->makeHidden(['id']);
            $this->msg = 'ok';
            $this->data = $newAppInfo;
        } else {
            $this->code = 404;
            $this->msg = 'app暂无更新';
        }
        return $this->sendJsonData();
    }

    // 删除app信息
    public function deleteAppInfo(Request $request, $appId)
    {
        $app = app_update_info::where('id', $appId)->where('issue', 0);
        if ($app->count()) {
            $appInfo_data = $app->first();
            $original_url = $appInfo_data->update_url;
            $original_file_name = substr(strrchr($original_url, "/"), 1);
            Storage::delete("app/$original_file_name");
            $app->delete();
            $this->msg = 'ok';
        } else {
            $this->code = 403;
            $this->msg = '删除失败,当前app正在发布中';
        }
        return $this->sendJsonData();
    }
}
