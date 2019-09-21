<?php

namespace App\Http\Controllers;

use App\Conversations\add_symbol;
use App\Conversations\addSymbol;
use App\Conversations\editSymbol;
use App\Conversations\listManage;
use App\Conversations\startConverstation;
use App\crypto_user;
use App\users_coin;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use Validator;

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
        $bot->startConversation(new listManage());
    }

    public function setting(BotMan $bot)
    {
        $bot->reply('این قسمت در حال طراحی میباشد...');
    }
}
