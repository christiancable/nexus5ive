<?php

namespace App\Helpers;

use App\Models\Topic;
use App\Models\User;
use App\Models\View;
use Illuminate\Support\Carbon;

class ViewHelper
{
    private static function findViewRecord(User $user, Topic $topic): ?View
    {
        return View::where('topic_id', $topic->id)->where('user_id', $user->id)->first();
    }

    /**
     * Records a user's read progress within a topic.
     */
    public static function updateReadProgress(User $user, Topic $topic): void
    {
        $progress = self::findViewRecord($user, $topic);

        if (! $progress) {
            $progress = new View;
            $progress->user_id = $user->id;
            $progress->topic_id = $topic->id;
            $progress->latest_view_date = $topic->most_recent_post_time;
            $progress->save();
        } elseif ($topic->most_recent_post_time !== $progress->latest_view_date) {
            $progress->latest_view_date = $topic->most_recent_post_time;
            $progress->save();
        }
    }

    public static function getReadProgress(User $user, Topic $topic): Carbon|false
    {
        $view = self::findViewRecord($user, $topic);

        return $view ? $view->latest_view_date : false;
    }

    /**
     * Returns true if the topic has posts the user hasn't seen since their last visit.
     * Returns false for topics the user has never opened — callers that need to
     * distinguish "has new posts" from "never read" should use getTopicStatus() instead.
     */
    public static function topicHasUnreadPosts(User $user, Topic $topic): bool
    {
        if (! $topic->most_recent_post_time) {
            return false;
        }

        $mostRecentlyReadPostDate = self::getReadProgress($user, $topic);

        if (! $mostRecentlyReadPostDate) {
            return false;
        }

        return $mostRecentlyReadPostDate != $topic->most_recent_post_time;
    }

    /**
     * Reports on the status of a given topic for a user.
     *
     * @return array{new_posts: bool, never_read: bool, unsubscribed: bool}
     */
    public static function getTopicStatus(User $user, Topic $topic): array
    {
        $status = [
            'new_posts' => false,
            'never_read' => false,
            'unsubscribed' => false,
        ];

        $view = self::findViewRecord($user, $topic);

        if ($view === null) {
            $status['never_read'] = true;

            return $status;
        }

        if ($view->unsubscribed) {
            $status['unsubscribed'] = true;
        }

        $mostRecentPostDate = $topic->most_recent_post_time;

        if ($mostRecentPostDate !== null && $view->latest_view_date !== null) {
            if ($mostRecentPostDate->gt($view->latest_view_date)) {
                $status['new_posts'] = true;
            }
        }

        return $status;
    }

    public static function unsubscribeFromTopic(User $user, Topic $topic): void
    {
        $progress = self::findViewRecord($user, $topic);

        if ($progress) {
            $progress->unsubscribed = true;
            $progress->update();
        } else {
            $progress = new View;
            $progress->user_id = $user->id;
            $progress->topic_id = $topic->id;
            $progress->latest_view_date = $topic->most_recent_post_time;
            $progress->unsubscribed = true;
            $progress->save();
        }
    }

    public static function subscribeToTopic(User $user, Topic $topic): void
    {
        $progress = self::findViewRecord($user, $topic);

        if ($progress) {
            $progress->unsubscribed = false;
            $progress->update();
        } else {
            $progress = new View;
            $progress->user_id = $user->id;
            $progress->topic_id = $topic->id;
            $progress->latest_view_date = $topic->most_recent_post_time;
            $progress->unsubscribed = false;
            $progress->save();
        }
    }

    /*
     * Updates the read progress of all previously read topics
     * with the latest post time of those topics.
     */
    public static function catchUpCatchUp(User $user): void
    {
        foreach ($user->views as $view) {
            if ($view->topic !== null) {
                self::updateReadProgress($user, $view->topic);
            }
        }
    }
}
