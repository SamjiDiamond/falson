<?php

namespace App\Http\Controllers;

use App\Models\airtimeserver;
use App\Models\AppAirtimeControl;
use App\Models\AppCableTVControl;
use App\Models\AppDataControl;
use App\Models\dataserver;
use App\Models\ResellerAirtimeControl;
use App\Models\ResellerCableTV;
use App\Models\ResellerDataPlans;
use App\Models\ResellerElecticity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResellerServiceController extends Controller
{
    public function airtime(Request $request)
    {
        $data = ResellerAirtimeControl::get();

        return view('reseller_control.airtimecontrol', compact('data'));
    }

    public function airtimeEdit($id)
    {
        $data = ResellerAirtimeControl::find($id);

        if(!$data){
            return redirect()->route('reseller.airtimecontrol')->with('error', 'Network does not exist');
        }

        return view('reseller_control.airtimecontrol_edit', compact('data'));
    }


    public function airtimecontrolED($id)
    {
        $data = ResellerAirtimeControl::find($id);

        if(!$data){
            return redirect()->route('reseller.airtimecontrol')->with('error', 'Plan does not exist');
        }

        $data->status=$data->status == 1 ? 0 : 1;
        $data->save();

        return back()->with("success", "Status Modified successfully");
    }


    public function airtimeUpdate(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id'      => 'required',
            'level1'      => 'required',
            'level2'      => 'required',
            'level3'      => 'required',
            'level4'      => 'required',
            'level5'      => 'required',
            'server' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }


        $data = ResellerAirtimeControl::where('id', $request->id)->first();
        if(!$data){
            return back()->with('error', 'Kindly choose correct plan. Kindly check and try again');
        }
        $data->level1 = $input['level1'];
        $data->level2 = $input['level2'];
        $data->level3 = $input['level3'];
        $data->level4 = $input['level4'];
        $data->level5 = $input['level5'];
        $data->server = $input['server'];
        $data->save();

        return redirect()->route('reseller.airtimecontrol')->with('success', $data->network . ' has been updated successfully');
    }

    public function dataserve2()
    {
        $data = ResellerDataPlans::paginate(10);

        return view('reseller_control.datacontrol', compact('data'));
    }

    public function dataserveedit($id)
    {
        $data = ResellerDataPlans::find($id);

        if(!$data){
            return redirect()->route('reseller.datacontrol')->with('error', 'Plan does not exist');
        }

        return view('reseller_control.datacontrol_edit', compact('data'));
    }

    public function datacontrolED($id)
    {
        $data = ResellerDataPlans::find($id);

        if(!$data){
            return redirect()->route('reseller.datacontrol')->with('error', 'Plan does not exist');
        }

        $data->status=$data->status == 1 ? 0 : 1;
        $data->save();

        return back()->with("success", "Status Modified successfully");
    }

    public function dataserveUpdate(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id'      => 'required',
            'product_name'      => 'required',
            'level1' => 'required',
            'level2' => 'required',
            'level3' => 'required',
            'level4' => 'required',
            'level5' => 'required',
            'server' => 'required',
            'note' => 'nullable'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }


        $data = ResellerDataPlans::where('id', $request->id)->first();
        if(!$data){
            return back()->with('error', 'Kindly choose correct plan. Kindly check and try again');
        }
        $data->name = $input['product_name'];
        $data->level1 = $input['level1'];
        $data->level2 = $input['level2'];
        $data->level3 = $input['level3'];
        $data->level4 = $input['level4'];
        $data->level5 = $input['level5'];
        $data->server = $input['server'];
//        $data->note = $input['note'];
        $data->save();

        return redirect()->route('reseller.dataplans')->with('success', $data->name . ' has been updated successfully');
    }


    public function tvserver()
    {
        $data = ResellerCableTV::paginate(10);

        return view('reseller_control.tvcontrol', compact('data'));
    }

    public function tvEdit($id)
    {
        $data = ResellerCableTV::find($id);

        if(!$data){
            return redirect()->route('reseller.tvcontrol')->with('error', 'Network does not exist');
        }

        return view('reseller_control.tvcontrol_edit', compact('data'));
    }


    public function tvcontrolED($id)
    {
        $data = ResellerCableTV::find($id);

        if(!$data){
            return redirect()->route('reseller.tvcontrol')->with('error', 'Plan does not exist');
        }

        $data->status=$data->status == 1 ? 0 : 1;
        $data->save();

        return back()->with("success", "Status Modified successfully");
    }


    public function tvUpdate(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id'      => 'required',
            'name'      => 'required',
            'price' => 'required',
            'level1' => 'required',
            'level2' => 'required',
            'level3' => 'required',
            'level4' => 'required',
            'level5' => 'required',
            'server' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }


        $data = ResellerCableTV::where('id', $request->id)->first();
        if(!$data){
            return back()->with('error', 'Kindly choose correct plan. Kindly check and try again');
        }
        $data->name = $input['name'];
        $data->amount = $input['price'];
//        $data->status = $input['status'];
        $data->server = $input['server'];
        $data->level1 = $input['level1'];
        $data->level2 = $input['level2'];
        $data->level3 = $input['level3'];
        $data->level4 = $input['level4'];
        $data->level5 = $input['level5'];
        $data->save();

        return redirect()->route('reseller.tvcontrol')->with('success', $data->name . ' has been updated successfully');
    }


    public function electricityserver()
    {
        $data = ResellerElecticity::get();

        return view('electricitycontrol', compact('data'));
    }

    public function electricityEdit($id)
    {
        $data = ResellerElecticity::find($id);

        if(!$data){
            return redirect()->route('electricitycontrol')->with('error', 'Electricity does not exist');
        }

        return view('electricitycontrol_edit', compact('data'));
    }

    public function electricityUpdate(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id'      => 'required',
            'discount' => 'required',
            'status' => 'required',
            'server' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }


        $data = AppCableTVControl::where('id', $request->id)->first();
        if(!$data){
            return back()->with('error', 'Kindly choose correct plan. Kindly check and try again');
        }

        $data->status = $input['status'];
        $data->server = $input['server'];
        $data->discount = $input['discount'];
        $data->save();

        return redirect()->route('electricitycontrol')->with('success', $data->name . ' has been updated successfully');
    }

}
