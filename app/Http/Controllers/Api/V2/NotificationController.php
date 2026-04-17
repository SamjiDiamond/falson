<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateCustomerAccountStatement;
use App\Mail\AccountStatementEmail;
use App\Models\Transaction;
use App\Notifications\UserNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Rap2hpoutre\FastExcel\FastExcel;

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
            'from' => 'required|date',
            'to' => 'required|date',
            'format' => 'required|in:pdf,excel',
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        $input['user_name'] = Auth::user()->user_name;
        $input['user_id'] = Auth::id();

        GenerateCustomerAccountStatement::dispatch($input)->onConnection('database');

        return response()->json(['success' => 1, 'message' => 'Action completed successfully. You will receive an email soon.']);
    }
}
