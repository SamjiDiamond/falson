<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $table = "tbl_promocode";

    protected $fillable = [
        'code',
        'amount',
        'used',
        'reuseable',
        'usedby',
        'generated_for',
    ];
}
