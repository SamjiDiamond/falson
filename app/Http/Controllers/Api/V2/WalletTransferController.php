<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WalletTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateUsername(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $user=User::where("user_name",$input['user_name'])->orwhere("email",$input['user_name'])->orwhere("phoneno",$input['user_name'])->first();

        if(!$user){
            return response()->json(['success' => 0, 'message' => 'Invalid username']);
        }


        return response()->json(['success' => 1, 'message' => 'Validated Successfully', 'data'=>$user->user_name]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transfer(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'amount' => 'required',
            'reference' => 'required',
            'narration' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

       return response()->json(['success' => 0, 'message' => 'Currently under maintenance']);

        $user=User::find(Auth::id());

        $r_user=User::where("user_name",$input['user_name'])->orwhere("email",$input['user_name'])->orwhere("phoneno",$input['user_name'])->first();

        if(!$r_user){
            return response()->json(['success' => 0, 'message' => 'Invalid username']);
        }

        if($r_user->user_name == $user->user_name){
            return response()->json(['success' => 0, 'message' => 'You can not transfer to yourself']);
        }

        if($user->wallet < 1){
            return response()->json(['success' => 0, 'message' => 'Insufficient fund']);
        }

        $amount=$input['amount'];

        if($amount < 1){
            return response()->json(['success' => 0, 'message' => 'Invalid amount']);
        }

        if($input['amount'] > $user->wallet){
            return response()->json(['success' => 0, 'message' => 'Insufficient fund ']);
        }

        $reference=$input['reference'];

        $check=Transaction::where("ref", $reference)->first();

        if($check){
            return response()->json(['success' => 0, 'message' => 'Reference already exist']);
        }

        $user->wallet-=$amount;
        $user->save();

        $input['device_details'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];
        $input['name'] = "wallet transfer";
        $input['amount']=$amount;
        $input['status']='successful';
        $input['description']='Wallet Transfer from '. $user->user_name .' to '.$r_user->user_name.' with the sum of #'.$amount;
        if(isset($input['narration'])){
            $input['description'].=". ".$input['narration'];
        }
        $input['code']='w2wtransfer';
        $input['user_name']=$user->user_name;
        $input['i_wallet']=$user->wallet;
        $input['f_wallet']=$user->wallet - $amount;
        $input['ref']=$reference;
        $input["ip_address"]=$_SERVER['REMOTE_ADDR'];
        $input["date"]=date("y-m-d H:i:s");

        Transaction::create($input);

        $input['user_name'] = $r_user->user_name;
        $input['i_wallet'] = $r_user->wallet;
        $input['f_wallet'] = $r_user->wallet + $amount;

        Transaction::create($input);

        $r_user->wallet += $amount;
        $r_user->save();

        $r_user->notify(new UserNotification($input['description'], "Wallet Transfer"));

        return response()->json(['success' => 1, 'message' => 'Transfer Successful']);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
