<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function scopeOfIdArr($query, $idArr, $nameFile = 'id')
    {
        if ($idArr) {
            if (is_array($idArr)) {
                return $query->whereIn($nameFile, $idArr);
            } else {
                return $query->where($nameFile, $idArr);
            }
        }
        return $query;
    }

    /**
     * 日期格式 yyyy-mm-dd
     * @param $query
     * @param $start_time
     * @param $end_time
     * @param string $fieldName
     * @return mixed
     */
    public function scopeOfDateStringBetween($query, $start_time, $end_time, $fieldName = 'created_at')
    {
        if ($start_time) {
            $query = $query->where($fieldName, '>=', $start_time);
        }
        if ($end_time) {
            $query = $query->where($fieldName, '<', $end_time);
        }
        return $query;
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
