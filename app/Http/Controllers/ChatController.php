<?php

namespace App\Http\Controllers;

use \App\Events\ChatMessageEvent;
use App\Models\Channels;
use Illuminate\Http\Request;
use App\Models\chatmessage;
use App\Http\Requests\ChatMessageRequest;

class ChatController extends Controller
{
    protected $headers = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => '*'
    ];

    public function initiateChat(Request $request)
    {
        $senderId = $request->input('senderId');
        $receiverId = $request->input('receiverId');


        ////  event(new ChatMessageEvent($senderId, $receiverId));

    }



    public function sendMessage(ChatMessageRequest $request)
    {

        $data = $request->validated();


        chatmessage::create([
            'senderId' => $data["senderId"],
            'receiverId' => $data["receiverId"],
            'messageBody' => $data["messageBody"],
            'role' => $data["role"]
        ]);

        return response()->json("sent", 200);

    }

    public function getMessage($senderId, $receiverId)
    {


        $senderMessages = chatmessage::where('senderId', $senderId)->where('receiverId', $receiverId)->get();
        $receiverMessages = chatmessage::where('senderId', $receiverId)->where('receiverId', $senderId)->get();


        return response()->json([
            "senderMessage" => $senderMessages,
            "receiverMessage" => $receiverMessages
        ], 200, $this->headers);




    }


}
