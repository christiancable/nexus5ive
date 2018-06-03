<?php

namespace App\Helpers;

class NxCodeHelper
{
    private static $youTubeHTMLStart = <<< 'HTML'
<div class="video-wrapper">
      <iframe id="youtube-player" src="//www.youtube.com/embed/
HTML;

    private static $youTubeHTMLStop = <<< 'HTML'
?rel=0&showinfo=0&autohide=1" frameborder="0" allowfullscreen></iframe>
    </div>
HTML;

    // based on http://stackoverflow.com/questions/6556559/youtube-api-extract-video-id#
    private static $youTubePattern =
        '%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
        $%x'
    ;

    /**
     * converts a string with NXCode tags into
     * markdown tags.
     *
     * @param $text string string with nxcode tags
     *
     * @return string with markdown tags
     **/
    public static function nxToMarkdown($nxText)
    {
        /*
        nexus tags are
        [www-][-www]
        [i-][-i]
        [b-][-b]
        [picture-][-picture]
        [youtube-][-youtube] @todo - add embed code
        [ascii-][-ascii]
        [quote-][-quote]

        [u-][-u]
        [small-][-small]
        [updated-][-updated]
        [hudson-][-hudson]
        [spoiler-][-spoiler]

        */

        $nxTags = [
            '[www-]',
            '[-www]',
            '[i-]',
            '[-i]',
            '[b-]',
            '[-b]',
            '[picture-]',
            '[-picture]',
            '[ascii-]',
            '[-ascii]',
            '[quote-]',
            '[-quote]',
        ];
        $mdTags = [
            '',
            '',
            '_',
            '_',
            '__',
            '__',
            '![image](',
            ')',
            '`',
            '`',
            '_',
            '_',
        ];

        $mdText = str_ireplace($nxTags, $mdTags, $nxText);

        return $mdText;
    }

    /**
     * converts a string with NXCode YouTube tags into
     * text with youtueb embed code.
     **/
    public static function embedYouTube($text)
    {
        $matches = [];
        $videoTags = [];
        $embedCodes = [];

        $pattern = '/\[youtube-\](.*)\[-youtube\]/im';
        // grab all youtube matches and populate array
        preg_match_all($pattern, $text, $matches);
        // echo "\n\n";
        // var_dump($matches);
        foreach ($matches[1] as $match) {
            preg_match(self::$youTubePattern, $match, $videoIDs);

            $currentVideoID = end($videoIDs);
            // if match is a valid you tube then
            if (strlen($currentVideoID)) {
                // make the valid embed code
                $videoTags[] = '[youtube-]'.$match.'[-youtube]';
                $embedCodes[] = self::$youTubeHTMLStart.$currentVideoID.self::$youTubeHTMLStop;
            } else {
                // replace invalid youtube tag with empty string
                $videoTags[] = '[youtube-]'.$match.'[-youtube]';
                $embedCodes[] = '';
            }
        }
        $text = str_ireplace($videoTags, $embedCodes, $text);

        return $text;
    }

    public static function spoilerTags($text)
    {
        $matches = [];
        $pattern = '/\[spoiler-\](.*)\[-spoiler\]/iU';

        $spoilers = [];
        $unspoiledspoilers = [];

        $spoilerStart = '<span class="spoiler">';
        $spoilerStop = '</span>';
        preg_match_all($pattern, $text, $matches);

        foreach ($matches[0] as $key => $value) {
            $spoilers[] = $value;
            $unspoiledspoilers[] = $spoilerStart . $matches[1][$key] . $spoilerStop;
        }

        if (!empty($matches)) {
            $text = str_replace($spoilers, $unspoiledspoilers, $text);
        }

        return $text;
    }

    public static function lazyLoadClass($text)
    {
        $pattern = '/(<img .*)()(\/?>)/mU';
        $subst = '$1 class="b-lazy"$3';

        $text = preg_replace($pattern, $subst, $text);

        // dd($text);
        return $text;
    }

    /**
     * decode text so that it is suitable for display
     * for use in blade templates.
     *
     * @param $text string - text of a post containing markdown and nxcode
     *
     * @return string - formatting version of $text
     **/
    public static function nxDecode($text)
    {
        $text = self::NxToMarkdown($text);
        $text = strip_tags($text);
        $text = self::embedYouTube($text);
        $text = self::spoilerTags($text);
        $text = self::lazyloadClass($text);
        $text = MarkdownHelper::markdown($text);
        $text = MentionHelper::highlightMentions($text);

        return $text;
    }
}
