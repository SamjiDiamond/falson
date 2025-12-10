<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GNews extends Model
{
    use HasFactory;

    protected $table = 'tbl_gnews';
    protected $guarded = [];
}
