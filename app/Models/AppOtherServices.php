<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AppOtherServices extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = "tbl_otherservices";

    protected $guarded = [];
}
