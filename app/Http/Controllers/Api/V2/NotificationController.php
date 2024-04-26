<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Mail\AccountStatementEmail;
use App\Models\Transaction;
use App\Notifications\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return response()->json(['success' => 0, 'message' => implode(",", $validator->errors()->all())]);
        }

        $date_from = $input['from'] ?? '';
        $date_to = $input['to'] ?? '';


        $trans = Transaction::where('user_name', Auth::user()->user_name)->OrderBy('id', 'desc')
            ->when(isset($date_from) && $date_from != '' && isset($date_to) && $date_to != '', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at', [Carbon::parse($date_from)->toDateTimeString(), Carbon::parse($date_to)->addDay()->toDateTimeString()]);
            })
            ->limit(100)->get();

        $filePath = storage_path('app/statement_' . time() . '.xlsx');

        (new FastExcel($trans))->export($filePath, function ($data) {
            return [
                'Name' => $data->name,
                'Amount' => $data->amount,
                'Status' => strtoupper($data->status),
                'Reference' => $data->ref,
                'Description' => $data->description,
                'Date' => $data->date,
                'IP Address' => $data->ip_address,
                'Prev Balance' => $data->i_wallet,
                'New Balance' => $data->f_wallet,
            ];
        });

        $customer = Auth::user();

        Mail::to('odejinmisamuel@gmail.com')->send(new AccountStatementEmail($customer, $filePath));

        return response()->json(['success' => 1, 'message' => 'Action completed successfully. You will receive an email soon.']);
    }
}
