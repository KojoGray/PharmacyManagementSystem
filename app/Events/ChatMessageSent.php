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
use App\Models\chatmessage;

class ChatMessageSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     *
     *
     */

    public $senderId;
    public $receiverId;
    public $message;
    public function __construct($senderId, $receiverId, $message)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->message = $message;

    }



    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $channelName = "private.chat.$this->senderId.$this->receiverId";
        chatmessage::create([
            'senderId' => $this->senderId,
            'receiverId' => $this->receiverId,
            'messageBody' => $this->message,
            'messageTime' => date('H:i:s')
        ]);

        return event(new PrivateChannel($channelName));
    }

    public function broadcastAs()
    {
        return 'ChatMessageSent';
    }

    public function broadcastWith()
    {
        $messages = chatmessage::where('senderId', $this->senderId)->orwhere('senderId', $this->receiverId)->where(
            'receiverId',
            $this->receiverId
        )->orwhere('receiverId', $this->receiverId);

        return [
            "messages" => $messages
        ];
    }
}
