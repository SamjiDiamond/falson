<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\PndL;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{

    function yearly(Request $request)
    {
        if (!isset($request->date)) {
            $date = Carbon::now()->format("Y");
        } else {
            $date = Carbon::parse($request->date)->format("Y");
        }

        $data['data'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['data_amount'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');

        $data['airtime'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['airtime_amount'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');

        $data['tv'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['tv_amount'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');

        $data['electricity'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['code', 'like', '%electricity%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['electricity_amount'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['code', 'like', '%electricity%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');


        $data['date'] = $date;

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $data]);
    }

    function monthly(Request $request)
    {
        if (!isset($request->date)) {
            $date = Carbon::now()->format("Y-m");
        } else {
            $date = Carbon::parse($request->date)->format("Y-m");
        }

        $data['data'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['data_amount'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');

        $data['airtime'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['airtime_amount'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');

        $data['tv'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['tv_amount'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');

        $data['electricity'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['code', 'like', '%electricity%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['electricity_amount'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['code', 'like', '%electricity%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');

        $data['date'] = $date;

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $data]);
    }

    function daily(Request $request)
    {
        if (!isset($request->date)) {
            $date = Carbon::now()->format("Y-m-d");
        } else {
            $date = Carbon::parse($request->date)->format("Y-m-d");
        }

        $data['data'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['data_amount'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');

        $data['airtime'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['airtime_amount'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');

        $data['tv'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['tv_amount'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');

        $data['electricity'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['code', 'like', '%electricity%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['electricity_amount'] = Transaction::where([['user_name', '=', Auth::user()->user_name], ['code', 'like', '%electricity%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');

        $data['date'] = $date;

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $data]);
    }

}
