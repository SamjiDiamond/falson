<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TwofaNotificationMail;
use App\Models\CodeRequest;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    public function login(Request $request)
    {

        $input = $request->all();

//        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password'], 'location_id' => $input['location_id']])) {
        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password']])) {
            // Authentication passed...
            if (auth()->user()->status != "admin" && auth()->user()->status != "staff" && auth()->user()->status != "superadmin") {
                $status = auth()->user()->status;

                DB::table('audits')->insert(
                    ['user_id' => auth()->user()->id, 'user_type' => 'App\Models\User', 'event' => 'login', 'auditable_id' => auth()->user()->id, 'auditable_type' => 'App\Models\User', 'tags' => 'Unauthorized login',  'old_values'=> [], 'new_values'=> [], 'ip_address' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'created_at'=>Carbon::now(), 'updated_at'=>Carbon::now()]
                );

                $this->guard()->logout();
                $request->session()->invalidate();

                return redirect('/login')->with('error', 'User not authorized, kindly contact support');
            }else {
                DB::table('audits')->insert(
                    ['user_id' => auth()->user()->id, 'user_type' => 'App\Models\User', 'event' => 'login', 'auditable_id' => auth()->user()->id, 'auditable_type' => 'App\Models\User', 'tags' => 'Login Successfully', 'old_values' => '[]', 'new_values' => '[]', 'ip_address' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
                );

                $type = "2fa_admin";

                if (!isset($input['otp'])) {
                    $datas['device'] = $_SERVER['HTTP_USER_AGENT'];
                    $datas['ip'] = $_SERVER['REMOTE_ADDR'];
//                    ProcessUser2faJob::dispatchSync(auth()->user(), $type, $datas);


                    $code = substr(rand(), 0, 6);

                    echo $code;

                    $u = auth()->user();

                    CodeRequest::create([
                        'mobile' => trim($u->email),
                        'code' => $code,
                        'status' => 0,
                        'type' => $type
                    ]);

                    $data['user_name'] = $u->user_name;
                    $data['email'] = $u->email;
                    $data['code'] = $code;


                    if (env('APP_ENV') != "local") {
                        Log::info("sending device email");
                        Mail::to($u->email)->send(new TwofaNotificationMail($data));
                    }

                    $this->guard()->logout();
                    $request->session()->invalidate();

                    return redirect('/login')->with(['success' => '2FA sent successfully to your mail. Kindly input it to proceed.', 'otp' => true]);
                }


                $nl = CodeRequest::where([['mobile', $input['email']], ['type', $type], ['status', 0]])->latest()->first();


                if (!$nl) {
                    $this->guard()->logout();
                    $request->session()->invalidate();
                    return redirect('/login')->with(['error' => 'Login error. Kindly start again']);
                }

                if ($nl->code != $input['otp']) {
                    $this->guard()->logout();
                    $request->session()->invalidate();
                    return redirect('/login')->with(['error' => 'Invalid code. Check your mail and try again', 'otp' => true]);
                }


                if (Carbon::parse($nl->created_at)->diffInMinutes(Carbon::now()) > 10) {
                    $this->guard()->logout();
                    $request->session()->invalidate();
                    return redirect('/login')->with(['error' => '2FA expired. Kindly login again',]);
                }

                $nl->status = 0;
                $nl->attempt += 1;
                $nl->save();


                return redirect()->intended('dashboard');
            }

        }else{
//            DB::table('audits')->insert(
//                ['user_id' => '1', 'user_type' => 'App\Models\User', 'event' => 'login', 'auditable_id' => 1, 'auditable_type' => 'App\Models\User', 'tags' => 'Failed Login Attempt with email: ' . $input['email'] , 'old_values'=> '[]', 'new_values'=> '[]',  'ip_address' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'created_at'=>Carbon::now(), 'updated_at'=>Carbon::now()]
//            );

            return redirect('/login')->with('error', 'These credentials do not match our records!');
        }
    }

    use AuthenticatesUsers;


    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'admin_password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return redirect()->intended('dashboard');
        }

        return "not working";
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
