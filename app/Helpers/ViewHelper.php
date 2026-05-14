<?php

namespace App\Helpers;

use App\Models\Topic;
use App\Models\User;
use App\Models\View;

class ViewHelper
{
    /**
     * records a users read progress within a topic
     */
    public static function updateReadProgress(User $user, Topic $topic)
    {
        $progress = View::where('topic_id', $topic->id)->where('user_id', $user->id)->first();

        if (! $progress) {
            // first time viewing this topic
            $progress = new View;
            $progress->user_id = $user->id;
            $progress->topic_id = $topic->id;
            $progress->latest_view_date = $topic->most_recent_post_time;
            $progress->save();
        } else {
            // if there's a newer post then update the progress
            if ($topic->most_recent_post_time !== $progress->latest_view_date) {
                $progress->latest_view_date = $topic->most_recent_post_time;
                $progress->save();
            }
        }
    }

    public static function getReadProgress(User $user, Topic $topic)
    {
        $result = false;

        $progress = View::select('latest_view_date')
            ->where('topic_id', $topic->id)
            ->where('user_id', $user->id)
            ->first();

        if ($progress) {
            $result = $progress->latest_view_date;
        }

        return $result;
    }

    /**
     * Reports whether a topic has posts newer than when the user last read it.
     * Requires the View to have topic and topic.most_recent_post already loaded
     * to avoid N+1 queries.
     */
    public static function viewHasUnreadPosts(View $view): bool
    {
        $postTime = $view->topic->latestPostByTime?->time;

        if (! $postTime) {
            return false;
        }

        return $postTime->timestamp !== $view->latest_view_date->timestamp;
    }

    /**
     * Reports if a given topic has been updated since the user last read it.
     *
     * @param  User  $user  - the user
     * @param  Topic  $topic  - a topic
     * @return bool has the topic being updated or not
     */
    public static function topicHasUnreadPosts(User $user, Topic $topic): bool
    {
        $latestViewDate = static::getReadProgress($user, $topic);

        if (! $latestViewDate) {
            return false;
        }

        $postTime = $topic->most_recent_post_time;

        if (! $postTime) {
            return false;
        }

        return $postTime->gt($latestViewDate);
    }

    /**
     * reports on the status of a given topic for a user
     *
     * @param  User  $user  - the user
     * @param  Topic  $topic  - a topic
     * @param  View|null  $view  - pre-loaded View record to avoid extra queries
     * @return array - of status values
     */
    public static function getTopicStatus(User $user, Topic $topic, ?View $view = null): array
    {
        $status = [
            'new_posts' => false,
            'never_read' => false,
            'unsubscribed' => false,
        ];

        $view = $view ?? View::where('topic_id', $topic->id)->where('user_id', $user->id)->first();

        if ($view === null) {
            $status['never_read'] = true;

            return $status;
        }

        if ($view->unsubscribed) {
            $status['unsubscribed'] = true;
        }

        $mostRecentPostDate = $topic->relationLoaded('latestPostByTime')
            ? $topic->latestPostByTime?->time
            : $topic->most_recent_post_time;

        $latestViewDate = $view->latest_view_date;

        if ($mostRecentPostDate !== null && $latestViewDate !== null) {
            if ($mostRecentPostDate->gt($latestViewDate)) {
                $status['new_posts'] = true;
            }
        }

        return $status;
    }

    /**
     * unsubscribes the user from the topic
     **/
    public static function unsubscribeFromTopic(User $user, Topic $topic)
    {
        $progress = View::where('topic_id', $topic->id)->where('user_id', $user->id)->first();

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

    /**
     * subscribes the user from the topic
     **/
    public static function subscribeToTopic(User $user, Topic $topic)
    {
        $progress = View::where('topic_id', $topic->id)->where('user_id', $user->id)->first();

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
        updates the read progress of all previously read topics
        with the latest post of those topics
    */
    public static function catchUpCatchUp(User $user)
    {
        $views = $user->views;

        foreach ($views as $view) {
            if ($view->topic != null) {
                self::updateReadProgress($user, $view->topic);
            }
        }
    }
}
