<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $fillable = [
        'openid',
        'avatarUrl',
        'city',
        'country',
        'nickName',
        'province',
    ];



    public static function addUser($reqData){
        $query = self::query()
            ->where('openid', 'like', '%' . $reqData['openid'] . '%')
            ->first();
        if($query){
            return [$query,true];
        }
        else{
            $res = self::create($reqData);
            if($res){
                return [$res,true];
            }else{
                return [[],false];
            }
        }
    }

    public static function getUserInfo($reqData){

    }
}

