<?php

namespace App\Conversations;

use App\crypto_user;
use App\users_coin;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\BotMan;


class addSymbol extends Conversation
{

    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        //
        $botman = app('botman');
        $run = new startConverstation();
        $user = $botman->bot->getUser();
        $chatId = $user->getId();
        $userInfo = crypto_user::where('chat_id', $chatId)->first();
        $run->askCoins($userInfo['id']);
    }
}
