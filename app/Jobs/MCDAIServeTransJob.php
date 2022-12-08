<?php

namespace App\Jobs;

use App\Http\Controllers\Api\ServeRequestController;
use App\Models\Serverlog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MCDAIServeTransJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $data;

    public function __construct($data)
    {
        $this->data=$data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $s=Serverlog::find($this->data);

        $input['user_name'] =$s->user_name;
        $input['api'] = $s->api;
        $input['coded'] = $s->coded;
        $input['phone'] = $s->phone;
        $input['amount'] = $s->amount;
        $input['transid'] = $s->transid;
        $input['service'] = $s->service;
        $input['network'] = $s->network;
        $input['payment_method'] = $s->payment_method;
        $input['version'] = $s->version;

        $r= new Request($input);
        if($s->service=="airtime"){
            $t=new ServeRequestController();
            $t->buyairtime($r);
        }

        if($s->service=="data"){
            $t=new ServeRequestController();
            $t->buydata($r);
        }
    }
}
