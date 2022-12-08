<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CGTransaction extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table="tbl_cg_transactions";

    protected $guarded=[];

    function cgbundle(){
        return $this->belongsTo(CGBundle::class, "bundle_id");
    }
}
