<?php

namespace App\Conversations;

use App\cryptoUser;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Inspiring;
use Illuminate\Notifications\Action;

class startConverstation extends Conversation
{

    private $coins = [];

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

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'startUse') {
                    $this->askCoins();
                } elseif ($answer->getValue() === 'moreInformation') {
                    $this->say(Inspiring::quote());
                }
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
        $question = Question::create($res)
            ->fallback('Unable to ask question')
            ->callbackId('register_coin');
        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->askTime();
                array_push($this->coins, $answer->getText());
            }
        });
    }

    public function askTime()
    {
        $con = '';
//        foreach ($this->coins as $coin) {
//            $con .= $coin . "\n";
//        }
        $res = 'ارز ' . $con . " به لیست اضافه گردید." . '\n';
        $question = Question::create($res)
            ->fallback('Unable to ask question')
            ->callbackId('register_coin')->addButtons(
                [
                    Button::create('افزودن ارز دیگر')->value('startUse'),
                    Button::create('مرحله بعد')->value('nextLevel'),
                ]
            );
        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'nextLevel') {
                    $this->askNextLevel();
                }
            }
        });
    }

    public function askNextLevel()
    {
        $res = 'لطفاً زمان بندی اطلاع رسانی را وارد نمایید.' . "\n\n";
        $res .= 'زمان را بر اساس دقیقه وارد نمایید.' . "\n";
        $res .= 'مثال: 5' . "\n";
        $res .= 'هر پنج دقیقه یک بار' . "\n\n";
        $question = Question::create($res)
            ->fallback('Unable to ask question')
            ->callbackId('register_next')->addButtons(
                [
                    Button::create('افزودن ارز دیگر')->value('startUse'),
                    Button::create('مرحله بعد')->value('nextLevel'),
                ]
            );
        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $res = 'اطلاعات ارز شما هر ' . $answer->getText() . ' دقیقه به اطلاع شما خواهد رسید.' . "\n\n";
                $this->say($res);
            }
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
