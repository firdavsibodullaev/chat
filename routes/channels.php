<?php

use App\Services\PrivateChatService;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/


Broadcast::channel('conversation.{chat_id}', function ($user, $chat_id) {
    return (new PrivateChatService())->checkChatUserExistence($chat_id, $user);
});

Broadcast::channel('chat.list.{user_id}', function ($user, $user_id) {
    return $user->id == $user_id;
});

Broadcast::channel('chat.deleted.{user_id}', function ($user, $user_id) {
    return $user_id == $user->id;
});
