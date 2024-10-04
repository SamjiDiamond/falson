<?php

namespace App\Http\Controllers;

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

    public function dataPlans($network)
    {

        $data = ResellerDataPlans::where('network', strtoupper($network))->paginate(10);
        $sme = ResellerDataPlans::where([['product_code', 'SME'], ['network', strtoupper($network)], ['status', 1]])->count() > 0 ? 1 : 0;
        $sme2 = ResellerDataPlans::where([['product_code', 'SME2'], ['network', strtoupper($network)], ['status', 1]])->count() > 0 ? 1 : 0;
        $cg = ResellerDataPlans::where([['product_code', 'CG'], ['network', strtoupper($network)], ['status', 1]])->count() > 0 ? 1 : 0;
        $dg = ResellerDataPlans::where([['product_code', 'DG'], ['network', strtoupper($network)], ['status', 1]])->count() > 0 ? 1 : 0;
        $dt = ResellerDataPlans::where([['product_code', 'DATA TRANSFER'], ['network', strtoupper($network)], ['status', 1]])->count() > 0 ? 1 : 0;
        $dc = ResellerDataPlans::where([['product_code', 'DATA COUPONS'], ['network', strtoupper($network)], ['status', 1]])->count() > 0 ? 1 : 0;
        $all = ResellerDataPlans::where([['network', strtoupper($network)], ['status', 1]])->count() > 0 ? 1 : 0;

        $server = 0;
        return view('reseller_control.datacontrol', compact('data', 'sme', 'sme2', 'cg', 'dg', 'dt', 'dc', 'all', 'server'));
    }

    public function dataPlans2($network, $server)
    {

        $data = ResellerDataPlans::where([['network', strtoupper($network)], ['server', $server]])->paginate(10);
        $sme = ResellerDataPlans::where([['product_code', 'SME'], ['network', strtoupper($network)], ['server', $server], ['status', 1]])->count() > 0 ? 1 : 0;
        $sme2 = ResellerDataPlans::where([['product_code', 'SME2'], ['network', strtoupper($network)], ['server', $server], ['status', 1]])->count() > 0 ? 1 : 0;
        $cg = ResellerDataPlans::where([['product_code', 'CG'], ['network', strtoupper($network)], ['server', $server], ['status', 1]])->count() > 0 ? 1 : 0;
        $dg = ResellerDataPlans::where([['product_code', 'DG'], ['network', strtoupper($network)], ['server', $server], ['status', 1]])->count() > 0 ? 1 : 0;
        $dt = ResellerDataPlans::where([['product_code', 'DATA TRANSFER'], ['network', strtoupper($network)], ['server', $server], ['status', 1]])->count() > 0 ? 1 : 0;
        $dc = ResellerDataPlans::where([['product_code', 'DATA COUPONS'], ['network', strtoupper($network)], ['server', $server], ['status', 1]])->count() > 0 ? 1 : 0;
        $all = ResellerDataPlans::where([['network', strtoupper($network)], ['status', 1]])->count() > 0 ? 1 : 0;

        return view('reseller_control.datacontrol', compact('data', 'sme', 'sme2', 'cg', 'dg', 'dt', 'dc', 'all', 'server'));
    }

    public function datanew(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'network' => 'required',
            'name' => 'required',
            'price' => 'required',
            'pricing' => 'required',
            'code' => 'required|unique:tbl_reseller_dataplans',
            'plan_id' => 'sometimes',
            'product_code' => 'sometimes',
            'server' => 'required',
            'allowance' => 'required|numeric',
            'note' => 'sometimes'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return redirect()->route('reseller.datanew')->withInput($input)->with('error', implode(",", $validator->errors()->all()));
        }

        $input['level1'] = $input['pricing'];
        $input['level2'] = $input['pricing'];
        $input['level3'] = $input['pricing'];
        $input['level4'] = $input['pricing'];
        $input['level5'] = $input['pricing'];
        $input['status'] = 1;
        $input['type'] = $input['allowance'];

        ResellerDataPlans::create($input);

        return redirect()->route('reseller.datanew')->with('success', 'Data Plan created successfully');
    }


    public function dataserveMultipleedit($network, $type, $status,$server)
    {
        if($type == "ALL"){
            if($server == 0) {
                ResellerDataPlans::where([['network', strtoupper($network)]])->update(['status' => $status]);
            }else{
                ResellerDataPlans::where([['network', strtoupper($network)], ['server', $server]])->update(['status' => $status]);
            }
        }else{
            if($server == 0) {
                ResellerDataPlans::where([['product_code', $type], ['network', strtoupper($network)]])->update(['status' => $status]);
            }else{
                ResellerDataPlans::where([['product_code', $type], ['network', strtoupper($network)], ['server', $server]])->update(['status' => $status]);
            }
        }

        if($server == 0) {
            return redirect()->route('reseller.dataList', $network)->with("success", "$type Status Modified successfully");
        }else{
            return redirect()->route('reseller.server_dataList', [$network, $server])->with("success", "$type Status Modified successfully");
        }
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

        return redirect()->route('reseller.dataList', $data->type)->with('success', $data->name . ' has been updated successfully');
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

    public function tvDiscount(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'level1' => 'required',
            'level2' => 'required',
            'level3' => 'required',
            'level4' => 'required',
            'level5' => 'required',
            'type' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }

        ResellerCableTV::where('type', strtolower($request->type))->update([
            'level1' => $input['level1'] . '%',
            'level2' => $input['level2'] . '%',
            'level3' => $input['level3'] . '%',
            'level4' => $input['level4'] . '%',
            'level5' => $input['level5'] . '%',
        ]);

        return redirect()->route('reseller.tvcontrol')->with('success', $request->type . ' discount has been updated successfully');
    }

    public function electricityserver()
    {
        $data = ResellerElecticity::get();

        return view('reseller_control.electricitycontrol', compact('data'));
    }

    public function electricityEdit($id)
    {
        $data = ResellerElecticity::find($id);

        if(!$data){
            return redirect()->route('electricitycontrol')->with('error', 'Electricity does not exist');
        }

        return view('reseller_control.electricitycontrol_edit', compact('data'));
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


        $data = ResellerElecticity::where('id', $request->id)->first();
        if(!$data){
            return back()->with('error', 'Kindly choose correct plan. Kindly check and try again');
        }

        $data->status = $input['status'];
        $data->server = $input['server'];
        $data->discount = $input['discount'];
        $data->save();

        return redirect()->route('reseller.electricitycontrol')->with('success', $data->name . ' has been updated successfully');
    }

}
