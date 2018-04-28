<?php
namespace App\Helpers;

class TopicHelper
{
    /**
     * returns a string of the route to a post within a topic, respects pagination
     * @param App\Post a post within a topic
     * @return string
     */
    public static function routeToPost(\App\Post $post)
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
        /*
        inspired by
        http://stackoverflow.com/questions/612231/how-can-i-select-rows-with-maxcolumn-value-distinct-by-another-column-in-sql
        */

        $sql = <<<SQL
SELECT tt.topic_id
FROM posts tt
INNER JOIN
    (SELECT topic_id, MAX(id) AS LatestPostID
    FROM posts
    WHERE deleted_at IS NULL 
    GROUP BY topic_id) groupedtt 
ON tt.topic_id = groupedtt.topic_id 
AND tt.id = groupedtt.LatestPostID 
WHERE deleted_at IS NULL 
ORDER BY tt.id desc limit $maxresults
SQL;
    
        $allTopicIDs = array_pluck(\DB::select(\DB::raw($sql)), 'topic_id');
        // $topics = \App\Topic::with('most_recent_post', 'most_recent_post.author', 'section')
        // removed most_recent_post from the eager loading here as it was killing memory in large forums
        $topics = \App\Topic::with('section')
        ->whereIn('id', $allTopicIDs)->get();
           
        // sorting here rather than in SQL because FIELD is MySQL only and so fails tests
        $sortedTopics = $topics->sortBy(function ($model) use ($allTopicIDs) {
            return array_search($model->id, $allTopicIDs);
        });

        return $sortedTopics;
    }
}
