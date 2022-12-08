<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AppCableTVControl extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = "tbl_serverconfig_cabletv";
    protected $fillable = ["name", "coded", "code", "price", "discount", "status", "type", "server"];
}
