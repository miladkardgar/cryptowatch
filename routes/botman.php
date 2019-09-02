<?php

use App\Http\Controllers\BotManController;
use App\Http\Controllers\ManageController;
use App\Conversations\ExampleConversation;

$botman = resolve('botman');

$botman->hears('/start', ManageController::class.'@start');
$botman->hears('/setting', ManageController::class . '@setting');
$botman->hears('/add_symbol', ManageController::class . '@add_symbol');
$botman->hears('/my_symbol_list', ManageController::class . '@my_symbol');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});
$botman->hears('Start conversation', BotManController::class . '@startConversation');
