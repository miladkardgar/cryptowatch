<?php

namespace App\Conversations;

use App\crypto_user;
use App\users_coin;
use BotMan\BotMan\Facades\BotMan;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Inspiring;
use App\Http\Controllers\BotManController;

class startConverstation extends Conversation
{

    public function adminMessage($Message)
    {
        $bot = app('botman');
        $bot->say("\xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	" . "\n\n" . $Message . "\n\n \xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	", env('ADMIN_ID'), TelegramDriver::class);
    }

    public function start()
    {

        $question = Question::create("سلام \n به ربات کریپتو واتچ خوش آمدید.\n\n
        این ربات یه شما کمک میکند تغییرات ارز های دیجیتال را به راحتی مدیریت نمایید.")
            ->fallback('Unable to ask question')
            ->callbackId('start')
            ->addButtons([
                Button::create('شروع استفاده از ربات')->value('startUse'),
                Button::create('اطلاعات بیشتر')->value('moreInformation'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'startUse') {

                    $user = $this->bot->getUser();
                    $name = $user->getFirstName();
                    $lastName = $user->getLastName();
                    $username = $user->getUsername();
                    $chatId = $user->getId();
                    $u = crypto_user::where('chat_id', $chatId)->first();
                    if (!$u) {
                        $userInfo = new crypto_user();
                        $userInfo->name = $name;
                        $userInfo->last_name = $lastName;
                        $userInfo->chat_id = $chatId;
                        $userInfo->username = $username;
                        $userInfo->save();
                        $userID = $userInfo['id'];
                        $adminMessage = $name . " " . $lastName . "\n\n" . " عضو سیستم شد.";
                        $this->adminMessage($adminMessage);
                    } else {
                        $userID = $u['id'];
                        $adminMessage = $name . " " . $lastName . "\n\n" . " وارد سیستم شد.";
                        $this->adminMessage($adminMessage);
                    }
                    $this->askCoins($userID);
                } elseif ($answer->getValue() === 'moreInformation') {
                    $this->say('این قسمت در حال طراحی میباشد...');
                }
            }
        });
    }

    public function askCoins($userID)
    {
        $this->userID = $userID;
        $user = BotMan::getUser();
        $this->name = $user->getFirstName();
        $this->last_name = $user->getLastName();
        $res = $user->getFirstName() . ' ' . $user->getLastName() . "\n";
        $res .= "وقت بخیر" . "\n\n";
        $res .= 'لطفاً نام ارز مورد نظر را وارد نمایید.' . "\n";
        $res .= ' مثال: BTCUSDT' . "\n\n";
        $this->ask($res, function (Answer $answer) {
            if ($this->check($answer->getText()) && is_string($answer->getText())) {
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
            if (is_numeric($answer->getText()) && $answer->getText() > 0 && $answer->getText() < 1000) {
                users_coin::where('id', $this->coin_id)->update(
                    [
                        'period' => $answer->getText()
                    ]
                );
                $this->bot->userStorage()->save([
                    'time' => $answer->getText(),
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
            if (is_numeric($answer->getText()) && $answer->getText() > 0 && $answer->getText() < 1000) {

                $this->bot->userStorage()->save([
                    'percent' => $answer->getText(),
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
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.binance.com/api/v3/ticker/price?symbol=" . strtoupper($symbol),
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
    public
    function run()
    {
        $this->start();
    }
}
