<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/v1/{path}', function (\Illuminate\Http\Request $request) {

    $temp = $request->root().'/v1';
    $url  = 'https://www.linyugui.cn:3008'.substr($request->fullUrl(),strlen($temp));
//    return $url;
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $tmpInfo = curl_exec($curl);
    curl_close($curl);
    return $tmpInfo;

})->where('path', '.*');


Route::controller('/test', 'TestController');
Route::controller('/user', 'UserController');
Route::controller('/music', 'MusicController');
Route::controller('/song', 'SongController');
Route::controller('/album', 'AlbumController');

