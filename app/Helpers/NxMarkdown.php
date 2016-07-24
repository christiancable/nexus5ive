<?php

namespace Nexus\Helpers;

class NxMarkdown extends \Parsedown
{

    /**
     * add target blank for external links
    **/
    protected function inlineLink($Excerpt)
    {
        $link = parent::inlineLink($Excerpt);
        if (isset($link['element']['attributes']['href'])) {
            if (stripos($link['element']['attributes']['href'], 'http') !== false) {
                $link['element']['attributes']['target'] = '_blank';
            }
        }
        return $link;
    }
}
