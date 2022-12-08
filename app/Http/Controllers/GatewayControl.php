<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GatewayControl
{
    public function gateway(Request $request)
    {
        $data = Settings::where('name','LIKE', 'fund_%' )->get();
        $i=1;

        return view('payment_gateway', compact('data', 'i'));
    }

    public function editgateway(Request $request)
    {
        $data = Settings::find($request->id);

        if(!$data){
            return back()->with('error', 'Kindly choose correct plan. Kindly check and try again');
        }

        return view('paymentgateway_edit', compact('data'));
    }

    public function updategateway(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id'      => 'required',
            'value'      => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }

        $data = Settings::find($input['id']);

        if(!$data){
            return back()->with('error', 'Kindly choose correct plan. Kindly check and try again');
        }


        $data->value = $input['value'];
        $data->save();

        return redirect()->route('paymentgateway')->with('Value has been updated successfully');
    }
}
