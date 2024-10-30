<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Jobs\PushNotificationJob;
use App\Mail\PasswordResetMail;
use App\Models\CGWallets;
use App\Models\PndL;
use App\Models\ResellerPaymentLink;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VirtualAccount;
use App\Models\VirtualAccountClient;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use OwenIt\Auditing\Models\Audit;

class UsersController extends Controller
{
    public function index(Request $request)
    {

        $users = DB::table('tbl_agents')->orderBy('id', 'desc')->get();

        $t_users = DB::table('tbl_agents')->count();

        $r_users = DB::table('tbl_agents')->where("referral","!=","")->count();

        $a_users = DB::table('tbl_agents')->where("status", "=", "reseller")->count();

        $u_wallet = DB::table('tbl_agents')->where("status", "!=", "admin")->sum('wallet');
        $fu_wallet = DB::table('tbl_agents')->where([["status", "!=", "admin"], ["fraud", "!=", ""]])->sum('wallet');
        $au_wallet = DB::table('tbl_agents')->where([["status", "!=", "admin"], ["fraud", "=", ""], ["wallet", ">=", "50"]])->sum('wallet');
        $iau_wallet = DB::table('tbl_agents')->where([["status", "!=", "admin"], ["fraud", "=", ""], ["wallet", "<", "50"]])->sum('wallet');

        return view('users', ['users' => $users, 't_users' => $t_users, 'r_users' => $r_users, 'u_wallet' => $u_wallet, 'a_users' => $a_users, 'fu_wallet' => $fu_wallet, 'iau_wallet' => $iau_wallet, 'au_wallet' => $au_wallet]);

    }

    public function topusers(Request $request)
    {

        $topUsers = User::select('tbl_agents.id', 'tbl_agents.user_name', 'tbl_agents.wallet', 'tbl_agents.status', DB::raw('SUM(tbl_transactions.amount) as total_amount'))
            ->join('tbl_transactions', 'tbl_agents.user_name', '=', 'tbl_transactions.user_name')
            ->groupBy('tbl_agents.id', 'tbl_agents.user_name', 'tbl_agents.wallet', 'tbl_agents.status')
            ->orderByDesc('total_amount')
            ->take(10)
            ->get();

        return view('top_users', ['users' => $topUsers, 'i' => 1]);

    }

    public function agents(Request $request)
    {

        $users = DB::table('tbl_agents')->where('status', 'agent')->orderBy('id', 'desc')->get();
        $trans = Transaction::where('date', 'LIKE', '%' . date("Y-m-d") . '%')->get();

        return view('agents', ['users' => $users, 'trans' => $trans]);

    }

    public function resellers(Request $request)
    {
        $users = User::where('status', 'reseller')->orderBy('id', 'desc')->get();

        return view('reseller', ['users' => $users]);
    }

    public function regenerateKey($id)
    {
        $u = User::find($id);
        $key="key_".uniqid().rand().Carbon::now()->timestamp;
        $u->api_key=$key;
        $u->save();

        return redirect()->route('resellers')->with('success', $u->user_name." API Key regenerated successful");
    }


    public function updateLevel(Request $request)
    {
        $role = User::where('id', $request->id)->first();

        $role->level = $request->level;
        $role->save();

        return redirect('/resellers')->with('success', "Level updated successfully");
    }


    public function gmblocked(Request $request)
    {

        $users = DB::table('tbl_generalmarket_blocked user')->orderBy('id', 'desc')->get();

        return view('gm_blocked_users', ['users' => $users]);
    }


    public function dormant(Request $request)
    {
        $startDate = '2022-06-01';
        $endDate = Carbon::now()->subMonth();

        $users = DB::table('tbl_agents')->whereBetween('last_login',[$startDate, $endDate])->paginate(10);

        return view('dormant_users', ['users' => $users]);
    }

    public function pending(Request $request)
    {

        $users = User::where([['target', 'like', '%in progress%'], ['document', 1]])->latest()->get();
        $tp = User::where('target', 'like', '%in progress%')->orderBy('id', 'desc')->count();
        $rp = User::where('target', 'like', '%Reseller in progress%')->orderBy('id', 'desc')->count();
        $ap = User::where('target', 'like', '%Agent in progress%')->orderBy('id', 'desc')->count();


        return view('pending_request', ['users' => $users, 'tp' => $tp, 'rp' => $rp, 'ap' => $ap]);

    }

    public function finduser(Request $request){
        $input = $request->all();
        $user_name=$input['user_name'];
        $phoneno=$input['phoneno'];
        $status=$input['status'];
        $wallet=$input['wallet'];
        $email=$input['email'];
        $regdate=$input['regdate'];

        // Instantiates a Query object
        $query = User::Where('user_name', 'LIKE', "%$user_name%")
            ->Where('phoneno', 'LIKE', "%$phoneno%")
            ->Where('email', 'LIKE', "%$email%")
            ->Where('status', 'LIKE', "%$status%")
            ->Where('wallet', 'LIKE', "%$wallet%")
            ->Where('reg_date', 'LIKE', "%$regdate%")
            ->limit(100)
            ->get();

        $cquery = User::Where('user_name', 'LIKE', "%$user_name%")
            ->Where('phoneno', 'LIKE', "%$phoneno%")
            ->Where('email', 'LIKE', "%$email%")
            ->Where('status', 'LIKE', "%$status%")
            ->Where('wallet', 'LIKE', "%$wallet%")
            ->Where('reg_date', 'LIKE', "%$regdate%")
            ->count();

        return view('find_user', ['users' => $query, 'count'=>$cquery, 'result'=>true]);
    }

    public function profile($user)
    {
        $ap = User::where('user_name', $user)->first();

        if(!$ap){
            return redirect('/users')->with("error", "User does not exist");
        }

//        $users = DB::table('tbl_agents')->where('target', 'like', '%in progress%')->orderBy('id', 'desc')->get();
        $tt = Transaction::where('user_name', $user)->count();
        $td = Transaction::where('user_name', $user)->orderBy('id', 'desc')->paginate(25);
        $v = DB::table('tbl_severlog')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $tw = DB::table('tbl_wallet')->where('user_name', $user)->count();
        $wd = DB::table('tbl_wallet')->where('user_name', $user)->orderBy('id', 'desc')->paginate(25);
        $tpld = DB::table('tbl_p_nd_l')->where('narration', 'like', '%' . $user . '%')->count();
        $pld = DB::table('tbl_p_nd_l')->where('narration', 'like', '%' . $user . '%')->orderBy('id', 'desc')->get();
        $referrals = User::where('referral', $user)->get();
        $sms = DB::table('tbl_smslog')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $email = DB::table('tbl_emaillog')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $push = DB::table('tbl_pushnotiflog')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $login = DB::table('tbl_login_attempt')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $cypto = DB::table('tbl_luno')->where('user_name', $user)->orderBy('id', 'desc')->get();
        $vaccounts=VirtualAccount::where('user_id', $ap->id)->orderBy('id', 'desc')->get();
        $cgs=CGWallets::where('user_id', $ap->id)->orderBy('id', 'desc')->get();

        $tat = Transaction::where([['user_name', $user], ['name', 'LIKE', '%airtime']])->count();
        $tdt = Transaction::where([['user_name', $user], ['name', 'LIKE', '%data']])->count();
        $tct = Transaction::where([['user_name', $user], ['name', 'LIKE', '%Card']])->count();
        $tpt = Transaction::where([['user_name', $user], ['name', 'LIKE', '%paytv']])->count();
        $trt = Transaction::where([['user_name', $user], ['name', '=', 'Result Checker']])->count();

        return view('profile', ['user' => $ap, 'tt' => $tt, 'td' => $td, 'tw' => $tw, 'wd' => $wd, 'tpld' => $tpld, 'pld' => $pld, 'referrals' => $referrals, 'version' => $v, 'sms' => $sms, 'email' => $email, 'push'=>$push, 'tat' =>$tat, 'tdt'=>$tdt, 'tpt'=>$tpt, 'tct'=>$tct, 'trt'=>$trt, 'login'=>$login, 'crypto'=>$cypto, 'vaccounts'=>$vaccounts, 'cgs' =>$cgs]);
    }

    public function approve(Request $request)
    {
        $input = $request->all();

        if ($input["type"] == "agent") {
            DB::table('tbl_agents')->where('user_name', $input['user_name'])->update(["status" => "agent", "target" => "Target: Buy 20 data and 20 airtime this month to complete your level."]);

            $ap = User::where('user_name', $input['user_name'])->first();


            $GLOBALS['email'] = $ap->email;

            $data = array('name' => $ap->full_name, 'date' => date("D, d M Y"));
            Mail::send('email_agent', $data, function ($message) {
                $message->to($GLOBALS['email'], 'MCD Agent')->subject('MCD Agent Approval');
                $message->from('info@5starcompany.com.ng', '5Star Company');
            });
        } else if ($input["type"] == "reseller") {
            DB::table('tbl_agents')->where('user_name', $input['user_name'])->update(["status" => "reseller", "target" => "Reseller Activated"]);
        }


        return redirect('profile/' . $input['user_name']);

    }

    public function referral_upgrade(Request $request)
    {
        $input = $request->all();

        if ($input["plan"] == "larvae") {
            $amount=1000;
        }else{
            $amount=5000;
        }
        $u = User::where('user_name', $input['user_name'])->first();

        if(!$u){
            return redirect('/referral_upgrade')->with('error', $input["user_name"]. ' does not exist!');
        } elseif($u->wallet >= $amount){
            $input['name'] = "Referral Upgrade";
            $input['amount'] = $amount;
            $input['status'] = 'successful';
            $input['description'] = "Being amount charged for referral upgrade to ".$input["plan"]." on ".$u->user_name;
            $input['user_name'] = $u->user_name;
            $input['code'] = 'aru';
            $input['i_wallet'] = $u->wallet;
            $wallet = $u->wallet + $amount;
            $input['f_wallet'] = $wallet;
            $input["ip_address"] = "127.0.0.1:A";
            $input["date"] = date("y-m-d H:i:s");
            $input["extra"]='Initiated by ' . Auth::user()->full_name;

            Transaction::create($input);

            $input["type"] = "income";
            $input["narration"] = $input['description'];

            PndL::create($input);

            $u->wallet-=$amount;
            $u->referral=$input["plan"];
            $u->save();

            $GLOBALS['email'] = $u->email;

            $data = array('name' => $u->full_name, 'date' => date("D, d M Y"));
            Mail::send('email_referral_upgrade', $data, function ($message) {
                $message->to($GLOBALS['email'], 'MCD Customer')->subject('MCD Referral Upgrade');
                $message->from('info@5starcompany.com.ng', '5Star Inn Company');
            });

            return redirect('/referral_upgrade')->with('success', $input["user_name"]. ' has been upgraded to '. $input["plan"].' successfully!');
        }else{
            return redirect('/referral_upgrade')->with('error', $input["user_name"]. ' wallet balance is currently low.');
        }
    }

    public function sendsms(Request $request)
    {
        $input = $request->all();


        $sms_id="15658";
        $sms_secret="66Wby95tGM15Wo3uQk1OwiYO3muum4Ds";
        $sms_pass="zEKJKdpxfvuDzYtTZipihelDJQ0NttZ28JMSXbpcHT";
        $sms_senderid="MCD Notification";
        $sms_charges=3;

//        $curl = curl_init();
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => "http://www.sms.5starcompany.com.ng/smsapi?pd_m=send&id=" . $sms_id . "&secret=" . $sms_secret . "&pass=" . $sms_pass . "&senderID=" . $sms_senderid . "&to_number=" . $input['phoneno'] . "&textmessage=" . $input['message'],
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_CUSTOMREQUEST => "GET",
//            CURLOPT_HTTPHEADER => [
//                "content-type: application/json",
//                "cache-control: no-cache"
//            ],
//        ));
//
//        $response = curl_exec($curl);
//        $err = curl_error($curl);
//
//        if ($err) {
//            // there was an error contacting the SMS Portal
//            die('Curl returned error: ' . $err);
//        }

//        Mail::to('odejinmisamuel@gmail.com')->send(new Notification());

//        DB::table('tbl_smslog')->insert(
//            ['user_name' => $input["user_name"], 'message' => $input['message'], 'phoneno' => $input['phoneno'], 'response' => $response]
//        );


        return redirect('profile/' . $input['user_name']);

    }

    public function sendemail(Request $request)
    {
        $input = $request->all();

        $ap = User::where('user_name', $input['user_name'])->first();

        $GLOBALS['email'] = $ap->email;

        $data = array('name' => $ap->full_name, 'messag' => $input['message']);
        Mail::send('email_notification', $data, function ($message) {
            $message->to($GLOBALS['email'], 'Client')->subject('Message from Admin');
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });

        DB::table('tbl_emaillog')->insert(
            ['user_name' => $input["user_name"], 'message' => $input['message'], 'email' => $ap->email, 'response' => 'sent']
        );

        return redirect('profile/' . $input['user_name']);
    }

    public function sendpushnotif(Request $request)
    {
        $input = $request->all();

        $ap = User::where('user_name', $input['user_name'])->first();

        PushNotificationJob::dispatch($ap->user_name,$input['message'],"PlanetF");

        DB::table('tbl_pushnotiflog')->insert(
            ['user_name' => $input["user_name"], 'message' => $input['message'], 'response' => "background"]
        );

        return redirect('profile/' . $input['user_name']);
    }

    public function addgnews(Request $request)
    {
        $input = $request->all();

        if(isset($input['image'])){
            $storage=Storage::put('public/banners', $input['image']);
            $link=Storage::url($storage);
            $image=explode("/", $link)[3];

            $set=Settings::where('name','banner')->first();
            $set->value=$image;
            $set->save();
        }

        if(isset($input['push_notification'])){
            PushNotificationJob::dispatch('general_notification', $input['message'], "General Notification");
        }else{
            if ($input["user_name"] == "") {

                if ($input['category'] == "all") {
                    User::where('user_name', '!=', "")->update(['gnews' => $input["message"]]);

                } else if ($input['category'] == "admin") {
                    User::whereIn('status', ["admin", "superadmin"])->update(['gnews' => $input["message"]]);
                } else if ($input['category'] == "reseller") {
                    User::where('status', '=', "reseller")->update(['gnews' => $input["message"]]);
                } else if ($input['category'] == "client") {
                    User::where('status', '=', "client")->update(['gnews' => $input["message"]]);
                } else if ($input['category'] == "top") {
                    User::where('status', '=', "client")->update(['gnews' => $input["message"]]);
                }


            }else{
                $user=User::where('user_name','=',$input["user_name"])->exists();
                if(!$user){
                    return redirect('/gnews')->with('success', 'Username does not exist!');
                }

                User::where('user_name','=',$input["user_name"])->update(['gnews'=>$input["message"]]);
            }
        }

        return redirect('/gnews')->with('success', 'Message sent successfully!');

    }

    public function agent_list()
    {

        $users=User::where('status', '=', 'agent')->get();

        return view('agent_payment', ['users' => $users, 'alist'=>true]);
    }

    public function agent_confirm(Request $request)
    {

        $input = $request->all();

        $user=User::where('user_name', '=', $input["user_name"])->first();

        if(!$user){
            return back()->with('error', 'Username does not exist!');
        }

        if($user->status != "agent"){
            return back()->with('error', 'User is not an Agent!');
        }

        $tc = Transaction::where([['code', '=', "acp_".Carbon::now()->subMonth()->format('m.y')], ['user_name', '=', $input["user_name"]]])->get();

        if(!$tc->isEmpty()){
            return back()->with('error', 'Payment made already!');
        }


        $trans = Transaction::where([['user_name', '=', $input["user_name"]], ['name', 'NOT LIKE', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', '%'.Carbon::now()->subMonth()->format("Y-m").'%']])->get();
        $trans_count = Transaction::where([['user_name', '=', $input["user_name"]], ['name', 'NOT LIKE', '%airtime%'], ['status', '=', 'delivered'], ['date', 'LIKE', '%'.Carbon::now()->subMonth()->format("Y-m").'%']])->count();

        if ($trans->isEmpty()) {
            return back()->with('error', 'No Transaction done last month!');
        }

        return view('agent_payment', ['trans' => $trans, 'count'=> $trans_count, 'user' =>$user, 'val'=>true]);
    }

    public function agent_payment(Request $request)
    {
        $input = $request->all();

        $user=User::where('user_name', $input['user_name'])->first();

        $f=$user->level;

        if($f==1){
            $fa=3;
        }
        if($f==2){
            $fa=5;
        }
        if($f==3){
            $fa=8;
        }
        if($f==4){
            $fa=9;
        }
        if($f==5){
            $fa=10;
        }
        if($f==6){
            $fa=13;
        }
        if($f==7){
            $fa=15;
        }
        if($f==8){
            $fa=20;
        }
        if($f==9){
            $fa=25;
        }
        if($f==10){
            $fa=30;
        }

        $amount=$fa * $input['count'];

        $user=User::where("user_name", "=", $input['user_name'])->first();
        $input["description"]="Being agent commission paid for the month of " . Carbon::now()->subMonth()->format('M, Y') . " with transaction count of " . $input['count'] . ' as a Level ' . $f . ' agent' ;
        $input["name"]="Agent Commission";
        $input["status"]="successful";
        $input["code"]="acp_".Carbon::now()->subMonth()->format('m.y');
        $input["ip_address"]="127.0.0.1";
        $input["amount"]=$amount;
        $input["user_name"]=$input['user_name'];
        $input["i_wallet"]=$user->agent_commision;
        $input["f_wallet"]=$user->agent_commision + $amount;
        $input["extra"]='Initiated by ' . Auth::user()->full_name;
        Transaction::create($input);

        $user->agent_commision =$input["f_wallet"];
        $user->save();

        $input["type"]="expenses";
        $input["gl"]="Agent Commission";
        $input["amount"]=$amount;
        $input["narration"]="Being agent commission on ".$input['user_name']. " for ".Carbon::now()->subMonth()->format('m.y');
        $input["date"]=Carbon::now();

        PndL::create($input);

        return redirect('/agentpayment')->with('success', 'Agent Payment paid successfully to ' . $user->user_name . ' with the sum of ' . $amount);
    }

    public function updateProfile(Request $request){
        $input = $request->all();

        $user = User::find($input['id']);

        $user->full_name = $input['full_name'] ?? "";
        $user->company_name = $input['company_name'] ?? "";
        $user->bvn = $input['bvn'] ?? "";
        $user->nin = $input['nin'] ?? "";
        $user->email = $input['email'] ?? "";
        $user->phoneno = $input['phoneno'] ?? "";
        $user->address = $input['address'] ?? "";
        $user->target = $input['target'] ?? "";
        $user->status = $input['status'] ?? "";
        $user->level = $input['level'] ?? "";
        $user->save();

        return redirect()->route('profile', $user->user_name)->with("success", "Profile Updated successfully");
    }

    public function updateService(Request $request)
    {
        $input = $request->all();

        $user = User::find($input['id']);

        $user->airtime = $input['airtime'];
        $user->data = $input['data'];
        $user->tv = $input['tv'];
        $user->electricity = $input['electricity'];
        $user->education = $input['education'];
        $user->airtime2cash = $input['airtime2cash'];
        $user->wallet_transfer = $input['wallet_transfer'];
        $user->save();

        return redirect()->route('profile', $user->user_name)->with("success", "Service Updated successfully");
    }

    public function passwordReset(Request $request)
    {
        $input = $request->all();

        $user = User::find($input['id']);

        $pass = str_shuffle(substr(date('sydmM') . rand() . $user->user_name, 0, 8));

        $user->mcdpassword = Hash::make($pass);
        $user->save();

        $tr['password'] = $pass;
        $tr['email'] = $user->email;
        $tr['user_name'] = $user->user_name;
        $tr['device'] = $_SERVER['HTTP_USER_AGENT'];
        $tr['ip'] = $_SERVER['REMOTE_ADDR'];

        if (env('APP_ENV') != "local") {
            Mail::to($user->email)->send(new PasswordResetMail($tr));
        }

        return redirect()->route('profile', $user->user_name)->with("success", "A new password has been sent to the customer mail successfully");
    }

    public function pinReset(Request $request){
        $input = $request->all();

        $user=User::find($input['id']);

        $user->pin = 1234;
        $user->save();

        PushNotificationJob::dispatch($user->user_name, "Hi " . $user->user_name . ", your pin has been reset to 1234 by the admin.", "Pin Reset");

        return redirect()->route('profile', $user->user_name)->with("success", "User Pin has been reset to 1234");
    }

    public function passwordResetAdmin($id){

        $user=User::find($id);

        $pass = str_shuffle(substr(date('sydmM') . rand() . $user->user_name, 0, 8));

        $user->password = Hash::make($pass);
        $user->save();

        $tr['password'] = $pass;
        $tr['email'] = $user->email;
        $tr['user_name'] = $user->user_name;
        $tr['device'] = "Admin Password";
        $tr['ip'] = $_SERVER['REMOTE_ADDR'];

        if (env('APP_ENV') != "local") {
            Mail::to($user->email)->send(new PasswordResetMail($tr));
        }

        return redirect()->route('profile', $user->user_name)->with("success", "A new password has been sent to the customer mail successfully");
    }

    public function bannUnbann($id){

        $user=User::find($id);

        $GLOBALS['email'] = $user->email;

        if($user->fraud == ""  || $user->fraud == null) {

            $user->fraud = "You have been banned by " . Auth::user()->user_name;
            $user->save();

            $setE=Settings::where('name', 'support_email')->first();

            $message = $user->fraud . ". Kindly send mail to support ($setE->value) if you think it is a mistake.";

            $data = array('name' => $user->full_name, 'messag' => $message);
            Mail::send('email_notification', $data, function ($message) {
                $message->to($GLOBALS['email'], 'Client')->subject('Restriction From Admin');
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            });

            $response="User has been banned successfully";
        }else{
            $user->fraud = "";
            $user->save();

            $message = "Your account has been activated. You can now login on the app.";

            $data = array('name' => $user->full_name, 'messag' => $message);
            Mail::send('email_notification', $data, function ($message) {
                $message->to($GLOBALS['email'], 'Client')->subject('Your Account is Activated');
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            });

            $response="User has been unbanned successfully";
        }

        return redirect()->route('profile', $user->user_name)->with("success", $response);
    }

    public function loginattempt()
    {

        $login = DB::table('tbl_login_attempt')->orderBy('id', 'desc')->paginate(10);

        return view('login_attempts', ['login' => $login]);
    }

    public function vaccounts()
    {
        $datas['accounts'] = VirtualAccount::with('user')->orderBy('id', 'desc')->paginate(20);
        $datas['banks'] = VirtualAccount::distinct('bank_name')->select('bank_name', 'status')->get();

        return view('virtual_accounts', $datas);
    }

    public function dvaccounts($bank, $status)
    {
        VirtualAccount::where('bank_name', $bank)->update(['status' => $status]);

        return redirect()->route('virtual-accounts')->with("success", 'Actioned Successfully');
    }

    public function resvaccounts()
    {
        $datas['accounts'] = VirtualAccountClient::orderBy('id', 'desc')->paginate(10);

        return view('resellers_virtual_accounts', $datas);
    }

    public function paymentLinks()
    {
        $datas['datas'] = ResellerPaymentLink::orderBy('id', 'desc')->paginate(10);

        return view('resellers_payment_links', $datas);
    }

    public function change_password(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'current_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required'
        );

        $validator = Validator::make($input, $rules);

        if (!$validator->passes()) {
            return redirect()->route('change_password')->withInput($input)->withErrors($validator);
        }

        if(!Hash::check($input['current_password'], Auth::user()->password)){
            return redirect()->route('change_password')->withInput($input)->with(['error' => 'Current password did not match']);
        }

        if($input['new_password'] != $input['confirm_password']){
            return redirect()->route('change_password')->withInput($input)->with(['error' => 'New password did not match with confirm password']);
        }

        $user=User::find(Auth::id());
        $user->password=Hash::make($input['new_password']);
        $user->save();

        return redirect()->route('change_password')->withInput($input)->with(['success' => 'Password changed successfully']);

    }

    public function audits(){
        $data['audits']=Audit::where('user_id', '!=', NULL)->OrderBy('id', 'desc')->limit(500)->get();

        \Illuminate\Support\Facades\DB::table('audits')->insert(
            ['user_id' => auth()->user()->id, 'user_type' => 'App\Models\User', 'event' => 'view', 'auditable_id' => '1', 'auditable_type' => 'App\Models\Audit', 'tags' => 'View all Audits',  'old_values'=> '[]', 'new_values'=> '[]',  'ip_address' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT'], 'created_at'=>Carbon::now(), 'updated_at'=>Carbon::now()]
        );

        return view('audits',$data);
    }

}
