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
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use Urlbox\Screenshots\Urlbox;

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

        $options["url"] = "https://google.com";
        $urlboxUrl = Urlbox::generateUrl($options);
        $message = OutgoingMessage::create('This is a cute dog.')->withAttachment($urlboxUrl);
        $bot->reply($message);

        $user = $bot->getUser();
        $userInfo = crypto_user::where('chat_id', $user->getId())->first();
        if ($userInfo['id']) {
            $list = users_coin::where('user_id', $userInfo['id'])->get();
            if (sizeof($list) >= 1) {
                $res = 'لیست ارز های ثبت شده:' . "\n";
                $res .= "----------------------------" . "\n";
                $i = 1;
                $bot->reply($res);
                $this->bot = $bot;
                foreach ($list as $item => $value) {
                    $res = "\xF0\x9F\x92\xA0	" . " نام ارز: " . strtoupper($value['symbol']) . "\n\n";
                    $res .= "\xE2\x8F\xB0	" . " زمانبدی ارسال: " . $value['period'] . " دقیقه" . "\n\n";
                    $res .= "\x23\xE2\x83\xA3	" . " درصد تغییر: " . $value['percent'] . "% " . "\n\n";
                    $res .= "\xF0\x9F\x92\xB1	" . " @cryptoowatch";

//                    $question = Question::create($res)->addButtons(
//                        [
//                            Button::create('حذف')->value('delete')->name('delete2'),
//                            Button::create('ویرایش')->value('edit')->name('edit2'),
//                        ]
//                    )->callbackId('id_'.$value['id']);
                    $bot->ask($res,function (Answer $answer) {
                        $this->say($answer->getCallbackId());
                        if ($answer->isInteractiveMessageReply()) {
//                            $this->say($answer->getCallbackId());
//                            switch ($answer->getValue()) {
//                                case 'delete':
//                                    $this->say('delete');
//                                    break;
//                                case 'edit':
//                                    $this->say('edit');
//                                    break;
//                            }
                        }
                    }, $this->keyboard());

                }
                $res .= "----------------------------" . "\n";
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

    public function setting(BotMan $bot)
    {
        $bot->reply('این قسمت در حال طراحی میباشد...');
    }


    public function keyboard()
    {
        return Keyboard::create()
            ->type(Keyboard::TYPE_INLINE)
            ->addRow(
                KeyboardButton::create('حذف')->callbackData('delete'),
                KeyboardButton::create('ویرایش')->callbackData('edit'))
            ->toArray();
    }
}
