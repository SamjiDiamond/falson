<?php

namespace App\Console\Commands;

use App\Models\AirtimeCountry;
use Exception;
use Illuminate\Console\Command;

class CurrencyRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'samji:fetchrates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch live rates from designated server';

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
        $this->info("Fetching live rates");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://apilayer.net/api/live?source=USD&format=1&access_key=82df487186fec31d3cee2b1966d2b8d5",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        echo $response;

        curl_close($curl);

//        $response='{"success":true,"terms":"https:\/\/currencylayer.com\/terms","privacy":"https:\/\/currencylayer.com\/privacy","timestamp":1641629463,"source":"USD","quotes":{"USDAED":3.673042,"USDAFN":105.000368,"USDALL":107.000368,"USDAMD":483.788207,"USDANG":1.802362,"USDAOA":550.928041,"USDARS":103.292284,"USDAUD":1.392651,"USDAWG":1.8005,"USDAZN":1.70397,"USDBAM":1.730287,"USDBBD":2.019286,"USDBDT":85.939976,"USDBGN":1.724636,"USDBHD":0.377041,"USDBIF":2007.5,"USDBMD":1,"USDBND":1.359195,"USDBOB":6.885566,"USDBRL":5.635804,"USDBSD":1.000044,"USDBTC":2.3780072e-5,"USDBTN":74.311622,"USDBWP":11.662759,"USDBYN":2.588933,"USDBYR":19600,"USDBZD":2.015836,"USDCAD":1.264015,"USDCDF":2007.000362,"USDCHF":0.91889,"USDCLF":0.030019,"USDCLP":828.310396,"USDCNY":6.377704,"USDCOP":4050.54,"USDCRC":642.225859,"USDCUC":1,"USDCUP":26.5,"USDCVE":97.503897,"USDCZK":21.512904,"USDDJF":177.720394,"USDDKK":6.547315,"USDDOP":57.650393,"USDDZD":139.502826,"USDEGP":15.716273,"USDERN":15.000078,"USDETB":49.310392,"USDEUR":0.88015,"USDFJD":2.14204,"USDFKP":0.754379,"USDGBP":0.735565,"USDGEL":3.09504,"USDGGP":0.754379,"USDGHS":6.16504,"USDGIP":0.754379,"USDGMD":52.803853,"USDGNF":9095.000355,"USDGTQ":7.720618,"USDGYD":209.236077,"USDHKD":7.79767,"USDHNL":24.46504,"USDHRK":6.619604,"USDHTG":99.985845,"USDHUF":315.70504,"USDIDR":14318.75,"USDILS":3.115204,"USDIMP":0.754379,"USDINR":74.46755,"USDIQD":1460,"USDIRR":42250.000352,"USDISK":128.650386,"USDJEP":0.754379,"USDJMD":154.449104,"USDJOD":0.70904,"USDJPY":115.61704,"USDKES":113.250385,"USDKGS":84.803801,"USDKHR":4075.000351,"USDKMF":433.503796,"USDKPW":899.999923,"USDKRW":1197.655039,"USDKWD":0.30265,"USDKYD":0.833378,"USDKZT":435.484584,"USDLAK":11260.000349,"USDLBP":1514.000349,"USDLKR":202.886653,"USDLRD":147.125039,"USDLSL":15.640382,"USDLTL":2.95274,"USDLVL":0.60489,"USDLYD":4.603765,"USDMAD":9.260381,"USDMDL":17.886407,"USDMGA":3955.000347,"USDMKD":54.509665,"USDMMK":1778.102488,"USDMNT":2858.384653,"USDMOP":8.034413,"USDMRO":356.999828,"USDMUR":43.705216,"USDMVR":15.450378,"USDMWK":817.503739,"USDMXN":20.391404,"USDMYR":4.209039,"USDMZN":63.830377,"USDNAD":15.635039,"USDNGN":413.210377,"USDNIO":35.730377,"USDNOK":8.838825,"USDNPR":118.898596,"USDNZD":1.474949,"USDOMR":0.385011,"USDPAB":1.000044,"USDPEN":3.954504,"USDPGK":3.530375,"USDPHP":51.333603,"USDPKR":177.000342,"USDPLN":4.000937,"USDPYG":6954.748529,"USDQAR":3.641038,"USDRON":4.352504,"USDRSD":103.495038,"USDRUB":75.450373,"USDRWF":1016,"USDSAR":3.753698,"USDSBD":8.087376,"USDSCR":13.874486,"USDSDG":437.503678,"USDSEK":9.08186,"USDSGD":1.35768,"USDSHP":1.377404,"USDSLL":11335.000339,"USDSOS":584.000338,"USDSRD":21.268038,"USDSTD":20697.981008,"USDSVC":8.750387,"USDSYP":2512.492783,"USDSZL":15.635038,"USDTHB":33.63037,"USDTJS":11.295882,"USDTMT":3.5,"USDTND":2.881038,"USDTOP":2.284504,"USDTRY":13.875038,"USDTTD":6.788251,"USDTWD":27.653038,"USDTZS":2308.000336,"USDUAH":27.497501,"USDUGX":3545.273588,"USDUSD":1,"USDUYU":44.748972,"USDUZS":10810.000335,"USDVEF":213830222338.07285,"USDVND":22690,"USDVUV":113.252653,"USDWST":2.600171,"USDXAF":580.300343,"USDXAG":0.044703,"USDXAU":0.000556,"USDXCD":2.70255,"USDXDR":0.715161,"USDXOF":579.503602,"USDXPF":105.525037,"USDYER":250.250364,"USDZAR":15.585745,"USDZMK":9001.203593,"USDZMW":16.856372,"USDZWL":321.999592}}';

        $rep = json_decode($response, true);

        $quotes = $rep['quotes'];

        $counArray = AirtimeCountry::get();

        $this->info('Now updating each country');
        foreach ($counArray as $ca) {
            $eCoun = AirtimeCountry::find($ca['id']);
            $cc = $eCoun->currencyCode;
            dump($eCoun->currencyCode);

            try {
                dump($quotes["USD$cc"]);

                $eCoun->USDrate = $quotes["USD$cc"];
                $eCoun->save();
            } catch (Exception $e) {
                echo "rate not found for this county";
            }
        }
    }
}
