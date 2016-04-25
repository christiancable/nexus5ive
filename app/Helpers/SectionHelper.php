<?php
namespace Nexus\Helpers;

class SectionHelper
{
    /**
    * create a collection of all descendant sections
    *
    * @param Nexus\Section - a section
    * @return collection of all descendant sections
    */
    public static function allChildSections(\Nexus\Section $section)
    {
        $allChildSections = new \Illuminate\Support\Collection;
        foreach ($section->sections as $child) {
            $allChildSections->prepend($child);
            $allChildSections = self::listChildren($child, $allChildSections);
        }

        return $allChildSections;
    }

    private static function listChildren(\Nexus\Section $section, $children)
    {
        foreach ($section->sections as $child) {
            $children->prepend($child);
            $children = self::listChildren($child, $children);
        }
        return $children;
    }
}
