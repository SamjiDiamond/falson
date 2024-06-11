<?php

namespace App\Http\Controllers;

use App\Models\airtimeserver;
use App\Models\AppAirtimeControl;
use App\Models\AppCableTVControl;
use App\Models\AppDataControl;
use App\Models\AppOtherServices;
use App\Models\dataserver;
use App\Models\ResellerElecticity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ServerController
{
    public function airtime(Request $request)
    {
        $data = AppAirtimeControl::get();

        return view('airtimecontrol', compact('data'));
    }

    public function airtimeEdit($id)
    {
        $data = AppAirtimeControl::find($id);

        if(!$data){
            return redirect()->route('airtimecontrol')->with('error', 'Network does not exist');
        }

        return view('airtimecontrol_edit', compact('data'));
    }


    public function airtimecontrolED($id)
    {
        $data = AppAirtimeControl::find($id);

        if(!$data){
            return redirect()->route('airtimecontrol')->with('error', 'Plan does not exist');
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
            'discount'      => 'required',
            'server' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }


        $data = AppAirtimeControl::where('id', $request->id)->first();
        if(!$data){
            return back()->with('error', 'Kindly choose correct plan. Kindly check and try again');
        }
        $data->discount = $input['discount'];
//        $data->status = $input['status'];
        $data->server = $input['server'];
        $data->save();

        return redirect()->route('airtimecontrol')->with('success', $data->network . ' has been updated successfully');
    }

    public function changeserver(Request $request)
    {
        $airtime = airtimeserver::where('name', 'airtime')->first();


        if ($request->network == "mtn") {

            $airtime->mtn = $request->number;
            $airtime->save();
        }
        if ($request->network == "glo") {

            $airtime->glo = $request->number;
            $airtime->save();
        }
        if ($request->network == "airtel") {

            $airtime->airtel = $request->number;
            $airtime->save();
        }
        if ($request->network == "etisalat") {

            $airtime->etisalat = $request->number;
            $airtime->save();
        }
        $success = $request->network . " Server Change To Server " . $request->number;

        return view('servercontrol', compact('airtime', 'success'));


    }

    public function dataserve2($network)
    {
        $data = dataserver::where('network', 'LIKE', strtoupper($network) . '%')->paginate(10);
        $sme = dataserver::where([['name', 'LIKE', '%SME%'], ['name', 'NOT LIKE', '%SME2%'], ['network', 'LIKE', strtoupper($network) . '%'], ['status', 1]])->count() > 0 ? 1 : 0;
        $sme2 = dataserver::where([['name', 'LIKE', '%SME2%'], ['network', 'LIKE', strtoupper($network) . '%'], ['status', 1]])->count() > 0 ? 1 : 0;
        $cg = dataserver::where([['name', 'LIKE', '%CG%'], ['network', 'LIKE', strtoupper($network) . '%'], ['status', 1]])->count() > 0 ? 1 : 0;
        $dg = dataserver::where([['name', 'LIKE', '%DG%'], ['network', 'LIKE', strtoupper($network) . '%'], ['status', 1]])->count() > 0 ? 1 : 0;
        $all = dataserver::where([['network', 'LIKE', strtoupper($network) . '%'], ['status', 1]])->count() > 0 ? 1 : 0;

        $server = 0;
        return view('datacontrol', compact('data', 'sme', 'sme2', 'cg', 'dg', 'all', 'server'));
    }

    public function dataserve3($network, $server)
    {
        $data = dataserver::where([['network', 'LIKE', strtoupper($network) . '%'], ['server', $server]])->paginate(10);
        $sme = dataserver::where([['name', 'LIKE', '%SME%'], ['name', 'NOT LIKE', '%SME2%'], ['network', 'LIKE', strtoupper($network) . '%'], ['server', $server], ['status', 1]])->count() > 0 ? 1 : 0;
        $sme2 = dataserver::where([['name', 'LIKE', '%SME2%'], ['network', 'LIKE', strtoupper($network) . '%'], ['server', $server], ['status', 1]])->count() > 0 ? 1 : 0;
        $cg = dataserver::where([['name', 'LIKE', '%CG%'], ['network', 'LIKE', strtoupper($network) . '%'], ['server', $server], ['status', 1]])->count() > 0 ? 1 : 0;
        $dg = dataserver::where([['name', 'LIKE', '%DG%'], ['network', 'LIKE', strtoupper($network) . '%'], ['server', $server], ['status', 1]])->count() > 0 ? 1 : 0;
        $all = dataserver::where([['network', 'LIKE', strtoupper($network) . '%'], ['server', $server], ['status', 1]])->count() > 0 ? 1 : 0;

        return view('datacontrol', compact('data', 'sme', 'sme2', 'cg', 'dg', 'all', 'server'));
    }

    public function dataserveedit($id)
    {
        $data = dataserver::find($id);

        if(!$data){
            return redirect()->route('datacontrol')->with('error', 'Plan does not exist');
        }

        return view('datacontrol_edit', compact('data'));
    }

    public function datanew(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'network'      => 'required',
            'name'      => 'required',
            'price' => 'required',
            'pricing' => 'required',
            'coded' => 'required',
            'server' => 'required',
            'note' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return redirect()->route('datanew')->with('error', 'Incomplete request. Kindly check and try again');
        }

        dataserver::create($input);

        return redirect()->route('datanew')->with('success', 'Data Plan created successfully');
    }

    public function dataserveED($id)
    {
        $data = dataserver::find($id);

        if(!$data){
            return redirect()->route('datacontrol')->with('error', 'Plan does not exist');
        }

        $data->status=$data->status == 1 ? 0 : 1;
        $data->save();

        return redirect()->route('dataplans', $data->network)->with("success", "Status Modified successfully");
    }

    public function dataserveMultipleedit($network, $type, $status, $server)
    {
        if($type == "ALL"){
            if($server == 0) {
                dataserver::where([['network', 'LIKE', strtoupper($network) . '%']])->update(['status' => $status]);
            }else{
                dataserver::where([['network', 'LIKE', strtoupper($network) . '%'], ['server', $server]])->update(['status' => $status]);
            }
        }else{
            if($server == 0) {
                dataserver::where([['name', 'LIKE', '%' . $type . '%'], ['network', 'LIKE', strtoupper($network) . '%']])->update(['status' => $status]);
            }else{
                dataserver::where([['name', 'LIKE', '%' . $type . '%'], ['network', 'LIKE', strtoupper($network) . '%'], ['server', $server]])->update(['status' => $status]);
            }
        }

        if($server == 0){
            return redirect()->route('dataplans', $network)->with("success", "$type Status Modified successfully");
        }else{
            return redirect()->route('server_dataplans', [$network, $server])->with("success", "$type Status Modified successfully");
        }

    }

    public function dataserveUpdate(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id'      => 'required',
            'product_name'      => 'required',
            'provider_price' => 'required',
            'amount' => 'required',
            'status' => 'required',
            'server' => 'required',
            'note' => 'nullable'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }


        $data = AppDataControl::where('id', $request->id)->first();
        if(!$data){
            return back()->with('error', 'Kindly choose correct plan. Kindly check and try again');
        }
        $data->name = $input['product_name'];
        $data->price = $input['provider_price'];
        $data->pricing = $input['amount'];
        $data->status = $input['status'];
        $data->server = $input['server'];
        $data->note = $input['note'];
        $data->save();

        return redirect()->route('dataplans', $data->network)->with('success', $data->name . ' has been updated successfully');
    }


    public function tvserver()
    {
        $data = AppCableTVControl::paginate(30);

        return view('tvcontrol', compact('data'));
    }

    public function tvEdit($id)
    {
        $data = AppCableTVControl::find($id);

        if(!$data){
            return redirect()->route('tvcontrol')->with('error', 'Network does not exist');
        }

        return view('tvcontrol_edit', compact('data'));
    }


    public function tvcontrolED($id)
    {
        $data = AppCableTVControl::find($id);

        if(!$data){
            return redirect()->route('tvcontrol')->with('error', 'Plan does not exist');
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
        $data->name = $input['name'];
        $data->price = $input['price'];
        $data->status = $input['status'];
        $data->server = $input['server'];
        $data->discount = $input['discount'];
        $data->save();

        return redirect()->route('tvcontrol')->with('success', $data->name . ' has been updated successfully');
    }

    public function tvDiscount(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'amount' => 'required',
            'type' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }

        AppCableTVControl::where('type', strtolower($request->type))->update(['discount' => $input['amount'] . '%']);

        return redirect()->route('tvcontrol')->with('success', $request->type . ' discount has been updated successfully');
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


    public function electricityED($id)
    {
        $data = ResellerElecticity::find($id);

        if(!$data){
            return redirect()->route('electricitycontrol')->with('error', 'Plan does not exist');
        }

        $data->status=$data->status == 1 ? 0 : 1;
        $data->save();

        return back()->with("success", "Status Modified successfully");
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

        return redirect()->route('electricitycontrol')->with('success', $data->name . ' has been updated successfully');
    }

    public function userole(Request $request)
    {
        $userlist = User::latest()->select('id', 'user_name')->paginate(50);
        $admins=User::where('status', 'admin')->orWhere('status', 'superadmin')->get();
        $i=1;

        return view('role', compact('userlist', 'admins', 'i'));
    }

    public function updateuserole(Request $request)
    {
        $role = user::where('id', $request->id)->first();

        $role->status = $request->status;
        $role->save();

        return redirect('/role')->with('success', $role->user_name . " role has been change to " . $request->status);
    }

    public function others()
    {
        $data = AppOtherServices::paginate(10);

        return view('other_services', compact('data'));
    }

    public function others_add()
    {
        return view('other_services_add');
    }

    public function othersedit($id)
    {
        $data = AppOtherServices::find($id);

        if(!$data){
            return redirect()->route('otherservices')->with('error', 'Service does not exist');
        }

        return view('other_services_edit', compact('data'));
    }

    public function others_addPost(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'name' => 'required',
            'action' => 'required',
            'image' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }


        $storage=Storage::put('public/sliders', $input['image']);
        $link=Storage::url($storage);
        $input['image']=explode("/", $link)[3];

        AppOtherServices::create($input );

        return redirect()->route('otherservices')->with('success', $input['name'] . ' has been created successfully');
    }

    public function Servicedestroy($id)
    {
        $slider=AppOtherServices::find($id);

        if (!$slider) {
            return back()->with('error', 'Invalid ID provided');
        }

        $slider->delete();

        return redirect()->route('otherservices')->with('success', 'Service removed successfully');
    }

    public function othersUpdate(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id'      => 'required',
            'name' => 'required',
            'status' => 'required',
            'action' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }

        $data = AppOtherServices::where('id', $request->id)->first();
        if(!$data){
            return back()->with('error', 'Kindly choose correct plan. Kindly check and try again');
        }

        $data->name = $input['name'];
        $data->action = $input['action'];
        $data->status = $input['status'];
        $data->save();

        return redirect()->route('otherservices')->with('success', $data->name . ' has been updated successfully');
    }



}
