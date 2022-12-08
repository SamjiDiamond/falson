<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewDevice extends Model
{
    protected $table = "tbl_newdevice";
    protected $fillable = ['user_name', 'device', 'code', 'status', 'expired'];
}
