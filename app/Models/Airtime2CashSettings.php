<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Airtime2CashSettings extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table='tbl_airtime2cash_settings';
}
