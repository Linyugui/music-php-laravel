<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;

use DB;
use App\Helpers\Util;
use App\Helpers\Urllib;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * 获取小程序用户的openid并保存到数据库中
     */
    public function getLogin(Request $request)
    {
        $rules = [
            'code' => 'string',
        ];
        $objData = $request->only(array_keys($rules));
        try {
            Util::checkInputData($objData, $rules);
            $appid = env('AppID');
            $appsecret = env('AppSecret');
            $code = $objData['code'];
            $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $appid . "&secret=" . $appsecret . "&js_code=" . $code . '&grant_type=authorization_code';
            $data = $this->curl_get_https($url);
            $data = json_decode($data, true);
            if (isset($data['openid'])) {
                $obj = [
                    'openid' => $data['openid']
                ];
                list($res, $type) = UserModel::addUser($obj);
                Util::ApiResponse($res, $type, 0, '获取openid成功');
            } else {
                Util::ApiResponse($data['errcode'], false, 0, '获取openid失败');
            }
        } catch (\Exception $e) {
            Util::ApiResponse($data, false, 0, Util::getExceptionMessage($e));
        }
    }

    public function getUserInfo(Request $request)
    {
        $rules = [
            'openid' => 'string',
        ];
        $objData = $request->only(array_keys($rules));
        try {
            Util::checkInputData($objData, $rules);
            $res = UserModel::query()
                ->where('openid', $objData['openid'])
                ->first();
            if ($res) {
                Util::ApiResponse($res, true, 1, '获取用户信息成功');
            } else {
                Util::ApiResponse($res, false, 0, '该用户不存在');
            }
        } catch (\Exception $e) {
            Util::ApiResponse($res, false, 0, Util::getExceptionMessage($e));
        }
    }

    public function getUpdateUserInfo(Request $request)
    {
        $rules = [
            'id' => 'string',
            'nickName' => 'string',
            'avatarUrl' => 'string',
            'province' => 'string',
            'city' => 'string',
            'country' => 'string',
        ];

        $objData = $request->only(array_keys($rules));
        try {
            Util::checkInputData($objData, $rules);
            $user = UserModel::query()
                ->where('id', $objData['id'])
                ->first();
            $user->nickName = $objData['nickName'];
            $user->avatarUrl = $objData['avatarUrl'];
            $user->province = $objData['province'];
            $user->city = $objData['city'];
            $user->country = $objData['country'];
            $user->save();
            Util::ApiResponse($user, true, 1, '更新用户信息成功');
        } catch (\Exception $e) {
            Util::ApiResponse([], false, 0, Util::getExceptionMessage($e));
        }
    }



    /**
     * 模拟get进行url请求
     * @param string $url
     * @param string $param
     */
    public function curl_get_https($url)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        $tmpInfo = curl_exec($curl);     //返回api的json对象
        //关闭URL请求
        curl_close($curl);
        return $tmpInfo;    //返回json对象
    }
}
