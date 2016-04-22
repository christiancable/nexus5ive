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
        $page = ceil($depth/env('NEXUS_PAGINATION'));

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
}
