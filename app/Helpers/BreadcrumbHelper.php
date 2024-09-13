<?php

namespace App\Helpers;

use App\Models\Section;
use App\Models\Topic;
use App\Models\User;

/*
   helper methods for dealing with breadcrumbs
*/

class BreadcrumbHelper
{
    /**
     * breadcrumbForTopic
     *
     * @return array
     */
    public static function breadcrumbForTopic(Topic $topic)
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
            $crumb['route'] = action('App\Http\Controllers\Nexus\SectionController@show', ['section' => $section->id]);
            $breadcrumbs[] = $crumb;
            $section = $section->parent;
        } while ($section != null);

        return array_reverse($breadcrumbs);
    }

    /**
     * @return array
     */
    public static function breadcrumbForSection(Section $section)
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
            $crumb['route'] = action('App\Http\Controllers\Nexus\SectionController@show', ['section' => $section->id]);
            $breadcrumbs[] = $crumb;
            $section = $section->parent;
        }

        return array_reverse($breadcrumbs);
    }

    /**
     * generates a fake breadcrumb trail for a page which isn't
     * a topic or a section
     *
     * @param  string  $location
     * @return array
     */
    public static function breadcumbForUtility($location)
    {
        // TODO
        return []; 

        $breadcrumbs = [];
        $crumb = [];
        $crumb['title'] = $location;
        $crumb['route'] = null;
        $breadcrumbs[] = $crumb;

        $section = Section::first(['id', 'title']);
        $crumb['title'] = $section->title;
        $crumb['route'] = action('App\Http\Controllers\Nexus\SectionController@show', ['section' => $section->id]);
        $breadcrumbs[] = $crumb;

        return array_reverse($breadcrumbs);
    }

    /**
     * generates a breadcrumb trail for a user profile
     *
     * @return array
     */
    public static function breadcrumbForUser(User $user)
    {
        $breadcrumbs = [];
        $crumb['title'] = $user->username;
        $crumb['route'] = null;
        $breadcrumbs[] = $crumb;

        $crumb['title'] = 'Users';
        $crumb['route'] = action('App\Http\Controllers\Nexus\UserController@index');
        $breadcrumbs[] = $crumb;

        $section = Section::first();
        $crumb['title'] = $section->title;
        $crumb['route'] = action('App\Http\Controllers\Nexus\SectionController@show', ['section' => $section->id]);
        $breadcrumbs[] = $crumb;

        return array_reverse($breadcrumbs);
    }

    /**
     * generates a breadcrumb trail for a user to user chat
     *
     * @return array
     */
    public static function breadcrumbForChat(string $username)
    {
        $breadcrumbs = [];
        $crumb['title'] = $username;
        $crumb['route'] = null;
        $breadcrumbs[] = $crumb;

        $crumb['title'] = 'Messages';
        $crumb['route'] = action('App\Http\Controllers\Nexus\ChatController@index');
        $breadcrumbs[] = $crumb;

        $section = Section::first();
        $crumb['title'] = $section->title;
        $crumb['route'] = action('App\Http\Controllers\Nexus\SectionController@show', ['section' => $section->id]);
        $breadcrumbs[] = $crumb;

        return array_reverse($breadcrumbs);
    }
}
