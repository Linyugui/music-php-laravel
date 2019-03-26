<?php

namespace App\Models;

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
        'picUrl',
    ];


   
}

