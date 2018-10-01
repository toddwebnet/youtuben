<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class VideoStats extends Model
{
    protected $table = 'video_statistics';

    protected $fillable = [
        'download_id',
        'video_id',
        'views',
        'likes',
        'dislikes',
        'favorites',
        'comment_count',
        'latest',
    ];
}