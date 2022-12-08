<?php

namespace App\Http\Controllers\Reseller;

use App\Models\User;
use Illuminate\Http\Request;

class BlockReseller
{
    public function listreseller(Request $request)
    {
        $reseller = user::where('status', 'client')->paginate(10);

        return view('seller', compact('reseller'));


    }

    public function updatereseller(Request $request)
    {

        $reseller = user::where('id', $request->id)->first();

        if ($reseller->fraud == "") {
            $give = "fraud";
        } else {
            $give = "";
        }
        $reseller->fraud = $give;
        $reseller->save();

        return redirect('/seller')->with('success', 'reseller updated ');


    }

    public function apireseller(Request $request)
    {


        $reseller = user::where('id', $request->id)->first();

        if ($reseller->fraud != "") {
            $reseller = user::where('status', 'client')->paginate(10);

            $status = "Kindly Enable the Api-key before generating key";
            return view('/seller', compact('status', 'reseller'));

        }
        $key = uniqid('falson_key', true);

        $reseller->api_key = $key;
        $reseller->save();

        return redirect('/seller')->with('success', 'reseller updated ');


    }

}
