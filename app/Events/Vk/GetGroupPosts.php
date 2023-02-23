<?php

namespace App\Events\Vk;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GetGroupPosts
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $ownerId;
    public string $token;
    public int $count;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($ownerId, $token, $count)
    {
        $this->ownerId = $ownerId;
        $this->token = $token;
        $this->count = $count;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
