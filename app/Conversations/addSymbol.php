<?php

namespace App\Conversations;

use App\crypto_user;
use App\users_coin;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\TelegramDriver;
use Validator;

class addSymbol extends Conversation
{
    public function adminMessage($Message)
    {
        $bot = app('botman');
        $bot->say("\xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	" . "\n\n" . $Message . "\n\n \xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	", env('ADMIN_ID'), TelegramDriver::class);
    }

    public function askCoins()
    {
        $user = $this->bot->getUser();
        $this->name = $user->getFirstName();
        $this->last_name = $user->getLastName();
        $userInfo = crypto_user::where('chat_id', $user->getId())->first();
        $this->userID = $userInfo['id'];
        $res = $user->getFirstName() . ' ' . $user->getLastName() . "\n";
        $res .= "وقت بخیر" . "\n\n";
        $res .= 'لطفاً نام ارز مورد نظر را وارد نمایید.' . "\n";
        $res .= ' مثال: BTC/USDT' . "\n\n";
        $this->ask($res, function (Answer $answer) {
            $validator = Validator::make(['coin' => $answer->getText()], [
                'coin' => 'required|string',
            ]);
            if ($validator->valid() && $this->check($answer->getText())) {
                $coinInfo = users_coin::where(
                    [
                        ['symbol', '=', $answer->getText()],
                        ['user_id', '=', $this->userID],
                    ]
                )->first();
                if (!$coinInfo) {
                    $coin = users_coin::create(
                        [
                            'symbol' => strtoupper($answer->getText()),
                            'user_id' => $this->userID,
                        ]
                    );
                    $coinID = $coin['id'];

                    $adminMessage = $this->name . " " . $this->last_name . "\n\n" . " ارز " . strtoupper($answer->getText()) . "\n\n اضافه کرد.";
                    $this->adminMessage($adminMessage);
                } else {
                    $coinID = $coinInfo['id'];
                }
                $this->bot->userStorage()->save([
                    'coins' => $answer->getText(),
                ]);
                $this->askNextLevel($coinID);
            } else {
                $adminMessage = $this->name . " " . $this->last_name . "\n\n" . " نام ارز را اشتباه وارد کرد.";
                $this->adminMessage($adminMessage);
                $this->say("\xE2\x9A\xA0	" . 'نام ارز وارد شده اشتباه میباشد.' . "\n\n" . 'لطفاً مجددا وارد نمایید.' . "\n \xE2\x9B\x94	");
                $this->askCoins($this->userID);
            }
        });
    }

    public function askNextLevel($coinID)
    {
        $this->coin_id = $coinID;
        $res = 'لطفاً زمان بندی اطلاع رسانی را وارد نمایید.' . "\n\n";
        $res .= 'زمان را بر اساس دقیقه وارد نمایید.' . "\n";
        $res .= 'مثال: 5' . "\n\n";
        $res .= '(هر پنج دقیقه یک بار)' . "\n\n \xE2\x8F\xB0	";
        $this->ask($res, function (Answer $answer) {
            $validator = Validator::make(['time' => $answer->getText()], [
                'time' => 'integer|min:0|max:500',
            ]);
            if ($validator->valid()) {
                users_coin::where('id', $this->coin_id)->update(
                    [
                        'period' => $answer->getText()
                    ]
                );
                $this->bot->userStorage()->save([
                    'time' => $answer->getText() ?? 5,
                ]);
                $this->askChange($this->coin_id);
            } else {
                $this->say("\xE2\x9A\xA0	" . 'زمان وارد شده صحیح نمیباشد.' . "\n\n" . 'بازه زمانی بین 1 و 500 دقیقه میباشد.' . "\n\n" . 'لطفاً مجدد زمان را وارد نمایید.' . "\xE2\x9B\x94	");
                $this->askNextLevel($this->coin_id);
            }
        });
    }

    public function askChange($coinID)
    {
        $this->coin_id = $coinID;
        $res = 'لطفاً میزان تغییر را  وارد نمایید.' . "\n\n";
        $res .= 'زمان را بر اساس درصد وارد نمایید.' . "\n";
        $res .= 'مثال: 2' . "\n\n";
        $res .= '(هر بار که ارز مورد نظر 2 درصد تغییر قیمتی داشت اطلاع رسانی میکند.)' . "\n\n \x23\xE2\x83\xA3";
        $this->ask($res, function (Answer $answer) {
            $validator = Validator::make(['percent' => $answer->getText()], [
                'percent' => 'integer|min:0|max:500',
            ]);
            if ($validator->valid()) {
                $this->bot->userStorage()->save([
                    'percent' => $answer->getText() ?? 1,
                ]);
                users_coin::where('id', $this->coin_id)->update(
                    [
                        'percent' => $answer->getText()
                    ]
                );
                $res = 'اطلاعات ارز ' . "\xF0\x9F\x92\xA0	" . strtoupper($this->bot->userStorage()->get('coins')) . "\n\n" . 'در بازه تغییر قیمتی ' . $this->bot->userStorage()->get('percent') . ' درصد ' . "\n\n" . 'در هر ' . $this->bot->userStorage()->get('time') . ' دقیقه' . "\n\n" . ' به اطلاع شما خواهد رسید.' . "\n\n \xE2\x9C\x85	";
                $this->say($res);

                $adminMessage = $this->bot->getUser()->getFirstName() . " " . $this->bot->getUser()->getLastName() . "\n\n" . $res;
                $this->adminMessage($adminMessage);
            } else {
                $this->say("\xE2\x9A\xA0	" . 'میزان درصد وارد شده صحیح نمیباشد.' . "\n\n" . 'بازه تغییر بین 1 و 500 درصد میباشد.' . "\n\n" . 'لطفاً مجدد درصد تغییر را وارد نمایید.' . "\xE2\x9B\x94	");
                $this->askChange($this->coin_id);
            }
        });
    }

    public function check($symbol)
    {
        $sy = strtoupper(str_replace("/", '', $symbol));
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.binance.com/api/v3/ticker/price?symbol=" . $sy,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "apikey: TZMPp6CmDEBJIgESoINrRmBu963Pft1OZc9iz6lia8xAFcL6RHIrqrLQAKfAGcDL",
                "cache - control: no - cache",
                "postman - token: f261e154 - 5bfd - 5091 - 6ef1 - 161c14d9bcf2",
                "secretkey: oSaJwiMYrA331cL6kPOuMK3bLGPNOu723CQlghqLY9oJIQgSP9kPm1dFSDlJKAwn"
            ),
        ));

        $response = curl_exec($curl);
        $this->say($response);

        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response, true);
            if (isset($response['code'])) {
                return false;
            } else {
                return true;
            };
        }
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        //
        $this->askCoins();
    }
}
