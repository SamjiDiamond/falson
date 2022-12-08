<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AgentPdfGeneratorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $input;
    public $user;

    public function __construct($input, $user)
    {
        $this->input = $input;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $request = $this->input;
        $input = $this->user;

        $url = "https://mcd.5starcompany.com.ng/app/agent_pdf_generator.php?";
        $params = "full_name=" . urlencode($input->full_name);
        $params .= "&company_name=" . urlencode($input->company_name);
        $params .= "&street_no=" . urlencode($request['street']);
        $params .= "&state=" . urlencode($request['state']);
        $params .= "&country=" . urlencode($request['country']);
        $params .= "&request=Agent";
        $params .= "&user_name=" . $input->user_name;
        $params .= "&email=" . urlencode($input->email);

        try {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url . $params,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
            ));

            $gf = curl_exec($curl);

            curl_close($curl);

//            ===================================

            $params .= "&dob=" . $input->dob;
            $params .= "&bvn=" . $input->bvn;
            $params .= "&address_no=" . urlencode($input->address);

            $url = "https://mcd.5starcompany.com.ng/app/update_agent_details_v2_mail.php?";

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url . $params,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
            ));

            $response = curl_exec($curl);

            curl_close($curl);

        } catch (Exception $e) {
            echo $e;
        }

        echo $gf;
        echo "<br/>";
        echo $response;
    }
}
