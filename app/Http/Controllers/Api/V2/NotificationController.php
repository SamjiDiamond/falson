<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{

    public function notifications(){
        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data'=>Auth::user()->notifications]);
    }

    public function unreadnotifications(){
        return response()->json(['success' => 1, 'message' => 'Fetch successfully', 'data'=>Auth::user()->unreadNotifications]);
    }

    public function markAsRead(){
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => 1, 'message' => 'Action completed successfully']);
    }


    public function generate(){
        Auth::user()->notify(new UserNotification("This is a test message", "General News"));
        return response()->json(['success' => 1, 'message' => 'Action completed successfully']);
    }

    public function generateAccountStatement(Request $request){
        $input = $request->all();
        $rules = array(
            'from' => 'required',
            'to' => 'required',
        );

        $validator = Validator::make($input, $rules);

        $input = $request->all();

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        return response()->json(['success' => 1, 'message' => 'Action completed successfully. You will receive an email soon.']);
    }
}
