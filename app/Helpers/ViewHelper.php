<?php
namespace App\Helpers;

class ViewHelper
{
    /**
     * records a users read progress within a topic
     */
    public static function updateReadProgress(\App\User $user, \App\Topic $topic)
    {

        $lastestView = \App\View::where('topic_id', $topic->id)->where('user_id', $user->id)->first();

        if ($lastestView) {
            $lastestView->latest_view_date = $topic->most_recent_post_time;
            $lastestView->update();
        } else {
            $view = new \App\View;
            $view->user_id = $user->id;
            $view->topic_id = $topic->id;
            $view->latest_view_date = $topic->most_recent_post_time;
            $view->save();
        }
    }

    public static function getReadProgress(\App\User $user, \App\Topic $topic)
    {
        $result = false;

        $latestView = \App\View::select('latest_view_date')
            ->where('topic_id', $topic->id)
            ->where('user_id', $user->id)
            ->first();

        if ($latestView) {
            $result = $latestView->latest_view_date;
        }

        return $result;
    }


    /**
     * reports if a given topic has been updated since the a user last read
     *
     * @param  \App\User $user - the user
     * @param  \App\Topic $topic - a topic
     * @return boolean has the topic being updated or not
     */
    public static function topicHasUnreadPosts(\App\User $user, \App\Topic $topic)
    {
        $return = true;
        $mostRecentlyReadPostDate =  \App\Helpers\ViewHelper::getReadProgress($user, $topic);

        if ($mostRecentlyReadPostDate) {
            if ($mostRecentlyReadPostDate <> $topic->most_recent_post_time) {
                $return = true;
            } else {
                $return = false;
            }
        } else {
            $return = false;
        }
        
        if (!$topic->most_recent_post_time) {
            $return = false;
        }

        return $return;
    }

    /**
     * reports on the status of a given topic for a user
     *
     * @param  \App\User $user - the user
     * @param  \App\Topic $topic - a topic
     * @return array - of status values
     */
    public static function getTopicStatus(\App\User $user, \App\Topic $topic)
    {
        $status = [
            'new_posts' => false,
            'never_read' => false,
            'unsubscribed' => false,
        ];

        $mostRecentlyReadPostDate =  \App\Helpers\ViewHelper::getReadProgress($user, $topic);
        $mostRecentPostDate = $topic->most_recent_post_time;

        $view = \App\View::where('topic_id', $topic->id)->where('user_id', $user->id)->first();

        if ($view !== null) {
            if ($view->unsubscribed != 0) {
                 $status['unsubscribed'] = true;
            }

            if ($mostRecentPostDate !== false && $mostRecentlyReadPostDate !== false) {
                if ($mostRecentPostDate->gt($mostRecentlyReadPostDate)) {
                    $status['new_posts'] = true;
                }
            }
        } else {
            $status['never_read'] = true;
        }

        return $status;
    }

    /**
     * unsubscribes the user from the topic
     **/
    public static function unsubscribeFromTopic(\App\User $user, \App\Topic $topic)
    {
        $lastestView = \App\View::where('topic_id', $topic->id)->where('user_id', $user->id)->first();

        if ($lastestView) {
            $lastestView->unsubscribed = true;
            $lastestView->update();
        } else {
            $view = new \App\View;
            $view->user_id = $user->id;
            $view->topic_id = $topic->id;
            $view->latest_view_date = $topic->most_recent_post_time;
            $view->unsubscribed = true;
            $view->save();
        }
    }

    /**
     * subscribes the user from the topic
     **/
    public static function subscribeToTopic(\App\User $user, \App\Topic $topic)
    {
        $lastestView = \App\View::where('topic_id', $topic->id)->where('user_id', $user->id)->first();

        if ($lastestView) {
            $lastestView->unsubscribed = false;
            $lastestView->update();
        } else {
            $view = new \App\View;
            $view->user_id = $user->id;
            $view->topic_id = $topic->id;
            $view->latest_view_date = $topic->most_recent_post_time;
            $view->unsubscribed = false;
            $view->save();
        }
    }
    
    /*
        updates the read progress of all previously read topics
        with the latest post of those topics
    */
    public static function catchUpCatchUp(\App\User $user)
    {
        $views = $user->views;
        
        foreach ($views as $view) {
            if ($view->topic != null) {
                self::updateReadProgress($user, $view->topic);
            }
        }
    }
}
