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
                    $user = $this->bot->getUser();
                    $res = $user->getFirstName() . ' ' . $user->getLastName() . "\n وقت بخیر \n" . "\n";
                    if ($user->getUsername() != "") {
                        $res .= 'نام کاربری شما: ' . $user->getUsername() . "\n";
                    }
                    $res .= 'لطفاً نام ارز مورد نظر را وارد نمایید.' . "\n";
                    $res .= ' مثال: BTCUSDT' . "\n\n";
//                    cryptoUser::create(
//                        [
//                            'chat_id' => $user->getId(),
//                            'name' => $user->getFirstName(),
//                            'last_name' => $user->getLastName(),
//                            'username' => $user->getUsername(),
//                        ]
//                    );

                    $question = Question::create($res)
                        ->fallback('Unable to ask question')
                        ->callbackId('start')
                        ->addButtons([
                            Button::create('افزودن ارز دیگر')->value('addCoin'),
                            Button::create('مرحله بعد')->value('nextLevel'),
                        ]);

                    return $this->ask($question, function (Answer $answer) {
                        if ($answer->isInteractiveMessageReply()) {
                            if ($answer->getValue() === 'addCoin') {
                                $this->say($answer->getText());
                            } elseif ($answer->getValue() === 'nextLevel') {
                                $res = 'لطفاً زمان بندی اطلاع رسانی را وارد نمایید.'."\n\n";
                                $res .= 'زمان را بر اساس دقیقه وارد نمایید.'."\n";
                                $res .= 'مثال: 5'."\n";
                                $res .= 'هر پنج دقیقه یک بار'."\n\n";
                                $this->say($answer->getText());
                            }
                        }
                    });

                    $this->say($res);
                } elseif ($answer->getValue() === 'moreInformation') {
                    $this->say(Inspiring::quote());
                }
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
