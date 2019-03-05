<?php

namespace App\Http\Controllers;

use App\Models\AlbumModel;
use Illuminate\Http\Request;

use DB;
use App\Helpers\Util;

class AlbumController extends Controller
{
    public function getAddLoveAlbum(Request $request){
        $rules = [
            'user_id' => 'int',
            'album_id' => 'int',
            'album_name' => 'string',
            'album_picurl' => 'string',
            'artist_name' => 'string',
        ];
        $objData = $request->only(array_keys($rules));
        try {
            $res = AlbumModel::create($objData);
            Util::ApiResponse($res, true, 0, '收藏专辑成功');
        } catch (\Exception $e) {
            Util::ApiResponse([], false, 0, Util::getExceptionMessage($e));
        }
    }

    public function getDelLoveAlbum(Request $request){
        $rules = [
            'user_id' => 'int',
            'album_id' => 'int',
            'album_name' => 'string',
            'album_picurl' => 'string',
            'artist_name' => 'string',
        ];
        $objData = $request->only(array_keys($rules));
        try {
            $res = AlbumModel::query()
                ->where('user_id',$objData['user_id'])
                ->where('album_id',$objData['album_id'])
                ->first();
            $res->delete();
            Util::ApiResponse($res, true, 0, '取消收藏成功');
        } catch (\Exception $e) {
            Util::ApiResponse([], false, 0, Util::getExceptionMessage($e));
        }
    }


    public function getAllLoveAlbum(Request $request){
        $rules = [
            'user_id' => 'int',
        ];
        $objData = $request->only(array_keys($rules));
        try {
            $res = AlbumModel::query()
                ->where('user_id',$objData['user_id'])
                ->get();
            $res_array = [];
            foreach ($res as $item){
                $res_array[]=$item['album_id'];
            }
            Util::ApiResponse($res_array, true, 0, '获取收藏专辑成功');
        } catch (\Exception $e) {
            Util::ApiResponse([], false, 0, Util::getExceptionMessage($e));
        }
    }

    public function getLoveAlbum(Request $request){
        $rules = [
            'user_id' => 'int',
        ];
        $objData = $request->only(array_keys($rules));
        try {
            $res = AlbumModel::query()
                ->where('user_id',$objData['user_id'])
                ->get();

            Util::ApiResponse($res, true, 0, '获取收藏专辑成功');
        } catch (\Exception $e) {
            Util::ApiResponse([], false, 0, Util::getExceptionMessage($e));
        }
    }
}
