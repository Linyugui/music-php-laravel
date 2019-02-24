<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\Urllib;

class TestController extends Controller
{
    public function getTestUrlLib()
    {
        $url = 'http://www.baidu.com';
        //$url = 'http://localhost/api/Category/info';
        $res = Urllib::get($url);
        //$res = Urllib::parse($res);
        Log::info(__method__.'() line:'.__line__.' $res = '.print_r($res, true));
        return $res;
    }
}
