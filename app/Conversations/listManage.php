<?php

namespace App\Conversations;

use App\crypto_user;
use App\users_coin;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Telegram\TelegramDriver;
use Validator;

class listManage extends Conversation
{

    public function adminMessage($Message)
    {
        $bot = app('botman');
        $bot->say("\xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	" . "\n\n" . $Message . "\n\n \xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	\xF0\x9F\x94\xB4	", env('ADMIN_ID'), TelegramDriver::class);
    }

    public function my_symbol_list()
    {


        $user = $this->bot->getUser();
        $userInfo = crypto_user::where('chat_id', $user->getId())->first();

        if ($userInfo['id']) {
            $list = users_coin::where('user_id', $userInfo['id'])->get();
            if (sizeof($list) >= 1) {
                $res = 'لیست ارز های ثبت شده:' . "\n";
                $res .= "----------------------------" . "\n";
                $i = 1;
                $this->bot->reply($res);
                foreach ($list as $item => $value) {
                    $res = "\xF0\x9F\x92\xA0	" . " نام ارز: " . strtoupper($value['symbol']) . "\n\n";
                    $res .= "\xE2\x8F\xB0	" . " زمانبدی ارسال: " . $value['period'] . " دقیقه" . "\n\n";
                    $res .= "\x23\xE2\x83\xA3	" . " درصد تغییر: " . $value['percent'] . "% " . "\n\n";
                    $res .= "\xF0\x9F\x92\xB1	" . " @cryptoowatch";

                    $question = Question::create($res)->addButtons(
                        [
                            Button::create('حذف')->value('delete_' . $value['id']),
                            Button::create('ویرایش')->value('edit_' . $value['id']),
                        ]
                    )->callbackId('response_coins');
                    $this->ask($question, function (Answer $answer) {
                        if ($answer->isInteractiveMessageReply()) {
                            $infoCallBack = explode('_', $answer->getValue());
                            $action = $infoCallBack[0];
                            $id = $infoCallBack[1];
                            if ($action == 'delete') {
                                users_coin::where('id', '=', $id)->delete();
                                $this->say("ارز مورد نظر از سیستم حذف گردید.");
                            }
                            if ($action == 'edit') {
                                return $this->askNextLevel($id);
                            }
                        }
                    });
                }
            } else {
                $res = "هیچ ارزی در سیتم ثبت نشده است." . "\n\n";
                $res .= "ثبت ارز جدید: " . "\n";
                $res .= "/add_symbol" . "\n";
                $this->bot->reply($res);
            }
        } else {
            $this->bot->reply("خطا در یافتن کاربر. مجددا تلاش نمایید.");
        }

    }

    public function askNextLevel($coinID)
    {
        $this->coin_id = $coinID;
        $res = 'لطفاً زمان بندی اطلاع رسانی را وارد نمایید.' . "\n\n";
        $res .= 'زمان را بر اساس دقیقه وارد نمایید.' . "\n";
        $res .= 'مثال: 5' . "\n\n";
        $res .= '(هر پنج دقیقه یک بار)' . "\n\n \xE2\x8F\xB0	";
        $this->ask($res, function (Answer $answer) {
            $validator = Validator::make(['time' => $answer->getText()], [
                'time' => 'integer|min:0|max:500',
            ]);
            if ($validator->valid()) {
                users_coin::where('id', $this->coin_id)->update(
                    [
                        'period' => $answer->getText()
                    ]
                );
                $this->bot->userStorage()->save([
                    'time' => $answer->getText(),
                ]);
                $this->askChange($this->coin_id);
            } else {
                $this->say("\xE2\x9A\xA0	" . 'زمان وارد شده صحیح نمیباشد.' . "\n\n" . 'بازه زمانی بین 1 و 500 دقیقه میباشد.' . "\n\n" . 'لطفاً مجدد زمان را وارد نمایید.' . "\xE2\x9B\x94	");
                $this->askNextLevel($this->coin_id);
            }
        });
    }

    public function askChange($coinID)
    {
        $this->coin_id = $coinID;
        $res = 'لطفاً میزان تغییر را  وارد نمایید.' . "\n\n";
        $res .= 'زمان را بر اساس درصد وارد نمایید.' . "\n";
        $res .= 'مثال: 2' . "\n\n";
        $res .= '(هر بار که ارز مورد نظر 2 درصد تغییر قیمتی داشت اطلاع رسانی میکند.)' . "\n\n \x23\xE2\x83\xA3";
        $this->ask($res, function (Answer $answer) {
            $validator = Validator::make(['percent' => $answer->getText()], [
                'percent' => 'integer|min:0|max:500',
            ]);
            if ($validator->valid()) {
                $this->bot->userStorage()->save([
                    'percent' => $answer->getText(),
                ]);
                users_coin::where('id', $this->coin_id)->update(
                    [
                        'percent' => $answer->getText()
                    ]
                );
                $res = 'اطلاعات ارز ' . "\xF0\x9F\x92\xA0	" . strtoupper($this->bot->userStorage()->get('coins')) . "\n\n" . 'در بازه تغییر قیمتی ' . $this->bot->userStorage()->get('percent') . ' درصد ' . "\n\n" . 'در هر ' . $this->bot->userStorage()->get('time') . ' دقیقه' . "\n\n" . ' به اطلاع شما خواهد رسید.' . "\n\n \xE2\x9C\x85	";
                $this->say($res);

                $adminMessage = $this->bot->getUser()->getFirstName() . " " . $this->bot->getUser()->getLastName() . "\n\n" . $res;
                $this->adminMessage($adminMessage);
            } else {
                $this->say("\xE2\x9A\xA0	" . 'میزان درصد وارد شده صحیح نمیباشد.' . "\n\n" . 'بازه تغییر بین 1 و 500 درصد میباشد.' . "\n\n" . 'لطفاً مجدد درصد تغییر را وارد نمایید.' . "\xE2\x9B\x94	");
                $this->askChange($this->coin_id);
            }
        });
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        //
        $this->my_symbol_list();
    }
}
