# Leap ("Next")

## What it does

`SectionController::leap()` powers the **Next** button in the top and bottom navigation bars. It is the primary catch-up mechanism: when a user clicks Next, the application finds their first subscribed topic that has new posts since they last read it, then redirects them to that topic's parent section and shows a flash alert linking directly to the topic.

If no unread subscribed topic exists, it redirects to the home section with a "nothing new" message.

## Step-by-step walkthrough

```
GET /leap  →  SectionController::leap()
```

1. **Load all subscribed View records** for the current user (`unsubscribed = false`), with the `topic` relationship eager-loaded in a single query.

2. **Iterate in PHP** over the collection, calling `$view->topic->most_recent_post_time` for each topic. `most_recent_post_time` is a computed accessor on `Topic` that fires a separate `SELECT` query per topic to find the latest post by its `time` column:

   ```php
   Post::select('time')
       ->where('topic_id', $this->id)
       ->orderBy('time', 'desc')
       ->first();
   ```

3. **Compare timestamps**: if the topic's latest post time differs from `$view->latest_view_date` (when the user last read the topic), the topic is considered unread.

4. **Take the first match**: the collection is not explicitly ordered, so the first result depends on database insertion order.

5. **Redirect and flash**:
   - *Unread topic found*: builds a Markdown flash alert containing the topic title as a link, and a secondary link to mark all subscribed topics as read. Redirects to the topic's **parent section** (not the topic directly). URLs are stripped of the base URL via `str_replace` to force relative paths.
   - *Nothing found*: redirects to the home section with a warning flash.

## Data model involved

| Model  | Role |
|--------|------|
| `View` | One record per user+topic pair. `latest_view_date` = when the user last read the topic. `unsubscribed = true` means the user has opted out. |
| `Topic` | Has `most_recent_post_time` (computed accessor) and `most_recent_post_id` (global scope subquery). |
| `Post`  | `time` column stores the canonical post timestamp (may differ from `created_at` for imported posts). |

## Existing tests

`tests/Browser/NextTest.php` covers:
- Jump to an updated subscribed topic
- No jump when nothing is new
- No jump to an unsubscribed topic
- Handling topics with only undated posts (time = null)
- Mixed dated/undated posts
- "Mark all as read" flow

---

## Issues and suggested improvements

### 1. N+1 query problem (performance)

Every call to `$view->topic->most_recent_post_time` fires a fresh database query. A user subscribed to 50 topics generates ~51 queries per leap request. The `Topic` model already has a global scope that attaches `most_recent_post_id` via a correlated subquery, but `most_recent_post_time` ignores this and always queries the database directly.

**Fix**: eager-load `topic.most_recent_post` in the leap query and read the time from the already-loaded relationship instead of the accessor.

```php
$views = View::subscribed()
    ->with('topic.most_recent_post')   // one extra query, not N
    ->where('user_id', $request->user()->id)
    ->get();

// then:
$postTime = $view->topic->most_recent_post?->time ?? $view->topic->created_at;
```

Note: the global scope sorts by `created_at` (via `latest()`), but `getMostRecentPostTimeAttribute` sorts by the `time` column. For Nexus2-imported posts these can differ — whichever approach is chosen should be consistent across the codebase.

### 2. Duplicated unread-detection logic

The same question — "does this topic have posts newer than the user last read?" — is answered in three different places with three slightly different comparisons:

| Location | Comparison |
|----------|-----------|
| `SectionController::leap` | `$view->latest_view_date->timestamp != $postTime->timestamp` |
| `ViewHelper::topicHasUnreadPosts` | `$mostRecentlyReadPostDate != $topic->most_recent_post_time` (object `!=`) |
| `ViewHelper::getTopicStatus` | `$mostRecentPostDate->gt($mostRecentlyReadPostDate)` (Carbon `>`) |

**Fix**: `leap` should delegate to `ViewHelper::topicHasUnreadPosts()`, which is the canonical location for this logic. That method itself should be reviewed — the loose object `!=` comparison can produce unexpected results when comparing Carbon instances across timezones or serialisation boundaries; prefer `->gt()` or a timestamp comparison throughout.

### 3. No ordering on the subscribed views query

The collection has no `ORDER BY`, so which topic is returned first is non-deterministic (depends on DB insertion order). In practice this may mean the same stale topic is always surfaced until it is read.

**Fix**: order by the most recent post time descending so the most actively updated topic is always shown first, or ascending to work through topics chronologically:

```php
View::subscribed()
    ->with('topic.most_recent_post')
    ->where('user_id', $request->user()->id)
    ->orderByDesc('latest_view_date')   // or join posts for a live sort
    ->get();
```

### 4. Redirects to section, not directly to the topic

After finding an unread topic, leap sends the user to the section page, then relies on the flash alert link to complete the navigation. The user lands on a page they didn't ask for and must click again.

**Suggested change**: redirect directly to the topic:

```php
return redirect()->action(
    TopicController::class . '@show',
    ['topic' => $destinationTopic->id]
);
```

The flash alert could still provide the "mark all as read" link without being the primary navigation mechanism. This is a UX judgement call — the current behaviour may be intentional to let the user see the surrounding section context.

### 5. URL stripping workaround

```php
$topicURL = str_replace(url('/'), '', $topicURL);
$subscribeAllURL = str_replace(url('/'), '', $subscribeAllURL);
```

The comment explains this is done to force relative URLs so the links don't open in a new window. The cleaner approach is to pass `false` as the absolute parameter to `action()`:

```php
$topicURL = action([TopicController::class, 'show'], ['topic' => $destinationTopic->id], false);
$subscribeAllURL = action([TopicController::class, 'markAllSubscribedTopicsAsRead'], [], false);
```
