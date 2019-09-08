<?php

namespace App\Console\Commands;

use App\crypto_user;
use App\Data;
use App\users_coin;
use App\users_coins_check;
use BotMan\Drivers\Telegram\TelegramDriver;
use Carbon\Carbon;
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
        $finalInsert = array();

        $symbols = users_coin::groupBy("symbol")->get();
        foreach ($symbols as $symbol => $value) {
//            $botman->say($value['symbol'], $adminID, TelegramDriver::class);
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


            $usersList = users_coin::where('symbol', $value['symbol'])->get();
            foreach ($usersList as $item) {
                $check = users_coins_check::where(
                    [
                        ['symbol_id', '=', $item['id']],
                        ['user_id', '=', $item['user_id']],
                    ])->latest('id')->first();
                $userInfo = crypto_user::find($item['user_id']);
                if (!$check['id']) {
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
                    $res = "---------------------------------\n";
                    $res .= "┌💎 #" . $finalInsert['symbol'] . "\n";
                    $res .= "├price: " . $finalInsert['price'] . "\n";
                    $res .= "├AVGPrice: \n";
                    $res .= "┊├minutes: " . $finalInsert['mins'] . "\n";
                    $res .= "┊├Price: " . $finalInsert['price2'] . "\n";
                    $res .= "├24hr: \n";
                    $res .= "┊├Price: " . $finalInsert['priceChange'] . "\n";
                    $res .= "┊├Price Percent: " . $finalInsert['priceChangePercent'] . "\n";
                    $res .= "┊├Volume: " . $finalInsert['volume'] . "\n";
                    $res .= "┊├quoteVolume: " . $finalInsert['quoteVolume'] . "\n";
                    $res .= "┊├count: " . $finalInsert['count'] . "\n";
                    $res .= "---------------------------------\n";
                    $res .= "\n\n @cryptoowatch \n\n";
                    $botman->say($res, $userInfo['chat_id'], TelegramDriver::class);
                } else {

                    $percentPrice=0;
                    $percentVolume=0;
                    $percentAvgPrice=0;
                    $percentChange=0;
                    $symbolVolume = "\xF0\x9F\x94\xBB";
                    $symbolPrice = "\xF0\x9F\x94\xBB";
                    $symbolAvg = "\xF0\x9F\x94\xBB";
                    $symbolChange = "\xF0\x9F\x94\xBB";

                    if ($check['price'] != 0) {
                        if ($check['price'] <= 0) {
                            $check['price'] *= 100000;
                            $finalInsert['price'] *= 100000;
                            $percentPrice = ($check['price'] - $finalInsert['price']) / $check['price'];
                            $percentPrice *= 100;
                            $check['price'] /= 100000;
                            $finalInsert['price'] /= 100000;
                        } else {
                            $percentPrice = ($check['price'] - $finalInsert['price']) / $check['price'];
                            $percentPrice *= 100;
                        }

                        if ($check['price'] < $finalInsert['price']) {
                            $percentPrice*=-1;
                            $symbolPrice = "\xF0\x9F\x94\xA5";
                        } elseif($check['price'] == $finalInsert['price']) {
                            $symbolPrice = "\xF0\x9F\x94\xB8";
                        }
                    }
                    if ($check['volume'] != 0) {
                        if ($check['volume'] <= 0) {
                            $check['volume'] *= 100000;
                            $finalInsert['volume'] *= 100000;
                            $percentVolume = ($check['volume'] - $finalInsert['volume']) / $check['volume'];
                            $percentVolume *= 100;
                            $check['volume'] /= 100000;
                            $finalInsert['volume'] /= 100000;
                        } else {
                            $percentVolume = ($check['volume'] - $finalInsert['volume']) / $check['volume'];
                            $percentVolume *= 100;
                        }

                        if ($check['volume'] < $finalInsert['volume']) {
                            $percentVolume*=-1;
                            $symbolVolume = "\xF0\x9F\x94\xA5";
                        } elseif($check['volume'] == $finalInsert['volume']) {
                            $symbolVolume = "\xF0\x9F\x94\xB8";
                        }
                    }
                    if ($check['avg_price'] != 0) {
                        if ($check['avg_price'] <= 0) {
                            $check['avg_price'] *= 100000;
                            $finalInsert['price2'] *= 100000;
                            $percentAvgPrice = ($check['avg_price'] - $finalInsert['price2']) / $check['avg_price'];
                            $percentAvgPrice *= 100;
                            $check['avg_price'] /= 100000;
                            $finalInsert['price2'] /= 100000;
                        } else {
                            $percentAvgPrice = ($check['avg_price'] - $finalInsert['price2']) / $check['avg_price'];
                            $percentAvgPrice *= 100;
                        }
                        if ($check['avg_price'] < $finalInsert['price2']) {
                            $percentAvgPrice*=-1;
                            $symbolAvg = "\xF0\x9F\x94\xA5";
                        } elseif($check['avg_price'] == $finalInsert['price2']) {
                            $symbolAvg = "\xF0\x9F\x94\xB8";
                        }
                    }
                    if ($check['priceChange'] != 0) {
                        if ($check['priceChange'] <= 0) {
                            $check['priceChange'] *= 1000000;
                            $finalInsert['priceChange'] *= 1000000;

                            $percentChange = ($check['priceChange'] - $finalInsert['priceChange']) / $check['priceChange'];
                            $percentChange *= 100;
                            $check['priceChange'] /= 1000000;
                            $finalInsert['priceChange'] /= 1000000;
                        } else {
                            $percentChange = ($check['priceChange'] - $finalInsert['priceChange']) / $check['priceChange'];
                            $percentChange *= 100;
                        }

                        if ($check['priceChange'] < $finalInsert['priceChange']) {
                            $percentChange*=-1;
                            $symbolChange = "\xF0\x9F\x94\xA5";
                        } elseif($check['priceChange'] == $finalInsert['priceChange']) {
                            $symbolChange = "\xF0\x9F\x94\xB8	";
                        }
                    }
                    $adminCheck = $check['price'] . "--\n" . $finalInsert['price'] . "\n\n";
                    $adminCheck .= $percentPrice . "--\n" . $percentVolume . "\n" . $finalInsert['symbol'];
//                    $botman->say($adminCheck, $adminID, TelegramDriver::class);
                    if ($percentPrice >= $item['percent'] || $percentVolume >= $item['percent']) {

                        $created = new Carbon($check['created_at']);
                        $now = Carbon::now();

                        if ($created->diff($now)->i > $item['period']) {
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

                            if($check['volume']>1){
                              $check['volume'] = round($check['volume'],2);
                            }
                            if($finalInsert['volume']>1){
                                $finalInsert['volume'] = round($finalInsert['volume'],2);
                            }

                            if($check['price']>1){
                                $check['price'] = round($check['price'],2);
                            }
                            if($finalInsert['price']>1){
                                $finalInsert['price']
                                    = round($finalInsert['price'],2);
                            }
                            $res = "┌💎 #" . $finalInsert['symbol'] . "\n";
                            $res .= "├price: \n┊├►" . $check['price'] . " --> <b>" . $finalInsert['price'] . "</b> | ($symbolPrice" . round($percentPrice, 0) . "%)" . "\n";
                            $res .= "├AVGPrice: \n";
                            $res .= "┊├minutes: " . $finalInsert['mins'] . "\n";
                            $res .= "┊├Price: \n┊┊├►" . $check['avg_price'] . " --> <b>" . $finalInsert['price2'] . "</b> | ($symbolAvg" . round($percentAvgPrice, 0) . " %)" . "\n";
                            $res .= "├24hr: \n";
                            $res .= "┊├Price: " . $finalInsert['priceChange'] . "($symbolChange" . round($percentChange, 0) . " %)" . "\n";
                            $res .= "┊├Price Percent: " . $finalInsert['priceChangePercent'] . "\n";
                            $res .= "┊├Volume: \n┊┊├►" . $check['volume'] . " --> <b>" . $finalInsert['volume'] . "</b> |($symbolVolume" . round($percentVolume, 0) . " %)" . "\n";
                            $res .= "┊├quoteVolume: " . $finalInsert['quoteVolume'] . "\n";
                            $res .= "┊├count: " . $finalInsert['count'] . "\n";
                            $res .= "└---------------------------------\n";
                            $res .= "\n @cryptoowatch \n\n";
                            $botman->say($res, $userInfo['chat_id'], TelegramDriver::class, ['parse_mode' => 'HTML']);
//                            $time = $finalInsert['symbol']."\n";
//                            $time .= $created->diff($now)->i.":".$created->diff($now)->h.":".$created->diff($now)->s." -> ".$item['period'];
//                            $time .= "\nVolume: ".round($percentVolume,2)."%";
//                            $time .= "\nPrice: ".round($percentPrice,2)."%";
//                            $time .= "\nPercent: ".$item['percent']."%";
//                            $botman->say($time."\n\n".$userInfo['name']." ".$userInfo['last_name']."\n\n", $adminID, TelegramDriver::class, ['parse_mode' => 'HTML']);
                        }
                    }
                }
            }
        }
    }
}
