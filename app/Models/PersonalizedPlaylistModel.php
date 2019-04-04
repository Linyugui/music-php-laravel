<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalizedPlaylistModel extends Model
{
    protected $table = 'personalized_playlist';
    protected $fillable = [
        'playlist_id',
        'name',
        'picUrl',
        'copywriter',
        'playCount',
        'trackCount',
        'type',
    ];

    public static function getPersonalized($objData)
    {

    }


    public static function generateDailyPersonalizedPlaylist()
    {
        $url = "https://www.linyugui.cn:3008/personalized";
        $res = self::get($url);
        if ($res && $res['code'] == 200) {
            $res = $res['result'];
            $length = count($res);
            for ($i = 0; $i < $length; $i++) {
                $temp = [
                    'playlist_id' => $res[$i]['id'],
                    'name' => $res[$i]['name'],
                    'picUrl' => $res[$i]['picUrl'],
                    'copywriter' => $res[$i]['copywriter'],
                    'playCount' => $res[$i]['playCount'],
                    'trackCount' => $res[$i]['trackCount'],
                    'type' => $res[$i]['type']
                ];
                self::create($temp);
            }
        }
    }

    public static function get($url)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($curl);
        $res = json_decode($res, true);
        curl_close($curl);
        return $res;
    }

}

