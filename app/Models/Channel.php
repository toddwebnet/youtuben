<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = [
        'youtube_channel_id',
        'title',
        'description',
        'custom_url',
        'published_at',
        'default_img_url',
        'medium_img_url',
        'high_img_url',
    ];

}
