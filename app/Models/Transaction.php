<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Transaction extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table='tbl_transactions';

    protected $fillable = [
        'name', 'amount', 'status', 'description', 'date', 'user_name', 'ip_address', 'device_details', 'code', 'i_wallet', 'f_wallet', 'extra', 'server', 'server_response', 'ref'
    ];

}
