<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ResellerCableTV extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = "tbl_reseller_cabletv";
    protected $fillable = ["name", "code", "amount", "discount", "status", "type", "server"];
}
