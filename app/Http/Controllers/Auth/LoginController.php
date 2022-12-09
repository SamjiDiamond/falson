<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            }else{
                DB::table('audits')->insert(
                    ['user_id' => auth()->user()->id, 'user_type' => 'App\Models\User', 'event' => 'login', 'auditable_id' => auth()->user()->id, 'auditable_type' => 'App\Models\User', 'tags' => 'Login Successfully',  'old_values'=> '[]', 'new_values'=> '[]',  'ip_address' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'created_at'=>Carbon::now(), 'updated_at'=>Carbon::now()]
                );

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
