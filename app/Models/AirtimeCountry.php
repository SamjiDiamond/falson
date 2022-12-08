<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AirtimeCountry extends Model
{
    protected $fillable = ["isoName", "name", "currencyCode", "currencyName", "flag", "callingCodes", "status", "USDrate"];
}
