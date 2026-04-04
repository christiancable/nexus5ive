# Models

## Core forum structure

| Model | File | Description |
|-------|------|-------------|
| [Section](section.md) | `app/Models/Section.php` | Hierarchical forum categories |
| [Topic](topic.md) | `app/Models/Topic.php` | Discussion threads within sections |
| [Post](post.md) | `app/Models/Post.php` | Individual messages within topics |
| [View](view.md) | `app/Models/View.php` | Per-user read tracking for topics |

## Users

| Model | File | Description |
|-------|------|-------------|
| [User](user.md) | `app/Models/User.php` | BBS accounts (active, imported, guest) |
| [Activity](activity.md) | `app/Models/Activity.php` | Latest page-visit record per user |
| [Comment](comment.md) | `app/Models/Comment.php` | Messages left on user profile pages |
| [Mention](mention.md) | `app/Models/Mention.php` | `@username` mention notifications |
| [Chat](chat.md) | `app/Models/Chat.php` | Private one-to-one conversations |
| [ChatMessage](chat-message.md) | `app/Models/ChatMessage.php` | Individual messages within a chat |

## Appearance

| Model | File | Description |
|-------|------|-------------|
| [Theme](theme.md) | `app/Models/Theme.php` | CSS stylesheets selectable by users |
| [Mode](mode.md) | `app/Models/Mode.php` | Named configuration presets (can force a global theme) |

## Moderation

| Model | File | Description |
|-------|------|-------------|
| [Report](report.md) | `app/Models/Report.php` | Content reports raised by users |
| [ModerationNote](moderation-note.md) | `app/Models/ModerationNote.php` | Staff notes attached to reports |
