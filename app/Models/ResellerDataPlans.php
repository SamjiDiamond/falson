<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ResellerDataPlans extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = "tbl_reseller_dataplans";
    protected $fillable = ["name", "code", "amount", "status", "type", "price", "level1", "level2", "level3", "level4", "level5", "product_code", "server", "network", "plan_id"];
}
