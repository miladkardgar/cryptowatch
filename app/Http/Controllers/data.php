<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\Drivers\Telegram\TelegramDriver;


class data extends Controller
{

    public function getData()
    {


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
            $botman = app('botman');
//            $botman->say("Data List:", env('TELEGRAM_CHANNEL'), TelegramDriver::class);
            $i = 0;
//            print_r($response);
            foreach ($response as $item => $value) {
//                    $botman->say($item, env('TELEGRAM_CHANNEL'), TelegramDriver::class);
                    echo $item['symbol'];
//                $res = '';
//                $res .= "---------------------------------\n";
//                $res .= "Symble: " . $value['symbol'] . "\n\n";
//                $res .= "Price: " . $value['priceChange'] . "\n";
//                $res .= "Price Percent: " . $value['priceChangePercent'] . "\n";
//                $res .= "Volume: " . $value['volume'] . "\n";
//                $res .= "quoteVolume: " . $value['quoteVolume'] . "\n";
//                $res .= "count: " . $value['count'] . "\n";
//                $res .= "\n\n @cryptoowatch \n";
//                $res .= "---------------------------------\n\n";
////                    $botman->say($res, env('TELEGRAM_CHANNEL'), TelegramDriver::class);
////                    sleep(2);
//                print_r($res);
//                echo "<br>";
//                echo "<br>";
//                $i++;
            }
        }

        die;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://dex.binance.org/api/v1/ticker/24hr",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "postman-token: 52a99a09-6f4e-aeee-483c-3e07d9c05bd3"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response, true);
            $botman = app('botman');
            $botman->say("Data List:", env('TELEGRAM_CHANNEL'), TelegramDriver::class);
            $i = 0;
            print_r($response);
            foreach ($response as $item => $value) {
                if ($i < 2) {
//                    $botman->say($item, env('TELEGRAM_CHANNEL'), TelegramDriver::class);
//                    print_r($value);
                    $res = '';
                    $res .= "---------------------------------\n";
                    $res .= "Symble: " . $value['symbol'] . "\n\n";
                    $res .= "Price: " . $value['priceChange'] . "\n";
                    $res .= "Price Percent: " . $value['priceChangePercent'] . "\n";
                    $res .= "Volume: " . $value['volume'] . "\n";
                    $res .= "quoteVolume: " . $value['quoteVolume'] . "\n";
                    $res .= "count: " . $value['count'] . "\n";
                    $res .= "\n\n @cryptoowatch \n";
                    $res .= "---------------------------------\n\n";
                    $botman->say($res, env('TELEGRAM_CHANNEL'), TelegramDriver::class);
//                    sleep(2);
                }
                print_r($value);
                echo "<br>";
                echo "<br>";
                $i++;
            }

//            echo $response;
        }
    }
}