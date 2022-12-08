<?php

namespace App\Console\Commands;

use App\Models\PromoCode;
use Illuminate\Console\Command;

class PromocodeGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promocode:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generating fresh promocode';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $qtys = env('PROMO_CODE_QUANTITY', 10);
        $this->info('Generating ' . $qtys . ' promo code');

        for ($i = 0; $i < $qtys; $i++) {
            $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $sh1 = str_shuffle($str);
            $st = $sh1 . uniqid();
            $sh2 = str_shuffle($st);
            $sh3 = "MCD-" . substr($sh2, 0, env('PROMO_CODE_LENGTH', 8));

            PromoCode::create([
                'code' => $sh3
            ]);
        }

        $this->info("Promo code generated successfully");

    }
}
