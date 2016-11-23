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
        $allTopicIDsString = implode(',', $allTopicIDs);
        $topics = \Nexus\Topic::with('most_recent_post', 'most_recent_post.author', 'section')
            ->whereIn('id', $allTopicIDs)
            ->orderByRaw(\DB::raw("FIELD(id, $allTopicIDsString)"))
            ->get();
    
        return $topics;
    }
}
