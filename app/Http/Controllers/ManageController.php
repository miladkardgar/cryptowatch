<?php

namespace App\Http\Controllers;

use App\Conversations\add_symbol;
use App\Conversations\addSymbol;
use App\Conversations\startConverstation;
use App\crypto_user;
use App\users_coin;
use BotMan\BotMan\BotMan;

class ManageController extends Controller
{
    //

    public function handle()
    {
        $botman = app('botman');
        $botman->listen();
    }

    public function start(BotMan $bot)
    {
        $bot->startConversation(new startConverstation());
    }

    public function add_symbol(BotMan $bot)
    {
        $bot->startConversation(new addSymbol());
    }

    public function my_symbol(BotMan $bot)
    {
        $user = $bot->getUser();
        $bot->reply($user->getId());

        $userInfo = crypto_user::where('chat_id', $user->getId())->first();
        if ($userInfo['id']) {
            $list = users_coin::where('user_id', $userInfo['id'])->get();
            $bot->reply($list);
        }

    }
}
