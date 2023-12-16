<?php

namespace App\Http\Controllers\AdminServer;

use App\Models\parent_type;
use App\Models\w_type;
use App\Utils\RedisUtil;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\TraitHelper\CommonORMQueryTrait;
use App\TraitHelper\CodeMsgDataTrait;
use Illuminate\Support\Facades\Storage;

class AdminConsumeTypeController extends Controller
{
    use CodeMsgDataTrait, CommonORMQueryTrait;

    // 分页获取消费类型加搜索父类型id
    public function getPaginateConsumeType(Request $request, $page, $limit, $parentTypeId)
    {
        $columns = ['*'];
        $pageName = 'page';
        $paginateApp = parent_type::find($parentTypeId)->w_type()->paginate($limit, $columns, $pageName, $page);
        $this->msg = 'ok';
        $array = array('total' => $paginateApp->total(), 'currentPage' => $paginateApp->currentPage(),
            'limit' => $paginateApp->perPage(), 'lastPage' => $paginateApp->lastPage(), 'data' => $paginateApp->items());
        $this->data = $array;
        return $this->sendJsonData();
    }

    // 添加父类型
    public function addConsumeParentType(Request $request)
    {
        $isExpend = $request->input('isExpend');
        $type_name = $request->input('type_name');
        $t_imgUrl = $request->input('t_imgUrl');
        $new_path = preg_replace('/(\/temp\/)/', '/', $t_imgUrl);
        Storage::move($t_imgUrl, $new_path);
        $new_path = config('app.url') . $new_path;
        try {
            $database_w_type = new parent_type();
            $database_w_type->t_name = $type_name;
            $database_w_type->isExpend = $isExpend;
            $database_w_type->t_imgUrl = $new_path;
            $database_w_type->save();
            $this->msg = 'ok';
        } catch (\PDOException $exception) {
            $this->code = 403;
            $this->msg = $exception->getMessage();
        }
        return $this->sendJsonData();
    }

    // 修改父类型
    public function updateConsumeParentType(Request $request)
    {
        $parentTypeId = $request->input('parentTypeId');
        $isExpend = $request->input('isExpend');
        $type_name = $request->input('type_name');
        $t_imgUrl = $request->input('t_imgUrl');
        if (preg_match('/^(http:\/\/|https:\/\/)/', $t_imgUrl)) {
            $new_path = $t_imgUrl;
        } else {
            $new_path = preg_replace('/(\/temp\/)/', '/', $t_imgUrl);
            Storage::move($t_imgUrl, $new_path);
            $new_path = config('app.url') . $new_path;
        }
        try {
            $database_parent_type = parent_type::find($parentTypeId);
            $database_parent_type->isExpend = $isExpend;
            $database_parent_type->t_name = $type_name;
            $database_parent_type->t_imgUrl = $new_path;
            $database_parent_type->save();
            $this->msg = 'ok';
        } catch (\PDOException $exception) {
            $this->code = 403;
            $this->msg = $exception->getMessage();
        }
        return $this->sendJsonData();
    }

    // 删除父类
    public function deleteConsumeParentType(Request $request, $parentTypeId)
    {
        $database_parent_type_count = parent_type::with('w_type')->find($parentTypeId)->w_type()->count();
        if ($database_parent_type_count) {
            $this->code = 403;
            $this->msg = '删除失败，当前类型下有子类存在';
            return $this->sendJsonData();
        }
        $res = parent_type::find($parentTypeId);
        $res = $res->delete();
        if ($res){
            $this->msg = 'ok';
        }else{
            $this->code = '403';
            $this->msg = "删除失败，$res";
        }
        return $this->sendJsonData();
    }
}
