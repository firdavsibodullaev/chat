<?php

namespace App\Services;

use App\Chat;
use App\Message;
use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MessageService
 * @package App\Services
 */
class MessageService
{
    /**
     * @param User $user
     * @param array $payload
     * @return Model
     */
    public function create(User $user, array $payload): Model
    {
        $chat = (new ChatService())->show($user->id);
        $payload['user_id'] = auth()->id();
        $payload['chat_id'] = $chat['id'];

        return Message::query()->create($payload);
    }
}
