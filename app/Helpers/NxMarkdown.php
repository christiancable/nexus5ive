<?php

namespace App\Helpers;

class NxMarkdown extends \Parsedown
{
    protected function addLinkTargetBlank($Excerpt)
    {
        if (isset($Excerpt['element']['attributes']['href'])) {
            if (stripos($Excerpt['element']['attributes']['href'], 'http') !== false) {
                $Excerpt['element']['attributes']['target'] = '_blank';
            }
        }
        return $Excerpt;
    }
    /**
     * add target blank for external links
    **/
    protected function inlineLink($Excerpt)
    {
        $Excerpt = parent::inlineLink($Excerpt);
        $Excerpt = self::addLinkTargetBlank($Excerpt);

        return $Excerpt;
    }

    /**
     * add target blank for external links
    **/
    protected function inlineUrl($Excerpt)
    {
        $Excerpt = parent::inlineUrl($Excerpt);
        $Excerpt = self::addLinkTargetBlank($Excerpt);

        return $Excerpt;
    }
}
