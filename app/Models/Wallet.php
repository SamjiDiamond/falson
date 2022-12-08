<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Wallet extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table='tbl_wallet';

    protected $fillable = [
        'o_wallet', 'n_wallet', 'medium', 'status', 'date', 'user_name', 'amount', 'ref', 'version', 'deviceid'
    ];
}
