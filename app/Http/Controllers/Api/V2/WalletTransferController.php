<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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


        return response()->json(['success' => 1, 'message' => 'Validated Successfully', 'data'=>$user->user_name, 'name' =>$user->full_name]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required',
            'amount' => 'required|numeric|min:1',
            'reference' => 'required|string|max:100',
            'narration' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => 'Required field(s) missing or invalid'
            ]);
        }

        $amount = $request->amount;
        $reference = $request->reference;

        try {

            $response = DB::transaction(function () use ($request, $amount, $reference) {

                // Lock sender wallet row
                $user = User::where('id', Auth::id())->lockForUpdate()->first();

                // Find receiver
                $r_user = User::where("user_name", $request->user_name)
                    ->orWhere("email", $request->user_name)
                    ->orWhere("phoneno", $request->user_name)
                    ->lockForUpdate()
                    ->first();

                if (!$r_user) {
                    return response()->json([
                        'success' => 0,
                        'message' => 'Invalid username'
                    ]);
                }

                if ($r_user->id == $user->id) {
                    return response()->json([
                        'success' => 0,
                        'message' => 'You cannot transfer to yourself'
                    ]);
                }

                if($user->wallet < 1){
                    return response()->json(['success' => 0, 'message' => 'Insufficient fund']);
                }

                if($amount < 1){
                    return response()->json(['success' => 0, 'message' => 'Invalid amount']);
                }

                if ($user->wallet < $amount) {
                    return response()->json([
                        'success' => 0,
                        'message' => 'Insufficient fund'
                    ]);
                }

                // Prevent duplicate reference
                if (Transaction::where("ref", $reference)->exists()) {
                    return response()->json([
                        'success' => 0,
                        'message' => 'Reference already exists'
                    ]);
                }

                $senderInitial = $user->wallet;
                $receiverInitial = $r_user->wallet;

                // Deduct sender wallet
                $user->wallet -= $amount;
                $user->save();

                // Credit receiver wallet
                $r_user->wallet += $amount;
                $r_user->save();

                $description = "Wallet Transfer from {$user->user_name} to {$r_user->user_name} with the sum of #{$amount}";

                if ($request->narration) {
                    $description .= ". " . $request->narration;
                }

                $device = $request->header('device') ?? $request->userAgent();

                // Sender transaction
                Transaction::create([
                    'user_name' => $user->user_name,
                    'name' => 'wallet transfer',
                    'amount' => $amount,
                    'status' => 'successful',
                    'description' => $description,
                    'code' => 'w2wtransfer',
                    'i_wallet' => $senderInitial,
                    'f_wallet' => $user->wallet,
                    'ref' => $reference,
                    'device_details' => $device,
                    'ip_address' => $request->ip(),
                    'date' => now()
                ]);

                // Receiver transaction
                Transaction::create([
                    'user_name' => $r_user->user_name,
                    'name' => 'wallet transfer',
                    'amount' => $amount,
                    'status' => 'successful',
                    'description' => $description,
                    'code' => 'w2wtransfer',
                    'i_wallet' => $receiverInitial,
                    'f_wallet' => $r_user->wallet,
                    'ref' => $reference . "_credit",
                    'device_details' => $device,
                    'ip_address' => $request->ip(),
                    'date' => now()
                ]);

                // Notify receiver
                $r_user->notify(new UserNotification($description, "Wallet Transfer"));

                return response()->json([
                    'success' => 1,
                    'message' => 'Transfer Successful'
                ]);
            });

            return $response;

        } catch (\Exception $e) {

            return response()->json([
                'success' => 0,
                'message' => 'Transfer failed. Try again.'
            ]);
        }
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
