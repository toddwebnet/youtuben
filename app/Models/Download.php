<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    /**
     * @var array
     * types: subs, channels
     */
    protected $fillable = ['type'];
}
