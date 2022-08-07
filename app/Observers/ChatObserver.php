<?php

namespace App\Observers;

use App\Chat;

class ChatObserver
{
    public function deleting(Chat $chat)
    {
        $chat->messages()->delete();
    }
}
