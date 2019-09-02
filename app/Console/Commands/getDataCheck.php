<?php

namespace App\Console\Commands;

use App\crypto_user;
use App\Data;
use App\users_coin;
use App\users_coins_check;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Console\Command;

class getDataCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkData:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check every 1 minutes data from binance';

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

        $symbols = users_coin::groupBy("symbol")->get();
        foreach ($symbols as $symbol) {

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.binance.com/api/v3/ticker/price?symbol=" . $symbol['tymbol'],
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
            }


            $usersList = users_coin::where('symbol', $finalInsert['symbol'])->get();
            foreach ($usersList as $item) {
                $check = users_coins_check::where('coin_id', $item['id'])->latest('id')->first();
                $userInfo = crypto_user::find($item['user_id']);
                if (!$check) {
                    users_coins_check::create(
                        [
                            'user_id' => $item['user_id'],
                            'symbol_id' => $item['id'],
                            'symbol' => $finalInsert['symbol'],
                            'price' => $finalInsert['price'],
                            'avg_mines' => $finalInsert['mins'],
                            'avg_price' => $finalInsert['price2'],
                            'priceChange' => $finalInsert['priceChange'],
                            'priceChangePercent' => $finalInsert['priceChangePercent'],
                            'volume' => $finalInsert['volume'],
                            'quoteVolume' => $finalInsert['quoteVolume'],
                            'count' => $finalInsert['count'],
                            'parent_id' => 0,
                            'volume_change' => 0,
                            'price_change' => 0,
                        ]
                    );
                    $avgPrice=0;
                    if ($finalInsert['price2'] > 1) {
                        $avgPrice = number_format(round($finalInsert['price2'], 2), 2);
                    }
                    $priceChange = 0;
                    $volume = 0;
                    $quoteVolume = 0;
                    if ($finalInsert['priceChange'] > 1) {
                        $priceChange = number_format(round($finalInsert['priceChange'], 2), 2);
                    }
                    if ($finalInsert['volume'] > 1) {
                        $volume = number_format(round($finalInsert['volume'], 2), 2);
                    }
                    if ($finalInsert['quoteVolume'] > 1) {
                        $quoteVolume = number_format(round($finalInsert['quoteVolume'], 2), 2);
                    }
                    $res = "---------------------------------\n";
                    $res .= "┌💎 #" . $finalInsert['symbol'] . "\n";
                    $res .= "├price: " . number_format($finalInsert['price'], 2) . " | ($symbolPrice" . round($percentPrice, 0) . "%)" . "\n";
                    $res .= "├AVGPrice: \n";
                    $res .= "┊├minutes: " . $finalInsert['mins'] . "\n";
                    $res .= "┊├Price: " . $avgPrice . "\n";
                    $res .= "├24hr: \n";
                    $res .= "┊├Price: " . $priceChange . "\n";
                    $res .= "┊├Price Percent: " . $finalInsert['priceChangePercent'] . "\n";
                    $res .= "┊├Volume: " . $volume. "\n";
                    $res .= "┊├quoteVolume: " . $quoteVolume . "\n";
                    $res .= "┊├count: " . $finalInsert['count'] . "\n";
                    $res .= "---------------------------------\n";
                    $res .= "\n\n @cryptoowatch \n\n";
                    $botman->say($res, $userInfo['chat_id'], TelegramDriver::class);
                } else {
                    $percentPrice = round(100 * ($finalInsert['price'] - $check['price']) / $check['price'], 0);
                    $percentVolume = round(100 * ($finalInsert['volume'] - $check['volume']) / $check['volume'], 0);
                    $percentAvgPrice = 100 * ($finalInsert['price2'] - $check['avg_price']) / $check['avg_price'];
                    $percentChange = 100 * ($finalInsert['priceChange'] - $check['priceChange']) / $check['priceChange'];

                    if ($percentVolume >= $item['percent'] || $percentVolume >= $item['percent']) {
                        $create = strtotime($item['created_at']);
                        $now = time() - $create;
                        $sendTime = $item['period'] * 60;
                        if ($now > $sendTime) {
                            users_coins_check::create(
                                [
                                    'user_id' => $item['user_id'],
                                    'symbol_id' => $item['id'],
                                    'symbol' => $finalInsert['symbol'],
                                    'price' => $finalInsert['price'],
                                    'avg_mines' => $finalInsert['mins'],
                                    'avg_price' => $finalInsert['price2'],
                                    'priceChange' => $finalInsert['priceChange'],
                                    'priceChangePercent' => $finalInsert['priceChangePercent'],
                                    'volume' => $finalInsert['volume'],
                                    'quoteVolume' => $finalInsert['quoteVolume'],
                                    'count' => $finalInsert['count'],
                                    'parent_id' => 0,
                                    'volume_change' => 0,
                                    'price_change' => 0,
                                ]
                            );

                            $symbolPrice = "\xF0\x9F\x92\xA4";
                            if ($percentPrice > 1) {
                                $symbolPrice = "\xF0\x9F\x94\xA5";
                            } elseif ($percentPrice <= -1) {
                                $symbolPrice = "\xF0\x9F\x93\x89";
                            }

                            $symbolAvg = "\xF0\x9F\x92\xA4";
                            if ($percentAvgPrice > 1) {
                                $symbolAvg = "\xF0\x9F\x94\xA5";
                            } elseif ($percentAvgPrice <= -1) {
                                $symbolAvg = "\xF0\x9F\x93\x89";
                            }
                            $avgPrice = 0;
                            if ($finalInsert['price2'] > 1) {
                                $avgPrice = number_format(round($finalInsert['price2'], 2), 2);
                            }


                            $symbolChange = "\xF0\x9F\x92\xA4";
                            if ($percentChange > 1) {
                                $symbolChange = "\xF0\x9F\x94\xA5";
                            } elseif ($percentChange <= -1) {
                                $symbolChange = "\xF0\x9F\x93\x89";
                            }

                            $symbolVolume = "\xF0\x9F\x92\xA4";
                            if ($percentVolume > 1) {
                                $symbolVolume = "\xF0\x9F\x94\xA5";
                            } elseif ($percentVolume <= -1) {
                                $symbolVolume = "\xF0\x9F\x93\x89";
                            }
                            $priceChange = 0;
                            $volume = 0;
                            $quoteVolume = 0;
                            if ($finalInsert['priceChange'] > 1) {
                                $priceChange = number_format(round($finalInsert['priceChange'], 2), 2);
                            }
                            if ($finalInsert['volume'] > 1) {
                                $volume = number_format(round($finalInsert['volume'], 2), 2);
                            }
                            if ($finalInsert['quoteVolume'] > 1) {
                                $quoteVolume = number_format(round($finalInsert['quoteVolume'], 2), 2);
                            }


                            $res = "---------------------------------\n";
                            $res .= "┌💎 #" . $finalInsert['symbol'] . "\n";
                            $res .= "├price: " . number_format($finalInsert['price'], 2) . " | ($symbolPrice" . round($percentPrice, 0) . "%)" . "\n";
                            $res .= "├AVGPrice: \n";
                            $res .= "┊├minutes: " . $finalInsert['mins'] . "\n";
                            $res .= "┊├Price: " . $avgPrice . "($symbolAvg" . round($percentAvgPrice, 0) . "%)" . "\n";
                            $res .= "├24hr: \n";
                            $res .= "┊├Price: " . $priceChange . "($symbolChange" . round($percentChange, 0) . "%)" . "\n";
                            $res .= "┊├Price Percent: " . $finalInsert['priceChangePercent'] . "\n";
                            $res .= "┊├Volume: " . $volume . "($symbolVolume" . round($percentVolume, 0) . "%)" . "\n";
                            $res .= "┊├quoteVolume: " . $quoteVolume . "\n";
                            $res .= "┊├count: " . $finalInsert['count'] . "\n";
                            $res .= "---------------------------------\n";
                            $res .= "\n\n @cryptoowatch \n\n";
                            $botman->say($res, $userInfo['chat_id'], TelegramDriver::class);
                        }
                    }
                }
            }
        }
    }
}
