# Chat

A private one-to-one conversation between two users. Each conversation is **duplicated** — every user holds their own copy of the chat, and messages are written to both copies simultaneously by `ChatHelper::sendMessage()`. Neither participant ever reads from the other's `Chat` record.

## Table: `chats`

| Column | Type | Notes |
|--------|------|-------|
| `id` | big int | Primary key |
| `owner_id` | unsigned int | FK → users; the user this copy belongs to |
| `partner_id` | unsigned int | FK → users; the other participant |
| `is_read` | boolean | Whether the owner has seen the latest message; default false |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | timestamp | Soft delete |

## Relationships

| Relation | Type | Notes |
|----------|------|-------|
| `chatMessages` | hasMany ChatMessage | Messages in this user's copy of the conversation |
| `partner` | hasOne User | via `partner_id` |

## How messages are sent

`ChatHelper::sendMessage(int $user_id, int $partner_id, string $text)`:

1. `firstOrCreate` the sender's `Chat` (`owner=sender, partner=recipient`) — creates it if first contact
2. Insert a `ChatMessage` into the sender's chat; mark their chat `is_read = true`
3. `firstOrCreate` the recipient's `Chat` (`owner=recipient, partner=sender`)
4. Insert an identical `ChatMessage` into the recipient's chat; mark their chat `is_read = false`

Each user's `Chat::chatMessages` therefore contains a complete copy of the conversation from their perspective. The `is_read` flag on each `Chat` independently tracks whether that owner has seen the latest message.

## Authorization

`ChatPolicy::create()` denies guest accounts. The Livewire `sendMessage()` action calls `$this->authorize('create', Chat::class)` on every send attempt.

## Deletion

Because each participant holds an independent copy, deleting a `Chat` (or soft-deleting it) only removes that owner's record. The other participant's `Chat` and its `ChatMessage` rows are unaffected. A user can therefore clear their own conversation history without any impact on the other person's copy.

## Related: ChatMessage

See [chat-message.md](chat-message.md).
