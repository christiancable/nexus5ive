<?php

namespace App\Helpers;

use App\Models\Chat;
use App\Models\ChatMessage;

/*
helper class for dealing with chats between users
*/

class ChatHelper
{
    public static function sendMessage(int $user_id, int $partner_id, string $text, $time = null)
    {
        // add message to users copy of the conversation
        $userChat = Chat::firstOrCreate(
            [
                'owner_id' => $user_id,
                'partner_id' => $partner_id,
            ]
        );

        $userMessage = ChatMessage::create([
            'chat_id' => $userChat->id,
            'sender_id' => $user_id,
            'message_text' => $text,
        ]);

        $userChat->is_read = true;
        $userChat->save();

        // add message to the partner's copy of the conversation
        $partnerChat = Chat::firstOrCreate(
            [
                'owner_id' => $partner_id,
                'partner_id' => $user_id,
            ]
        );

        $partnerMessage = ChatMessage::create([
            'chat_id' => $partnerChat->id,
            'sender_id' => $user_id,
            'message_text' => $text,
        ]);

        if ($time) {
            $userMessage->created_at = $time;
            $userMessage->save();
            $partnerMessage->created_at = $time;
            $partnerMessage->save();
        }
        $partnerChat->is_read = false;
        $partnerChat->save();
    }
}
