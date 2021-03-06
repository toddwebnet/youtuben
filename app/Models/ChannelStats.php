<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelStats extends Model
{
    protected $table = 'channel_statistics';
    protected $fillable = [
        'download_id',
        'channel_id',
        'views',
        'comment_count',
        'subscriber_count',
        'video_count',
        'latest'
    ];
}
