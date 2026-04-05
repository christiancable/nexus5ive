# ChatMessage

A single message within a private Chat conversation. Because each participant holds their own copy of a chat, every sent message results in **two** `ChatMessage` rows with identical content — one attached to the sender's `Chat` and one to the recipient's `Chat`.

## Table: `chat_messages`

| Column | Type | Notes |
|--------|------|-------|
| `id` | big int | Primary key |
| `chat_id` | unsigned big int | FK → chats |
| `sender_id` | unsigned int | FK → users |
| `message_text` | text | The message body |
| `created_at` / `updated_at` | timestamps | |

## Relationships

`ChatMessage` has no explicit relationship methods defined on the model itself. Navigation goes through the parent:

- `Chat::chatMessages()` — hasMany ChatMessage
- The sender can be resolved via `User::find($message->sender_id)` or by eager-loading alongside the chat

## Authorization

There is no separate policy for ChatMessage. Access is controlled entirely through the parent `Chat` — if a user can access the chat, they can read its messages. `ChatPolicy::create()` governs whether a user may send messages at all (guest accounts are denied).
