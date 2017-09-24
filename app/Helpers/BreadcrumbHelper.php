<?php
namespace App\Helpers;

/* 
   helper methods for dealing with breadcrumbs
*/

class BreadcrumbHelper
{
    /**
     * @param App\Topic
     * @return an array of links to go in a breadcrumb trail
     *
     */
    public static function breadcrumbForTopic(\App\Topic $topic)
    {
        $breadcrumbs = [];
        $crumb = [];
        $crumb['title'] = $topic->title;
        $crumb['route'] = null;
        $breadcrumbs[] = $crumb;

        $section = $topic->section;
        do {
            $crumb = [];
            $crumb['title'] = $section->title;
            $crumb['route'] = action('Nexus\SectionController@show', ['section_id' => $section->id]);
            $breadcrumbs[] = $crumb;
            $section = $section->parent;
        } while ($section != null);
        
        return array_reverse($breadcrumbs);
    }

    /**
     * @param App\Section
     * @return an array of links to go in a breadcrumb trail
     *
     */
    public static function breadcrumbForSection(\App\Section $section)
    {
        $breadcrumbs = [];
        $crumb = [];
        $crumb['title'] = $section->title;
        $crumb['route'] = null;
        $breadcrumbs[] = $crumb;
        $section = $section->parent;

        while ($section != null) {
            $crumb = [];
            $crumb['title'] = $section->title;
            $crumb['route'] = action('Nexus\SectionController@show', ['section_id' => $section->id]);
            $breadcrumbs[] = $crumb;
            $section = $section->parent;
        }
        return array_reverse($breadcrumbs);
    }

    /**
     * generates a fake breadcrumb trail for a page which isn't
     * a topic or a section
     *
     * @param string
     * @return an array of links to go in a breadcrumb trail
     */
    public static function breadcumbForUtility($location)
    {
        $breadcrumbs = [];
        $crumb = [];
        $crumb['title'] = $location;
        $crumb['route'] = null;
        $breadcrumbs[] = $crumb;

        $section = \App\Section::first();
        $crumb['title'] = $section->title;
        $crumb['route'] = action('Nexus\SectionController@show', ['section_id' => $section->id]);
        $breadcrumbs[] = $crumb;

        return array_reverse($breadcrumbs);
    }

    /**
     * generates a breadcrumb trail for a user profile
     *
     * @param App\User
     * @return an array of links to go in a breadcrumb trail
     */
    public static function breadcrumbForUser(\App\User $user)
    {
        $breadcrumbs = [];
        $crumb['title'] = $user->username;
        $crumb['route'] = null;
        $breadcrumbs[] = $crumb;

        $crumb['title'] = 'Users';
        $crumb['route'] = action('Nexus\UserController@index');
        $breadcrumbs[] = $crumb;

        $section = \App\Section::first();
        $crumb['title'] = $section->title;
        $crumb['route'] = action('Nexus\SectionController@show', ['section_id' => $section->id]);
        $breadcrumbs[] = $crumb;

        return array_reverse($breadcrumbs);
    }
}
