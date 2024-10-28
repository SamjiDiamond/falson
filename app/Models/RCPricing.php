<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RCPricing extends Model
{
    use HasFactory;

    protected $table = 'rc_price_plan';

    protected $fillable = ["plan", "amount", "mtn", "glo", "airtel", "ninemobile", "status"];
}
