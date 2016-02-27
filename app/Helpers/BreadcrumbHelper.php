<?php
namespace Nexus\Helpers;

/* 
   helper methods for dealing with breadcrumbs
*/

class BreadcrumbHelper
{
    /**
     * @param Nexus\Topic
     * @return an array of links to go in a breadcrumb trail
     *
     */
    public static function breadcrumbForTopic(\Nexus\Topic $topic)
    {
        $breadcrumbs = array();
        $crumb['title'] = $topic->title;
        $crumb['route'] = null;
        $breadcrumbs[] = $crumb;

        $section = $topic->section;
        do {
            $crumb = array();
            $crumb['title'] = $section->title;
            $crumb['route'] = action('Nexus\SectionController@show', ['section_id' => $section->id]);
            $breadcrumbs[] = $crumb;
            $section = $section->parent;
        } while ($section != null);
        
        return array_reverse($breadcrumbs);
    }

    /**
     * @param Nexus\Section
     * @return an array of links to go in a breadcrumb trail
     *
     */
    public static function breadcrumbForSection(\Nexus\Section $section)
    {
        $breadcrumbs = array();
        $crumb['title'] = $section->title;
        $crumb['route'] = null;
        $breadcrumbs[] = $crumb;
        $section = $section->parent;

        while ($section != null) {
            $crumb = array();
            $crumb['title'] = $section->title;
            $crumb['route'] = action('Nexus\SectionController@show', ['section_id' => $section->id]);
            $breadcrumbs[] = $crumb;
            $section = $section->parent;
        }
        return array_reverse($breadcrumbs);
    }
}
