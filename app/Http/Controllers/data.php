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
            CURLOPT_URL => "https://api.binance.com/api/v1/ticker/24hr?symbol=LTCBTC",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"symbol=LTCBTC&side=BUY&type=LIMIT&timeInForce=GTC&quantity=1&price=0.1&recvWindow=5000&timestamp=1499827319559\"\r\n\r\n\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
                "postman-token: 718e82c8-00ed-2c0c-e4dc-a5d38d94b9f8"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
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