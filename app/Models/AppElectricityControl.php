<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AppElectricityControl extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = "tbl_serverconfig_electricity";
    protected $fillable = ["name", "coded", "code", "price", "discount", "status", "type", "server"];
}
