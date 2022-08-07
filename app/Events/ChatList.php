<?php

namespace App\Events;

use App\Chat;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatList implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var User
     */
    private $user;
    /**
     * @var Chat
     */
    private $chat;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Chat $chat)
    {
        $this->user = $user;
        $this->chat = $chat;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("chat.list." . $this->chat->participant_1_id),
            new PrivateChannel("chat.list." . $this->chat->participant_2_id)
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.chat';
    }

    /**
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'host' => UserResource::make($this->chat->host),
            'guest' => UserResource::make($this->chat->guest),
        ];
    }
}
