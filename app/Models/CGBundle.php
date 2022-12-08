<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CGBundle extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table="tbl_cg_bundles";

    protected $guarded=[];
}
