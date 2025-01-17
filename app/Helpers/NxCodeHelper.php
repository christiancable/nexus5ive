<?php

namespace App\Helpers;

// phpcs:disable Generic.Files.LineLength
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

    /**
     * converts a string with NXCode tags into
     * markdown tags.
     *
     * @param  string  $nxText  string with nxcode tags
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
     * adds embed code around youtube video links
     * based on https://regex101.com/r/OY96XI/1
     **/
    public static function embedYouTube($text)
    {
        $re = '/(?:https?:)?(?:\/\/)?(?:[0-9A-Z-]+\.)?(?:youtu\.be\/|youtube(?:-nocookie)?\.com\S*?[^\w\s-])([\w-]{11})(?=[^\w-]|$)(?![?=&+%\w.-]*(?:[\'"][^<>]*>|<\/a>))[?=&+%\w.-]*/im';

        $subst = self::$youTubeHTMLStart.'$1'.self::$youTubeHTMLStop;
        $result = preg_replace($re, $subst, $text);

        return $result;
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
            $unspoiledspoilers[] = $spoilerStart.$matches[1][$key].$spoilerStop;
        }

        if (! empty($matches)) {
            $text = str_replace($spoilers, $unspoiledspoilers, $text);
        }

        return $text;
    }

    /**
     * add the loading lazy attribure to image tags
     *
     * @param  string  $text  - html text
     * @return string html with updated anchor tag markup
     **/
    public static function lazyLoadAttribute(string $text): string
    {
        return str_replace('<img src="', '<img loading="lazy" src="', $text);
    }

    /**
     * decode text so that it is suitable for display
     * for use in blade templates.
     *
     * @param  string  $text  - text of a post containing markdown and nxcode
     * @return string - formatted version of $text
     **/
    public static function nxDecode($text)
    {
        $text = self::NxToMarkdown($text);
        $text = strip_tags($text);
        $text = self::embedYouTube($text);
        $text = self::spoilerTags($text);
        $text = MarkdownHelper::markdown($text);
        $text = self::lazyLoadAttribute($text);
        $text = MentionHelper::highlightMentions($text);

        return $text;
    }
}
