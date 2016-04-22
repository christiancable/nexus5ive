<?php
namespace Nexus\Helpers;

class TopicHelper
{
    /**
     * returns a string of the route to a post within a topic, respects pagination
     * @param Nexus\Post a post within a topic
     * @return string | null  of recent activities
     */
    public static function routeToPost(\Nexus\Post $post)
    {
        // which page are we on?
        $position = $post->topic->posts()->count() - $post->topic->posts()->where('id', '<=', $post->id)->count();
        $page = (int)ceil($position/env('NEXUS_PAGINATION'));
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
