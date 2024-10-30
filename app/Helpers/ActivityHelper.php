<?php

namespace App\Helpers;

use App\Models\Activity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/*
   helper methods for dealing with activities
*/

class ActivityHelper
{
    /**
     * returns a collection of activities within the last NEXUS_RECENT_ACTIVITY minutes
     *
     * @return Collection of recent activities
     */
    public static function recentActivities()
    {

        $within = config('nexus.recent_activity');
        $activities = Activity::where('time', '>=', Carbon::now()->subMinutes($within))
            ->get();

        return $activities;
    }

    /**
     * updates or creates the current record of a user's activity in the BBS
     *
     * @param  string  $text  - description of the activity eg 'Reading AreaOrange'
     * @param  string  $route  - the url of the activity if it exists eg '/topics/123'
     * @param  int  $user_id  - the user id
     */
    public static function updateActivity($user_id, $text = null, $route = null)
    {
        $activity = Activity::firstOrNew(['user_id' => $user_id]);
        $activity->text = $text;
        $activity->route = $route;
        $activity->time = Carbon::now();
        $activity->save();
    }

    /**
     * removes any existing activity for the current user
     *
     * @param  int  $user_id  - the user id
     */
    public static function removeActivity($user_id)
    {
        Activity::where('user_id', $user_id)->delete();
    }
}
