<?php
namespace Nexus\Helpers;

class RestoreHelper
{
    /**
     * restores a topic, along with its posts and views, to a section
     * @param int     * @param int $section_id - the id of the section
     */
    public static function restoreTopicToSection(\Nexus\Topic $topic, \Nexus\Section $section)
    {
        $topic->posts()->restore();
        $topic->views()->restore();
        $topic->restore();

        $topic->section_id = $section->id;
    }
}
