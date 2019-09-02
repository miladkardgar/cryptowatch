<?php

namespace App\Console\Commands;

use App\Data;
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

        $botman = app('botman');
        $adminID = env('ADMIN_ID');
        $channel = env('TELEGRAM_CHANNEL');
        $finalInsert = array();
        $res = '';
        $i = 0;
        $resFinal = false;
        $max = Data::latest('id')->first();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT",
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
            if ($response['price'] - $max['price'] <= 0) {
                $min = (int)$response['price'] - (int)$max['price'];
            } else {
                $min = (int)$response['price'] - (int)$max['price'];
            }
            $r = (100 * $min) / $max['price'];
            if ($r > 1) {
                $resFinal = true;
            }
            $symbol = "\xF0\x9F\x92\xA4";
            if ($r > 1) {
                $symbol = "\xF0\x9F\x94\xA5";
            } elseif ($r <= -1) {
                $symbol = "\xF0\x9F\x93\x89";
            }


            $finalInsert['symbol'] = $response['symbol'];
            $finalInsert['price'] = $response['price'];

            if ($response['price'] > 1) {
                $response['price'] = number_format(round($response['price'], 2), 2);
            }

            $res .= "---------------------------------\n";
            $res .= "┌💎 #" . $response['symbol'] . "\n";
            $res .= "├price: " . $response['price'] . " | ($symbol" . round($r, 0) . "%)" . "\n";
            $i++;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.binance.com/api/v3/avgPrice?symbol=BTCUSDT",
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
            $response2 = json_decode($response, true);
            $finalInsert['mins'] = $response2['mins'];
            $finalInsert['price2'] = $response2['price'];
            $a = 100 * ($response2['price'] - $max['avg_price']) / $max['avg_price'];

            $symbol = "\xF0\x9F\x92\xA4";
            if ($a > 1) {
                $symbol = "\xF0\x9F\x94\xA5";
            } elseif ($a <= -1) {
                $symbol = "\xF0\x9F\x93\x89";
            }
            if ($response2['price'] > 1) {
                $response2['price'] = number_format(round($response2['price'], 2), 2);
            }
            $res .= "├AVGPrice: \n";
            $res .= "┊├minutes: " . $response2['mins'] . "\n";
            $res .= "┊├Price: " . $response2['price'] . "($symbol" . round($a, 0) . "%)" . "\n";
            $i++;
        }

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
            $response3 = json_decode($response, true);


            $finalInsert['priceChange'] = $response3['priceChange'];
            $finalInsert['priceChangePercent'] = $response3['priceChangePercent'];
            $finalInsert['volume'] = $response3['volume'];
            $finalInsert['quoteVolume'] = $response3['quoteVolume'];
            $finalInsert['count'] = $response3['count'];

            $h = 100 * ($response3['priceChange'] - $max['priceChange']) / $max['priceChange'];

            $symbol = "\xF0\x9F\x92\xA4";
            if ($h > 1) {
                $symbol = "\xF0\x9F\x94\xA5";
            } elseif ($h <= -1) {
                $symbol = "\xF0\x9F\x93\x89";
            }
            $w = 100 * ($response3['volume'] - $max['volume']) / $max['volume'];
            if ($w > 1) {
                $resFinal = true;
            }
            $symbolW = "\xF0\x9F\x92\xA4";
            if ($w > 1) {
                $symbolW = "\xF0\x9F\x94\xA5";
            } elseif ($w <= -1) {
                $symbolW = "\xF0\x9F\x93\x89";
            }

            if ($response3['priceChange'] > 1) {
                $response3['priceChange'] = number_format(round($response3['priceChange'], 2), 2);
            }
            if ($response3['volume'] > 1) {
                $response3['volume'] = number_format(round($response3['volume'], 2), 2);
            }
            if ($response3['quoteVolume'] > 1) {
                $response3['quoteVolume'] = number_format(round($response3['quoteVolume'], 2), 2);
            }
            $res .= "├24hr: \n";
            $res .= "┊├Price: " . $response3['priceChange'] . "($symbol" . round($h, 0) . "%)" . "\n";
            $res .= "┊├Price Percent: " . $response3['priceChangePercent'] . "\n";
            $res .= "┊├Volume: " . $response3['volume'] . "($symbolW" . round($w, 0) . "%)" . "\n";
            $res .= "┊├quoteVolume: " . $response3['quoteVolume'] . "\n";
            $res .= "┊├count: " . $response3['count'] . "\n";
            $res .= "---------------------------------\n";
            $res .= "\n\n @cryptoowatch \n\n";
            $i++;
        }
        if ($i == 3 && $resFinal) {
            Data::create(
                [
                    'symbol' => $finalInsert['symbol'],
                    'price' => $finalInsert['price'],
                    'avg_mines' => $finalInsert['mins'],
                    'avg_price' => $finalInsert['price2'],
                    'priceChange' => $finalInsert['priceChange'],
                    'priceChangePercent' => $finalInsert['priceChangePercent'],
                    'volume' => $finalInsert['volume'],
                    'quoteVolume' => $finalInsert['quoteVolume'],
                    'count' => $finalInsert['count'],
                ]
            );
            $botman->say($res, $channel, TelegramDriver::class);
        }
    }
}
