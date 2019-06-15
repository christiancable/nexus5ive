<?php

namespace Tests\Intergration\Models;

use App\User;
use App\Topic;
use App\Section;
use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SectionTest extends BrowserKitTestCase
{
    use RefreshDatabase;
        
    /**
     * @test
     */
    public function deletingSectionSoftDeletesSectionAndOnlyThatOne()
    {
        $user = factory(User::class)->create();

        // GIVEN we have a main menu with a subsection
        $mainmenu = factory(Section::class)
            ->create([
                'parent_id' => null,
                'user_id' => $user->id,
                ]);
        $section = factory(Section::class)
            ->create([
                'parent_id' => $mainmenu->id,
                'user_id' => $user->id,
                ]);
        
        // AND some other sections
        factory(Section::class)
            ->create([
                'parent_id' => $mainmenu->id,
                'user_id' => $user->id,
                ]);
        
        $sectionCount = Section::all()->count();

        // WHEN that particular section is deleted
        $section->delete();
        
        // THEN number of sections goes down by one
        $sectionCountAfterDeletion = Section::all()->count();
        $this->assertEquals($sectionCountAfterDeletion, $sectionCount-1);

        // AND that particular section is soft deleted
        $this->assertTrue($section->trashed());
    }
    
    
    public function deletingSectionSoftDeletesItsTopics()
    {
        // GIVEN we have a user
        $user = factory(User::class)->create();
        
        // AND we have a section
        $section = factory(Section::class)
            ->create([
                'parent_id' => null,
                'user_id' => $user->id,
                ]);

        // AND that section has topics
        factory(Topic::class)->create(['section_id' => $section->id]);
        $topicsInSectionCount = $section->topics->count();
                
        $topicCount = Topic::all()->count();
    
        // WHEN we delete that section
        $section->delete();

        // THEN the total number of topics is reduced by the number of topics
        // belonging to the original section
        $topicCountAfterDeletion = Topic::all()->count();
        $this->assertEquals($topicCount - $topicsInSectionCount, $topicCountAfterDeletion);
        
        // AND the count of topics for that section is now zero
        $this->assertEquals(Topic::where('section_id', $section->id)->count(), 0);
        
        // BUT that section has soft deleted topics with match the orignal count
        $this->assertEquals(Topic::withTrashed()->where('section_id', $section->id)->count(), $topicsInSectionCount);
    }

    public function deletingSectionSoftDeletesItsSubsections()
    {
        // given we have a user with a section and that sub section
         $user = factory(User::class)->create();
        
        // AND we have a section
        $section = factory(Section::class)
            ->create([
                'parent_id' => null,
                'user_id' => $user->id,
                ]);

        // with subsections
        factory(Section::class, 6)
            ->create([
                'parent_id' => $section->id,
                'user_id' => $user->id,
                ]);

        $subsectionCount = Section::where('parent_id', $section->id)->count();

        // when we delete that section
        $section->delete();

        // then section and subsections are soft deleted
        $this->assertTrue($section->trashed());

        // we have no subsections
        $this->assertEquals(Section::where('parent_id', $section->id)->count(), 0);

        // we have the right amount of soft deleted subsections
        $this->assertEquals(Section::withTrashed()->where('parent_id', $section->id)->count(), $subsectionCount);
    }
}
