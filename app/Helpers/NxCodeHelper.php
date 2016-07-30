<?php

namespace Nexus\Helpers;

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

    // http://regexlib.com/REDetails.aspx?regexp_id=3514
    private static $youTubePattern = <<< 'PATTERN'
/(?:[hH][tT]{2}[pP][sS]{0,1}:\/\/)?[wW]{0,3}\.{0,1}[yY][oO][uU][tT][uU](?:\.[bB][eE]|[bB][eE]\.[cC][oO][mM])?\/(?:(?:[wW][aA][tT][cC][hH])?(?:\/)?\?(?:.*)?[vV]=([a-zA-Z0-9--]+).*|([A-Za-z0-9--]+))/
PATTERN;

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
        $matches = array();
        $videoTags = array();
        $embedCodes = array();

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
        $matches = array();
        $pattern = '/\[spoiler-\](.*)\[-spoiler\]/iU';

        $spoilers = array();
        $unspoiledspoilers = array();

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
        $text = MarkdownHelper::markdown($text);

        return $text;
    }
}
