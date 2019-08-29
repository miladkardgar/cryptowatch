<?php

namespace App\Http\Controllers;

use App\User;

class ManageController extends Controller
{
    //
    public function start($bot)
    {

        $user = $bot->getUser();
        $bot->reply('Hello ' . $user->getFirstName() . ' ' . $user->getLastName());
        $bot->reply('Your username is: ' . $user->getUsername());
        $bot->reply('Your ID is: ' . $user->getId());
//        User::create(
//            [
//                'chat_id' => $user->getId(),
//                'name' => $user->getFirstName(),
//                'last_name' => $user->getLastName(),
//                'username' => $user->getUsername(),
//            ]
//        );
    }
}
