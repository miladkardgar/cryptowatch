<?php

namespace App\Http\Controllers;

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
}
