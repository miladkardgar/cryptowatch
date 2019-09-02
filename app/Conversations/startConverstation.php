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


    public function __construct()
    {
        $this->userTable = new crypto_user();
        $user = $this->bot->getUser();
        $this->chat_id = $user->getId();
        $this->userInfo = crypto_user::where('chat_id', $this->chat_id)->first();
        $this->coin_id = 0;
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
                    if (!crypto_user::where('chat_id', $chatId)->first()) {
                        $this->userTable->name = $name;
                        $this->userTable->last_name = $lastName;
                        $this->userTable->chat_id = $chatId;
                        $this->userTable->username = $username;
                        $this->userTable->save();
                        $this->userInfo = crypto_user::find('id', $this->userTable['id']);
                    }
                    $this->askCoins();

                } elseif ($answer->getValue() === 'moreInformation') {
                    $this->say(Inspiring::quote());
                }
            } else {
                $this->askCoins();
            }
        });
    }

    public function askCoins()
    {
        $user = $this->bot->getUser();
        $res = $user->getFirstName() . ' ' . $user->getLastName() . "\n";
        $res .= "وقت بخیر" . "\n\n";
        $res .= 'لطفاً نام ارز مورد نظر را وارد نمایید.' . "\n";
        $res .= ' مثال: BTCUSDT' . "\n\n";
        $this->ask($res, function (Answer $answer) {
            $this->bot->userStorage()->save([
                'coins' => $answer->getText(),
            ]);
            if (!users_coin::where(
                [
                    ['symbol', '=', $answer->getText()],
                    ['user_id', '=', $this->userInfo['id']],
                ]
            )->first()) {
                $coin = users_coin::create(
                    [
                        'symbol' => $answer->getText(),
                        'user_id' => $this->userInfo['id'],
                    ]
                );
                $this->coin_id = $coin['id'];
            }
            $this->askTime();
        });
    }

    public function askTime()
    {
        $res = 'ارز ' . $this->bot->userStorage()->get('coins') . " به لیست اضافه گردید.";
        $this->say($res);
        $this->askNextLevel();
    }

    public function askNextLevel()
    {
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
