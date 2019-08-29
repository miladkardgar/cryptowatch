<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Inspiring;

class startConverstation extends Conversation
{


    public function start()
    {
        $question = Question::create("سلام \n\n به ربات کریپتو واتچ خوش آمدید.\n\n\n
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

//                    $user = $bot->getUser();
//                    $bot->reply('Hello ' . $user->getFirstName() . ' ' . $user->getLastName());
//                    $bot->reply('Your username is: ' . $user->getUsername());
//                    $bot->reply('Your ID is: ' . $user->getId());
//        User::create(
//            [
//                'chat_id' => $user->getId(),
//                'name' => $user->getFirstName(),
//                'last_name' => $user->getLastName(),
//                'username' => $user->getUsername(),
//            ]
//        );
                    $joke = "information";
                    $this->say($joke->value->joke);
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