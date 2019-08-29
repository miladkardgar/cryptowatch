<?php

use App\Http\Controllers\BotManController;
use App\Http\Controllers\ManageController;
use App\Conversations\ExampleConversation;

$botman = resolve('botman');


$botman->hears('/start', function ($bot) {
    $user = $bot->getUser();
    $bot->reply('Hello ' . $user->getFirstName() . ' ' . $user->getLastName());
    $bot->reply('Your username is: ' . $user->getUsername());
    $bot->reply('Your ID is: ' . $user->getId());

    \App\User::create(
        [
            'chat_id' => $user->getId(),
            'name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'username' => $user->getUsername(),
        ]
    );
});
$botman->hears('/setting', ManageController::class . '@setting');


$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});
$botman->hears('Start conversation', BotManController::class . '@startConversation');
