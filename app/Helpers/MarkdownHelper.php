<?php

namespace App\Helpers;

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
        $parser = new NxMarkdown();
        $html = $parser->text($markdown);

        return $html;
    }
}
