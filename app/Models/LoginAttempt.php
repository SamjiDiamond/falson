<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $table='tbl_login_attempt';

    protected $fillable = [
        'status', 'user_name', 'password', 'ip_address', 'device', 'city', 'region', 'country', 'provider', 'response'
    ];
}
