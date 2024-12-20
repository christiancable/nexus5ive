<?php

namespace App\Helpers;

use App\Models\Topic;
use App\Models\Section;

class RestoreHelper
{
    /**
     * restores a topic, along with its posts and views, to a section
     */
    public static function restoreTopicToSection(Topic $topic, Section $section)
    {
        $topic->posts()->restore();
        $topic->views()->restore();
        $topic->restore();

        $topic->section_id = $section->id;
        $topic->save();
    }

    public static function restoreSectionToSection(Section $deletedSection, Section $destinationSection)
    {
        foreach ($deletedSection->trashedTopics as $trashedTopic) {
            self::restoreTopicToSection($trashedTopic, $deletedSection);
        }
        $deletedSection->restore();

        $deletedSection->parent_id = $destinationSection->id;
        $deletedSection->save();
    }
}
