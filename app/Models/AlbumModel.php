<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlbumModel extends Model
{
    protected $table = 'love_album';
    protected $fillable = [
        'user_id',
        'album_id',
        'album_name',
        'album_picurl',
        'artist_name',
    ];
   
}

