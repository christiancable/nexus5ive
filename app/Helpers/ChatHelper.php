<?php

namespace App\Helpers;

use App\Models\Chat;
use App\Models\ChatMessage;

/*
helper class for dealing with chats between users
*/

class ChatHelper
{
    /**
     * Sends a message between two users and manages their chat records.
     *
     * This method creates or retrieves chat records for both the sender and the recipient,
     * adds the message to their respective chat histories, and updates the read status.
     * If a timestamp is provided, it sets the creation time of the message.
     *
     * @param  int  $user_id  The ID of the user sending the message.
     * @param  int  $partner_id  The ID of the user receiving the message.
     * @param  string  $text  The content of the message to be sent.
     * @param  string|null  $time  Optional. A specific timestamp for when the message was created.
     */
    public static function sendMessage(int $user_id, int $partner_id, string $text, $time = null): void
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
