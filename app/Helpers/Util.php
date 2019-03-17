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

    /**
     * 判断某个值是否在数组中存在，存在则返回值，否则返回默认值
     * @param array $objData 查询数组
     * @param string $field 查询字段，多维数组用点连接，如 'a'、'a.b'、'a.b.c'
     * @param mixed $result 默认值
     * @return mixed
     */
    public static function issetValue($objData, $field, $result = '')
    {
        $fieldArr = explode('.', $field);
        foreach ($fieldArr as $fld) {
            $objData = isset($objData[$fld]) ? $objData[$fld] : [];
        }
        if ($objData === []) $objData = $result;
        return $objData;
    }

    // 大部分模型都需要实现的名称筛选条件
    public function scopeOfName($query, $name, $nameField = 'name')
    {
        if ($name) {
            return $query->where($nameField, 'like', '%' . $name . '%');
        } else {
            return $query;
        }
    }
}