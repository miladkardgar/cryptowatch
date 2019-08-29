<?php
use App\Http\Controllers\BotManController;
use App\Http\Controllers\ManageController;
use App\Conversations\ExampleConversation;
$botman = resolve('botman');



$botman->hears('/start', 'App\Http\Controllers\ManageController@start');
$botman->hears('/setting', ManageController::class.'@setting');


$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});
$botman->hears('Start conversation', BotManController::class.'@startConversation');
