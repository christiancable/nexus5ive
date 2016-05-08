<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nexus\User;
use Nexus\Section;
use Nexus\Topic;

class SectionTest extends TestCase
{
    use DatabaseTransactions;
        
    public function test_deleting_section_soft_deletes_section_and_only_that_one()
    {
        $user = factory(User::class, 1)->create();

        // GIVEN we have a main menu with a subsection
        $mainmenu = factory(Section::class, 1)
            ->create([
                'parent_id' => null,
                'user_id' => $user->id,
                ]);
        $section = factory(Section::class, 1)
            ->create([
                'parent_id' => $mainmenu->id,
                'user_id' => $user->id,
                ]);
        
        // AND some other sections
        factory(Section::class, 10)
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
    
    
    public function test_deleting_section_soft_deletes_its_topics()
    {
        // GIVEN we have a user
        $user = factory(User::class, 1)->create();
        
        // AND we have a section
        $section = factory(Section::class, 1)
            ->create([
                'parent_id' => null,
                'user_id' => $user->id,
                ]);

        // AND that section has topics
        factory(Topic::class, 10)->create(['section_id' => $section->id]);
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

    public function test_deleting_section_soft_deletes_its_subsections()
    {
        // given we have a user with a section and that sub section
         $user = factory(User::class, 1)->create();
        
        // AND we have a section
        $section = factory(Section::class, 1)
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
