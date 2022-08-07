<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $guest;
    /**
     * @var int
     */
    private $host;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(int $guest, int $host)
    {
        $this->guest = $guest;
        $this->host = $host;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('chat.deleted.' . $this->guest),
            new PrivateChannel('chat.deleted.' . $this->host),
        ];
    }

    public function broadcastAs()
    {
        return 'chat.delete';
    }

    public function broadcastWith(): array
    {
        return [
            $this->guest, $this->host
        ];
    }

}
