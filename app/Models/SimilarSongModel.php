<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SimilarSongModel extends Model
{
    protected $table = 'similar_song';
    protected $fillable = [
        'user_id',
        'song_id',
        'album_name',
        'artist_name',
        'song_name',
        'st',
        'pl',
        'dt',
        'picUrl',
    ];

    public static function getDailySimilarSong($objData){
        $start_time = Carbon::today();
        $end_time = Carbon::tomorrow()->subSecond();
        $res = self::query()
            ->where('created_at', '>=', $start_time)
            ->where('created_at', '<=', $end_time)
            ->select(
                'updated_at',
                'song_id as id',
                'song_name as name',
                'album_name',
                'user_id',
                'artist_name',
                'st',
                'pl',
                'dt',
                'picUrl'
            )
            ->where('user_id',$objData['user_id']);
        $count = $res->count();
        $res = $res->get();
        return [$res,$count];


    }


    /***
     * @param $artists_size 随机的歌手数量
     * @param $song_size    一个歌手推荐几首歌曲
     * @param $similar_size 总的推荐歌曲数量
     * @return array
     */
    public static function generateDailySimilarSong($artists_size, $song_size, $similar_size)
    {

        $users = UserModel::query()
            ->select('id', 'nickName')
            ->get();
        foreach ($users as $user) {
            $data = [];
            $count = 0;

            //获取收藏歌曲中的所有歌手
            $artists = SongModel::query()
                ->select('artist_name')
                ->groupBy('artist_name')
                //->where('user_id', 1)
                ->where('user_id', $user['id'])
                ->get();

            //获取收藏歌曲中的所有歌曲id
            $love_song = SongModel::query()
                //->where('user_id', 1)
                ->where('user_id', $user['id'])
                ->get()
                ->pluck('song_id')
                ->toArray();

            //获取新歌速递
            $url = "https://www.linyugui.cn:3008/top/song";
            $new_song = self::get($url);

            if ($new_song) {
                //$new_song = json_decode($new_song, true);
                $new_song = $new_song['data'];
                shuffle($new_song);
            } else {
                $new_song = [];
            }


            $artists_count = $artists->count();
            if ($artists_count < $artists_size) {
                $artists = $artists->pluck('artist_name');
            } else {
                if ($artists_size == 0) {
                    $artists = [];
                } else {
                    $artists = $artists->random($artists_size)->pluck('artist_name');
                }
            }

            for ($i = 0; $i < count($artists); $i++) {
                $url = "https://www.linyugui.cn:3008/search?keywords=" . $artists[$i] . "&type=1";
                //echo "进行搜索" . $url . "<br>";
                $res = self::get($url);

                if ($res && $res['code'] == 200 && $res['result']['songCount'] != 0) {
                    //echo "搜索成功" . $artists[$i] . "<br>";
                    $songs = $res['result']['songs'];
                    shuffle($songs);
                    //i主要用于遍历收藏歌曲中的歌手
                    //j主要用于遍历每一个歌手推荐的歌曲数量
                    //k主要用于遍历每一个歌手的歌曲数量

//                    /**************************************************/
//                    echo $artists[$i]."的歌曲数量:".count($songs)."<br>";
//                    echo "url为：".$url."<br>";
//                    print_r($songs);
//
//                    /**************************************************/

                    for ($j = 0, $k = 0; $j < $song_size && $k < count($songs) && $count < $similar_size; $k++) {
                        if (!in_array($songs[$k]['id'], $love_song)) {
                            //echo "没有收藏" . $songs[$k]['id'] . "<br>";
                            $data[] = $songs[$k]['id'];
                            $love_song[] = $songs[$k]['id'];
                            $j++;
                            $count++;
                        }
                    }
                } else {
                    //echo "搜索不成功" . $artists[$i] . "<br>";
                }
            }

            //用新歌速递来补充
            for ($i = 0; $count < $similar_size && $i < count($new_song); $i++) {
                if (!in_array($new_song[$i]['id'], $love_song)) {
                    $count++;
                    $data[] = $new_song[$i]['id'];
                    $love_song[] = $new_song[$i]['id'];
                }
            }
            //将数组乱序
            shuffle($data);
            $url = "https://www.linyugui.cn:3008/song/detail?ids=" . implode(",", $data);
            $result = self::get($url);
            $similar_songs = $result['songs'];
            $similar_privileges = $result['privileges'];


            for ($i = 0; $i < count($similar_songs); $i++) {
                $temp = [
                    'user_id' => $user['id'],
                    'song_id' => $similar_songs[$i]['id'],
                    'album_name' => $similar_songs[$i]['al']['name'],
                    'artist_name' => $similar_songs[$i]['ar'][0]['name'],
                    'song_name' => $similar_songs[$i]['name'],
                    'st' => $similar_privileges[$i]['st'],
                    'pl' => $similar_privileges[$i]['pl'],
                    'dt' => $similar_songs[$i]['dt'],
                    'picUrl' => $similar_songs[$i]['al']['picUrl'],
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

