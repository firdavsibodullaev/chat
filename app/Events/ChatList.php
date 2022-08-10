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
     * @var Chat
     */
    private $chat;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn(): array
    {
        $channels = [];
        foreach ($this->chat->participants->pluck('id')->toArray() as $id) {
            $channels[] = new PrivateChannel("chat.list." . $id);
        }
        return $channels;
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
            'host' => UserResource::make(auth()->user()),
            'guest' => UserResource::make($this->chat->participants->whereNotIn('id', [auth()->id()])->first()),
        ];
    }
}
