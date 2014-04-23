<?php 

namespace nexusfive;

/* one day this will all be TWIG */

class NxInterface
{
    public function getBreadcrumbs($section_info)
    {
        // accepts an array of section info and returns HTML for breadcrumbs

        $HTML = <<<'HTML'
<font size="-1"><a href="section.php?section_id=%SECTION_ID%">%SECTION_NAME%</a> -&gt; </font>
HTML;

        $HTML = str_replace('%SECTION_NAME%', $section_info['section_title'], $HTML);
        $HTML = str_replace('%SECTION_ID%', $section_info['section_id'], $HTML);

        return $HTML;
    }
}
