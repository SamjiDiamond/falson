<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSettings extends Model
{
    protected $table='tbl_settings';

    protected $fillable = [
        'name'
    ];
}
