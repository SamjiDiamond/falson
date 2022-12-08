<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use DB;

class passwordhash extends Controller
{
       public function update() {
      // Validate the new password length...
      echo Hash::make("samijibaba"); // Hashing passwords
       }

    public function login(Request $request)
    {
        $input = $request->all();

//        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password'], 'location_id' => $input['location_id']])) {
        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password']])) {
            // Authentication passed...
            if(auth()->user()->status!="active"){
                $status=auth()->user()->status;

//                DB::table('audit_trail')->insert(
//                    ['admin_id' => auth()->user()->id, 'subject' => 'User '.$status, 'action' => 'Login', 'type'=> 'Account', 'ip' => $_SERVER['REMOTE_ADDR'], 'device' => $_SERVER['HTTP_USER_AGENT']]
//                );

                $this->guard()->logout();
                $request->session()->invalidate();

                return redirect('/login')->with('inactive', 'User '.$status.', kindly contact support at '.$loc->email);
            }else{
//                DB::table('audit_trail')->insert(
//                    ['admin_id' => auth()->user()->id, 'subject' => 'Login Successfully', 'action' => 'Login', 'type'=> 'Account', 'ip' => $_SERVER['REMOTE_ADDR'], 'device' => $_SERVER['HTTP_USER_AGENT']]
//                );

                return redirect()->intended('dashboard');
            }

        }else{
            return redirect('/login')->with('error', 'These credentials do not match our records!');
        }
    }

}
