<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AppDataControl extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = "tbl_serverconfig_data";

    protected $guarded =[];
}
