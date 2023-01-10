<?php

namespace App\Http\Controllers;

use App\Jobs\Airtime2CashNotificationJob;
use App\Jobs\ATMtransactionserveJob;
use App\Models\Airtime2Cash;
use App\Models\Airtime2CashSettings;
use App\Models\CGWallets;
use App\Models\PndL;
use App\Models\Serverlog;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PDF;

class TransactionController extends Controller
{
    public function index(Request $request)
    {

        $data = Transaction::orderBy('id', 'desc')->limit(500)->get();
        $tt = Transaction::count();
        $ft = Transaction::where([['date', 'like', Carbon::now()->format('Y-m-d') . '%']])->count();
        $st = Transaction::where([['date', 'like', Carbon::now()->subDay()->format('Y-m-d') . '%']])->count();
        $rt = Transaction::where([['date', 'like', Carbon::now()->subDays(2)->format('Y-m-d') . '%']])->count();

//        $mutable = Carbon::now();
//        $gdate="";
//        $gtrans="";
//        $gwallet="";
//        for($x = 0; $x <= 7; $x++){
//            $modifiedImmutable = CarbonImmutable::now()->add('-'.$x, 'day');
//            $imdf =substr($modifiedImmutable, 0, 10);
//            $gt = DB::table('tbl_transactions')
//                ->where([['status', '=', 'delivered']])
//                ->whereDate('date', $imdf)
//                ->count();
//
//            $ft = DB::table('tbl_transactions')
//                ->where([['name', '=', 'wallet funding']])
//                ->whereDate('date', $imdf)
//                ->count();
//
//            $imdf =substr($modifiedImmutable, 8, 2);
//                $gdate = $gdate . ", " . $imdf;
//                $gtrans = $gtrans . "," . $gt;
//                $gwallet = $gwallet . "," . $ft;
//
//        }

        return view('transactions', ['data' => $data, 'tt' => $tt, 'ft' => $ft, 'st' => $st, 'rt' => $rt]);

    }

    public function trans_data(Request $request)
    {

        $data = Transaction::where("code", 'LIKE', 'data_%')->orderBy('id', 'desc')->limit(500)->get();
        $tt = Transaction::where("code", 'LIKE', 'data_%')->count();
        $ft = Transaction::where([["code", 'LIKE', 'data_%'],['date', 'like', Carbon::now()->format('Y-m-d') . '%']])->count();
        $st = Transaction::where([["code", 'LIKE', 'data_%'],['date', 'like', Carbon::now()->subDay()->format('Y-m-d') . '%']])->count();
        $rt = Transaction::where([["code", 'LIKE', 'data_%'],['date', 'like', Carbon::now()->subDays(2)->format('Y-m-d') . '%']])->count();

        return view('transactions', ['data' => $data, 'tt' => $tt, 'ft' => $ft, 'st' => $st, 'rt' => $rt]);

    }

    public function trans_airtime(Request $request)
    {

        $data = Transaction::where("code", 'LIKE', 'airtime_%')->limit(500)->get();
        $tt = Transaction::where("code", 'LIKE', 'airtime_%')->count();
        $ft = Transaction::where([["code", 'LIKE', 'airtime_%'],['date', 'like', Carbon::now()->format('Y-m-d') . '%']])->count();
        $st = Transaction::where([["code", 'LIKE', 'airtime_%'],['date', 'like', Carbon::now()->subDay()->format('Y-m-d') . '%']])->count();
        $rt = Transaction::where([["code", 'LIKE', 'airtime_%'],['date', 'like', Carbon::now()->subDays(2)->format('Y-m-d') . '%']])->count();

        return view('transactions', ['data' => $data, 'tt' => $tt, 'ft' => $ft, 'st' => $st, 'rt' => $rt]);
    }

    public function trans_tv(Request $request)
    {

        $data = Transaction::where("code", 'LIKE', 'tv_%')->orderBy('id', 'desc')->limit(500)->get();
        $tt = Transaction::where("code", 'LIKE', 'tv_%')->count();
        $ft = Transaction::where([["code", 'LIKE', 'tv_%'],['date', 'like', Carbon::now()->format('Y-m-d') . '%']])->count();
        $st = Transaction::where([["code", 'LIKE', 'tv_%'],['date', 'like', Carbon::now()->subDay()->format('Y-m-d') . '%']])->count();
        $rt = Transaction::where([["code", 'LIKE', 'tv_%'],['date', 'like', Carbon::now()->subDays(2)->format('Y-m-d') . '%']])->count();

        return view('transactions', ['data' => $data, 'tt' => $tt, 'ft' => $ft, 'st' => $st, 'rt' => $rt]);
    }

    public function trans_electricity(Request $request)
    {
        $data = Transaction::where("code", 'LIKE', 'electricity%')->orderBy('id', 'desc')->limit(500)->get();
        $tt = Transaction::where("code", 'LIKE', 'electricity%')->count();
        $ft = Transaction::where([["code", 'LIKE', 'electricity%'],['date', 'like', Carbon::now()->format('Y-m-d') . '%']])->count();
        $st = Transaction::where([["code", 'LIKE', 'electricity%'],['date', 'like', Carbon::now()->subDay()->format('Y-m-d') . '%']])->count();
        $rt = Transaction::where([["code", 'LIKE', 'electricity%'],['date', 'like', Carbon::now()->subDays(2)->format('Y-m-d') . '%']])->count();

        return view('transactions', ['data' => $data, 'tt' => $tt, 'ft' => $ft, 'st' => $st, 'rt' => $rt]);
    }

    public function trans_resultchecker(Request $request)
    {

        $data = Transaction::where("code", 'LIKE', 'rch%')->orderBy('id', 'desc')->limit(500)->get();
        $tt = Transaction::where("code", 'LIKE', 'rch%')->count();
        $ft = Transaction::where([["code", 'LIKE', 'rch%'],['date', 'like', Carbon::now()->format('Y-m-d') . '%']])->count();
        $st = Transaction::where([["code", 'LIKE', 'rch%'],['date', 'like', Carbon::now()->subDay()->format('Y-m-d') . '%']])->count();
        $rt = Transaction::where([["code", 'LIKE', 'rch%'],['date', 'like', Carbon::now()->subDays(2)->format('Y-m-d') . '%']])->count();

        return view('transactions', ['data' => $data, 'tt' => $tt, 'ft' => $ft, 'st' => $st, 'rt' => $rt]);
    }

    public function trans_funding(Request $request)
    {

        $data = Transaction::where("name", 'LIKE', 'wallet funding')->orderBy('id', 'desc')->limit(500)->get();
        $tt = Transaction::where("name", 'LIKE', 'wallet funding')->count();
        $ft = Transaction::where([["name", 'LIKE', 'wallet funding'],['date', 'like', Carbon::now()->format('Y-m-d') . '%']])->count();
        $st = Transaction::where([["name", 'LIKE', 'wallet funding'],['date', 'like', Carbon::now()->subDay()->format('Y-m-d') . '%']])->count();
        $rt = Transaction::where([["name", 'LIKE', 'wallet funding'],['date', 'like', Carbon::now()->subDays(2)->format('Y-m-d') . '%']])->count();

        return view('transactions', ['data' => $data, 'tt' => $tt, 'ft' => $ft, 'st' => $st, 'rt' => $rt]);
    }

    public function pending(Request $request)
    {

        $data = Transaction::where('status', 'pending')->orderBy('id', 'desc')->paginate(25);

        return view('transactions_pending', ['data' => $data]);
    }

    public function trans_resubmit(Request $request)
    {
        $input = $request->all();

        $tran = Transaction::where('id', '=', $input["id"])->orwhere('ref', '=', $input["id"])->orderby('id', 'desc')->first();

        if (!$tran) {
            return back()->with('error', 'Transaction doesnt exist!');
        }

        $s = Serverlog::where("transid", $tran->ref)->first();

        if(!$s){
            return redirect()->route('trans_pending')->with('error', 'This transaction can not be reversed '.$tran->ref);
        }

        ATMtransactionserveJob::dispatch($s->id, "reprocess");

        $tran->status = "inprogress";
        $tran->save();

        return back()->with('success', 'Transaction has been reprocess in background');

    }

    public function trans_resubmitAll(Request $request)
    {
        $input = $request->all();

        try {
            $numbers = $input['selectIDs'];
        }catch (\Exception $e){
            return redirect()->route('trans_pending')->with('error', 'Kindly select some box!');
        }

        $all_type=$input['all_type'];

        if(count($numbers) < 1){
            return redirect()->route('trans_pending')->with('error', 'Kindly select some box!');
        }

        if($all_type == "reprocess") {

            foreach ($numbers as $id) {

                $tran = Transaction::where('id', '=', $id)->orwhere('ref', '=', $id)->orderby('id', 'desc')->first();

                if (!$tran) {
                    return back()->with('error', 'Transaction doesnt exist!');
                }

                $s = Serverlog::where("transid", $tran->ref)->first();

                if (!$s) {
                    return redirect()->route('trans_pending')->with('error', 'This transaction can not be reversed ' . $tran->ref);
                }

                ATMtransactionserveJob::dispatch($s->id, "reprocess");

                $tran->status = "inprogress";
                $tran->save();
            }
        }


        if($all_type == "delivered") {

            foreach ($numbers as $id) {

                $tran = Transaction::where('id', '=', $id)->orwhere('ref', '=', $id)->orderby('id', 'desc')->first();

                if (!$tran) {
                    return back()->with('error', 'Transaction doesnt exist!');
                }

                $tran->status = "delivered";
                $tran->save();
            }
        }

        if($all_type == "reverse") {

            foreach ($numbers as $id) {


                $tran = Transaction::find($id);

                $desc = "Being reversal of " . $tran->description;
                $user_name = $tran->user_name;

                $rtran = Transaction::where('ref', '=', $tran->ref)->get();

                foreach ($rtran as $tran) {
                    $tran->status = "reversed";
                    $tran->save();

                    $amount = $tran->amount;

                    $user = User::where("user_name", "=", $tran->user_name)->first();

                    if ($tran->code == "tcommission") {
                        $nBalance = $user->agent_commision - $tran->amount;

                        $input["description"] = "Being reversal of " . $tran->description;
                        $input["name"] = "Reversal";
                        $input["status"] = "successful";
                        $input["code"] = "reversal";
                        $input["amount"] = $amount;
                        $input["user_name"] = $tran->user_name;
                        $input["i_wallet"] = $user->agent_commision;
                        $input["f_wallet"] = $nBalance;
                        $input["extra"] = 'Initiated by ' . Auth::user()->full_name;

                        $user->update(["agent_commision" => $nBalance]);
                        Transaction::create($input);
                    } else {
                        if ($tran->name == "data") {
                            $amount = $tran->amount + 20;
                            $nBalance = $user->wallet + $amount;

                            $input["type"] = "expenses";
                            $input["gl"] = "Data";
                            $input["amount"] = 20;
                            $input['date'] = Carbon::now();
                            $input["narration"] = "Being data reversal of " . $tran->ref;

                            PndL::create($input);
                        } else {
                            $nBalance = $user->wallet + $tran->amount;
                        }

                        $input["description"] = "Being reversal of " . $tran->description;
                        $input["name"] = "Reversal";
                        $input["status"] = "successful";
                        $input["code"] = "reversal";
                        $input["amount"] = $amount;
                        $input["user_name"] = $tran->user_name;
                        $input["i_wallet"] = $user->wallet;
                        $input["f_wallet"] = $nBalance;
                        $input["extra"] = 'Initiated by ' . Auth::user()->full_name;

                        $user->update(["wallet" => $nBalance]);
                        Transaction::create($input);

                    }
                }

                try {
                    $at = new PushNotificationController();
                    $at->PushNoti($user_name, $desc, "Reversal");
                } catch (Exception $e) {
                    echo "error while sending notification";
                }

            }
        }

        return redirect()->route('trans_pending')->with('success', 'Transactions has been process in background');

    }

    public function trans_delivered($id)
    {
        $tran = Transaction::where('id', '=', $id)->orwhere('ref', '=', $id)->orderby('id', 'desc')->first();

        if (!$tran) {
            return back()->with('error', 'Transaction doesnt exist!');
        }

        $tran->status = "delivered";
        $tran->save();

        return back()->with('success', 'Transaction has been marked delivered');
    }

    public function server8(Request $request)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://honourworld.ng/transactions/data-top-up',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('action' => 'get-user-tranx'),
            CURLOPT_HTTPHEADER => array(
                env('SERVER8_AUTH'),
                'referer: https://honourworld.ng/products/data-top-up',
                'user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Mobile Safari/537.36'
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        $rep = json_decode($response, true);


        return view('transactions_server8', ['data' => $rep['data']]);

    }

    public function rechargecard(Request $request)
    {
        $input = $request->all();

        $user = DB::table('tbl_agents')->where('user_name', $input['user_name'])->first();

        if (!$user) {
            return redirect()->route('rechargecard')
                ->with('success','User doesnt exist');
        }


        /*        $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://www.nellobytesystems.com/APIEPINV1.asp?UserID=CK10123847&APIKey=W5352Q23GDS924D7UA1B84YYY506178I69DDE4JR1ZRAR80FCBQF819D4T7HKI85&MobileNetwork=".$input['network']."&Value=".$input['amount']."&Quantity=".$input['quantity']."&CallBackURL=http://www.5starcompany.com.ng",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => [
                        "content-type: application/json",
                        "cache-control: no-cache"
                    ],
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                if($err){
                    // there was an error contacting the Paystack API
                    die('Curl returned error: ' . $err);
                }*/

//        $response='{"TXN_EPIN":[{"transactionid":"6342727713","transactiondate":"5/14/2020 11:00:00 AM","batchno":"203682","mobilenetwork":"GLO","sno":"680401306984254","pin":"146297803330390","amount":"100"},{"transactionid":"6342727714","transactiondate":"5/14/2020 11:00:00 AM","batchno":"203682","mobilenetwork":"GLO","sno":"680401306984255","pin":"146297845881340","amount":"100"},{"transactionid":"6342727715","transactiondate":"5/14/2020 11:00:00 AM","batchno":"203682","mobilenetwork":"GLO","sno":"680401306984256","pin":"146297693718491","amount":"100"},{"transactionid":"6342727716","transactiondate":"5/14/2020 11:00:00 AM","batchno":"203682","mobilenetwork":"GLO","sno":"680401306984257","pin":"146297614150942","amount":"100"},{"transactionid":"6342727717","transactiondate":"5/14/2020 11:00:00 AM","batchno":"203682","mobilenetwork":"GLO","sno":"680401306984258","pin":"146297845985782","amount":"100"}]}';
//        $response='{"TXN_EPIN":[{"transactionid":"6342615591","transactiondate":"5/14/2020 9:41:00 PM","batchno":"205352","mobilenetwork":"MTN","sno":"00000005099279064","pin":"17287167093507274","amount":"100"},{"transactionid":"6342615594","transactiondate":"5/14/2020 9:41:00 PM","batchno":"205352","mobilenetwork":"MTN","sno":"00000005105116628","pin":"58508681769179769","amount":"100"},{"transactionid":"6342615584","transactiondate":"5/14/2020 9:41:00 PM","batchno":"205352","mobilenetwork":"MTN","sno":"00000005164416614","pin":"50815693195674413","amount":"100"},{"transactionid":"6342615587","transactiondate":"5/14/2020 9:41:00 PM","batchno":"205352","mobilenetwork":"MTN","sno":"00000005208228089","pin":"74256074917601404","amount":"100"},{"transactionid":"6342615588","transactiondate":"5/14/2020 9:41:00 PM","batchno":"205352","mobilenetwork":"MTN","sno":"00000005239979674","pin":"07022003391573100","amount":"100"}]}';
        $response='{"TXN_EPIN":[{"transactionid":"6343954670","transactiondate":"5/22/2020 7:58:00 AM","batchno":"220639","mobilenetwork":"GLO","sno":"780409013324722","pin":"904525101269726","amount":"100"},{"transactionid":"6343954671","transactiondate":"5/22/2020 7:58:00 AM","batchno":"220639","mobilenetwork":"GLO","sno":"780409013324723","pin":"904525074115295","amount":"100"},{"transactionid":"6343954672","transactiondate":"5/22/2020 7:58:00 AM","batchno":"220639","mobilenetwork":"GLO","sno":"780409013324724","pin":"904525395664686","amount":"100"},{"transactionid":"6343954673","transactiondate":"5/22/2020 7:58:00 AM","batchno":"220639","mobilenetwork":"GLO","sno":"780409013324725","pin":"904525223872610","amount":"100"},{"transactionid":"6343954674","transactiondate":"5/22/2020 7:58:00 AM","batchno":"220639","mobilenetwork":"GLO","sno":"780409013324726","pin":"904525463396642","amount":"100"}]}';

        $tranx = json_decode($response, true);

//        echo $response. "<br />";

        $findme = 'TXN_EPIN';
        $pos = strpos($response, $findme);
        // Note our use of ===.  Simply == would not work as expected
        if ($pos === false) {
            return response()->json(['status' => 0, 'message'=>'Error generating recharge card']);
        }else{
            foreach ($tranx['TXN_EPIN'] as $pin){
                DB::table('tbl_rechargecards')->insert(
                    ['pin' => $pin['pin'], 'serial' => $pin['sno'] , 'network' => $pin['mobilenetwork'], 'amount'=>$pin['amount'], 'batchno'=> $pin['batchno'], 'transactionno'=> $pin['transactionid'], 'status'=>'unused', 'user_name'=>$input['user_name']]
                );
            }
        }

//        $cards = DB::table('tbl_rechargecards')->where([['status', 'unused'], ['network', $network] ])->skip(0)->take($quantity)->get();

        $data = ['user' => $user, 'cards'=>$tranx['TXN_EPIN']];
        $pdf = PDF::loadView('pdf_rechargecard', $data);

        /*        foreach ($cards as $card){
                    DB::table('tbl_rechargecards')->where('id', $card->id)->update(["status"=>"sent", "user_name"=>$user_name]);
                }*/

//        return $pdf->stream($user_name.'_rechargecard.pdf');
        //start generating reference
        $num=date('smdhiy');
        $shuffled = str_shuffle($num);
        $sfinal=substr($shuffled, 0, 8).rand();
        $genId=substr($sfinal, 0, 5);
        //finish generating reference

        $GLOBALS['pathToFile']=public_path().'/'.$input['user_name'].'_rechargecard_'.$input['network'].$genId.'.pdf';

//        return PDF::loadFile(public_path().'/myfile.html')->save('/path-to/my_stored_file.pdf')->stream('download.pdf');
        return $pdf->save($GLOBALS['pathToFile']);

//        return view('rechargecard', ['user' => $user, 'cards'=>$cards]);

        $GLOBALS['email']=$user->email;

        $data = array('date'=>date("D, d M Y"));
        Mail::send('mail_rechargecard', $data, function($message) {
            $message->to($GLOBALS['email'], 'MCD Agent')->subject('MCD Recharge Card');
            $message->from('info@5starcompany.com.ng','5Star Company');
            $message->attach($GLOBALS['pathToFile']);
        });

        return redirect()->route('rechargecard')
            ->with('success','Recharge sent successfully to '.$user->email . ' @ '. $user->user_name);
    }

    public function rechargemanual(Request $request)
    {
        $user_name="ebenyam";
        $quantity=5;
        $network="GLO";
        $amount=200;

        $user = DB::table('tbl_agents')->where('user_name', $user_name)->first();

        if (!$user) {
            return redirect()->route('rechargecard')
                ->with('success', 'User doesnt exist');
        }

        $cards = DB::table('tbl_rechargecards')->where([['status', 'unused'], ['network', $network], ['amount', $amount] ])->skip(0)->take($quantity)->get();

        $data = ['user' => $user, 'cards'=>$cards];
        $pdf = PDF::loadView('pdf_rechargecard', $data);

        foreach ($cards as $card){
            DB::table('tbl_rechargecards')->where('id', $card->id)->update(["status"=>"sent", "user_name"=>$user_name]);
        }

        return $pdf->stream($user_name.'_rechargecard.pdf');

    }


    public function monnify(Request $request){
        $input = $request->all();

        DB::table('monnify')->insert(
            ['request' => $request, 'input'=>'hello']
        );
    }

    function hook(Request $request){
        $input = $request->all();

        echo $request;
        echo "<p> </p>";
        $data1= implode($input);
        echo "<p> </p>";
        $data2= json_encode($input);
//        echo $data;

        DB::table('test')->insert(['name'=> 'webhook', 'request'=>$request, 'data1'=>$data1, 'data2'=>$data2]);
    }

    public function addtransaction(Request $request){
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'network'      => 'required',
            'amount' => 'required');

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            $sms_id="15658";
            $sms_secret="66Wby95tGM15Wo3uQk1OwiYO3muum4Ds";
            $sms_pass="zEKJKdpxfvuDzYtTZipihelDJQ0NttZ28JMSXbpcHT";
            $sms_senderid="MCD Transaction";
            $sms_charges=3;
            $charge_treshold=2000;
            $charges=50;
            $amount=$input["amount"];
            $user= User::where('user_name', $input["user_name"])->first();
            $sms_description="Dear ".$input['user_name'].", a transaction of ".$input["network"]. " #".$amount." on ".$input["phoneno"]. " just occured on your account by admin. Regards.";

            if($user){
                $amt=0.02 * $input["amount"];
                $input["amount"] -= $amt;

                if($user->wallet<$input["amount"]){
                    return redirect('/addtransaction')->with('success', $input["user_name"]. ' wallet balance is too low!');
                }

                $input["description"]=$input["user_name"] . " buy airtime_".$input["network"]. "_".$amount. " on ".$input["phoneno"]. " using wallet";
                $input["i_wallet"]=$user->wallet;
                $input["f_wallet"]=$input["i_wallet"] - $input["amount"];
                $input["ip_address"]="127.0.0.1";
                $input["code"]="airtime_".$input["network"]. "_".$amount;
                $input["status"]="delivered";
                $input["date"]=date("y-m-d H:i:s");
                $input["name"]=$input["network"]. "airtime";
                $input["extra"]='Initiated by ' . Auth::user()->full_name;

                Transaction::create($input);

                /*$input["description"]="Being sms charge";
                $input["name"]="SMS Charge";
                $input["amount"]=$sms_charges;
                $input["code"]="smsc";
                $input["i_wallet"]=$input["f_wallet"];
                $input["f_wallet"]=$input["f_wallet"] - $sms_charges;

                Transaction::create($input);

                $amount+=$sms_charges;*/

                $user->wallet-=$amount;
                $user->save();

                /*$curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://www.sms.5starcompany.com.ng/smsapi?pd_m=send&id=".$sms_id."&secret=".$sms_secret."&pass=".$sms_pass."&senderID=".$sms_senderid."&to_number=".$user->phoneno."&textmessage=".$sms_description,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => [
                        "content-type: application/json",
                        "cache-control: no-cache"
                    ],
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                if($err){
                    // there was an error contacting the SMS Portal
                    die('Curl returned error: ' . $err);
                }


                DB::table('tbl_smslog')->insert(
                    ['user_name' => $input["user_name"], 'message' => $sms_description, 'phoneno' => $user->phoneno, 'response' => $response]
                );*/

                $topic=$user->user_name;

                $topi=str_replace(" ","", $topic);

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS =>"{\n\"to\": \"/topics/".$topi."\",\n\"data\": {\n\t\"extra_information\": \"PLANETF\"\n},\n\"notification\":{\n\t\"title\": \"MCD Notification\",\n\t\"text\":\"". $sms_description."\"\n\t}\n}\n",
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: key=AAAAOW0II6E:APA91bHyum5pMhub2JVHcHnQghuWOdktOuhW9e4ZvmMDudjMZk9y1u71Nr7yl_FZLpsjuC6Hz1Fd49OrWfPYNKpAvahAZ5Rjv0y7IW24nqjYrPnMer8IvTkzZFB5W3hrOHAwbq2EOMOE",
                        "Content-Type: application/json",
                        "Content-Type: text/plain"
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
//                echo $response;


                return redirect('/addtransaction')->with('success', $input["user_name"]. ' transaction added successfully!');
            }else{
                $validator->errors()->add('username', 'The username does not exist!');

                return redirect('/addtransaction')
                    ->withErrors($validator)
                    ->withInput($input);
            }

        }else{

            return redirect('/addtransaction')
                ->withErrors($validator)
                ->withInput($input);
//            return response()->json(['status'=> 0, 'message'=>'Unable to login with errors', 'error' => $validator->errors()]);;
        }
    }

    public function addtransaction_data(Request $request){
        $input = $request->all();
        $rules = array(
            'user_name'      => 'required',
            'network'      => 'required',
            'plan'      => 'required',
            'amount' => 'required|int|min:2');

        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {

            $amount=$input["amount"];
            $user= User::where('user_name', $input["user_name"])->first();

            if($user){
                if($user->wallet<$input["amount"]){
                    return redirect('/adddatatransaction')->with('success', $input["user_name"]. ' wallet balance is too low!');
                }
                $input["description"]=$input["user_name"] . " buy data_".$input["network"]. "_".$input["plan"]. " on ".$input["phoneno"]. " using wallet";
                $input["i_wallet"]=$user->wallet;
                $input["f_wallet"]=$input["i_wallet"] - $input["amount"];
                $input["ip_address"]="127.0.0.1";
                $input["code"]="data_".$input["network"]. "_".$input["plan"];
                $input["status"]="delivered";
                $input["date"]=date("y-m-d H:i:s");
                $input["name"]=$input["network"]. "data";
                $input["extra"]='Initiated by ' . Auth::user()->full_name;

                Transaction::create($input);

                $user->wallet-=$input["amount"];
                $user->save();

                return redirect('/adddatatransaction')->with('success', $input["user_name"]. ' transaction added successfully!');
            }else{
                $validator->errors()->add('username', 'The username does not exist!');

                return redirect('/adddatatransaction')
                    ->withErrors($validator)
                    ->withInput($input);
            }

        }else{

            return redirect('/adddatatransaction')
                ->withErrors($validator)
                ->withInput($input);
//            return response()->json(['status'=> 0, 'message'=>'Unable to login with errors', 'error' => $validator->errors()]);;
        }
    }

    public function reversal_confirm(Request $request)
    {
        $input = $request->all();

        $tran = Transaction::where('id', '=', $input["id"])->orwhere('ref', '=', $input["id"])->orderby('id', 'desc')->first();

        if (!$tran) {
            return redirect()->route('reversal')->with('error', 'Transaction doesnt exist!');
        }

        if ($tran->status == "reversed") {
            return redirect()->route('reversal')->with('error', 'Transactions has been reversed earlier!');
        }

        if ($tran->ref) {
            $rtran = Transaction::where('ref', '=', $tran->ref)->get();
        } else {
            $rtran = Transaction::where('id', '=', $tran->id)->get();
        }
        return view('reversal', ['data' => $tran, 'rtran' => $rtran, 'val' => true]);
    }

    public function reverse(Request $request, $id)
    {
        $input = $request->all();

        $tran = Transaction::find($id);

        $desc = "Being reversal of " . $tran->description;
        $user_name = $tran->user_name;

        $rtran = Transaction::where('ref', '=', $tran->ref)->get();

        foreach ($rtran as $tran) {
            $tran->status = "reversed";
            $tran->save();

            $amount = $tran->amount;

            $user = User::where("user_name", "=", $tran->user_name)->first();

            if ($tran->code == "tcommission") {
                $nBalance = $user->agent_commision - $tran->amount;

                $input["description"] = "Being reversal of " . $tran->description;
                $input["name"] = "Reversal";
                $input["status"] = "successful";
                $input["code"] = "reversal";
                $input["amount"] = $amount;
                $input["user_name"] = $tran->user_name;
                $input["i_wallet"] = $user->agent_commision;
                $input["f_wallet"] = $nBalance;
                $input["extra"] = 'Initiated by ' . Auth::user()->full_name;

                $user->update(["agent_commision" => $nBalance]);
                Transaction::create($input);
            } else {
                if ($tran->name == "data") {
                    $extra=explode("|",$tran->extra);

                    if(isset($extra[2])){
                        $cg=CGWallets::where([["user_id", $extra[2]], ['name', $extra[1]]])->first();

                        if(!$cg){
                            return redirect('/reversal')->with('success', 'Invalid payment selected encounter while reversing');
                        }

                        $cg->balance+=doubleval($extra[0]);
                        $cg->save();
                        $nBalance = $user->wallet;
                    }else{
                        $amount = $tran->amount;
                        $nBalance = $user->wallet + $amount;
                    }

                } else {
                    $nBalance = $user->wallet + $tran->amount;
                }

                $input["description"] = "Being reversal of " . $tran->description;
                $input["name"] = "Reversal";
                $input["status"] = "successful";
                $input["code"] = "reversal";
                $input["amount"] = $amount;
                $input["user_name"] = $tran->user_name;
                $input["i_wallet"] = $user->wallet;
                $input["f_wallet"] = $nBalance;
                $input["extra"] = 'Initiated by ' . Auth::user()->full_name;

                $user->update(["wallet" => $nBalance]);
                Transaction::create($input);

            }
        }

        try {
            $at = new PushNotificationController();
            $at->PushNoti($user_name, $desc, "Reversal");
        } catch (Exception $e) {
            echo "error while sending notification";
        }

        return redirect('/reversal')->with('success', 'Transaction reversed successfully!');

    }

    public function reverse2(Request $request, $id)
    {
        $input = $request->all();

        $tran = Transaction::find($id);

        $desc = "Being reversal of " . $tran->description;
        $user_name = $tran->user_name;

        $rtran = Transaction::where('ref', '=', $tran->ref)->get();

        foreach ($rtran as $tran) {
            $tran->status = "reversed";
            $tran->save();

            $amount = $tran->amount;

            $user = User::where("user_name", "=", $tran->user_name)->first();

            if ($tran->code == "tcommission") {
                $nBalance = $user->agent_commision - $tran->amount;

                $input["description"] = "Being reversal of " . $tran->description;
                $input["name"] = "Reversal";
                $input["status"] = "successful";
                $input["code"] = "reversal";
                $input["amount"] = $amount;
                $input["user_name"] = $tran->user_name;
                $input["i_wallet"] = $user->agent_commision;
                $input["f_wallet"] = $nBalance;
                $input["extra"] = 'Initiated by ' . Auth::user()->full_name;

                $user->update(["agent_commision" => $nBalance]);
                Transaction::create($input);
            } else {
                if ($tran->name == "data") {
                    $extra=explode("|",$tran->extra);

                    if(isset($extra[2])){
                        $cg=CGWallets::where([["user_id", $extra[2]], ['name', $extra[1]]])->first();

                        if(!$cg){
                            return redirect()->route('trans_pending')->with('success', 'Invalid payment selected encounter while reversing');
                        }

                        $cg->balance+=doubleval($extra[0]);
                        $cg->save();
                        $nBalance = $user->wallet;
                    }else{
                        $amount = $tran->amount;
                        $nBalance = $user->wallet + $amount;
                    }

                } else {
                    $nBalance = $user->wallet + $tran->amount;
                }

                $input["description"] = "Being reversal of " . $tran->description;
                $input["name"] = "Reversal";
                $input["status"] = "successful";
                $input["code"] = "reversal";
                $input["amount"] = $amount;
                $input["user_name"] = $tran->user_name;
                $input["i_wallet"] = $user->wallet;
                $input["f_wallet"] = $nBalance;
                $input["extra"] = 'Initiated by ' . Auth::user()->full_name;

                $user->update(["wallet" => $nBalance]);
                Transaction::create($input);

            }
        }

        try {
            $at = new PushNotificationController();
            $at->PushNoti($user_name, $desc, "Reversal");
        } catch (Exception $e) {
            echo "error while sending notification";
        }

        return redirect()->route('trans_pending')->with('success', 'Transaction reversed successfully!');

    }

    public function airtime2cashSettings()
    {
        $datas=Airtime2CashSettings::get();

        return view('airtime_cash_settings', ['datas' => $datas, 'i' =>1]);
    }

    public function airtime2cashSettingsEdit($id)
    {
        $data=Airtime2CashSettings::find($id);


        if(!$data){
            return redirect()->route('transaction.airtime2cashSettings')->with('error', 'Record does not exist');
        }


        return view('airtime_cash_settings_edit', ['data' => $data]);
    }

    public function airtime2cashSettingsED($id)
    {
        $data = Airtime2CashSettings::find($id);

        if(!$data){
            return redirect()->route('transaction.airtime2cashSettings')->with('error', 'Record does not exist');
        }

        $data->status=$data->status == 1 ? 0 : 1;
        $data->save();

        return redirect()->route('transaction.airtime2cashSettings')->with("success", "Status Modified successfully");
    }


    public function airtime2cashSettingsModify(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'number' => 'required',
            'discount' => 'required',
            'id' => 'required'
        );

        $validator = Validator::make($input, $rules);


        if (!$validator->passes()) {
            return back()->with('error', 'Incomplete request. Kindly check and try again');
        }


        $data=Airtime2CashSettings::find($input['id']);


        if(!$data){
            return redirect()->route('transaction.airtime2cashSettings')->with('error', 'Record does not exist');
        }

        $data->number=$input['number'];
        $data->discount=$input['discount'];
        $data->save();

        return redirect()->route('transaction.airtime2cashSettings')->with('success', 'Record updated successfully');

    }

    public function airtime2cash()
    {
        $datas=Airtime2Cash::where('receiver', '=', 'wallet')->orderBy('id', 'desc')->paginate(25);

        return view('airtime_cash', ['datas' => $datas, 'alist'=>true]);
    }

    public function airtime2cashpayment(Request $request)
    {
        $ref=Airtime2Cash::where('ref',$request->input('ref'))->first();

        if(!$ref){
            return back()->with('error', 'Invalid Reference Number!');
        }

        if($ref->status=="successful"){
            return back()->with('error', 'Payment made already!');
        }

        $r=0.2 * $ref->amount;
        $r_amount=round($ref->amount - $r);

        $user=User::where("user_name", "=", $ref->user_name)->first();

        $input["description"]=$ref->user_name ." wallet funded using Airtime2Wallet with the sum of #". $ref->amount ." Ref=>" . $ref->ref;
        $input["name"]="wallet funding";
        $input["status"]="successful";
        $input["code"]="fund_a2w";
        $input["amount"] = $r_amount;
        $input["user_name"] = $ref->user_name;
        $input["i_wallet"] = $user->wallet;
        $input["f_wallet"] = $user->wallet + $r_amount;
        $input["extra"] = 'Initiated by ' . Auth::user()->full_name;

        $ref->status = "successful";
        $ref->save();
        $user->update(["wallet" => $user->wallet + $r_amount]);
        Transaction::create($input);

        if ($ref->webhook_url != "" && $ref->webhook_url != null) {
            Airtime2CashNotificationJob::dispatch($ref)->delay(now()->addSeconds());
        }

        $at = new PushNotificationController();
        $at->PushNoti($input['user_name'], $input["description"], "Airtime Converter");

        return redirect('/airtime2cash')->with('success', 'Transaction successful!');
    }

    public function airtime2cashpaymentSuccess($id)
    {
        $ref=Airtime2Cash::find($id);

        if(!$ref){
            return back()->with('error', 'Invalid Reference Number!');
        }

        if($ref->status=="successful"){
            return back()->with('error', 'Payment made already!');
        }

        $ref->status = "successful";
        $ref->save();

        $at = new PushNotificationController();
        $at->PushNoti($ref->user_name, "Your recent airtime to cash is successful", "Airtime Converter");

        return redirect('/airtime2cash')->with('success', 'Transaction successful!');
    }

    public function airtime2cashpaymentCancel($id)
    {
        $ref=Airtime2Cash::find($id);

        if(!$ref){
            return back()->with('error', 'Invalid Reference Number!');
        }

        if($ref->status=="successful"){
            return back()->with('error', 'Payment made already!');
        }

        $ref->status = "cancelled";
        $ref->save();

        return redirect('/airtime2cash')->with('success', 'Transaction Cancelled!');
    }

    public function gmhistory(){

        $wallet = DB::table('tbl_generalmarket')->orderBy('id', 'desc')->paginate(25);

        return view('gmhistory', ['data' => $wallet]);
    }

    public function plcharges(){

        $wallet = DB::table('tbl_p_nd_l')->orderBy('id', 'desc')->paginate(25);

        return view('plcharges', ['data' => $wallet]);
    }

    public function cryptos()
    {

        $crypto = DB::table('tbl_luno')->orderBy('id', 'desc')->paginate(25);

        return view('crypto_request', ['crypto' => $crypto]);
    }

    public function finduser(Request $request)
    {
        $input = $request->all();
        $user_name = $input['user_name'];
        $phoneno = $input['phoneno'];
        $reference = $input['reference'];
        $amount = $input['amount'];
        $transaction_type = $input['transaction_type'];
        $date = $input['date'];

        // Instantiates a Query object
        $query = Transaction::OrderBy('id', 'desc')
            ->when(isset($user_name), function ($query) use ($user_name) {
                $query->where('user_name', 'LIKE', "%$user_name%");
            })
            ->when(isset($phoneno), function ($query) use ($phoneno) {
                $query->where('description', 'LIKE', "%$phoneno%");
            })
            ->when(isset($transaction_type), function ($query) use ($transaction_type) {
                $query->where('name', 'LIKE', "%$transaction_type%");
            })
            ->when(isset($reference), function ($query) use ($reference) {
                $query->where('ref', 'LIKE', "%$reference%");
            })
            ->when(isset($amount), function ($query) use ($amount) {
                $query->where('amount', "$amount");
            })
            ->when(isset($date), function ($query) use ($date) {
                $query->where('date', 'LIKE', "%$date%");
            })
            ->limit(100)
            ->get();

        $cquery = $query->count();

        return view('find_transaction', ['datas' => $query, 'count' => $cquery, 'result' => true]);
    }

}
