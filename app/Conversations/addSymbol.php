<?php

namespace App\Conversations;

use App\crypto_user;
use App\users_coin;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\BotMan;


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
        $this->bot = BotMan::class;
        $run = new startConverstation();
        $user = $this->bot->getUser();
        $name = $user->getFirstName();
        $lastName = $user->getLastName();
        $username = $user->getUsername();
        $chatId = $user->getId();

        $userInfo = crypto_user::where('chat_id', $chatId)->first();

        return $run->askCoins($userInfo['id']);
    }
}
