<?php

namespace App\Http\Controllers;

use App\Models\PndL;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    function pnl(Request $request)
    {
        if (!isset($request->date)) {
            $date = Carbon::now()->format("Y-m");
        } else {
            $date = Carbon::parse($request->date)->format("Y-m");
        }
        $data['income'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'income']])->get();
        $data['incomed'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'income']])->distinct('gl')->select('gl')->get();
        $data['income_sum'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'income']])->sum('amount');
        $data['expenses'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'expenses']])->get();
        $data['expensed'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'expenses']])->distinct('gl')->select('gl')->get();
        $data['expense_sum'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'expenses']])->sum('amount');
        $data['ti'] = 0;
        $data['te'] = 0;
        $data['date'] = $date;

        return view('report_pnl', $data);
    }

    function yearly(Request $request)
    {
        if (!isset($request->date)) {
            $date = Carbon::now()->format("Y");
        } else {
            $date = Carbon::parse($request->date)->format("Y");
        }

        $data['data'] = Transaction::where([['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['data_amount'] = Transaction::where([['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');
        $data['airtime'] = Transaction::where([['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['airtime_amount'] = Transaction::where([['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');
        $data['tv'] = Transaction::where([['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['tv_amount'] = Transaction::where([['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');
        $data['electricity'] = Transaction::where([['code', 'like', '%electricity%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['electricity_amount'] = Transaction::where([['code', 'like', '%electricity%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');
        $data['funding_charges'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['gl', 'funding_charges']])->count();
        $data['funding_charges_amount'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['gl', 'funding_charges']])->sum('amount');

        $data['expensed'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'expenses']])->distinct('gl')->select('gl')->get();
        $data['expense_sum'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'expenses']])->sum('amount');
        $data['te'] = 0;

        $data['date'] = $date;

        return view('report_yearly', $data);
    }

    function monthly(Request $request)
    {
        if (!isset($request->date)) {
            $date = Carbon::now()->format("Y-m");
        } else {
            $date = Carbon::parse($request->date)->format("Y-m");
        }

        $data['data'] = Transaction::where([['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['data_amount'] = Transaction::where([['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');
        $data['airtime'] = Transaction::where([['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['airtime_amount'] = Transaction::where([['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');
        $data['tv'] = Transaction::where([['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['tv_amount'] = Transaction::where([['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');
        $data['electricity'] = Transaction::where([['code', 'like', '%electricity%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['electricity_amount'] = Transaction::where([['code', 'like', '%electricity%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');
        $data['funding_charges'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['gl', 'funding_charges']])->count();
        $data['funding_charges_amount'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['gl', 'funding_charges']])->sum('amount');

        $data['expensed'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'expenses']])->distinct('gl')->select('gl')->get();
        $data['expense_sum'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'expenses']])->sum('amount');
        $data['te'] = 0;

        $data['date'] = $date;

        return view('report_monthly', $data);
    }

    function daily(Request $request)
    {
        if (!isset($request->date)) {
            $date = Carbon::now()->format("Y-m-d");
        } else {
            $date = Carbon::parse($request->date)->format("Y-m-d");
        }

        $data['data'] = Transaction::where([['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['data_amount'] = Transaction::where([['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');
        $data['airtime'] = Transaction::where([['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['airtime_amount'] = Transaction::where([['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');
        $data['tv'] = Transaction::where([['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['tv_amount'] = Transaction::where([['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');
        $data['electricity'] = Transaction::where([['code', 'like', '%electricity%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->count();
        $data['electricity_amount'] = Transaction::where([['code', 'like', '%electricity%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $date . '%']])->sum('amount');
        $data['funding_charges'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['gl', 'funding_charges']])->count();
        $data['funding_charges_amount'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['gl', 'funding_charges']])->sum('amount');

        $data['expensed'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'expenses']])->distinct('gl')->select('gl')->get();
        $data['expense_sum'] = PndL::where([['date', 'LIKE', '%' . $date . '%'], ['type', 'expenses']])->sum('amount');
        $data['te'] = 0;

        $data['date'] = $date;

        return view('report_daily', $data);
    }
}
