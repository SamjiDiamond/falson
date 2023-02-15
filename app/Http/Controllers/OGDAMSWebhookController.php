<?php

namespace App\Http\Controllers;

use App\Jobs\ReverseTransactionJob;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OGDAMSWebhookController extends Controller
{
    public function index(Request $request)
    {

        $input = $request->all();

        $ref=$input['event']['data']['reference'];

        DB::table('tbl_webhook_ogdams')->insert(['code' => $input['code'], 'reference' => $ref, 'api_response' => $input['event']['data']['msg'], 'ip' => $_SERVER['REMOTE_ADDR'], 'extra' => json_encode($input)]);


        $rules = array(
            'code' => 'required',
            'event' => 'required',
            'status' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['message' => 'ok'], 400);
        }

        $tran = Transaction::where(['server_ref' => $ref])->latest()->first();

        if (!$tran) {
            return response()->json(['message' => 'ok'], 404);
        }

        if ($tran->status == "reversed") {
            return response()->json(['message' => 'ok'], 202);
        }

        if ($input['code'] == 200) {
            $tran->status = "delivered";
            $tran->save();
            return response()->json(['message' => 'ok'], 202);
        }

        if ($input['code'] == 424) {
            ReverseTransactionJob::dispatch($tran, "Webhook")->onQueue('high');
            return response()->json(['message' => 'Reversal Initiated'], 200);
        }

        return response()->json(['message' => 'ok'], 200);
    }
}
