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
     * reports if a topic has been updated since the a user last read
     *
     * @param  int $user_id id of a user
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
}
