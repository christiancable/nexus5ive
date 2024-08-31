<?php

namespace App\Helpers;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownHelper
{
    /**
     * translates markdown into html
     *
     * @param  string  $markdown
     * @return string
     */
    public static function markdown($markdown)
    {

        $config = [
            'renderer' => [
                'block_separator' => "\n",
                'inner_separator' => "\n",
                'soft_break' => "\n",
            ],
            'commonmark' => [
                'enable_em' => true,
                'enable_strong' => true,
                'use_asterisk' => true,
                'use_underscore' => true,
                'unordered_list_markers' => ['-', '*', '+'],
            ],
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
            'max_nesting_level' => PHP_INT_MAX,
            'slug_normalizer' => [
                'max_length' => 255,
            ],
            'disallowed_raw_html' => [
                'disallowed_tags' => ['title', 'textarea', 'style', 'xmp', 'noembed', 'noframes', 'script', 'plaintext'],
            ],
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension);

        /*
        laravel's Str markdown helper uses this under the hood but does not
        allow us to change the config for the disallowed_raw_html so instead
        use the underlying league/commonmark classes so allow youtube iframe embeds
        */
        $environment->addExtension(new AutolinkExtension);
        $environment->addExtension(new DisallowedRawHtmlExtension);
        $environment->addExtension(new StrikethroughExtension);
        $environment->addExtension(new TableExtension);
        $environment->addExtension(new TaskListExtension);

        $converter = new MarkdownConverter($environment);

        return trim($converter->convert($markdown));
    }
}
