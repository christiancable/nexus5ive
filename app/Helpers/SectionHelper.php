<?php
namespace App\Helpers;

class SectionHelper
{
    /**
    * create a collection of all descendant sections
    *
    * @param App\Section - a section
    * @return collection of all descendant sections
    */
    public static function allChildSections(\App\Section $section)
    {
        $allChildSections = new \Illuminate\Support\Collection;
        foreach ($section->sections as $child) {
            $allChildSections->prepend($child);
            $allChildSections = self::listChildren($child, $allChildSections);
        }

        return $allChildSections;
    }

    private static function listChildren(\App\Section $section, $children)
    {
        foreach ($section->sections as $child) {
            $children->prepend($child);
            $children = self::listChildren($child, $children);
        }
        return $children;
    }
}
