<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AppAirtimeControl extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = "tbl_serverconfig_airtime";
}
