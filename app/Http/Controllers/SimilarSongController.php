<?php

namespace App\Http\Controllers;

use App\Models\SongModel;
use App\Models\UserModel;
use Illuminate\Http\Request;

use DB;
use App\Helpers\Util;
use App\Helpers\Urllib;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SimilarSongController extends Controller
{
    public function getAddLoveSong(Request $request){
        $rules = [
            'user_id' => 'int',
            'song_id' => 'int',
            'album_name' => 'string',
            'artist_name' => 'string',
            'song_name' => 'string',
            'st' => 'int',
            'pl' => 'int',
            'picUrl' => 'string'
        ];
        $objData = $request->only(array_keys($rules));
        try {
            $res = SongModel::create($objData);
            Util::ApiResponse($res, true, 0, '收藏歌曲成功');
        } catch (\Exception $e) {
            Util::ApiResponse([], false, 0, Util::getExceptionMessage($e));
        }
    }

    public function getDelLoveSong(Request $request){
        $rules = [
            'user_id' => 'int',
            'song_id' => 'int',
        ];
        $objData = $request->only(array_keys($rules));
        try {
            $res = SongModel::query()
                ->where('user_id',$objData['user_id'])
                ->where('song_id',$objData['song_id'])
                ->first();
            $res->delete();
            Util::ApiResponse($res, true, 0, '取消收藏成功');
        } catch (\Exception $e) {
            Util::ApiResponse([], false, 0, Util::getExceptionMessage($e));
        }
    }

    public function getAllLoveSong(Request $request){
        $rules = [
            'user_id' => 'int',
        ];
        $objData = $request->only(array_keys($rules));
        try {
            $res = SongModel::query()
                ->where('user_id',$objData['user_id'])
                ->get();
            $res_array = [];
            foreach ($res as $item){
                $res_array[]=$item['song_id'];
            }
            Util::ApiResponse($res_array, true, 0, '获取收藏歌曲成功');
        } catch (\Exception $e) {
            Util::ApiResponse([], false, 0, Util::getExceptionMessage($e));
        }
    }

    public function getLoveSongDetail(Request $request){
        $rules = [
            'user_id'   =>  'int',
            'limit'     =>  'int',
            'skip'      =>  'int',
        ];
        $objData = $request->only(array_keys($rules));
        $limit = Util::issetValue($objData,'limit',1000);
        $skip = Util::issetValue($objData,'skip',0);
        try {
            $res = SongModel::query()
                ->select(
                    'updated_at',
                    'song_id as id',
                    'song_name as name',
                    'album_name',
                    'user_id',
                    'artist_name',
                    'st',
                    'picUrl'
                )
                ->where('user_id',$objData['user_id'])
                ->orderBy('updated_at','desc');
            $count = $res->count();
            $res = $res->skip($skip)
                ->take($limit)
                ->get();

            Util::ApiResponse($res, true, $count, '获取收藏歌曲成功');
        } catch (\Exception $e) {
            Util::ApiResponse([], false, 0, Util::getExceptionMessage($e));
        }
    }


}
