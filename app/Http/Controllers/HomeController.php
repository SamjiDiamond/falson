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
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }

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
        $data['pending_trans'] = Transaction::where('status', 'pending')->count();
        $data['inprogress_trans'] = Transaction::where('status', 'inprogress')->count();

        return view('home', $data);
    }

    public function partnerBalances()
    {
        //OGDAMS
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
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => false,
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

            $data['ogdams'] = $og['data']['msg'];
        } catch (\Exception $e) {
            Log::error('OGDAMS Balance Check: ' . $e->getMessage());
            $data['ogdams'] = json_decode('{"mainBalance":"583.15","dgMtn":"0.00","dgAirtel":"0.00","dgGlo":"0.00","dg9mobile":"0.00","smeMtn":"0.00","smeAirtel":"0.00","smeGlo":"0.00","smeSme":"0.00","vtuMtn":"0.00","vtuAirtel":"0.00","vtuGlo":"0.00","vtu9mobile":"0.00","momoMtn":"0.00","momoGlo":"0.00","momo9mobile":"0.00","cgMtn":"0.00","cgAirtel":"506.38","cgGlo":"400.00","cg9mobile":"0.00"}', true);;
        }


        //HW
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('HW_BASEURL') . 'wallet/manage-wallet-balance',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('HW_AUTH'),
                    'User-Agent: samji'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);


            Log::info("HW BALANCE CHECK");
            Log::info($response);

            $hw = json_decode($response, true);

            $data['hw'] = $hw['data'];
        } catch (\Exception $e) {
            Log::error('HW Balance Check: ' . $e->getMessage());
            $data['hw'] = "0";
        }


        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('UZOBEST_BASEURL') . 'user/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Token ' . env('UZOBEST_TOKEN'),
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            Log::info("Uzobest BALANCE CHECK");
            Log::info($response);

            $hw = json_decode($response, true);

            $data['uzobest'] = $hw['user'];
        } catch (\Exception $e) {
            Log::error('Uzobest Balance Check: ' . $e->getMessage());
            $data['uzobest'] = "0";
        }


        //IYII
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('IYIINSTANT_BASEURL') . 'user/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Token ' . env('IYIINSTANT_AUTH'),
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            Log::info("IYII BALANCE CHECK");
            Log::info($response);

            $hw = json_decode($response, true);

            $data['iyii'] = $hw['user'];
        } catch (\Exception $e) {
            Log::error('IYII Balance Check: ' . $e->getMessage());
            $data['iyii'] = "0";
        }

        //Autosync
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('AUTOSYNCNG_BASEURL') . "me",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('AUTOSYNCNG_AUTH'),
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            Log::info("Autosync BALANCE CHECK");
            Log::info($response);

            $hw = json_decode($response, true);

            $data['autosync'] = $hw['data']['user'];
        } catch (\Exception $e) {
            Log::error('Autosync Balance Check: ' . $e->getMessage());
            $data['autosync'] = "0";
        }

        //Ringo
        $payload = '{
            "serviceCode": "INFO"
        }';

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('RINGO_BASEURL'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => array(
                    'email: ' . env('RINGO_EMAIL'),
                    'password: ' . env('RINGO_PASSWORD'),
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);


            Log::info("Ringo BALANCE CHECK");
            Log::info($response);

            $rep = json_decode($response, true);

            $data['ringo'] = $rep['wallet']['wallet'];
        } catch (\Exception $e) {
            Log::error('Ringo Balance Check: ' . $e->getMessage());
            $data['ringo'] = [
                "code" => "8574855",
                "balance" => 37019.38,
                "bonus_balance" => "0.00",
                "commission_balance" => "630.02"
            ];
        }

        return view('partner_balances', $data);
    }

    public function allsettings()
    {
        $data = Settings::where('name', 'min_funding')->orWhere('name', 'max_funding')->orWhere('name', 'bithday_message')->orWhere('name', 'disable_resellers')->orWhere('name', 'live_chat')->orWhere('name', 'email_note')->orWhere('name', 'transaction_email_copy')->orWhere('name', 'reseller_fee')->orWhere('name', 'reseller_terms')->orWhere('name', 'biz_verification_price_reseller')->orWhere('name', 'biz_verification_price_customer')->orWhere('name', 'data')->orWhere('name', 'airtime')->orWhere('name', 'paytv')->orWhere('name', 'resultchecker')->orWhere('name', 'rechargecard')->orWhere('name', 'electricity')->orWhere('name', 'airtimeconverter')->orWhere('name', 'LIKE', 'support_%')->orWhere('name', 'LIKE', 'enable_%')->orWhere('name', 'privacy_policy')->orWhere('name', 'cg_wallet_bank_details')->orWhere('name', 'tv_server')->orWhere('name', 'referral_bonus')->orWhere('name', 'referral_bonus_min_funding')->orWhere('name', 'LIKE', 'verification_charge%')->orWhere('name', 'LIKE', 'bulk_sms_price%')->orWhere('name', 'LIKE', '%_enabled')->orWhere('name', 'admin_emails')->orWhere('name', 'force_kyc_update')->get();

        return view('allsettings', ['data' => $data]);
    }

    public function allsettingsEdit($id)
    {
        $data = Settings::find($id);

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
        } elseif ($type == "data_uzobest") {
            Artisan::queue('samji:uzobest --command=data');
        } elseif ($type == "data_uzobest_mtn") {
            Artisan::queue('samji:uzobest --command=data --type=MTN');
        } elseif ($type == "data_uzobest_glo") {
            Artisan::queue('samji:uzobest --command=data --type=GLO');
        } elseif ($type == "data_uzobest_airtel") {
            Artisan::queue('samji:uzobest --command=data --type=AIRTEL');
        } elseif ($type == "data_uzobest_9mobile") {
            Artisan::queue('samji:uzobest --command=data --type=9MOBILE');
        } elseif ($type == "data_autosyncng_mtn") {
            Artisan::queue('samji:autosyncng --command=data --type=MTN');
        } elseif ($type == "data_autosyncng_glo") {
            Artisan::queue('samji:autosyncng --command=data --type=GLO');
        } elseif ($type == "data_autosyncng_airtel") {
            Artisan::queue('samji:autosyncng --command=data --type=AIRTEL');
        } elseif ($type == "data_autosyncng_9mobile") {
            Artisan::queue('samji:autosyncng --command=data --type=9MOBILE');
        } elseif ($type == "tv") {
            Artisan::queue('samji:hw --command=tv');
            Artisan::queue('samji:ringo --command=tv');
        } elseif ($type == "tv_autosyncng") {
            Artisan::queue('samji:autosyncng --command=tv');
        } elseif ($type == "electricity") {
            Artisan::queue('samji:hw --command=electricity');
        } else {
            return redirect()->route('allsettings')->with('error', 'Invalid Type');
        }

        return redirect()->route('allsettings')->with('success', 'Plans has started refreshing in background');

    }

}
