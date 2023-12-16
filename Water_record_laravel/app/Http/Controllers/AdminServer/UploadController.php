<?php

namespace App\Http\Controllers\AdminServer;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\TraitHelper\CodeMsgDataTrait;

class UploadController extends Controller
{
    use CodeMsgDataTrait;

    // app 文件上传
    public function uploadAppFile(Request $request)
    {
        $res = $this->uploadFileJudge($request, 'app/temp', ['apk', 'wgt']);
        if ($res) {
            $this->msg = 'ok';
            $this->data = $res;
        } else {
            $this->code = 500;
            $this->msg = '图片格式错误';
        }
        return $this->sendJsonData();
    }

    // 父类型图片上传
    public function uploadParentTypeFile(Request $request)
    {
        $res = $this->uploadFileJudge($request, 'parentType/temp');
        if ($res) {
            $this->msg = 'ok';
            $this->data = $res;
        } else {
            $this->code = 500;
            $this->msg = '图片格式错误';
        }
        return $this->sendJsonData();
    }

    // 子类型图片上传
    public function uploadChildrenTypeFile(Request $request)
    {
        $res = $this->uploadFileJudge($request, 'childrenType/temp');
        if ($res) {
            $this->msg = 'ok';
            $this->data = $res;
        } else {
            $this->code = 500;
            $this->msg = '图片格式错误';
        }
        return $this->sendJsonData();
    }

    /**
     * @param $request
     * @param string $address 添加文件成功后的临时地址
     * @param array $suffixArray 需要检查的文件后缀
     * @return bool|string
     */
    private function uploadFileJudge($request, string $address, array $suffixArray = ['png', 'jpg', 'jpeg', 'gif']): bool|string
    {
        $file = $request->file('file');
        $fileSuffix = $file->getClientOriginalExtension();
        $res = $this->isSomeFileSuffix($fileSuffix, $suffixArray);
        if ($res) {
            return $file->storeAs($address, $file->getClientOriginalName());
        } else {
            return false;
        }
    }

    /**
     * @param string $fileSuffix 文件后缀
     * @param array $suffixArray
     * @return bool
     */
    private function isSomeFileSuffix(string $fileSuffix, array $suffixArray): bool
    {
        return in_array($fileSuffix, $suffixArray, true);

    }
}
