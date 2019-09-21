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
        echo "Result Code: " .  $response->getStatusCode(); # 200
        echo "<br>";
        echo "result: " .  $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'
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
        echo "Result Code: " .  $response->getStatusCode(); # 200
        echo "<br>";
        echo "result: " .  $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'
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
            echo "update ID: " .  $updateId;
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
}
