<?php
namespace Nexus\Helpers;

class RestoreHelper
{
    /**
     * restores a topic, along with its posts and views, to a section
     */
    public static function restoreTopicToSection(\Nexus\Topic $topic, \Nexus\Section $section)
    {
        $topic->posts()->restore();
        $topic->views()->restore();
        $topic->restore();

        $topic->section_id = $section->id;
        $topic->save();
    }

    public static function restoreSectionToSection(\Nexus\Section $deletedSection, \Nexus\Section $destinationSection)
    {
        foreach ($deletedSection->trashedTopics as $trashedTopic) {
            self::restoreTopicToSection($trashedTopic, $deletedSection);
        };
        $deletedSection->restore();

        $deletedSection->parent_id = $destinationSection->id;
        $deletedSection->save();
    }
}
