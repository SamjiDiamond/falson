<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionsController extends Controller
{

    public function transactions()
    {
        $user = Auth::user();
        $trans = Transaction::where('user_name', $user->user_name)->OrderBy('id', 'desc')->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsPending()
    {
        $trans = Transaction::where([['user_name', Auth::user()->user_name], ['status', 'pending']])->OrderBy('id', 'desc')->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsReversed()
    {
        $trans = Transaction::where([['user_name', Auth::user()->user_name], ['status', 'reversed']])->OrderBy('id', 'desc')->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsSuccess()
    {
        $trans = Transaction::where([['user_name', Auth::user()->user_name], ['status', 'delivered']])->OrderBy('id', 'desc')->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsData()
    {
        $trans = Transaction::where([['user_name', Auth::user()->user_name], ['code', 'LIKE', 'data_%']])->latest()->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsAirtime()
    {
        $trans = Transaction::where([['user_name', Auth::user()->user_name], ['code', 'LIKE', 'airtime_%']])->latest()->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsTv()
    {
        $trans = Transaction::where([['user_name', Auth::user()->user_name], ['code', 'LIKE', 'tv_%']])->latest()->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsElectricity()
    {
        $trans = Transaction::where([['user_name', Auth::user()->user_name], ['code', 'LIKE', 'electricity_%']])->latest()->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsEducation()
    {
        $trans = Transaction::where([['user_name', Auth::user()->user_name], ['code', 'rch']])->latest()->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function commissions()
    {
        $user = Auth::user();
        $trans = Transaction::where([['user_name', $user->user_name], ['name', 'Commission']])->OrderBy('id', 'desc')->simplepaginate();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }
}
