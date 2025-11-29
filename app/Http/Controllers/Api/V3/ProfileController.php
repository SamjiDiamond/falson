<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationMail;
use App\Models\CodeRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function securitySettings(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'twofa' => 'required|int|in:0,1',
            'new_device_otp' => 'required|int|in:0,1',
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        User::where('id', Auth::id())->update([
            'twofa' => $input['twofa'],
            'new_device_otp' => $input['new_device_otp']
        ]);

        return response()->json(['success' => 1, 'message' => 'Security Updated Successfully',]);
    }

    public function tfas(Request $request)
    {
        $email = Auth::user()->twofa;
        $phone = Auth::user()->twofa_phone;
        $auth = Auth::user()->two_factor_enabled;
        return response()->json(['success' => 1, 'message' => '2FAs Fetched Successfully', 'data' => [
            'phone_number' => $phone,
            'email' => $email,
            'authenticator' => $auth,
        ]]);
    }

    public function emailCode(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'email' => 'required|email'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        $fuser = User::where('email', $input['email'])->first();

        if (!$fuser) {
            return response()->json(['success' => 0, 'message' => 'Kindly use your valid email']);
        }

        $cr = CodeRequest::where([["mobile", $input['email']], ['status', 0]])->latest()->first();

        if ($cr) {
            if (Carbon::parse($cr->created_at)->diffInMinutes() < 10) {
                return response()->json(['success' => 1, 'message' => 'Code has been sent to your mail. Kindly check your inbox, promotions or spam']);
            }
        }

        $type = "2fa";

        $code = substr(rand(), 0, 6);

        CodeRequest::create([
            'mobile' => trim($input['email']),
            'code' => $code,
            'status' => 0,
            'type' => $type
        ]);

        $edata['code'] = $code;
        $edata['email'] = $input['email'];
        $edata['ip'] = $request->ip();

        Mail::to($input['email'])->send(new EmailVerificationMail($edata));

        return response()->json(['success' => 1, 'message' => 'Code sent to your mail successfully']);
    }

    public function smsCode(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'phone' => 'required|string|min:11|max:11'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        $fuser = User::where('phoneno', $input['phone'])->first();

        if (!$fuser) {
            return response()->json(['success' => 0, 'message' => 'Kindly use your valid phone']);
        }

        $cr = CodeRequest::where([["mobile", $input['phone']], ['status', 0]])->latest()->first();

        if ($cr) {
            if (Carbon::parse($cr->created_at)->diffInMinutes() < 10) {
                return response()->json(['success' => 1, 'message' => 'Code has been sent to your phone. Kindly check your inbox, promotions or spam']);
            }
        }

        $type = "2fa";

        $code = substr(rand(), 0, 6);

        CodeRequest::create([
            'mobile' => trim($input['phone']),
            'code' => $code,
            'status' => 0,
            'type' => $type
        ]);

        return response()->json(['success' => 1, 'message' => 'Code sent to your phone successfully', 'data' => $code]);
    }


    public function emailToggle(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'email' => 'required|email',
            'code' => 'required|digits:6'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        $fuser = User::where('email', $input['email'])->first();

        if (!$fuser) {
            return response()->json(['success' => 0, 'message' => 'Kindly use your valid email']);
        }

        $cr = CodeRequest::where([["mobile", $input['email']], ['status', 0], ['type', '2fa']])->latest()->first();

        if (!$cr) {
            return response()->json(['success' => 0, 'message' => 'Kindly restart verification process']);
        }

        $max_attempt = 3;

        $cur_attempt = $max_attempt - $cr->attempt;

        if ($cur_attempt <= 0) {
            return response()->json(['success' => 0, 'message' => "Maximum attempt exceeded. Kindly request a new code"]);
        } else {
            $cr->attempt += 1;
            $cr->save();
        }

        if ($cr->code != $input['code']) {
            return response()->json(['success' => 0, 'message' => "Code does not match. You have $cur_attempt attempt left."]);
        } else {
            $cr->status = 1;
            $cr->save();
        }

        $fuser->twofa = $fuser->twofa == 1 ? 0 : 1;
        $fuser->save();

        return response()->json(['success' => 1, 'message' => '2Fa Toggled successfully on Email', 'data' => $fuser->twofa == 1 ? 0 : 1]);
    }

    public function smsToggle(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'phone' => 'required|string|min:11|max:11',
            'code' => 'required|digits:6'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        $fuser = User::where('phoneno', $input['phone'])->first();

        if (!$fuser) {
            return response()->json(['success' => 0, 'message' => 'Kindly use your valid email']);
        }

        $cr = CodeRequest::where([["mobile", $input['phone']], ['status', 0], ['type', '2fa']])->latest()->first();

        if (!$cr) {
            return response()->json(['success' => 0, 'message' => 'Kindly restart verification process']);
        }

        $max_attempt = 3;

        $cur_attempt = $max_attempt - $cr->attempt;

        if ($cur_attempt <= 0) {
            return response()->json(['success' => 0, 'message' => "Maximum attempt exceeded. Kindly request a new code"]);
        } else {
            $cr->attempt += 1;
            $cr->save();
        }

        if ($cr->code != $input['code']) {
            return response()->json(['success' => 0, 'message' => "Code does not match. You have $cur_attempt attempt left."]);
        } else {
            $cr->status = 1;
            $cr->save();
        }

        $fuser->twofa_phone = $fuser->twofa_phone == 1 ? 0 : 1;
        $fuser->save();

        return response()->json(['success' => 1, 'message' => '2Fa Toggled successfully on Phone', 'data' => $fuser->twofa_phone == 1 ? 0 : 1]);
    }
}
