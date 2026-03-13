<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KYC extends Model
{
    use HasFactory;

    protected $table='tbl_kyc';
    protected $guarded=[];

    protected $casts = [
        'data' => 'array',
    ];
}
