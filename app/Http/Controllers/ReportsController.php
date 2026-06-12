<?php

namespace App\Http\Controllers;

use App\Models\PndL;
use App\Models\Transaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

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

        $data['user_count'] = User::where('reg_date', 'LIKE', '%' . $date . '%')->count();

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

        $data['user_count'] = User::where('reg_date', 'LIKE', '%' . $date . '%')->count();

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

    public function earnings(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);

        $incomeByGl = PndL::select('gl', DB::raw('SUM(amount) as total_amount'))
            ->where('type', 'income')
            ->whereBetween('date', [$from, $to])
            ->groupBy('gl')
            ->orderByDesc('total_amount')
            ->get();

        $expenseByGl = PndL::select('gl', DB::raw('SUM(amount) as total_amount'))
            ->where('type', 'expenses')
            ->whereBetween('date', [$from, $to])
            ->groupBy('gl')
            ->orderByDesc('total_amount')
            ->get();

        $incomeSum = (float) PndL::where('type', 'income')->whereBetween('date', [$from, $to])->sum('amount');
        $expenseSum = (float) PndL::where('type', 'expenses')->whereBetween('date', [$from, $to])->sum('amount');

        return view('report_earnings', [
            'from' => $from,
            'to' => $to,
            'incomeByGl' => $incomeByGl,
            'expenseByGl' => $expenseByGl,
            'incomeSum' => $incomeSum,
            'expenseSum' => $expenseSum,
            'profit' => $incomeSum - $expenseSum,
        ]);
    }

    public function earningsPdf(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);

        $incomeByGl = PndL::select('gl', DB::raw('SUM(amount) as total_amount'))
            ->where('type', 'income')
            ->whereBetween('date', [$from, $to])
            ->groupBy('gl')
            ->orderByDesc('total_amount')
            ->get();

        $expenseByGl = PndL::select('gl', DB::raw('SUM(amount) as total_amount'))
            ->where('type', 'expenses')
            ->whereBetween('date', [$from, $to])
            ->groupBy('gl')
            ->orderByDesc('total_amount')
            ->get();

        $incomeSum = (float) PndL::where('type', 'income')->whereBetween('date', [$from, $to])->sum('amount');
        $expenseSum = (float) PndL::where('type', 'expenses')->whereBetween('date', [$from, $to])->sum('amount');

        $pdf = Pdf::loadView('pdf_earnings_report', [
            'from' => $from,
            'to' => $to,
            'incomeByGl' => $incomeByGl,
            'expenseByGl' => $expenseByGl,
            'incomeSum' => $incomeSum,
            'expenseSum' => $expenseSum,
            'profit' => $incomeSum - $expenseSum,
        ]);

        return $pdf->stream('earnings_report.pdf');
    }

    public function earningsExcel(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);

        $rows = PndL::whereBetween('date', [$from, $to])
            ->orderBy('date', 'asc')
            ->get(['type', 'gl', 'amount', 'narration', 'date'])
            ->map(function (PndL $row) {
                return [
                    'type' => $row->type,
                    'gl' => $row->gl,
                    'amount' => $row->amount,
                    'narration' => $row->narration,
                    'date' => $row->date,
                ];
            });

        return (new FastExcel($rows))->download('earnings_report.xlsx');
    }

    private function parseDateRange(Request $request): array
    {
        $fromInput = $request->get('from');
        $toInput = $request->get('to');

        if ($fromInput && $toInput) {
            $from = Carbon::parse($fromInput)->startOfDay();
            $to = Carbon::parse($toInput)->endOfDay();
            return [$from, $to];
        }

        $from = Carbon::now()->startOfMonth()->startOfDay();
        $to = Carbon::now()->endOfDay();
        return [$from, $to];
    }
}
