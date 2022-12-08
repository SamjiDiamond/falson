<?php

namespace App\Http\Controllers;

use App\Models\CGBundle;
use App\Models\CGTransaction;
use App\Models\CGWallets;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CGBundleController extends Controller
{
    public function index(){
        $data=CGBundle::get();
        return view('cg_bundle_add', ['data' => $data]);
    }

    public function create(Request $request){
        $input = $request->all();
        $rules = array(
            'display_name' => 'required',
            'value'      => 'required',
            'network' => 'required',
            'type' => 'required',
            'price' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return redirect()->route('cgbundle.index')->with('error', 'Incomplete request. Kindly check and try again');
        }

        CGBundle::create([
            "display_name"=>$input['display_name'],
            "value"=>$input['value'],
            "network"=>$input['network'],
            "type"=>$input['type'],
            "price"=>$input['price'],
            "created_by"=>Auth::user()->user_name,
        ]);

        return redirect()->route('cgbundle.list')->with(["success" => "Bundle created successfully"]);
    }

    public function update(Request $request){
        $input = $request->all();
        $rules = array(
            'id' => 'required',
            'display_name' => 'required',
            'value'      => 'required',
            'network' => 'required',
            'type' => 'required',
            'price' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return redirect()->route('cgbundle.edit', $input['id'])->with('error', 'Incomplete request. Kindly check and try again');
        }

        $fcg=CGBundle::find($input['id']);

        if(!$fcg){
            return redirect()->route('cgbundle.edit',$input['id'])->with(["error" => "Bundle not found"]);
        }

        $fcg->display_name=$input['display_name'];
        $fcg->value=$input['value'];
        $fcg->network=$input['network'];
        $fcg->type=$input['type'];
        $fcg->price=$input['price'];
        $fcg->save();

        return redirect()->route('cgbundle.list')->with(["success" => "Bundle updated successfully"]);
    }

    public function lists(){
        $data=CGBundle::latest()->get();
        return view('cg_bundles', ['data' => $data]);
    }

    public function cgtrans(){
        $data=CGTransaction::with('cgbundle')->latest()->get();
        return view('cg_transactions', ['data' => $data]);
    }

    public function modify($id){
        $data=CGBundle::find($id);
        if(!$data){
            return redirect()->route('cgbundle.list')->with(["error" => "Bundle not found"]);
        }

        $data->status=$data->status== 1 ? 0 : 1;
        $data->save();

        return redirect()->route('cgbundle.list')->with(["success" => "Bundle modified successfully"]);
    }

    public function edit($id){
        $data=CGBundle::find($id);
        if(!$data){
            return redirect()->route('cgbundle.list')->with(["error" => "Bundle not found"]);
        }

        return view('cg_bundle_edit', ['data' => $data]);
    }

    public function applyView(){
        $data=CGBundle::where("status", "1")->get();
        $trans=CGTransaction::with('cgbundle')->latest()->limit(10)->get();
        return view('cg_bundle_apply', ['data' => $data, 'trans'=>$trans]);
    }


    public function apply(Request $request){
        $input = $request->all();
        $rules = array(
            'user_name' => 'required',
            'bundle_id'      => 'required',
            'charge' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return redirect()->route('cgbundle.apply')->with('error', 'Incomplete request. Kindly check and try again');
        }

        $data=CGBundle::find($input['bundle_id']);
        if(!$data){
            return redirect()->route('cgbundle.apply')->with(["error" => "Bundle not found"]);
        }

        $user=User::where("user_name", $input['user_name'])->orWhere("phoneno", $input['user_name'])->orWhere("email", $input['user_name'])->first();
        if(!$user){
            return redirect()->route('cgbundle.apply')->with(["error" => "User not found"]);
        }


        if($input['charge'] == "yes"){
            $bal=$user->wallet;

            if($data->price > $bal){
                return redirect()->route('cgbundle.apply')->with(["error" => "Insufficient balance on customer wallet"]);
            }
        }

        $cw=$data->network." ".$data->type;

        $cgwallet=CGWallets::where(["name" => $cw, "user_id" => $user->id])->first();

        if(!$cgwallet){
            return redirect()->route('cgbundle.apply')->with(["error" => "Customer does not have this data wallet"]);
        }

        if($input['charge'] == "yes"){
            $bal=$user->wallet;

            $newBal= $bal - $data->price;

            $tr['name'] = "CG Bundle";
            $tr['user_name'] = $user->user_name;
            $tr['description'] =$data->value . "GB NGN".$data->price. " - ".$data->network. " ".$data->type. " by admin";
            $tr['code'] = "cgbundle";
            $tr['amount'] = $data->price;
            $tr['status'] = "successful";
            $tr['i_wallet'] = $bal;
            $tr['f_wallet'] = $newBal;
            $tr['extra'] = Auth::user()->user_name;
            Transaction::create($tr);

            $user->wallet=$newBal;
            $user->save();
        }


        $cgwallet->balance+=$data->value;
        $cgwallet->save();

        CGTransaction::create([
            "bundle_id" => $input['bundle_id'],
            "user_name" => $user->user_name,
            "charge" => $input['charge'],
            "created_by" => Auth::user()->user_name
        ]);

        return redirect()->route('cgbundle.apply')->with(["success" => "Bundle credited successfully"]);
    }

    public function apply_credit($id){
       $cgtran=CGTransaction::find($id);

       if(!$cgtran){
           return redirect()->route('cgbundle.trans')->with(["error" => "Transaction not found"]);
       }

       if($cgtran->status == 1){
           return redirect()->route('cgbundle.trans')->with(["error" => "Transaction processed already"]);
       }

        $data=CGBundle::find($cgtran->bundle_id);
        if(!$data){
            return redirect()->route('cgbundle.trans')->with(["error" => "Bundle not found"]);
        }

        $user=User::where("user_name", $cgtran->user_name)->orWhere("phoneno", $cgtran->user_name)->orWhere("email", $cgtran->user_name)->first();
        if(!$user){
            return redirect()->route('cgbundle.trans')->with(["error" => "User not found"]);
        }


        $cw=$data->network." ".$data->type;

        $cgwallet=CGWallets::where(["name" => $cw, "user_id" => $user->id])->first();

        if(!$cgwallet){
            return redirect()->route('cgbundle.trans')->with(["error" => "Customer does not have this data wallet"]);
        }

        $cgwallet->balance+=$data->value;
        $cgwallet->save();

        $cgtran->status=1;
        $cgtran->save();

        return redirect()->route('cgbundle.trans')->with(["success" => "Bundle credited successfully"]);
    }

}
