<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table='tbl_allsettings';

    protected $fillable = [
        'name', 'value', 'status'
    ];
}
