<?php

namespace App\Conversations;

use App\crypto_user;
use App\users_coin;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Inspiring;

class startConverstation extends Conversation
{
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
                    $u = crypto_user::where('chat_id', $chatId)->get();
                    if (!$u) {
                        $userInfo = new crypto_user();
                        $userInfo->name = $name;
                        $userInfo->last_name = $lastName;
                        $userInfo->chat_id = $chatId;
                        $userInfo->username = $username;
                        $userInfo->save();
                        $userID = $userInfo['id'];
                    } else {
                        $userID = $u['id'];
                    }
                    $this->askCoins($userID);

                } elseif ($answer->getValue() === 'moreInformation') {
                    $this->say(Inspiring::quote());
                }
            }
        });
    }

    public function askCoins($userID)
    {
        $this->userID = $userID;
        $user = $this->bot->getUser();
        $res = $user->getFirstName() . ' ' . $user->getLastName() . "\n";
        $res .= "وقت بخیر" . "\n\n";
        $res .= 'لطفاً نام ارز مورد نظر را وارد نمایید.' . "\n";
        $res .= ' مثال: BTCUSDT' . "\n\n";
        $this->ask($res, function (Answer $answer) {
            if (!users_coin::where(
                [
                    ['symbol', '=', $answer->getText()],
                    ['user_id', '=', $this->userID],
                ]
            )->first()) {
                $coin = users_coin::create(
                    [
                        'symbol' => $answer->getText(),
                        'user_id' => $this->userID,
                    ]
                );
                $coinID = $coin['id'];
            }
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
        $this->start();
    }
}
