<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralMarket extends Model
{
    protected $table='tbl_generalmarket';

    protected $fillable = [
        'i_wallet', 'f_wallet', 'type', 'date', 'user_name', 'amount', 'transid', 'version', 'device_details'
    ];
}
