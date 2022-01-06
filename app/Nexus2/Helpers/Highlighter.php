<?php

namespace App\Nexus2\Helpers;

class Highlighter
{
    /**
     * translates nexus2 formatting to markdown
     *
     * @param string $raw - nexus2 text
     *
     * @return string text with markdown formatting
     *
     */
    public function highlight(string $raw): string
    {
        $highlightedString = $this->curlyBraces($raw);
        $highlightedString = $this->singleLetter($highlightedString);
        return $highlightedString;
    }


    private function curlyBraces(string $raw): string
    {
        $re = '/{(.*)(}|$)/mU';
        $subst = '**$1**';
        $result = preg_replace($re, $subst, $raw);

        return $result;
    }

    private function singleLetter(string $raw): string
    {
        $re = '/@(.)/mU';
        $subst = '**$1**';
        $result = preg_replace($re, $subst, $raw);

        return $result;
    }
}
