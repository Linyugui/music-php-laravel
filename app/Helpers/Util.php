<?php

namespace App\Helpers;
use Exception;

use App\Helpers\ValidationException as VE;

class Util
{
    public static function getValidatorErrorMessage($validator, $firstMessageOnly = false)
    {
        $messages = $validator->errors();
        $msgArr = $messages->all();
        $arr = [];
        foreach ($msgArr as $k => $v) {
            $arr[] = $v;
        }
        $ret = implode(",", $arr);

        return $ret;
    }
    /**
     * @param $obj array 待验证的参数数组
     * @param $rules array 参数验证规则
     * @param array $errMsg 参数验证不通过时的自定义提示
     * @throws ValidationException
     */
    public static function checkInputData($obj, $rules, $errMsg = [])
    {
        $validator = app('validator')->make($obj, $rules, $errMsg);
        if ($validator->fails()) throw new VE(self::getValidatorErrorMessage($validator));
    }

    //  从Exception的 $e 对象获取异常内容
    public static function getExceptionMessage(Exception $e)
    {
        $msg = $e->getMessage() . "\r\n File: " . $e->getFile() . "\r\n Line: " . $e->getLine() . "\r\n Trace :" . $e->getTraceAsString();
        return $msg;
    }

    public static function ApiResponse($data = '', $type = 'true', $count = 0, $msg = '')
    {
        header('Content-Type: application/json');
        echo json_encode(['data' => $data, 'result' => $type, 'count' => $count, 'msg' => $msg]);
        exit;
    }
}