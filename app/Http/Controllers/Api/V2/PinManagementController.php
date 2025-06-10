<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Mail\PinVerificationMail;
use App\Models\CodeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PinManagementController extends Controller
{
    public function change_pin(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'o_pin' => 'required',
            'n_pin' => 'required',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $user = Auth::user();
        if ($user->pin != $input['o_pin']) {
            return response()->json(['success' => 0, 'message' => 'Wrong Old Pin']);
        }

        $user->pin = $input['n_pin'];
        $user->save();

        return response()->json(['success' => 1, 'message' => 'Pin changed successfully']);
    }

    public function togglePin(Request $request)
    {
        $user = Auth::user();
        $user->pin_enabled = $user->pin_enabled == 0 ? 1 : 0;
        $user->save();

        return response()->json(['success' => 1, 'message' => 'Transaction Pin Toggled successfully', 'data' => $user->pin_enabled]);
    }

    public function resetPin(Request $request)
    {

        $input = $request->all();
        $rules = array(
            'email' => 'required',
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $user = User::where('user_name', $input['email'])->orWhere('email', $input['email'])->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User does not exist']);
        }

        $type = "recover";

        $code = substr(rand(), 0, 6);

        CodeRequest::create([
            'mobile' => trim($input['email']),
            'code' => $code,
            'status' => 0,
            'type' => $type
        ]);

        $edata['device'] = $_SERVER['HTTP_USER_AGENT'];
        $edata['user_name'] = $user->user_name;
        $edata['code'] = $code;
        $edata['email'] = $input['email'];
        $edata['ip'] = $request->ip();

        Mail::to($input['email'])->later(now()->addSeconds(2), new PinVerificationMail($edata));

        return response()->json(['success' => 1, 'message' => 'A verification code has been sent to your mail.']);
    }

    public function completeResetPin(Request $request)
    {

        $input = $request->all();
        $rules = array(
            'email' => 'required',
            'code' => 'required',
            'pin' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $user = User::where('user_name', $input['email'])->orWhere('email', $input['email'])->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User does not exist']);
        }

        $type = "recover";


        $code = $input['code'];

        $code = CodeRequest::where([
            'mobile' => trim($input['email']),
            'code' => $code,
            'status' => 0,
            'type' => $type
        ])->latest()->first();

        if (!$code) {
            return response()->json(['success' => 0, 'message' => 'Invalid code supplied']);
        }

        $user->pin = $input['pin'];
        $user->save();

        $code->status = 1;
        $code->save();

        return response()->json(['success' => 1, 'message' => 'Pin changed successfully']);
    }

}
