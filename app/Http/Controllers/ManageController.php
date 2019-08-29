<?php

namespace App\Http\Controllers;

use App\Conversations\add_symbol;
use App\Conversations\addSymbol;
use App\Conversations\startConverstation;
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
}
