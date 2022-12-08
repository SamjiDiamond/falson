<?php

namespace App\Http\Controllers;

use App\Models\GiveAway;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\VirtualAccountClient;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        $today = Carbon::now()->format('Y-m-d');
//        $data['today_user'] = User::where('reg_date', 'LIKE', '%' . $today . '%')->count();

        $data['today_deposits'] = Transaction::where([['name', '=', 'wallet funding'], ['date', 'LIKE', '%' . $today . '%']])->sum('amount');

//        $data['today_transaction'] = Transaction::where('date', 'LIKE', '%' . $today . '%')->count();
//        $data['yesterday_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDay()->format('Y-m-d') . '%')->count();
//        $data['d2_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDays(2)->format('Y-m-d') . '%')->count();
//        $data['d3_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDays(3)->format('Y-m-d') . '%')->count();
//        $data['d4_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDays(4)->format('Y-m-d') . '%')->count();
//        $data['d5_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDays(5)->format('Y-m-d') . '%')->count();
//        $data['d6_transaction'] = Transaction::where('date', 'LIKE', '%' . Carbon::now()->subDays(6)->format('Y-m-d') . '%')->count();

//        $data['users'] = User::orderBy('id', 'DESC')->limit(15)->get();

        $data['data'] = Transaction::where([['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['airtime'] = Transaction::where([['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['tv'] = Transaction::where([['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['betting'] = Transaction::where([['code', 'like', '%bet%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['electricity'] = Transaction::where([['code', 'like', '%electricity%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['rch'] = Transaction::where([['code', 'like', '%rch%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['upgrade'] = Transaction::where([['code', 'like', '%aru%'], ['status', 'like', 'successful'], ['date', 'LIKE', $today . '%']])->count();
        $data['airtime2cash'] = Transaction::where([['code', 'like', '%a2b%'], ['status', 'like', 'successful'], ['date', 'LIKE', $today . '%']])->count();
        $data['airtime2wallet'] = Transaction::where([['code', 'like', '%a2w%'], ['status', 'like', 'successful'], ['date', 'LIKE', $today . '%']])->count();
        $data['virtualaccount'] = VirtualAccountClient::where([['created_at', 'LIKE', $today . '%']])->count();
        $data['withdraw'] = Withdraw::where([['created_at', 'LIKE', $today . '%']])->count();
        $data['giveaway'] = GiveAway::where([['created_at', 'LIKE', $today . '%']])->count();

//        $data['p_nd_l'] = PndL::where([['type', '=', 'income'], ['date', 'LIKE', '%' . $today . '%']])->count();
//        $data['allsettings'] = DB::table('tbl_allsettings')->limit(14)->get();
//        $data['general_market'] = DB::table('tbl_allsettings')->where("name", "=", "general_market")->first();

        return view('home', $data);
    }

    public function allsettings(){
        $data=Settings::where('name','min_funding')->orWhere('name','max_funding')->orWhere('name','funding_charges')->orWhere('name','bithday_message')->orWhere('name','disable_resellers')->orWhere('name','live_chat')->orWhere('name','email_note')->orWhere('name','support_email')->orWhere('name','transaction_email_copy')->orWhere('name','reseller_fee')->orWhere('name','reseller_terms')->orWhere('name','biz_verification_price_reseller')->orWhere('name','biz_verification_price_customer')->orWhere('name','data')->orWhere('name','airtime')->orWhere('name','paytv')->orWhere('name','resultchecker')->orWhere('name','rechargecard')->orWhere('name','electricity')->orWhere('name','airtimeconverter')->orWhere('name','support_whatsapp')->orWhere('name','support_call')->orWhere('name','privacy_policy')->get();

        return view('allsettings', ['data' => $data]);
    }

    public function allsettingsEdit($id){
        $data=Settings::find($id);

        return view('allsettings_edit', ['data' => $data]);
    }

    public function allsettingsUpdate(Request $request){
        $input = $request->all();
        $rules = array(
            'id'      => 'required',
            'value'      => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }

        $data=Settings::find($input['id']);
        $data->value=$input['value'];
        $data->save();

        return redirect()->route('allsettings')->with('success', $data->name . ' updated successfully');
    }
}
