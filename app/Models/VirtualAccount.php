<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualAccount extends Model
{
    protected $guarded = [];

    function user()
    {
        return $this->belongsTo(User::class)->select(['full_name', 'phoneno', 'email', 'id']);
    }
}
