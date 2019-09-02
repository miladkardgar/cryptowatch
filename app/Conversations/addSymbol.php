<?php

namespace App\Conversations;

use App\crypto_user;
use BotMan\BotMan\Facades\BotMan;
use BotMan\BotMan\Messages\Conversations\Conversation;


class addSymbol extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        //
        $bo = BotMan::getUser();
        $run = new startConverstation();
        $chatId = $bo->getId();
        $userInfo = crypto_user::where('chat_id', $chatId)->first();
        return $run->askCoins($userInfo['id']);
    }
}
