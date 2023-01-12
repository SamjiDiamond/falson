<?php

namespace App\Http\Controllers;

use App\Jobs\ReverseTransactionJob;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IyiiWebhookController extends Controller
{

    public function index(Request $request)
    {

        $input = $request->all();

        DB::table('tbl_webhook_iyii')->insert(['ident' => $input['ident'], 'mobile_number' => $input['mobile_number'], 'status' => $input['Status'], 'api_response' => $input['api_response'], 'ip' => $_SERVER['REMOTE_ADDR'], 'extra' => json_encode($input)]);


        $rules = array(
            'ident' => 'required',
            'mobile_number' => 'required',
            'Status' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['message' => 'ok'], 400);
        }

        $tran = Transaction::where(['server_ref' => $input['ident']])->latest()->first();

        if (!$tran) {
            return response()->json(['message' => 'ok'], 404);
        }

        if ($tran->status == "reversed") {
            return response()->json(['message' => 'ok'], 202);
        }

        if ($input['Status'] == "successful") {
            $tran->status = "delivered";
            $tran->save();
        }

        if ($input['Status'] == "failed") {
            ReverseTransactionJob::dispatch($tran, "Webhook")->onQueue('high');
        }

        return response()->json(['message' => 'ok'], 200);
    }
}
