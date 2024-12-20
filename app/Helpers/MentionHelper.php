<?php

namespace App\Helpers;
use App\Models\Post;
use App\Models\User;

class MentionHelper
{
    private static $mentionPattern = '/(?<=[\W]|^)@(\w+)/';

    /**
     * searches for @user mentions in text
     *
     * @param  string  $text
     * @return array of usernames
     */
    public static function identifyMentions($text)
    {
        $matches = [];

        $matchCount = preg_match_all(self::$mentionPattern, $text, $matches);

        if ($matchCount) {
            $return = $matches[1];
        } else {
            $return = [];
        }

        return $return;
    }

    public static function makeMentions(Post $post)
    {
        $users = self::identifyMentions($post->text);
        foreach ($users as $username) {
            $user = User::where('username', $username)->first();
            if ($user) {
                $user->addMention($post);
            }
        }
    }

    /**
     * added css to highlight @mentions to text
     *
     * @param  string  $text
     * @return string $text
     */
    public static function highlightMentions($text)
    {
        $replacement = '<span class="text-muted">@</span><mark><strong><a href="/users/${1}">${1}</a></strong></mark>';
        $highlightedText = preg_replace(self::$mentionPattern, $replacement, $text);

        return $highlightedText;
    }
}
