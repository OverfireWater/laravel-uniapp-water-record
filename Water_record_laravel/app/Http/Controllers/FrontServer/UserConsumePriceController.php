<?php

namespace App\Http\Controllers\FrontServer;

use App\Models\parent_type;
use App\Models\w_type;
use App\Models\w_user;
use App\Models\w_user_price;
use App\TraitHelper\CodeMsgDataTrait;
use App\Utils\RedisUtil;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redis;

class UserConsumePriceController extends Controller
{
    use CodeMsgDataTrait;

    // redis 设置的常量key
    private const CONSUME_PRICE_KEY = 'userConsumePrice:';
    // redis 设置 有序集合的key
    private const SORTED_SET_CONSUME_KEY = 'sortedSet' . self::CONSUME_PRICE_KEY;
    // redis 设置hash的key
    private const HASH_SET_CONSUME_KEY = 'hash' . self::CONSUME_PRICE_KEY;

    //获取某个用户所有的消费
    public function getUserAllConsume(Request $request)
    {
        $userId = getTokenId($request);
        $key = self::SORTED_SET_CONSUME_KEY . $userId;
        $user_price = w_user::find($userId)->w_user_price()->orderBy('updated_at', 'desc');
        $data = $user_price->with('w_type')->get();
        $dataCount = $data->count();
        $RedisCount = Redis::zcard($key);
        if ($dataCount !== $RedisCount) {
            Redis::ZREMRANGEBYRANK($key, 0, -1);
            foreach ($data as $v) {
                $score = $v->updated_at->timestamp;
                $array = array('data' => $v->toJson());
                Redis::zadd($key, $score, self::HASH_SET_CONSUME_KEY . $v->id);
                Redis::hmset(self::HASH_SET_CONSUME_KEY . $v->id, $array);
            }
        }
        $start = 0;
        $result = Redis::zrevrange($key, 0, -1, true);
        $array = array();
        $arrayCount = 0;
        foreach ($result as $k => $v) {
            $data = Redis::hgetall($k);
            $array[$arrayCount] = json_decode($data['data'], true);
            $arrayCount++;
        }
        $this->msg = 'ok';
        $this->data = $array;
        return $this->sendJsonData();
    }

    // 获取今日消费
    public function getTodayPrice(Request $request)
    {
        $userId = getTokenId($request);
        $today = date('Y-m-d');
        // 初始化消费金额
        $today_expend_price = 0.00;
        $today_income_price = 0.00;
        $todayUserPriceExpendData = [];
        $todayData = w_user::find($userId)->w_user_price()->where('updated_at', 'like', "%$today%")->orderBy('updated_at', 'desc');
        if ($todayData->count()) {
            // 今日消费加类型
            $todayUserPriceExpendData = $todayData->with('w_type')->get();
            // 今日消费不加类型
            $today_price = $todayData->get();
            foreach ($today_price as $v) {
                if (!$v->isExpend) {
                    $today_expend_price += (float)$v->price;
                } else {
                    $today_income_price += (float)$v->price;
                }
            }
        }
        // 本月消费
        $month = date('Y-m');
        $thisMonthExpend = 0.00;
        $thisMonthIncome = 0.00;
        $thisMonthData = w_user::with('w_user_price')->find($userId)->w_user_price()->where('updated_at', 'like', "%$month%")->orderBy('updated_at', 'desc');
        if ($thisMonthData->count()) {
            $thisMonthUserPriceData = $thisMonthData->get();
            foreach ($thisMonthUserPriceData as $v) {
                if (!$v->isExpend) {
                    $thisMonthExpend += (float)$v->price;
                } else {
                    $thisMonthIncome += (float)$v->price;
                }
            }
        }
        $array = array('today_expend_price' => number_format($today_expend_price, 2), 'today_income_price' => number_format($today_income_price, 2),
            'month_all_expend_price' => number_format($thisMonthExpend, 2), 'month_all_income_price' => number_format($thisMonthIncome, 2),
            'data' => $todayUserPriceExpendData);
        $this->msg = 'ok';
        $this->data = $array;
        return $this->sendJsonData();
    }


    public function test()
    {
        return 'hello world';
//        $columns = ['*'];
//        $pageName = 'page';
//        $limit = 5;
//        $page = 1;
//        $parentId = 1;
//        $paginateApp = parent_type::find($parentId)->w_type()->paginate($limit, $columns, $pageName, $page);
//        $this->msg = 'ok';
//        $array = array('total' => $paginateApp->total(), 'currentPage' => $paginateApp->currentPage(),
//            'limit' => $paginateApp->perPage(), 'lastPage' => $paginateApp->lastPage(), 'data' => $paginateApp->items());
//        $this->data = $array;
//        return $array;
    }

    // 获取用户的详细消费
    public function getUserConsumeById(Request $request, $consumeId)
    {
        $userId = getTokenId($request);
        $info = w_user_price::where('id', $consumeId)->where('uid', $userId)->with('w_type');
        if ($info->count()) {
            $this->msg = 'ok';
            $this->data = $info->first();
        } else {
            $this->code = 404;
            $this->msg = '没有查询到信息';
        }
        return $this->sendJsonData();
    }

    //添加消费
    public function addConsume(Request $request)
    {
        $userId = getTokenId($request);
        $typeId = $request->input('typeId');
        $price = $request->input('price');
        $remark = $request->input('remark');
        // 是否为消费类型 0 为支出，1为收入
        $isExpend = $request->input('isExpend');
        $datetime = $request->input('datetime');
        try {
            $user_price = new w_user_price();
            $user_price->uid = $userId;
            $user_price->typeId = $typeId;
            $user_price->price = $price;
            $user_price->isExpend = $isExpend;
            $user_price->remark = $remark;
            $user_price->timestamps = false;
            $user_price->created_at = $datetime;
            $user_price->updated_at = $datetime;
            $user_price->save();
            $this->msg = 'ok';
        } catch (\PDOException $e) {
            $this->code = 403;
            $this->msg = '系统错误';
        }
        return $this->sendJsonData();
    }

    // 修改某个用户的某个消费
    public function updateUserConsume(Request $request)
    {
        $userId = getTokenId($request);
        $price_id = $request->input('id');
        $typeId = $request->input('typeId');
        $price = $request->input('price');
        $remark = $request->input('remark');
        // 是否为消费类型 0 为支出，1为收入
        $isExpend = $request->input('isExpend');
        $datetime = $request->input('datetime');
        try {
            $user_price = w_user_price::find($price_id);
            $user_price->typeId = $typeId;
            $user_price->price = $price;
            $user_price->isExpend = $isExpend;
            $user_price->remark = $remark;
            $user_price->timestamps = false;
            $user_price->updated_at = $datetime;
            $user_price->save();
            $this->msg = 'ok';
            $data = w_user_price::with('w_type')->find($price_id)->toJson();
            $key = self::HASH_SET_CONSUME_KEY . $price_id;
            Redis::hset($key, 'data', $data);
        } catch (\Exception $e) {
            $this->code = 403;
            $this->msg = $e->getMessage();
        }
        return $this->sendJsonData();
    }

    // 删除用户的消费
    public function deleteConsume(Request $request, $consumeId)
    {
        $userId = getTokenId($request);
        $user_price = w_user_price::find($consumeId);
        if (!$user_price) {
            $this->code = 404;
            $this->msg = '删除失败，此消费不存在';
        } else {
            $res = $user_price->delete();
            if ($res) {
                $key = self::HASH_SET_CONSUME_KEY . $consumeId;
                Redis::DEL($key);
                $this->msg = 'ok';
            } else {
                $this->code = 403;
                $this->msg = '删除失败';
            }
        }
        return $this->sendJsonData();
    }


}
