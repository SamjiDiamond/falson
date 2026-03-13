<?php

namespace App\Http\Controllers;

use App\Jobs\BudpayVirtualAccountJob;
use App\Jobs\CreatePaylonyVirtualAccountJob;
use App\Jobs\CreateProvidusAccountJob;
use App\Jobs\MonnifyUpdateVAJob;
use App\Jobs\MonnifyVirtualAccountJob;
use App\Jobs\PalmPayVirtualAccountJob;
use App\Jobs\ReverseTransactionJob;
use App\Models\KYC;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VirtualAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SprintcheckController extends Controller
{

    public function index(Request $request)
    {
        $input = $request->all();
        Log::info("SPRINTCHECK WEBHOOK ".json_encode($input));


        $rules = array(
            'event' => 'required',
            'event_type' => 'required',
            'reference' => 'required',
            'identifier' => 'required',
            'kyc_details' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['message' => 'Payload Errors'], 400);
        }


        $user=User::where('email',$input['identifier'])->orwhere('user_name',$input['identifier'])->first();
        if(!$user){
            return response()->json(['message' => 'User not found'], 400);
        }

        $name= $input['kyc_details']['lastName'] . " " . $input['kyc_details']['firstName']. " " . $input['kyc_details']['middleName'];

        KYC::create([
            "user_name" => $user->user_name,
            "type" => $input['event_type'],
            "number" => $input['number'],
            "reference" => $input['reference'],
            "name" => $name,
            "data" => $input['kyc_details'],
            "source" => "SPRINTCHECK"
        ]);

        if($input['event_type'] == "BVN VERIFICATION") {
            $user->bvn = $input['number'];
            $user->photo=$input['image'];
            $user->dob=$input['kyc_details']['birthday'] ?? $input['kyc_details']['dateOfBirth'];
            $user->full_name=$name;
        }else{
            $user->nin = $input['number'];
        }
        $user->save();

        $v=VirtualAccount::where(["user_id" => $user->id,"provider" => "monnify", "status" => 1])->count();

        if($v == 0){
            CreateProvidusAccountJob::dispatch($user->id);
            BudpayVirtualAccountJob::dispatch($user->id);
            CreatePaylonyVirtualAccountJob::dispatch($user->id);
        }

        return response()->json(['message' => 'Request Processed Successfully'], 200);
    }
}
