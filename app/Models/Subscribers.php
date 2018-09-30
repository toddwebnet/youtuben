<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscribers extends Model
{
    protected $fillable = [
        'download_id',
        'channel_id',
        'owner_channel_id'
    ];

}
