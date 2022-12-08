<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ResellerElecticity extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = "tbl_reseller_electricity";
    protected $fillable = ['name', 'code', 'discount', 'status'];

}
