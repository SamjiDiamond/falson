<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SMSinbox extends Model
{
    protected $table="tbl_smsinbox";
    protected $fillable=["sender", "message", "time", "status"];
}
