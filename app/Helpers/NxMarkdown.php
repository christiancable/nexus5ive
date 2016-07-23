<?php

namespace Nexus\Helpers;

class NxMarkdown extends \Parsedown
{

    protected function inlineLink($Excerpt)
    {
        $link = parent::inlineLink($Excerpt);
        $link['element']['attributes']['target'] = '_blank';
        return $link;
    }
}
