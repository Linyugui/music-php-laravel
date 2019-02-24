<?php

namespace posapi\Models;


class QATagModel extends BaseModel
{


    protected $table = 'qa_tag';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'key',
        'name',
        'FK_staff_id',
        'FK_staff_name',
        'department_id',
        'department_name',
        'level',
        'parent_id',
        'parent_name',
    ];

    /***
     * 获取树形结构的标签列表
     * @param $reqData
     * @return array
     */
    public static function getQATagTree($reqData){
        $query = self::query()
            ->OfIdArr($reqData['department_id'],'department_id')
            ->OfIdArr(1,'level')
            ->get();
        $count = $query->count();
        $query = $query->each(function ($item){
            $sub = self::query()
                ->OfIdArr($item['id'],'parent_id')
                ->get();
            $item->sub = $sub;
        });
        return [$query,$count];
    }

    public static function getGroupTag($group,$pages){
        $query = self::query()->select('name', 'id')->OfGroupArr($group, 'parent_name');
        $count = $query->count();

        if ($pages) {
            $query = $query->skip(($pages[0] - 1) * $pages[1])->take($pages[1]);
        }
        $query = $query->get();
        return [$query, $count];
    }


}
