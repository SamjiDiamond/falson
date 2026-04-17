<?php

namespace App\Jobs;

use App\Mail\AccountStatementEmail;
use App\Models\Transaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Rap2hpoutre\FastExcel\FastExcel;

class GenerateCustomerAccountStatement implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public function uniqueId(): string
    {
        $input = $this->input;

        return sha1(json_encode([
            'user_id' => $input['user_id'] ?? null,
            'user_name' => $input['user_name'] ?? null,
            'from' => $input['from'] ?? null,
            'to' => $input['to'] ?? null,
            'format' => $input['format'] ?? null,
        ]));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $input = $this->input;

        $date_from = $input['from'] ?? '';
        $date_to = $input['to'] ?? '';
        $userName = $input['user_name'] ?? null;
        $userId = $input['user_id'] ?? null;
        if (! is_string($userName) || $userName === '' || ! is_numeric($userId)) {
            return;
        }

        $trans = Transaction::where('user_name', $userName)->orderBy('id', 'asc')
            ->when($date_from !== '' && $date_to !== '', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at', [
                    Carbon::parse($date_from)->toDateTimeString(),
                    Carbon::parse($date_to)->addDay()->toDateTimeString(),
                ]);
            })
            ->limit(100)->get();

        $customer = User::find((int) $userId);
        if (! $customer || ! $customer->email) {
            return;
        }

        if (($input['format'] ?? null) == 'excel') {
            $filePath = storage_path('app/statement_'.$userId.'_'.time().'.xlsx');

            (new FastExcel($trans))->export($filePath, function ($data) {
                return [
                    'Name' => $data->name,
                    'Amount' => $data->amount,
                    'Status' => strtoupper($data->status),
                    'Reference' => $data->ref,
                    'Description' => $data->description,
                    'Date' => $data->date,
                    'IP Address' => $data->ip_address,
                    'Prev Balance' => $data->i_wallet,
                    'New Balance' => $data->f_wallet,
                ];
            });
        } else {
            $filePath = storage_path('app/statement_'.$userId.'_'.time().'.pdf');

            $data = ['user' => $customer, 'trans' => $trans, 'i' => 1, 'startDate' => $input['from'], 'endDate' => $input['to']];
            PDF::loadView('pdf_accountstatement', $data)->save($filePath);
        }

        Mail::to($customer->email)->send(new AccountStatementEmail($customer, $filePath));

    }
}
