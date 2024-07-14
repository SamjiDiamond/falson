<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class Transaction extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'tbl_transactions';

    protected $fillable = [
        'name', 'amount', 'status', 'description', 'date', 'user_name', 'ip_address', 'device_details', 'code', 'i_wallet', 'f_wallet', 'extra', 'server', 'server_response', 'ref'
    ];

    function serverlog()
    {
        return $this->hasOne(Serverlog::class, 'transid', 'ref')->select(['transid', 'payment_method']);
    }

    public function paylonyFunding($ref)
    {
        return DB::table('tbl_webhook_paylony')->where('payment_reference', $ref)->first();
    }

    public function monnifyFunding($ref)
    {
        return DB::table('tbl_webhook_monnify')->where('payment_reference', $ref)->first();
    }

    public function budpayFunding($ref)
    {
        return DB::table('tbl_webhook_budpay')->where('payment_reference', $ref)->first();
    }

}
