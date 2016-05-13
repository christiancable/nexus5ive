<?php
namespace Nexus\Helpers;

class ViewHelper
{
    /**
     * records a users read progress within a topic
     */
    public static function updateReadProgress(\Nexus\User $user, \Nexus\Topic $topic)
    {

        $lastestView = \Nexus\View::where('topic_id', $topic->id)->where('user_id', $user->id)->first();

        if ($lastestView) {
            $lastestView->latest_view_date = $topic->most_recent_post_time;
            $lastestView->update();
        } else {
            $view = new \Nexus\View;
            $view->user_id = $user->id;
            $view->topic_id = $topic->id;
            $view->latest_view_date = $topic->most_recent_post_time;
            $view->save();
        }
    }

    public static function getReadProgress(\Nexus\User $user, \Nexus\Topic $topic)
    {
        $result = false;

        $latestView = \Nexus\View::select('latest_view_date')
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
     * @param  \Nexus\User $user - the user
     * @param  \Nexus\Topic $topic - a topic
     * @return boolean has the topic being updated or not
     */
    public static function topicHasUnreadPosts(\Nexus\User $user, \Nexus\Topic $topic)
    {
        $return = true;
        $mostRecentlyReadPostDate =  \Nexus\Helpers\ViewHelper::getReadProgress($user, $topic);

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
     * @param  \Nexus\User $user - the user
     * @param  \Nexus\Topic $topic - a topic
     * @return array - of status values
     */
    public static function getTopicStatus(\Nexus\User $user, \Nexus\Topic $topic)
    {
        $status = [
            'new_posts' => false,
            'never_read' => false,
            'unsubscribed' => false,
        ];

        $mostRecentlyReadPostDate =  \Nexus\Helpers\ViewHelper::getReadProgress($user, $topic);
        $mostRecentPostDate = $topic->most_recent_post_time;

        $view = \Nexus\View::where('topic_id', $topic->id)->where('user_id', $user->id)->first();

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
    public static function unsubscribeFromTopic(\Nexus\User $user, \Nexus\Topic $topic)
    {
        $lastestView = \Nexus\View::where('topic_id', $topic->id)->where('user_id', $user->id)->first();

        if ($lastestView) {
            $lastestView->unsubscribed = true;
            $lastestView->update();
        } else {
            $view = new \Nexus\View;
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
    public static function subscribeToTopic(\Nexus\User $user, \Nexus\Topic $topic)
    {
        $lastestView = \Nexus\View::where('topic_id', $topic->id)->where('user_id', $user->id)->first();

        if ($lastestView) {
            $lastestView->unsubscribed = false;
            $lastestView->update();
        } else {
            $view = new \Nexus\View;
            $view->user_id = $user->id;
            $view->topic_id = $topic->id;
            $view->latest_view_date = $topic->most_recent_post_time;
            $view->unsubscribed = false;
            $view->save();
        }
    }
}