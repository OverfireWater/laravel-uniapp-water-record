<?php

namespace App\Http\Controllers\FrontServer;

use App\Models\parent_type;
use App\Models\w_type;
use App\Models\w_user;
use App\TraitHelper\CodeMsgDataTrait;
use App\TraitHelper\CommonORMQueryTrait;
use App\Utils\RedisUtil;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class UserTypeController extends Controller
{
    use CodeMsgDataTrait, CommonORMQueryTrait;

    private const USER_TYPE_KEY = 'userType';

    // 获取消费类型
    public function getUserType(Request $request)
    {
        $id = getTokenId($request);
        $redis_key = self::USER_TYPE_KEY . $id;
//        RedisUtil::getIns($redis_key)->setRedis(function () use ($id) {
//            // 消费
//            $expend_user_type = w_user::find($id)->w_type->where('isExpend', '0')->values();
//            // 收入
//            $income_user_type = w_user::find($id)->w_type->where('isExpend', '1')->values();
//            // 消费
//            $expend_all_type = w_type::whereNull('uid')->where('isExpend', '0')->get();
//            // 收入
//            $income_user_all_type = w_type::whereNull('uid')->where('isExpend', '1')->get();
//
//            $array = array('expend' => array('commonType' => $expend_all_type, 'userType' => $expend_user_type),
//                'income' => array('commonType' => $income_user_all_type, 'userType' => $income_user_type));
//            return json_encode($array, true);
//        });
//        $redis_data = RedisUtil::getIns($redis_key)->getRedis();
        // 消费
        $expend_user_type = w_user::find($id)->w_type->where('isExpend', '0')->values();
        // 收入
        $income_user_type = w_user::find($id)->w_type->where('isExpend', '1')->values();
        // 消费
        $expend_all_type = w_type::whereNull('uid')->where('isExpend', '0')->get();
        // 收入
        $income_user_all_type = w_type::whereNull('uid')->where('isExpend', '1')->get();

        $array = array('expend' => array('commonType' => $expend_all_type, 'userType' => $expend_user_type),
            'income' => array('commonType' => $income_user_all_type, 'userType' => $income_user_type));
        $this->msg = 'ok';
        $this->data = $array;
        return $this->sendJsonData();
    }

    // 获取消费类型的支出或收入的展示数居
    public function getConsumeTypeData(Request $request)
    {
        $userId = getTokenId($request);
        $month = date('Y-m');
        $user_price_data = w_user::with('w_user_price')->find($userId)->w_user_price()
            ->where('updated_at', 'like', "%$month%")->orderByDesc('updated_at')->with('w_type');
        if (!$user_price_data->count()) {
            $this->code = 404;
            $this->msg = '本月没有数据';
        } else {
            // 创建一个新的数组用于存放合并后的数据
            $mergedExpendData = [];
            $mergedIncomeData = [];
            foreach ($user_price_data->get() as $item) {
                $typeId = $item['typeId'];
                $isExpend = $item['isExpend'];
                $price = (float)$item['price'];
                unset($item['id'], $item['remark']);
                if ($isExpend === 0) {
                    $targetArray = &$mergedExpendData;
                } else {
                    $targetArray = &$mergedIncomeData;
                }
                if (isset($targetArray[$typeId])) {
                    $targetArray[$typeId]['price'] += $price;
                } else {
                    $targetArray[$typeId] = $item;
                }
            }
            $ExpendData = array_values($mergedExpendData);
            $IncomeData = array_values($mergedIncomeData);
            $ExpendData = $this->BubbleSort($ExpendData, 'price');
            $IncomeData = $this->BubbleSort($IncomeData, 'price');
            $result = [
                'expendData' => $ExpendData,
                'incomeData' => $IncomeData
            ];
            $this->msg = 'ok';
            $this->data = $result;
        }

        return $this->sendJsonData();
    }

    // 获取父类型下的子类型
    public function getParentTypeAndChildrenType(Request $request)
    {
        $userId = getTokenId($request);
        $database_userInfo = $this->getUserInfo($request);
        if ($database_userInfo->role === 0) {
            $data = parent_type::with('w_type')->get();
        } else {
            $data = parent_type::with(['w_type' => function ($query) use ($userId) {
                $query->whereNull('uid')->orWhere('uid', $userId);
            }])->get();
        }
        if (!$data->count()) {
            $this->code = 404;
            $this->msg = '没有查询到信息';
        } else {
            $ExpendParentType = [];
            $IncomeParentType = [];
            foreach ($data as $k => $v) {
                if ($v['isExpend']) {
                    $IncomeParentType[$k] = $v;
                } else {
                    $ExpendParentType[$k] = $v;
                }
            }
            $array = [
                'ExpendType' => array_values($ExpendParentType),
                'IncomeType' => array_values($IncomeParentType),
            ];
            $this->msg = 'ok';
            $this->data = $array;
        }
        return $this->sendJsonData();
    }

    // 添加用户子类型
    public function addConsumeUserType(Request $request)
    {
        $userId = getTokenId($request);
//        $redisKey = self::USER_TYPE_KEY . $userId;
        $isExpend = $request->input('isExpend');
        $parentTypeId = $request->input('parentTypeId');
        $type_name = $request->input('type_name');
        $t_imgUrl = $request->input('t_imgUrl');
        $isDelete = 1;
        if ($t_imgUrl) {
            $new_path = preg_replace('/(\/temp\/)/', '/', $t_imgUrl);
            Storage::move($t_imgUrl, $new_path);
            $new_path = config('app.url') . $new_path;
        }
        $database_w_user = $this->getUserInfo($request);
        if (!$database_w_user->role) {
            $userId = null;
            $isDelete = 0;
        }
        try {
            $database_w_type = new w_type();
            $database_w_type->uid = $userId;
            $database_w_type->parent_type_id = $parentTypeId;
            $database_w_type->t_name = $type_name;
            $database_w_type->isExpend = $isExpend;
            $database_w_type->isDelete = $isDelete;
            if ($t_imgUrl){
                $database_w_type->t_imgUrl = $new_path;
            }
            $database_w_type->save();
            $this->msg = 'ok';
//            RedisUtil::getIns($redisKey)->destoryRedis();
        } catch (\PDOException $exception) {
            $this->code = 403;
            $this->msg = $exception->getMessage();
        }
        return $this->sendJsonData();
    }

    // 修改用户类型
    public function updateConsumeUserType(Request $request)
    {
        $userId = getTokenId($request);
//        $redisKey = self::USER_TYPE_KEY . $userId;
        $typeId = $request->input('typeId');
        $isExpend = $request->input('isExpend');
        $parentTypeId = $request->input('parentTypeId');
        $type_name = $request->input('type_name');
        $t_imgUrl = $request->input('t_imgUrl');
        try {
            if (preg_match('/^(http:\/\/|https:\/\/)/', $t_imgUrl)) {
                $new_path = $t_imgUrl;
            } else {
                $new_path = preg_replace('/(\/temp\/)/', '/', $t_imgUrl);
                Storage::move($t_imgUrl, $new_path);
                $new_path = config('app.url') . $new_path;
            }
            $database_userInfo = $this->getUserInfo($request);
            if ($database_userInfo->role === 0) {
                $database_w_type = w_type::find($typeId);
            } else {
                $database_w_type = w_type::where('uid', $userId)->find($typeId);
                if (!$database_w_type) {
                    $this->code = 404;
                    $this->msg = '错误，没有该用户的类型';
                    return $this->sendJsonData();
                }
            }
            $database_w_type->parent_type_id = $parentTypeId;
            $database_w_type->t_name = $type_name;
            $database_w_type->isExpend = $isExpend;
            if ($t_imgUrl) {
                $database_w_type->t_imgUrl = $new_path;
            }
            $database_w_type->save();
            $this->msg = 'ok';
//            RedisUtil::getIns($redisKey)->destoryRedis();
        } catch (\PDOException $exception) {
            $this->code = 403;
            $this->msg = $exception->getMessage();
        }
        return $this->sendJsonData();
    }

    //删除类型
    public function deleteUserConsumeType(Request $request, $typeId)
    {
        $userId = getTokenId($request);
        $type_data_count = w_type::with('w_user_price')->find($typeId)->w_user_price()->count();
        if ($type_data_count) {
            $this->code = 403;
            $this->msg = '删除失败，当前类型下有消费记录';
            return $this->sendJsonData();
        }
        $userInfo_role = $this->getUserInfo($request)->role;
        if ($userInfo_role === 0) {
            $type_data = w_type::find($typeId);
        } else {
            $type_data = w_type::where('uid', $userId)->find($typeId);
        }
        if ($type_data) {
            $res = $type_data->delete();
            if ($res) {
                $this->msg = 'ok';
            } else {
                $this->code = '403';
                $this->msg = "删除失败，$res";
            }
//            if ($res) {
//                $key = self::USER_TYPE_KEY . $userId;
//                RedisUtil::getIns($key)->destoryRedis();
//            }
        } else {
            $this->code = 404;
            $this->msg = '错误，没有该用户的类型';
        }
        return $this->sendJsonData();
    }

    // 冒泡排序
    private function BubbleSort($array, $attribute)
    {
        $n = count($array);
        for ($i = 0; $i < $n - 1; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                if ($array[$i][$attribute] < $array[$j][$attribute]) {
                    $data = $array[$i];
                    $array[$i] = $array[$j];
                    $array[$j] = $data;
                }
            }
        }
        return $array;
    }
}
