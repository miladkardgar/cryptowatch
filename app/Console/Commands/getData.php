<?php

namespace App\Console\Commands;

use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Console\Command;

class getData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getData:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send every 1 minutes data from binance';

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
        //
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.binance.com/api/v1/ticker/24hr?symbol=BTCUSDT",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "apikey: TZMPp6CmDEBJIgESoINrRmBu963Pft1OZc9iz6lia8xAFcL6RHIrqrLQAKfAGcDL",
                "cache-control: no-cache",
                "postman-token: f261e154-5bfd-5091-6ef1-161c14d9bcf2",
                "secretkey: oSaJwiMYrA331cL6kPOuMK3bLGPNOu723CQlghqLY9oJIQgSP9kPm1dFSDlJKAwn"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response, true);
            print_r($response);
            $botman = app('botman');
            $botman->say("Data List:", env('TELEGRAM_CHANNEL'), TelegramDriver::class);
            $res = '';
            $res .= "---------------------------------\n";
            $res .= "Symble: " . $response['symbol'] . "\n\n";
            $res .= "Price: " . $response['priceChange'] . "\n";
            $res .= "Price Percent: " . $response['priceChangePercent'] . "\n";
            $res .= "Volume: " . $response['volume'] . "\n";
            $res .= "quoteVolume: " . $response['quoteVolume'] . "\n";
            $res .= "count: " . $response['count'] . "\n";
            $res .= "\n\n @cryptoowatch \n";
            $res .= "---------------------------------\n\n";
            $botman->say($res, env('TELEGRAM_CHANNEL'), TelegramDriver::class);
        }
    }
}
