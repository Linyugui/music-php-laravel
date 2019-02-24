<?php

namespace posapi\Models;

use function Clue\StreamFilter\fun;

use posapi\Models\QuestionExamModel;
use posapi\Models\QAOptionsModel;
use posapi\Models\QATagModel as Tags;
use posapi\Models\QaQuestionTagRelationModel as Relation;
use DB;


class QuestionModel extends BaseModel
{


    protected $table = 'qa_question';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'name',
        'title',
        'description',
        'FK_staff_id',
        'FK_staff_name',
        'FK_resource_id',
        'FK_question_type_id',
        'FK_question_type_name',
        'FK_question_type_key',
        'FK_region_id',
        'FK_region_name',
        'department_id',
        'department_name',
        'blank_setting'
    ];

    public function detail()
    {
        return $this->hasOne(QuestionExamModel::class, 'FK_question_id', 'id');
    }

    public function options()
    {
        return $this->hasMany(QAOptionsModel::class, 'FK_question_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tags::class, 'qa_question_tag_relation', 'FK_question_id', 'FK_tag_id');
    }

    public function resource(){
        return $this->hasOne(QaResourceModel::class,'uuid','FK_resource_id');
    }

    public function scopeOfWhereInTag($query, $tagArr)
    {
        if ($tagArr) {
            if (is_array($tagArr)) {
                return $query->whereHas('tags', function ($query) use ($tagArr) {
                    $query->whereIn('id', $tagArr);
                });
            } else {
                throw new \Exception('$tagArr must be array');
            }
        }
        return $query;
    }

    public static function getAllQuestionList($reqData, $pages, $orderByField)
    {
        $query = self::query()
            ->OfIdArr($reqData['department_id'], 'department_id')
            ->OfIdArr($reqData['FK_staff_id'], 'FK_staff_id')
            ->OfIdArr($reqData['FK_region_id'], 'FK_region_id')
            ->OfIdArr($reqData['FK_question_type_id'], 'FK_question_type_id')
            ->where('type', 1)
            ->wherehas('detail', function ($query) use ($reqData) {
                $query->OfIdArr($reqData['FK_difficulty_degree_id'], 'FK_difficulty_degree_id')
                    ->OfIdArr($reqData['FK_question_classify_id'], 'FK_question_classify_id');
            });

        $count = $query->count();
        if ($orderByField) {
            $order_by = $orderByField[0];
            $order    = $orderByField[1] == 1 ? 'desc' : 'asc';
            $query->orderby($order_by, $order);
        }
        if ($pages) {
            $query = $query->skip(($pages[0] - 1) * $pages[1])->take($pages[1]);
        }
        $query = $query->with('options', 'detail', 'tags','resource')->get();
        return [$query, $count];
    }

    public static function getQuestionListByTag($department_id, $FK_region_id, $tags, $count)
    {
        $queryArr = [];
        $precise  = [
            'department_id' => $department_id,
            'FK_region_id'  => $FK_region_id
        ];
        foreach ($tags as $tag) {
            array_push($queryArr, $tag['id']);
        }
        $questions = self::query()
            ->ofPrecise($precise)
            ->OfWhereInTag($queryArr)
            ->with('detail')
            ->get()->random($count);
        return $questions;
    }

    public static function addQuestion($reqData)
    {
        $res = DB::transaction(function () use ($reqData) {
            $region        = QATagModel::query()->where('id', $reqData['FK_region_id'])->first();
            $question_type = QATagModel::query()->where('id', $reqData['FK_question_type_id'])->first();
//            $resource = QaResourceModel::query()->where('id',$reqData['FK_resource_id'])->first();
            $question_data = [
                'title'                 => $reqData['title'],
                'description'           => $reqData['description'],
                'FK_staff_id'           => $reqData['FK_staff_id'],
                'FK_staff_name'         => $reqData['FK_staff_name'],
                //'FK_resource_id'        => $reqData['FK_resource_id'],
                'FK_question_type_id'   => $question_type->id,
                'FK_question_type_name' => $question_type->name,
                'FK_question_type_key'  => $question_type->key,
                'FK_region_id'          => $region->id,
                'FK_region_name'        => $region->name,
                'department_id'         => $reqData['department_id'],
                'department_name'       => $reqData['department_name'],
            ];
            $question      = QuestionModel::create($question_data);
            if (!$question) {
                throw new \Exception('新增问题失败');
            }
            $reqData['id'] = $question['id'];

            $assess = QAOptionsModel::addQAOptions($reqData);
            if (!$assess) {
                throw new \Exception('新增问题失败');
            }

            $question_exam = QuestionExamModel::addQuestionExam($reqData, $assess);
            if (!$question_exam) {
                throw new \Exception('新增问题失败');
            }

            $build = [
                'FK_question_id'   => $question['id'],
                'FK_question_name' => $question['title'],
                'tags'             => json_decode($reqData['tags'], true)
            ];
            Relation::rebuildRelation($build);

            return $question_exam;
        });
        return $res;
    }

    public static function editQuestion($reqData)
    {
        $res = DB::transaction(function () use ($reqData) {
            $question = self::query()->where('id', $reqData['id'])->first();
            if (!$question) {
                throw new \Exception('编辑问题失败');
            }
            $question->title       = $reqData['title'];
            $question->description = $reqData['description'];
            $question->save();

            $assess = QAOptionsModel::addQAOptions($reqData);
            if (!$assess) {
                throw new \Exception('新增问题失败');
            }
            $question_exam = QuestionExamModel::editQuestionExam($reqData, $assess);

            $build = [
                'FK_question_id'   => $question['id'],
                'FK_question_name' => $question['title'],
                'tags'             => json_decode($reqData['tags'], true)
            ];
            Relation::rebuildRelation($build);

            return $question_exam;
        });
        return $res;
    }


}
