<?php

namespace App\Http\Controllers;

use App\Models\PersonalizedPlaylistModel;
use Illuminate\Http\Request;

use DB;
use App\Helpers\Util;
use App\Helpers\Urllib;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class PersonalizedPlaylistController extends Controller
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


    public function getPersonalizedPlaylist(Request $request)
    {
        list($res, $list) = PersonalizedPlaylistModel::generateDailyPersonalizedPlaylist();

    }



}
