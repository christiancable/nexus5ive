<?php
namespace Nexus\Helpers;

class MentionHelper
{
    public static function addMention(\Nexus\User $user, \Nexus\Post $post)
    {
        $mention = new \Nexus\Mention;
        $mention->user_id = $user->id;
        $mention->post_id = $post->id;
        $mention->save();
    }

     /**
     * searches for @user mentions in text
     *
     * @param  string $text
     * @return array of usernames
     */
    public static function identifyMentions($text)
    {
        $matches = array();
        $pattern = '/@([[:word:]]+)/';

        $matchCount = preg_match_all($pattern, $text, $matches);
        
        if ($matchCount) {
            $return = $matches[1];
        } else {
            $return = array();
        }
        return $return;
    }

    public static function makeMentions(\Nexus\Post $post)
    {
        $users = self::identifyMentions($post->text);
        foreach ($users as $username) {
            $user = \Nexus\User::where('username', $username)->first();
            if ($user) {
                self::addMention($user, $post);
            }
        }
    }

    public static function removeMentions(\Nexus\User $user, \Nexus\Post $post)
    {
        \Nexus\Mention::where('post_id', $post->id)->where('user_id', $user->id)->delete();
    }
}
