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
            foreach ($response as $item) {
                print_r($item);
            }
            $botman = app('botman');
//            $botman->say($response, env('TELEGRAM_CHANNEL'), TelegramDriver::class);
//            echo $response;
        }
    }
}