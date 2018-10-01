<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class VideoTag extends Model
{
    protected $fillable = [
        'video_id',
        'tag'
    ];
}