<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class viewController extends Controller
{
    //

    public function index()
    {
        return view('actions');
    }

    public function set()
    {
        $client = new Client();
        $response = $client->get('https://api.telegram.org/bot968863053:AAFMZZpZUXKdxONPlLvQFclh3W-PR1G--9k/setWebhook?url=https://drrejeem.ir/botman');

        echo "<hr>";
        echo "<br>";
        echo "<h3>Set Information</h3>";
        echo "<br><br>";
        echo "Result Code: " . $response->getStatusCode(); # 200
        echo "<br>";
        echo "result: " . $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'
        echo "<br>";
        echo "<hr>";
        $this->info();

    }

    public function info()
    {
        $client = new Client();
        $response = $client->get('https://api.telegram.org/bot968863053:AAFMZZpZUXKdxONPlLvQFclh3W-PR1G--9k/getWebhookInfo');
        echo "<hr>";
        echo "<br>";
        echo "<h3>Info</h3>";
        echo "<br><br>";
        echo "Result Code: " . $response->getStatusCode(); # 200
        echo "<br>";
        echo "result: " . $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'
        echo "<br>";
        echo "<hr>";


    }

    public function update()
    {

        $client = new Client();
        $this->disable();
        $response = $client->get('https://api.telegram.org/bot968863053:AAFMZZpZUXKdxONPlLvQFclh3W-PR1G--9k/getUpdates');
        echo "<hr>";
        echo "<br>";
        echo "<h3>Update Info</h3>";
        echo "<br><br>";
        echo $response->getStatusCode(); # 200
//        echo $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'
        $updateId = 0;
        $res = json_decode($response->getBody(), true)['result'];
        if (sizeof($res) >= 1) {
            $this->disable();
            foreach ($res as $val => $item) {
                $updateId = $item['update_id'];
            }
            echo "<br>";
            echo "<h3>delete Pending Message</h3>";
            echo "<br><br>";
            echo "update ID: " . $updateId;
            echo "<br>";
            $client = new Client();
            $updateId += 1;
            echo "new Update ID: " . $updateId;
            $response = $client->get('https://api.telegram.org/bot968863053:AAFMZZpZUXKdxONPlLvQFclh3W-PR1G--9k/getUpdates?offset=' . $updateId);
            echo "<br>";
            echo "result: " . $response->getStatusCode(); # 200
            $this->set();
        }
        echo "<br>";
        echo "<hr>";
        $this->info();

    }

    public function disable()
    {
        $this->info();
        $client = new Client();
        $response = $client->get('https://api.telegram.org/bot968863053:AAFMZZpZUXKdxONPlLvQFclh3W-PR1G--9k/setWebhook');
        echo "<hr>";
        echo "<br>";
        echo "<h3>disabled</h3>";
        echo "<br><br>";
        echo $response->getStatusCode(); # 200
        echo "<br>";
        echo $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'
        echo "<br>";
        echo "<hr>";

    }

    public function testCode()
    {
        $start5Min = strtotime('-5 minutes', time());
        $end5Min = time();
        $start1Hour = strtotime('-1 hour', time());
        $start12Hour = strtotime('-12 hour', time());
        $start1Day = strtotime('-24 hour', time());
        $start7Day = strtotime('-7 day', time());
        $d['timeStart'] = $start5Min;
        $d['timeEnd'] = $end5Min;
        $d['start'] = date("Y/m/d H:i:s", $start5Min);
        $d['end'] = date("Y/m/d H:i:s", $end5Min);
        $d['TestStart'] = date("Y/m/d H:i:s", '1607361300');
        $d['TestEnd'] = date("Y/m/d H:i:s", '1607361599');


        $rsInfo['5min'] = $this->getInfo('BTCUSDT', '5m', $start5Min, $end5Min);
        $rsInfo['1Hour'] = $this->getInfo('BTCUSDT', '1h', $start1Hour, $end5Min);
        $rsInfo['12Hour'] = $this->getInfo('BTCUSDT', '12h', $start12Hour, $end5Min);
        $rsInfo['1Day'] = $this->getInfo('BTCUSDT', '1d', $start1Day, $end5Min);
        $rsInfo['7Day'] = $this->getInfo('BTCUSDT', '1w', $start7Day, $end5Min);
        return $rsInfo;
    }

    public function getInfo($symbol, $interval, $start, $end)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.binance.com/api/v3/klines?symbol={$symbol}&interval={$interval}&startTime={$start}000&endTime={$end}000&limit=500",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "postman-token: 58a2bb8a-5723-7a0f-3cdc-c3a2d0f15b7c"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $res = json_decode($response, true);
            echo $interval;
            print_r($res);
            $rs = $res[0][1] / $res[0][2];
            return (100 - (100 / (1 + $rs)));
        }
    }
}
