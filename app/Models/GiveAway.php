<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiveAway extends Model
{
    protected $table = "tbl_giveaway";

    protected $fillable = ['user_name', 'amount', 'quantity', 'type', 'status', 'expired_at', 'views', 'image', 'description', 'type_code', 'show_contact'];
}
