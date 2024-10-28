<?php

namespace App\Http\Controllers;

use App\Models\RCPricing;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RechargeCardController extends Controller
{
    public function pricing()
    {
        $data = RCPricing::get();

        return view('rechargecard.pricing', compact('data'));
    }

    public function pricingModify($id)
    {
        $data = RCPricing::find($id);

        if (!$data) {
            return redirect()->route('rechargecard.pricing')->with('error', 'Invalid Pricing');
        }

        return view('rechargecard.pricing_edit', compact('data'));
    }

    public function pricingUpdate(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id' => 'required',
            'plan' => 'required',
            'amount' => 'required',
            'mtn' => 'required',
            'glo' => 'required',
            'airtel' => 'required',
            'ninemobile' => 'required',
            'status' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }

        RCPricing::where("id", $input['id'])->update([
            "plan" => $input['plan'],
            "amount" => $input['amount'],
            "mtn" => $input['mtn'],
            "glo" => $input['glo'],
            "airtel" => $input['airtel'],
            "ninemobile" => $input['ninemobile'],
            "status" => $input['status'],
        ]);

        return redirect()->route('rechargecard.pricing')->with('success', 'Updated successfully');
    }

    public function payments()
    {
        $data = Transaction::where("code", "rc_setup")->latest()->get();
        $i = 1;

        return view('rechargecard.payments', compact('data', 'i'));
    }

    public function transactions()
    {
        $data = Transaction::where("code", "rc")->latest()->get();
        $i = 1;

        return view('rechargecard.transactions', compact('data', 'i'));
    }


}
