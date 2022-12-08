<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiveAwayRequest extends Model
{
    protected $table = 'tbl_giveaway_request';
    protected $fillable = ['giveaway_id', 'user_name', 'amount', 'status', 'receiver'];
}
