<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualAccountClient extends Model
{
    protected $table = "tbl_virtual_account_clients";
    protected $fillable = ['account_reference', 'currency_code', 'customer_email', 'customer_name', 'customer_phone', 'account_number', 'bank_name', 'status', 'created_on', 'reservation_reference', 'extra', 'webhook_url', 'reseller_id'];
}
