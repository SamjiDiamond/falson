<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Mail;
use Illuminate\Support\Facades\View;

class Dashboard extends Controller
{
    public function index(Request $request){

    	$price = DB::table('tbl_agents')->max('wallet');

    	return view('home', ['total_user' => $price]);

    }
}
