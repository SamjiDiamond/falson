<?php

namespace App\Jobs;

use App\Http\Controllers\Api\SellAirtimeController;
use App\Http\Controllers\Api\SellDataController;
use App\Http\Controllers\Api\SellElectricityController;
use App\Http\Controllers\Api\SellTVController;
use App\Models\AppAirtimeControl;
use App\Models\AppCableTVControl;
use App\Models\AppDataControl;
use App\Models\ResellerElecticity;
use App\Models\Serverlog;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ATMtransactionserveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $id;
    public $from;

    public function __construct($id, $from = "atm")
    {
        $this->id = $id;
        $this->from = $from;
    }

    /**
     * Execute the job.
     *
     * @return JsonResponse
     */
    public function handle()
    {
        $s=Serverlog::find($this->id);

        $input['user_name'] = $s->user_name;
        $input['api'] = $s->api;
        $input['coded'] = $s->coded;
        $input['phone'] = $s->phone;
        $input['amount'] = $s->amount;
        $input['transid'] = $s->transid;
        $input['service'] = $s->service;
        $input['network'] = $s->network;
        $input['payment_method'] = $s->payment_method;
        $input['version'] = $s->version;
        $input['ip_address'] = $s->ip_address;

        $discount = 0;
        $server = 0;

        if ($this->from == "atm") {

            $user = User::where("user_name", $input['user_name'])->first();

            if ($input['service'] == "airtime") {
                $tr['name'] = strtoupper($input['network']) . " " . $input['service'];
                $tr['description'] = $user->user_name . " purchase " . $input['network'] . " " . $input['amount'] . " airtime on " . $input['phone'] . " using " . $input['payment_method'];
                $tr['code'] = $input['service'];
            } elseif ($input['service'] == "electricity" || $input['service'] == "betting") {
                $tr['name'] = strtoupper($input['service']);
                $tr['description'] = $user->user_name . " pay " . $input['amount'] . " on " . $input['phone'] . " using " . $input['payment_method'];
                $tr['code'] = $input['service'];
            } elseif($input['service'] == "data") {
                $rac = AppDataControl::where("coded", strtolower($input['coded']))->first();

                $tr['name'] = $input['service'];
                $tr['description'] = $user->user_name . " purchase " . " " . $rac->name . " on " . $input['phone'] . " using " . $input['payment_method'];
                $tr['code'] = $input['service'] . "_" . $input['coded'];
            }
            else {
                $tr['name'] = $input['service'];
                $tr['description'] = $user->user_name . " purchase " . " " . $input['coded'] . " on " . $input['phone'] . " using " . $input['payment_method'];
                $tr['code'] = $input['service'] . "_" . $input['coded'];
            }


            $tr['amount'] = $input['amount'];
            $tr['date'] = Carbon::now();
            $tr['device_details'] = "api";
            $tr['ip_address'] = $input['ip_address'];
            $tr['i_wallet'] = $user->wallet;
            $tr['f_wallet'] = $tr['i_wallet'];
            $tr['user_name'] = $user->user_name;
            $tr['ref'] = $input['transid'];
            $tr['code'] = $input['service'] . "_" . $input['coded'];
            $tr['server'] = "server" . $server;
            $tr['server_response'] = "";
            $tr['payment_method'] = $input['payment_method'];
            $tr['status'] = "pending";
            $tr['extra'] = $discount;
            $t = Transaction::create($tr);
        } else {
            $t = Transaction::where('ref', $s->transid)->first();
        }

        $dada['tid'] = $t->id;
        $dada['amount'] = $input['amount'];
        $dada['discount'] = $discount;

        $r = new Request($input);
        if ($s->service == "airtime") {

            $airtime=AppAirtimeControl::where("network", $input['network'])->first();

            if(!$airtime){
                return response()->json(['success' => 0, 'message' => 'Invalid Network. Available are  MTN, 9MOBILE, GLO, AIRTEL.']);
            }

            $server = $airtime->server;
            $discount = $airtime->discount;


            $t->server = "server" . $server;
            $t->save();


            $air = new SellAirtimeController();

            switch (strtolower($server)) {
                case "3":
                    return $air->server3($r, $input['amount'], $input['phone'], $input['transid'], $input['network'], $input, $dada, "mcd");
                case "2":
                    return $air->server2($r, $input['amount'], $input['phone'], $input['transid'], $input['network'], $input, $dada, "mcd");
                case "1":
                    return $air->server1($r, $input['amount'], $input['phone'], $input['transid'], $input['network'], $input, $dada, "mcd");
                default:
                    return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
            }
        }

        if ($s->service == "data") {

            $rac = DB::table("tbl_serverconfig_data")->where("coded", strtolower($input['coded']))->first();

            if ($rac == "") {
                return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
            }

            $t->server = "server" . $rac->server;
            $t->save();

            $air = new SellDataController();

            switch (strtolower($rac->server)) {
                case "2":
                    return $air->server2($r, $input['coded'], $input['phone'], $input['transid'], $rac->network, $input, $dada, "mcd");
                case "1":
                    return $air->server1($r, $input['coded'], $input['phone'], $input['transid'], $rac->network, $input, $dada, "mcd");
                default:
                    return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
            }
        }

        if ($s->service == "tv") {

            $rac = AppCableTVControl::where("coded", strtolower($input['coded']))->first();

            if ($rac == "") {
                return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
            }

            $t->server = "server" . $rac->server;
            $t->save();

            $air = new SellTVController();

            switch (strtolower($rac->server)) {
                case "1":
                    return $air->server1($r, $input['coded'], $input['phone'], $input['transid'], $rac->network, $input, $dada, "mcd");
                case "2":
                    return $air->server2($r, $input['coded'], $input['phone'], $input['transid'], $rac->network, $input, $dada, "mcd");
                default:
                    return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
            }

        }

        if ($s->service == "electricity") {

            $rac = ResellerElecticity::where("code", strtolower($input['network']))->first();

            if ($rac == "") {
                return response()->json(['success' => 0, 'message' => 'Invalid coded supplied']);
            }

            $t->server = "server" . $rac->server;
            $t->save();

            $air = new SellElectricityController();

            switch (strtolower($rac->server)) {
                case "1":
                    return $air->server1($r, $input['network'], $input['phone'], $input['transid'], $input['network'], $input, $dada, "mcd");
                default:
                    return response()->json(['success' => 0, 'message' => 'Kindly contact system admin']);
            }

        }

    }
}
