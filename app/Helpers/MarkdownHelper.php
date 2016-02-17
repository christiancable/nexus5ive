<?php

namespace Nexus\Helpers;

use Parsedown;

class MarkdownHelper
{
    /**
     * translates markdown into html
     *
     * @param  string $markdown
     * @return string
     */
    public static function markdown($markdown)
    {

        $parser = new Parsedown();
        $html = $parser->text($markdown);

        return $html;
    }
}
