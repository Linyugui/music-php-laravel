<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SongModel extends Model
{
    protected $table = 'love_song';
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


   
}

