<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $table='tbl_withdraw';

    protected $fillable = [
        'user_name', 'wallet', 'amount', 'ref', 'account_number', 'bank', 'bank_code', 'device_details', 'version', 'status'
    ];
}
