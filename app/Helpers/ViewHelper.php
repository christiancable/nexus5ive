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
            $view->user_id = \Auth::user()->id;
            $view->topic_id = $topic->id;
            $view->latest_view_date = $topic->most_recent_post_time;
            $view->save();
        }
    }
}
