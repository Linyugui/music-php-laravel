<?php

namespace App\Http\Controllers;

use App\Models\SimilarSongModel;
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
    public function getDailySimilarSong(Request $request)
    {
        $rules = [
            'user_id' => 'int',
            'limit' => 'int',
            'skip' => 'int',
        ];
        $objData = $request->only(array_keys($rules));
        try {
            list($res, $list) = SimilarSongModel::getDailySimilarSong($objData);
            Util::ApiResponse($res, true, $list, '收藏歌曲成功');
        } catch (\Exception $e) {
            Util::ApiResponse([], false, 0, Util::getExceptionMessage($e));
        }
    }


    public function getSimilarSong(Request $request)
    {
        list($res, $list) = SimilarSongModel::generateDailySimilarSong(20, 2, 20);
    }


}
