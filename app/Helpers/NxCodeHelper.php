<?php

namespace Nexus\Helpers;

class NxCodeHelper
{
    /**
    * converts a string with NXCode tags into
    * markdown tags
    * @param $text string string with nxcode tags
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
            '[youtube-]',
            '[-youtube]',
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
            '',
            '',
            '`',
            '`',
            '_',
            '_',
        ];

        $mdText = str_ireplace($nxTags, $mdTags, $nxText);

        return $mdText;
    }


    /**
     * decode text so that it is suitable for display
     * for use in blade templates
     *
     * @param $text string - text of a post containing markdown and nxcode
     * @return string - formatting version of $text
     **/
    public static function nxDecode($text)
    {
        $text = self::NxToMarkdown($text);
        $text = strip_tags($text);
        $text = MarkdownHelper::markdown($text);
        
        return $text;
    }
}
