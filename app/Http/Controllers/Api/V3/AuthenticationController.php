<?php

namespace App\Http\Controllers\Api\V3;

use App\Events\NewDeviceEvent;
use App\Http\Controllers\Controller;
use App\Jobs\BudpayVirtualAccountJob;
use App\Jobs\CreateCGWalletsJob;
use App\Jobs\CreatePaylonyVirtualAccountJob;
use App\Jobs\LoginAttemptApiFinderJob;
use App\Jobs\ProcessUser2faJob;
use App\Mail\EmailPasswordVerificationMail;
use App\Mail\EmailVerificationMail;
use App\Models\CodeRequest;
use App\Models\LoginAttempt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function signup(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'full_name' => 'required',
            'user_name' => 'required',
            'password' => 'required',
            'phoneno' => 'required',
            'email' => 'required',
            'referral' => 'nullable',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $user_name = $input['user_name'];
        $deviceid = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

        $user = User::where('user_name', $user_name)->get();
        if (!$user->isEmpty()) {
            return response()->json(['success' => 0, 'message' => 'User name already exist']);
        }

        $user = User::where('email', $input['email'])->get();
        if (!$user->isEmpty()) {
            return response()->json(['success' => 0, 'message' => 'Email already exist']);
        }

        $user = User::where('phoneno', $input['phoneno'])->get();
        if (!$user->isEmpty()) {
            return response()->json(['success' => 0, 'message' => 'Phone Number already exist']);
        }

        if (isset($input['referral'])) {
            $user = User::where('user_name', $input['referral'])->get();
            if ($user->isEmpty()) {
                return response()->json(['success' => 0, 'message' => 'Invalid referral ID']);
            }
        }

        //values gotten
        $create["wallet"] = "0";
        $create["status"] = "client";
        $create["level"] = "1";
        $create["target"] = env('NEW_ACCOUNT_MESSAGE');
        $create["user_name"] = $user_name;
        $create["full_name"] = $input["full_name"];
        $create["email"] = $input["email"];
        $create["phoneno"] = $input["phoneno"];
        $create["mcdpassword"] = Hash::make($input["password"]);
        $create["password"] = "";
        $create["referral"] = $input["referral"];
        $create["gnews"] = 'Are you looking forward to spending less money on data subscriptions? Or Pay Electricity bills, and even buy airtime conveniently without moving a finger, you just arrived at the right place';
        $date = date("Y-m-d H:i:s");
        $create["devices"] = $deviceid;
        $create["reg_date"] = Carbon::now();

        if (User::create($create)) {

            $type = "email_verify";

            $code = substr(rand(), 0, 8);

            CodeRequest::create([
                'mobile' => trim($input['email']),
                'code' => $code,
                'status' => 0,
                'type' => $type
            ]);

            $edata['code'] = $code;
            $edata['email'] = $input['email'];
            $edata['ip'] = $request->ip();

            Mail::to($input['email'])->later(now()->addSeconds(2), new EmailVerificationMail($edata));

            return response()->json(['success' => 1, 'message' => 'Account created successfully']);
        } else {

            return response()->json(['success' => 0, 'message' => 'Oops! An error occurred.']);
        }

    }

    public function login(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'password' => 'required'
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {

            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $input['device'] = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];


        $input['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $la = LoginAttempt::create($input);
        $job = (new LoginAttemptApiFinderJob($la->id))
            ->delay(Carbon::now()->addSeconds(1));
        dispatch($job);


        $user = User::where('user_name', trim($input["user_name"]))->orwhere('email', trim($input["user_name"]))->orwhere('phoneno', trim($input["user_name"]))->first();
        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User does not exist']);
        }

        if (!Hash::check($input['password'], $user->mcdpassword)) {
            return response()->json(['success' => 0, 'message' => 'Incorrect password attempt']);
        }

        if ($user->fraud != "" || $user->fraud != null) {
            return response()->json(['success' => 0, 'message' => $user->fraud]);
        }

        if ($user->new_device_otp == 1) {
            if ($user->user_name != "Ebunola") {
                if ($user->devices != $input['device']) {
                    $datas['device'] = $input['device'];
                    $datas['ip'] = $_SERVER['REMOTE_ADDR'];
                    NewDeviceEvent::dispatch($user, $datas);

                    $la->status = "new_device";
                    $la->save();

                    return response()->json(['success' => 2, 'message' => 'Login successfully. Kindly verify your device.', '_links' => ['url' => route('api_newdevice'), 'method' => 'POST', 'payload' => ['user_name, code']]]);
                }
            }
        }

        if ($user->twofa == 1) {
            $datas['device'] = $input['device'];
            $datas['ip'] = $_SERVER['REMOTE_ADDR'];
            ProcessUser2faJob::dispatch($user, "2fa", $datas);

            $la->status = "authorized_2fa";
            $la->save();

            return response()->json(['success' => 3, 'message' => '2FA code sent to your mail successfully. It will expire in 10 minutes', '_links' => ['url' => route('api_2falogin'), 'method' => 'POST', 'payload' => ['user_name, code']]]);
        }

        $la->status = "authorized";
        $la->save();

        $job = (new CreateCGWalletsJob($user->id))
            ->delay(Carbon::now()->addSecond());
        dispatch($job);

//        CreateProvidusAccountJob::dispatch($user->id);
        BudpayVirtualAccountJob::dispatch($user->id);
        CreatePaylonyVirtualAccountJob::dispatch($user->id);

        $user->last_login = Carbon::now();
        $user->save();

        // Revoke all tokens...
        $user->tokens()->delete();

        $token = $user->createToken($input['device'])->plainTextToken;


        return response()->json(['success' => 1, 'message' => 'Login successfully', 'token' => $token, 'balance' => $user->wallet, 'bvn' => $user->bvn != ""]);
    }

    public function login2fa(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'code' => 'required',
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => 'Required field(s) is missing']);
        }

        $input['version'] = $request->header('version');

        $device = $request->header('device') ?? $_SERVER['HTTP_USER_AGENT'];

        $user = User::where('user_name', $input['user_name'])->orWhere('email', $input['user_name'])->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'User does not exist']);
        }

        $type = "2fa";

        $nl = CodeRequest::where([['mobile', $user->email], ['type', $type], ['status', 0]])->latest()->first();


        if (!$nl) {
            return response()->json(['success' => 0, 'message' => 'Kindly login']);
        }

        if ($nl->code != $input['code']) {
            return response()->json(['success' => 0, 'message' => 'Invalid code. Check your mail and try again']);
        }


        if (Carbon::parse($nl->created_at)->diffInMinutes(Carbon::now()) > 10) {
            return response()->json(['success' => 3, 'message' => '2FA expired. Kindly login again']);
        }

        $nl->status = 0;
        $nl->save();

        $user->devices = $device;
        $user->last_login = Carbon::now();
        $user->save();

        // Revoke all tokens...
        $user->tokens()->delete();

        $token = $user->createToken($device)->plainTextToken;

        return response()->json(['success' => 1, 'message' => '2FA Verified Successfully', 'data' => $token, 'balance' => $user->wallet]);
    }

    public function resetpassword(Request $request)
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

        $edata['user_name'] = $user->user_name;
        $edata['code'] = $code;
        $edata['email'] = $input['email'];
        $edata['ip'] = $request->ip();

        Mail::to($input['email'])->later(now()->addSeconds(2), new EmailPasswordVerificationMail($edata));

        return response()->json(['success' => 1, 'message' => 'A new password has been sent to your mail successfully']);
    }

    public function checkresetpassword(Request $request)
    {

        $input = $request->all();
        $rules = array(
            'email' => 'required',
            'code' => 'required'
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

        return response()->json(['success' => 1, 'message' => 'Code is valid']);
    }

    public function completeresetpassword(Request $request)
    {

        $input = $request->all();
        $rules = array(
            'email' => 'required',
            'code' => 'required',
            'password' => 'required'
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

        $user->mcdpassword = Hash::make($input['password']);
        $user->save();

        $code->status = 1;
        $code->save();

        return response()->json(['success' => 1, 'message' => 'Password changed successfully']);
    }


}
