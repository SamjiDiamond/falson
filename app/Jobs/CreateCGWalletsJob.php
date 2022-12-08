<?php

namespace App\Jobs;

use App\Models\CGWallets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateCGWalletsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $user;
    public function __construct($user)
    {
        $this->user=$user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cg=env('CG_WALLETS');
        $cgs=explode("|", $cg);

        foreach ($cgs as $c) {
            $cgw = CGWallets::where([['user_id', $this->user], ['name', $c]])->exists();

            if(!$cgw){
                CGWallets::create([
                    "name" => $c,
                    "user_id" => $this->user
                ]);
            }
        }
    }
}
