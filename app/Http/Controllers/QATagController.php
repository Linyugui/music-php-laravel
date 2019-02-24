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
use posapi\Models\QATagModel;
use Exception;

class QATagController extends BaseController
{

    public function postAdmAddTag(Request $request){
        try{

            $rules = [
                'name'              => 'required | unique:qa_tag,name',
                'key'               => 'required',
                'level'             => 'required',
                'parent_id'         => 'required',
            ];
            $reqData = $request->all();
            Util::checkInputData($reqData,$rules);
            $department  = $this->getMerchantFromRequest();
            $staff = $this->getStaffFromRequest();
            Util::checkInputData($reqData,$rules);
            $data = [
                'name'            => $reqData['name'],
                'key'             => $reqData['key'],
                'parent_id'       => $reqData['parent_id'],
                'parent_name'     => $reqData['parent_name'],
                'level'           => $reqData['level'],
                'department_id'   => $department->id,
                'department_name' => $department->name,
                'FK_staff_id'     => $staff->id,
                'FK_staff_name'   => $staff->name,
            ];
            $res = QATagModel::create($data);
            $this->ApiResponse($res,'true',1,'添加标签成功');
        }catch (Exception $e){
            Util::writeLog($e);
            $this->ApiResponse([],Err::$ERROR,1,Util::getExceptionMessage($e));
        }
    }


    public function postAdmGetTagTree(Request $request){
        try{
            $rules=[
                'level' => ''
            ];
            $reqData = $request->only(array_keys($rules));
            Util::checkInputData($reqData,$rules);
            $reqData['department_id'] = $this->getMerchantFromRequest()->id;
            list($res,$count) = QATagModel::getQATagTree($reqData);
            $this->ApiResponse($res,Err::$SUCCESS,$count,'获取数据成功');
        } catch (Exception $e){
            Util::writeLog($e);
            $this->ApiResponse([],Err::$ERROR,1,Util::getExceptionMessage($e));
        }
    }

    public function postAdmGetTag(Request $request){
        try{
            $group = '';
            if ($request['group']) {
                $group = explode(',', $request['group']);
            }

            $pages = $this->_paginationRule;
            list($res, $count) = QATagModel::getGroupTag($group, $pages);
            $this->ApiResponse($res, Err::$SUCCESS, $count, '获取数据成功');
        } catch (Exception $e){
            Util::WriteLog($e);
        }
    }




}