<?php
/**
 * Created by PhpStorm.
 * User: linyugui
 * Date: 18-8-16
 * Time: 下午2:35
 */

namespace posapi\Http\Controllers;


use posapi\Helpers\Err;
use posapi\Helpers\Util;
use Illuminate\Http\Request;
use posapi\Models\QuestionExamModel;
use posapi\Models\QuestionModel;
use Exception;

class QuestionController extends BaseController
{

    public function postAdmGetAllQuestionList(Request $request){
        try{
            $rules=[
                'FK_difficulty_degree_id' => '',
                'FK_question_classify_id' => '',
                'FK_staff_id'             => '',
                'FK_region_id'            => '',
                'FK_question_type_id'     => ''
            ];
            $reqData = $request->only(array_keys($rules));
            $reqData['department_id'] = $this->getMerchantFromRequest()->id;
            $pages = $this->_paginationRule;
            $orderByField = Util::getOrderField($this->request);
            list($res,$count) = QuestionModel::getAllQuestionList($reqData,$pages,$orderByField);
            $this->ApiResponse($res,Err::$SUCCESS,$count,'获取数据成功');

        }catch (Exception $e){
            Util::WriteLog($e);
            $this->ApiResponse([],Err::$ERROR,1,Util::getExceptionMessage($e));
        }
    }

    /***
     * @param Request $request
     * 这个api主要是给管理后台用的，新增题目
     */
    public function postAdmAddQuestion(Request $request){
        try{
            $rules = [
                'title'                   => 'required',
                'description'             => '',
                //'FK_resource_id'          => '',
                'FK_question_type_id'     => 'required',
                'FK_region_id'            => 'required',
                'FK_difficulty_degree_id' => 'required',
                'FK_question_classify_id' => 'required',
                'options'                 => 'required',
                'assess_description'      => '',
                'tags'                    => 'required'
            ];
            $reqData = $request->only(array_keys($rules));
            Util::checkInputData($reqData,$rules);
            $staff = $this->getStaffFromRequest();
            $department = $this->getMerchantFromRequest();
            $reqData['FK_staff_id']=$staff->id;
            $reqData['FK_staff_name']=$staff->name;
            $reqData['department_id']=$department->id;
            $reqData['department_name']=$department->name;
            $res = QuestionModel::addQuestion($reqData);
            $this->ApiResponse($res,Err::$SUCCESS,1,'新增问题成功');
        }catch (Exception $e){
            Util::WriteLog($e);
            $this->ApiResponse([],Err::$ERROR,1,Util::getExceptionMessage($e));
        }
    }

    /***
     * @param Request $request
     * 这个api主要是给管理后台用的，删除题目，使问题的状态变为无效
     */
    public function postAdmDeleteQuestion(Request $request){
        try{
            $rules = [
                'checked_question' => 'required'
            ];
            $reqData = $request->only(array_keys($rules));
            Util::checkInputData($reqData,$rules);
            $question_list = explode(",",$reqData['checked_question']);
            $res = QuestionModel::query()
                ->whereIn('id',$question_list)
                ->update(['type'=>0]);
            if(!$res){
                throw new Exception('有部分问题不存在或已被删除');
            }
            $this->ApiResponse($res,Err::$SUCCESS,1,'删除问题成功');
        }catch (Exception $e){
            Util::WriteLog($e);
            $this->ApiResponse([],Err::$ERROR,1,Util::getExceptionMessage($e));
        }
    }

    public function postAdmEditQuestion(Request $request){
        try{
            $rules = [
                'id'                 => 'required',
                'title'              => 'required',
                'description'        => '',
                'options'            => 'required',
                'assess_description' => '',
                'tags'               => 'required',
            ];
            $reqData = $request->only(array_keys($rules));
            Util::checkInputData($reqData,$rules);
            $staff = $this->getStaffFromRequest();
            $department = $this->getMerchantFromRequest();
            $reqData['FK_staff_id']=$staff->id;
            $reqData['FK_staff_name']=$staff->name;
            $reqData['department_id']=$department->id;
            $reqData['department_name']=$department->name;
            $res = QuestionModel::editQuestion($reqData);
            $this->ApiResponse($res,Err::$SUCCESS,1,'修改问题成功');
        }catch (Exception $e){
            Util::WriteLog($e);
            $this->ApiResponse([],Err::$ERROR,1,Util::getExceptionMessage($e));
        }
    }

}