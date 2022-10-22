<?php

namespace App\Helpers;

use Illuminate\Support\Str;


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

        $html = Str::of($markdown)->markdown();

        return $html;
    }
}
