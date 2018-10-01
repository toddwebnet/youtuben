<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'channel_id',
        'youtube_video_id',
        'duration',
        'title',
        'descr',
        'default_img_url',
        'medium_img_url',
        'high_img_url',
        'player_html',
        'published_at',
    ];
}