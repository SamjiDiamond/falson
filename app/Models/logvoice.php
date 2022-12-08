<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class logvoice extends Model
{
    protected $table='tbl_voicelog';

    protected $fillable = [
        'name', 'user_name', 'voice', 'version', 'page', 'code', 'device_details'
    ];
}
