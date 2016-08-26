<?php
namespace Nexus\Helpers;

class MentionHelper
{
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
                $user->addMention($post);
            }
        }
    }

    /**
    * added css to highlight @mentions to text
    * @param string $text
    * @return string $text
    */
    public static function highlightMentions($text)
    {
        $pattern = '/@([[:word:]]+)/';
        $replacement = '<span class="text-muted">@</span><mark><strong>${1}</strong></mark>';
        $highlightedText = preg_replace($pattern, $replacement, $text);
        return $highlightedText;
    }
}
