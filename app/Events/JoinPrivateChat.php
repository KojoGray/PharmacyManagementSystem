<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Channels;

class ChatMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $receiverId;
    public $senderId;
    public function __construct($senderId, $receiverId)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;

    }

    /**
     * Get the
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {

        $cName1 = "private.chat.$this->senderId.$this->receiverId";
        $cName2 = "private.chat.$this->receiverId.$this->senderId";
        $channel = Channels::where('channelName', $cName1)->orwhere('channelName', $cName2)->first();

        if ($channel === null) {
            Channels::create([
                'channelName' => $cName1
            ]);
        }



        return new PrivateChannel($cName1);
    }
}
