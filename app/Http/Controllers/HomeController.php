<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
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
        $data['today_user'] = User::where('reg_date', 'LIKE', '%' . $today . '%')->count();

        $data['today_deposits'] = Transaction::where([['name', '=', 'wallet funding'], ['date', 'LIKE', '%' . $today . '%']])->sum('amount');

        $data['data'] = Transaction::where([['name', 'like', '%data%'], ['status', '=', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['airtime'] = Transaction::where([['name', 'like', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['tv'] = Transaction::where([['code', 'like', '%tv%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['betting'] = Transaction::where([['code', 'like', '%bet%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['electricity'] = Transaction::where([['code', 'like', '%electricity%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['rch'] = Transaction::where([['code', 'like', '%rch%'], ['status', 'like', 'delivered'], ['date', 'LIKE', $today . '%']])->count();
        $data['upgrade'] = Transaction::where([['code', 'like', '%aru%'], ['status', 'like', 'successful'], ['date', 'LIKE', $today . '%']])->count();
        $data['airtime2cash'] = Transaction::where([['code', 'like', '%a2b%'], ['status', 'like', 'successful'], ['date', 'LIKE', $today . '%']])->count();
        $data['airtime2wallet'] = Transaction::where([['code', 'like', '%a2w%'], ['status', 'like', 'successful'], ['date', 'LIKE', $today . '%']])->count();


        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('OGDAMS_BASEURL') . 'get/balances',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER =>0,
                CURLOPT_SSL_VERIFYHOST=>false,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('OGDAMS_TOKEN')
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $og = json_decode($response, true);

            Log::info("OGDAMS BALANCE CHECK");
            Log::info($response);

            if(isset($og['data']['msg']['cgAirtel'])){
                $data['ogdams_cgairtel'] = $og['data']['msg']['cgAirtel'];
            }else{
                $data['ogdams_cgairtel'] = "0";
            }

        }catch (\Exception $e){
            $data['ogdams_cgairtel'] = "0";
        }


        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('HW_BASEURL') . 'fetch/balance',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('HW_AUTH')
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $hw = json_decode($response, true);

            $data['hw_bal'] = $hw['data']['balance'];
        }catch (\Exception $e){
            $data['hw_bal'] = "0";
        }


        return view('home', $data);
    }

    public function allsettings(){
        $data = Settings::where('name', 'min_funding')->orWhere('name', 'max_funding')->orWhere('name', 'funding_charges')->orWhere('name', 'bithday_message')->orWhere('name', 'disable_resellers')->orWhere('name', 'live_chat')->orWhere('name', 'email_note')->orWhere('name', 'transaction_email_copy')->orWhere('name', 'reseller_fee')->orWhere('name', 'reseller_terms')->orWhere('name', 'biz_verification_price_reseller')->orWhere('name', 'biz_verification_price_customer')->orWhere('name', 'data')->orWhere('name', 'airtime')->orWhere('name', 'paytv')->orWhere('name', 'resultchecker')->orWhere('name', 'rechargecard')->orWhere('name', 'electricity')->orWhere('name', 'airtimeconverter')->orWhere('name', 'LIKE', 'support_%')->orWhere('name', 'LIKE', 'enable_%')->orWhere('name', 'privacy_policy')->orWhere('name', 'cg_wallet_bank_details')->orWhere('name', 'funding_message')->orWhere('name', 'tv_server')->orWhere('name', 'LIKE', '%_funding_message')->orWhere('name', 'referral_bonus')->orWhere('name', 'referral_bonus_min_funding')->get();

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

    public function plansRefresh($type){

        if($type == "data"){
            Artisan::queue('samji:hw --command=data');
            Artisan::queue('samji:iyii --command=data');
            Artisan::queue('samji:ogdams --command=data');
            Artisan::queue('samji:uzobest --command=data');
        }elseif($type == "data_hw"){
            Artisan::queue('samji:hw --command=data');
        }elseif($type == "data_iyii"){
            Artisan::queue('samji:iyii --command=data');
        }elseif($type == "data_iyii_mtn"){
            Artisan::queue('samji:iyii --command=data --type=MTN');
        }elseif($type == "data_iyii_glo"){
            Artisan::queue('samji:iyii --command=data --type=GLO');
        }elseif($type == "data_iyii_airtel"){
            Artisan::queue('samji:iyii --command=data --type=AIRTEL');
        }elseif($type == "data_iyii_9mobile"){
            Artisan::queue('samji:iyii --command=data --type=9MOBILE');
        }elseif($type == "data_ogdams"){
            Artisan::queue('samji:ogdams --command=data');
        }elseif($type == "data_ogdams_mtn"){
            Artisan::queue('samji:ogdams --command=data --type=MTN');
        }elseif($type == "data_ogdams_glo"){
            Artisan::queue('samji:ogdams --command=data --type=GLO');
        }elseif($type == "data_ogdams_airtel"){
            Artisan::queue('samji:ogdams --command=data --type=AIRTEL');
        }elseif($type == "data_ogdams_9mobile"){
            Artisan::queue('samji:ogdams --command=data --type=9MOBILE');
        }elseif($type == "data_uzobest"){
            Artisan::queue('samji:uzobest --command=data');
        }elseif($type == "data_uzobest_mtn"){
            Artisan::queue('samji:uzobest --command=data --type=MTN');
        }elseif($type == "data_uzobest_glo"){
            Artisan::queue('samji:uzobest --command=data --type=GLO');
        }elseif($type == "data_uzobest_airtel"){
            Artisan::queue('samji:uzobest --command=data --type=AIRTEL');
        }elseif($type == "data_uzobest_9mobile"){
            Artisan::queue('samji:uzobest --command=data --type=9MOBILE');
        }elseif($type == "tv"){
            Artisan::queue('samji:hw --command=tv');
            Artisan::queue('samji:ringo --command=tv');
        }elseif($type == "electricity"){
            Artisan::queue('samji:hw --command=electricity');
        }else{
            return redirect()->route('allsettings')->with('error', 'Invalid Type');
        }

        return redirect()->route('allsettings')->with('success', 'Plans has started refreshing in background');

    }

}
