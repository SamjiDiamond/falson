<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionsController extends Controller
{

    public function transactions(Request $request)
    {
        $input = $request->all();
        $date_from = $input['date_from'] ?? '';
        $date_to = $input['date_to'] ?? '';

        $user = Auth::user();
        $trans = Transaction::with('serverlog')->where('user_name', $user->user_name)->OrderBy('id', 'desc')
            ->when(isset($date_from) && $date_from != '' && isset($date_to) && $date_to != '', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at', [Carbon::parse($date_from)->toDateTimeString(), Carbon::parse($date_to)->addDay()->toDateTimeString()]);
            })
            ->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsRecent(Request $request)
    {
        $user = Auth::user();
        $trans = Transaction::with('serverlog')->where('user_name', $user->user_name)->OrderBy('id', 'desc')->limit(10)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsSummary(Request $request)
    {
        $user = Auth::user();

        $input = $request->all();
        $date_from = $input['date_from'] ?? '';
        $date_to = $input['date_to'] ?? '';

        $trans_in = Transaction::where([['user_name', $user->user_name], ['status', 'delivered']])
            ->when(isset($date_from) && $date_from != '' && isset($date_to) && $date_to != '', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at', [Carbon::parse($date_from)->toDateTimeString(), Carbon::parse($date_to)->addDay()->toDateTimeString()]);
            })
            ->sum('amount');
        $trans_out = Transaction::where([['user_name', $user->user_name], ['name', 'wallet funding']])
            ->when(isset($date_from) && $date_from != '' && isset($date_to) && $date_to != '', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at', [Carbon::parse($date_from)->toDateTimeString(), Carbon::parse($date_to)->addDay()->toDateTimeString()]);
            })
            ->sum('amount');

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => ['inflow' => $trans_in, 'outflow' => $trans_out]]);
    }

    public function transactionsPending(Request $request)
    {
        $input = $request->all();
        $date_from = $input['date_from'] ?? '';
        $date_to = $input['date_to'] ?? '';

        $trans = Transaction::with('serverlog')->where([['user_name', Auth::user()->user_name], ['status', 'pending']])->OrderBy('id', 'desc')
            ->when(isset($date_from) && $date_from != '' && isset($date_to) && $date_to != '', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at', [Carbon::parse($date_from)->toDateTimeString(), Carbon::parse($date_to)->addDay()->toDateTimeString()]);
            })
            ->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsReversed(Request $request)
    {
        $input = $request->all();
        $date_from = $input['date_from'] ?? '';
        $date_to = $input['date_to'] ?? '';

        $trans = Transaction::with('serverlog')->where([['user_name', Auth::user()->user_name], ['status', 'reversed']])->OrderBy('id', 'desc')
            ->when(isset($date_from) && $date_from != '' && isset($date_to) && $date_to != '', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at', [Carbon::parse($date_from)->toDateTimeString(), Carbon::parse($date_to)->addDay()->toDateTimeString()]);
            })
            ->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsSuccess(Request $request)
    {
        $input = $request->all();
        $date_from = $input['date_from'] ?? '';
        $date_to = $input['date_to'] ?? '';

        $trans = Transaction::with('serverlog')->where([['user_name', Auth::user()->user_name], ['status', 'delivered']])->OrderBy('id', 'desc')
            ->when(isset($date_from) && $date_from != '' && isset($date_to) && $date_to != '', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at', [Carbon::parse($date_from)->toDateTimeString(), Carbon::parse($date_to)->addDay()->toDateTimeString()]);
            })
            ->limit(100)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsData(Request $request)
    {
        $input = $request->all();
        $date_from = $input['date_from'] ?? '';
        $date_to = $input['date_to'] ?? '';
        $limit = $input['limit'] ?? 100;

        $trans = Transaction::with('serverlog')->where([['user_name', Auth::user()->user_name], ['code', 'LIKE', 'data_%']])
            ->when(isset($date_from) && $date_from != '' && isset($date_to) && $date_to != '', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at', [Carbon::parse($date_from)->toDateTimeString(), Carbon::parse($date_to)->addDay()->toDateTimeString()]);
            })
            ->latest()->limit($limit)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsEpin(Request $request)
    {
        $input = $request->all();
        $date_from = $input['date_from'] ?? '';
        $date_to = $input['date_to'] ?? '';
        $limit = $input['limit'] ?? 100;

        $trans = Transaction::where([['user_name', Auth::user()->user_name], ['code', 'airtimepin']])
            ->when(isset($date_from) && $date_from != '' && isset($date_to) && $date_to != '', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at', [Carbon::parse($date_from)->toDateTimeString(), Carbon::parse($date_to)->addDay()->toDateTimeString()]);
            })
            ->latest()->limit($limit)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsAirtime(Request $request)
    {
        $input = $request->all();
        $date_from = $input['date_from'] ?? '';
        $date_to = $input['date_to'] ?? '';
        $limit = $input['limit'] ?? 100;

        $trans = Transaction::with('serverlog')->where([['user_name', Auth::user()->user_name], ['code', 'airtime']])
            ->when(isset($date_from) && $date_from != '' && isset($date_to) && $date_to != '', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at', [Carbon::parse($date_from)->toDateTimeString(), Carbon::parse($date_to)->addDay()->toDateTimeString()]);
            })
            ->latest()->limit($limit)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsTv(Request $request)
    {
        $input = $request->all();
        $date_from = $input['date_from'] ?? '';
        $date_to = $input['date_to'] ?? '';
        $limit = $input['limit'] ?? 100;

        $trans = Transaction::with('serverlog')->where([['user_name', Auth::user()->user_name], ['code', 'LIKE', 'tv_%']])
            ->when(isset($date_from) && $date_from != '' && isset($date_to) && $date_to != '', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at', [Carbon::parse($date_from)->toDateTimeString(), Carbon::parse($date_to)->addDay()->toDateTimeString()]);
            })
            ->latest()->limit($limit)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsElectricity(Request $request)
    {
        $input = $request->all();
        $date_from = $input['date_from'] ?? '';
        $date_to = $input['date_to'] ?? '';
        $limit = $input['limit'] ?? 100;

        $trans = Transaction::with('serverlog')->where([['user_name', Auth::user()->user_name], ['code', 'LIKE', 'electricity_%']])
            ->when(isset($date_from) && $date_from != '' && isset($date_to) && $date_to != '', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at', [Carbon::parse($date_from)->toDateTimeString(), Carbon::parse($date_to)->addDay()->toDateTimeString()]);
            })
            ->latest()->limit($limit)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsEducation(Request $request)
    {
        $input = $request->all();
        $date_from = $input['date_from'] ?? '';
        $date_to = $input['date_to'] ?? '';
        $limit = $input['limit'] ?? 100;

        $trans = Transaction::with('serverlog')->where([['user_name', Auth::user()->user_name], ['code', 'rch']])
            ->when(isset($date_from) && $date_from != '' && isset($date_to) && $date_to != '', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at', [Carbon::parse($date_from)->toDateTimeString(), Carbon::parse($date_to)->addDay()->toDateTimeString()]);
            })
            ->latest()->limit($limit)->get();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function transactionsFunding(Request $request)
    {
        $input = $request->all();
        $date_from = $input['date_from'] ?? '';
        $date_to = $input['date_to'] ?? '';

        $trans = Transaction::where([['user_name', Auth::user()->user_name], ['code', 'LIKE', 'fund_%']])
//            ->with('paylonyFunding')
            ->when(isset($date_from) && $date_from != '' && isset($date_to) && $date_to != '', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at', [Carbon::parse($date_from)->toDateTimeString(), Carbon::parse($date_to)->addDay()->toDateTimeString()]);
            })
            ->latest()->limit(100)->get();

        // Manually attach the paylonyFunding data to each transaction
        $trans->each(function ($transaction) {
            $ttt = new Transaction();
            $transaction->paylonyFunding = $ttt->paylonyFunding($transaction->ref);
        });

        $trans->each(function ($transaction) {
            $ttt = new Transaction();
            $transaction->monnifyFunding = $ttt->monnifyFunding($transaction->ref);
        });

        $trans->each(function ($transaction) {
            $ttt = new Transaction();
            $transaction->budpayFunding = $ttt->budpayFunding($transaction->ref);
        });


        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function commissions()
    {
        $user = Auth::user();
        $trans = Transaction::where([['user_name', $user->user_name], ['name', 'Commission']])->OrderBy('id', 'desc')->simplepaginate();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }

    public function bonus()
    {
        $user = Auth::user();
        $trans = Transaction::where([['user_name', $user->user_name], ['name', 'Bonus']])->OrderBy('id', 'desc')->simplepaginate();

        return response()->json(['success' => 1, 'message' => 'Fetched successfully', 'data' => $trans]);
    }
}
