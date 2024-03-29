<?php

namespace Tests\Feature;

use App\User;
use App\Section;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateSectionTest extends TestCase
{
    use RefreshDatabase;

    /**
    * @test
    */
    public function moderatorCanCreateNewSubsection()
    {
        /*
        GIVEN we have
        - a home section
        - s sysop
        - a sub section
        - a moderator for the sub section
        */
        $sysop = User::factory()->create();
        $home = Section::factory()->create(['parent_id' => null]);
        $home->moderator()->associate($sysop);
        $home->save();

        $moderator = User::factory()->create();
        $section = Section::factory()->create();
        $section->parent()->associate($home);
        $section->moderator()->associate($moderator);
        $section->save();

        /*
        WHEN
        - the moderator creates a new section within the sub section
        */

        $newSection = Section::factory()->make(['parent_id' => $section->id]);

        $this->actingAs($moderator);
        $response = $this->post('/section', $newSection->toArray());

        /*
        THEN
        - we have no errors
        - we are redirected to the new section
        - the new section contains the title and into
        */
        $response->assertSessionMissing('errors');
        $response->assertStatus(302);
        $this->get($response->getTargetUrl())
        ->assertSee($newSection->intro)
        ->assertSee($newSection->title);
    }

    /**
    * @test
    */
    public function userCannotCreateSubsection()
    {
        /*
        GIVEN we have
        - a home section
        - s sysop
        - a sub section
        - a moderator for the sub section
        - a user who is not a moderator
        */
        $sysop = User::factory()->create();
        $home = Section::factory()->create(['parent_id' => null]);
        $home->moderator()->associate($sysop);
        $home->save();

        $moderator = User::factory()->create();
        $section = Section::factory()->create();
        $section->parent()->associate($home);
        $section->moderator()->associate($moderator);
        $section->save();

        $user = User::factory()->create();

        /*
        WHEN
        - a normal user tries to create a new section within the sub section
        */
        $newSection = Section::factory()->make(['parent_id' => $section->id]);

        $this->actingAs($user);
        $response = $this->post('/section', $newSection->toArray());

        /*
        THEN
        - an user is not authorized
        */
        $response->assertStatus(403);
    }

    /**
    * @test
    */
    public function moderatorCannotCreateInvalidSubsection()
    {
         /*
        GIVEN we have
        - a home section
        - s sysop
        - a sub section
        - a moderator for the sub section
        */
        $sysop = User::factory()->create();
        $home = Section::factory()->create(['parent_id' => null]);
        $home->moderator()->associate($sysop);
        $home->save();

        $moderator = User::factory()->create();
        $section = Section::factory()->create();
        $section->parent()->associate($home);
        $section->moderator()->associate($moderator);
        $section->save();

        /*
        WHEN
        - the moderator creates a new section within the sub section
        */

        $newSection = Section::factory()
            ->make([
                'parent_id' => 'madeupnumber',
                'title' => null
            ]);

        $this->actingAs($moderator);
        $response = $this->post('/section', $newSection->toArray());

        /*
        THEN
        - we have errors about the parent_id and title
        - we are redirected back
        */
        $response->assertSessionHas('errors');
        $response->assertSessionHasErrorsIn('sectionCreate', ['title', 'parent_id']);
        $response->assertStatus(302);
    }
}
