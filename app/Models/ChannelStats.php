<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelStats extends Model
{
    protected $table = 'channel_statistics';
    protected $fillable = [
        'channel_id',
        'view_count',
        'comment_count',
        'subscriber_count',
        'video_count',
        'latest'
    ];
}
