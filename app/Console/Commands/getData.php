<?php

namespace App\Console\Commands;

use App\Data;
use App\users_coin;
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
        $symbols = users_coin::groupBy("symbol")->get();
        foreach ($symbols as $symbol => $value) {
            $res = '';
            $finalInsert = array();
            $resFinal = false;
            $max = Data::latest('id')->where('symbol', $value['symbol'])->first();
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.binance.com/api/v3/ticker/price?symbol=" . strtoupper($value['symbol']),
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
                $finalInsert['symbol'] = $response['symbol'];
                $finalInsert['price'] = $response['price'];
            }

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.binance.com/api/v3/avgPrice?symbol=" . strtoupper($value['symbol']),
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
            }

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.binance.com/api/v1/ticker/24hr?symbol=" . strtoupper($value['symbol']),
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
            }


            $percentPrice = 0;
            $percentVolume = 0;
            $percentAvg = 0;
            $percentChange = 0;
            $symbolVolume = "\xF0\x9F\x94\xBB";
            $symbolPrice = "\xF0\x9F\x94\xBB";
            $symbolAvg = "\xF0\x9F\x94\xBB";
            $symbolChange = "\xF0\x9F\x94\xBB";

            if ($max['price'] != 0) {
                if ($max['price'] <= 0) {
                    $max['price'] *= 100000;
                    $finalInsert['price'] *= 100000;
                    $percentPrice = ($max['price'] - $finalInsert['price']) / $max['price'];
                    $percentPrice *= 100;
                    $max['price'] /= 100000;
                    $finalInsert['price'] /= 100000;
                } else {
                    $percentPrice = ($max['price'] - $finalInsert['price']) / $max['price'];
                    $percentPrice *= 100;
                }

                if ($max['price'] < $finalInsert['price']) {
                    $percentPrice *= -1;
                    $symbolPrice = "\xF0\x9F\x94\xA5";
                } elseif ($max['price'] == $finalInsert['price']) {
                    $symbolPrice = "\xF0\x9F\x94\xB8";
                }
            }
            if ($max['volume'] != 0) {
                if ($max['volume'] <= 0) {
                    $max['volume'] *= 100000;
                    $finalInsert['volume'] *= 100000;
                    $percentVolume = ($max['volume'] - $finalInsert['volume']) / $max['volume'];
                    $percentVolume *= 100;
                    $max['volume'] /= 100000;
                    $finalInsert['volume'] /= 100000;
                } else {
                    $percentVolume = ($max['volume'] - $finalInsert['volume']) / $max['volume'];
                    $percentVolume *= 100;
                }

                if ($max['volume'] < $finalInsert['volume']) {
                    $percentVolume *= -1;
                    $symbolVolume = "\xF0\x9F\x94\xA5";
                } elseif ($max['volume'] == $finalInsert['volume']) {
                    $symbolVolume = "\xF0\x9F\x94\xB8";
                }
            }
            if ($max['avg_price'] != 0) {
                if ($max['avg_price'] <= 0) {
                    $max['avg_price'] *= 100000;
                    $finalInsert['price2'] *= 100000;
                    $percentAvg = ($max['avg_price'] - $finalInsert['price2']) / $max['avg_price'];
                    $percentAvg *= 100;
                    $max['avg_price'] /= 100000;
                    $finalInsert['price2'] /= 100000;
                } else {
                    $percentAvg = ($max['avg_price'] - $finalInsert['price2']) / $max['avg_price'];
                    $percentAvg *= 100;
                }
                if ($max['avg_price'] < $finalInsert['price2']) {
                    $percentAvg *= -1;
                    $symbolAvg = "\xF0\x9F\x94\xA5";
                } elseif ($max['avg_price'] == $finalInsert['price2']) {
                    $symbolAvg = "\xF0\x9F\x94\xB8";
                }
            }
            if ($max['priceChange'] != 0) {
                if ($max['priceChange'] <= 0) {
                    $max['priceChange'] *= 1000000;
                    $finalInsert['priceChange'] *= 1000000;

                    $percentChange = ($max['priceChange'] - $finalInsert['priceChange']) / $max['priceChange'];
                    $percentChange *= 100;
                    $max['priceChange'] /= 1000000;
                    $finalInsert['priceChange'] /= 1000000;
                } else {
                    $percentChange = ($max['priceChange'] - $finalInsert['priceChange']) / $max['priceChange'];
                    $percentChange *= 100;
                }

                if ($max['priceChange'] < $finalInsert['priceChange']) {
                    $percentChange *= -1;
                    $symbolChange = "\xF0\x9F\x94\xA5";
                } elseif ($max['priceChange'] == $finalInsert['priceChange']) {
                    $symbolChange = "\xF0\x9F\x94\xB8	";
                }
            }

            if ($percentPrice >= 1 || $percentVolume >= 1) {
                $resFinal = true;
                $maxVolume = $max['volume'];
                $finalVolume = $finalInsert['volume'];
                $maxPrice = $max['price'];
                $finalPrice= $finalInsert['price'];
                if($max['volume']>1){
                    $maxVolume = round($max['volume'],2);
                }
                if($finalInsert['volume']>1){
                    $finalVolume = round($finalInsert['volume'],2);
                }
                if($max['price']>1){
                    $maxPrice = round($max['price'],2);
                }
                if($finalInsert['price']>1){
                    $finalPrice = round($finalInsert['price'],2);
                }

                $res = "┌💎 #" . $finalInsert['symbol'] . "\n";
                $res .= "├price: \n┊├►" . $maxPrice . " --> <b>" . $finalPrice . "</b> | ($symbolPrice" . round($percentPrice, 2) . "%)" . "\n";
                $res .= "├AVGPrice: \n";
                $res .= "┊├minutes: " . $finalInsert['mins'] . "\n";
                $res .= "┊├Price: \n┊┊├►" . $max['avg_price'] . " --> <b>" . $finalInsert['price2'] . "</b> | ($symbolAvg" . round($percentAvg, 2) . " %)" . "\n";
                $res .= "├24hr: \n";
                $res .= "┊├Price: " . $finalInsert['priceChange'] . "($symbolChange" . round($percentChange, 0) . " %)" . "\n";
                $res .= "┊├Price Percent: " . $finalInsert['priceChangePercent'] . "\n";
                $res .= "┊├Volume: \n┊┊├►" . $maxVolume . " --> <b>" . $finalVolume . "</b> |($symbolVolume" . round($percentVolume, 2) . " %)" . "\n";
                $res .= "┊├quoteVolume: " . $finalInsert['quoteVolume'] . "\n";
                $res .= "┊├count: " . $finalInsert['count'] . "\n";
                $res .= "└---------------------------------\n";
                $res .= "\n @cryptoowatch \n\n";
            }


            $botman->say($res, $channel, TelegramDriver::class, ['parse_mode' => 'HTML']);
            if ($resFinal == true) {
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
            }
        }
    }
}
