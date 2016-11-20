<?php
namespace Nexus\Helpers;

class TopicHelper
{
    /**
     * returns a string of the route to a post within a topic, respects pagination
     * @param Nexus\Post a post within a topic
     * @return string
     */
    public static function routeToPost(\Nexus\Post $post)
    {
    
        // how many pages worth of posts are we into the topic
        $depth = $post->topic->posts()->where('id', '>=', $post->id)->count();
        $page = ceil($depth/config('nexus.pagination'));

        // create the route
        $route = action(
            'Nexus\TopicController@show',
            [
                'id' => $post->topic->id,
                'page' => $page,
            ]
        );
        return $route;
    }

    public static function recentTopics($maxresults = 10)
    {

        $allTopicIDs = \Nexus\Topic::with('most_recent_post_id')
            ->select('id')
            ->get()
            ->pluck('id', 'most_recent_post_id.post_id')
            ->flip()
            ->sort()
            ->reverse()
            ->flip()
            ->values()
            ->take($maxresults);
        
        $allTopicIDsOrdered = implode(',', $allTopicIDs->toArray());

        $topics = \Nexus\Topic::with('most_recent_post')
            ->whereIn('id', $allTopicIDs)
            ->orderByRaw(\DB::raw("FIELD(id, $allTopicIDsOrdered)"))
            ->get();
    
        return $topics;
    }
}
