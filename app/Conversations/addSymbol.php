<?php

namespace App\Conversations;

use App\crypto_user;
use App\users_coin;
use BotMan\BotMan\Facades\BotMan;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;


class addSymbol extends Conversation
{

    public function askCoins()
    {
        $user = $this->bot->getUser();
        $userInfo = crypto_user::where('chat_id', $user->getId())->first();
        $this->userID = $userInfo['id'];
        $res = $user->getFirstName() . ' ' . $user->getLastName() . "\n";
        $res .= "وقت بخیر" . "\n\n";
        $res .= 'لطفاً نام ارز مورد نظر را وارد نمایید.' . "\n";
        $res .= ' مثال: BTCUSDT' . "\n\n";
        $this->ask($res, function (Answer $answer) {
            $coinInfo = users_coin::where(
                [
                    ['symbol', '=', $answer->getText()],
                    ['user_id', '=', $this->userID],
                ]
            )->first();
            if (!$coinInfo) {
                $coin = users_coin::create(
                    [
                        'symbol' => $answer->getText(),
                        'user_id' => $this->userID,
                    ]
                );
                $coinID = $coin['id'];
            } else {
                $coinID = $coinInfo['id'];
            }
            $this->bot->userStorage()->save([
                'coins' => $answer->getText(),
            ]);
            $this->askTime($coinID);
        });
    }

    public function askTime($coinID)
    {
        $res = 'ارز ' . $this->bot->userStorage()->get('coins') . " به لیست اضافه گردید.";
        $this->say($res);
        $this->askNextLevel($coinID);
    }

    public function askNextLevel($coinID)
    {
        $this->coin_id = $coinID;
        $res = 'لطفاً زمان بندی اطلاع رسانی را وارد نمایید.' . "\n\n";
        $res .= 'زمان را بر اساس دقیقه وارد نمایید.' . "\n";
        $res .= 'مثال: 5' . "\n\n";
        $res .= '(هر پنج دقیقه یک بار)' . "\n\n.";
        $this->ask($res, function (Answer $answer) {
            users_coin::where('id', $this->coin_id)->update(
                [
                    'period' => $answer->getText()
                ]
            );
            $res = 'اطلاعات ارز شما هر ' . $answer->getText() . ' دقیقه به اطلاع شما خواهد رسید.' . "\n\n";
            $this->bot->userStorage()->save([
                'time' => $answer->getText(),
            ]);
            $this->say($res);
        });
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
