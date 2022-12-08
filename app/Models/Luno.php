<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Luno extends Model
{
    protected $table='tbl_luno';

    protected $fillable = [
        'user_name', 'asset', 'address', 'name', 'receive_fee', 'account_id', 'data', 'transid'
    ];
}
