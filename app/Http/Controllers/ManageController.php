<?php

namespace App\Http\Controllers;

use App\Conversations\add_symbol;
use App\Conversations\addSymbol;
use App\Conversations\startConverstation;
use App\crypto_user;
use App\users_coin;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

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
        $userInfo = crypto_user::where('chat_id', $user->getId())->first();
        if ($userInfo['id']) {
            $list = users_coin::where('user_id', $userInfo['id'])->get();
            if (sizeof($list) >= 1) {
                $res = 'لیست ارز های ثبت شده:' . "\n";
                $res .= "----------------------------" . "\n";
                $i = 1;
                foreach ($list as $item => $value) {
                    $res .= $i . "- " . $value['symbol'];
                    $res .= " - " . $value['period'];
                    $res .= " دقیقه یک بار" . "\n";
                    $i++;
                }
                $res .= "----------------------------" . "\n";
                $bot->reply($res);
            } else {
                $res = "هیچ ارزی در سیتم ثبت نشده است." . "\n\n";
                $res .= "ثبت ارز جدید: " . "\n";
                $res .= "/add_symbol" . "\n";
                $bot->reply($res);
            }
        } else {
            $bot->reply("خطا در یافتن کاربر. مجددا تلاش نمایید.");
        }

    }
}
