<?php

namespace App\Http\Controllers;

use App\Conversations\startConverstation;
use App\User;
use BotMan\BotMan\BotMan;

class ManageController extends Controller
{
    //
    public function start(BotMan $bot)
    {
        $bot->startConversation(new startConverstation());
    }
}
