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
        $crumb = array();
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
        $crumb = array();
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

    /**
     * generates a fake breadcrumb trail for a page which isn't
     * a topic of a section
     *
     * @param string
     * @return an array of links to go in a breadcrumb trail
     */
    public static function breadcumbForUtility($location)
    {
        $breadcrumbs = array();
        $crumb = array();
        $crumb['title'] = $location;
        $crumb['route'] = null;
        $breadcrumbs[] = $crumb;

        $section = \Nexus\Section::first();
        $crumb['title'] = $section->title;
        $crumb['route'] = action('Nexus\SectionController@show', ['section_id' => $section->id]);
        $breadcrumbs[] = $crumb;

        return array_reverse($breadcrumbs);
    }

    /**
     * generates a breadcrumb trail for a user profile
     *
     * @param Nexus\User
     * @return an array of links to go in a breadcrumb trail
     */
    public static function breadcrumbForUser(\Nexus\User $user)
    {
        $breadcrumbs = array();
        $crumb['title'] = $user->username;
        $crumb['route'] = null;
        $breadcrumbs[] = $crumb;

        $crumb['title'] = 'Users';
        $crumb['route'] = action('Nexus\UserController@index');
        $breadcrumbs[] = $crumb;

        $section = \Nexus\Section::first();
        $crumb['title'] = $section->title;
        $crumb['route'] = action('Nexus\SectionController@show', ['section_id' => $section->id]);
        $breadcrumbs[] = $crumb;

        return array_reverse($breadcrumbs);
    }
}
