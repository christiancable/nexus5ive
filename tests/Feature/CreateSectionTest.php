<?php

namespace Tests\Feature;

use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateSectionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function moderatorCanCreateNewSubsection(): void
    {
        /*
        GIVEN we have
        - a home section
        - s sysop
        - a sub section
        - a moderator for the sub section
        */
        $sysop = User::factory()->forTheme()->create();
        $home = Section::factory()->for($sysop, 'moderator')->create([
            'parent_id' => null,
        ]);

        $moderator = User::factory()->forTheme()->create();
        $section = Section::factory()
            ->for($moderator, 'moderator')
            ->for($home, 'parent')
            ->create();

        /*
        WHEN
        - the moderator creates a new section within the sub section
        */
        $newSection = Section::factory()
            ->for($section, 'parent')
            ->make();

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

    #[Test]
    public function userCannotCreateSubsection(): void
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
        $home = Section::factory()->for($sysop, 'moderator')->create([
            'parent_id' => null,
        ]);

        $moderator = User::factory()->create();
        $section = Section::factory()
            ->for($moderator, 'moderator')
            ->for($home, 'parent')
            ->create();

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

    #[Test]
    public function moderatorCannotCreateInvalidSubsection(): void
    {
        /*
        GIVEN we have
        - a home section
        - s sysop
        - a sub section
        - a moderator for the sub section
        */
        $sysop = User::factory()->create();
        $home = Section::factory()
            ->for($sysop, 'moderator')
            ->create(['parent_id' => null]);

        $moderator = User::factory()->create();
        $section = Section::factory()
            ->for($moderator, 'moderator')
            ->for($home, 'parent')
            ->create();

        /*
        WHEN
        - the moderator creates a new section within the sub section
        */

        $newSection = Section::factory()
            ->make([
                'parent_id' => 'madeupnumber',
                'title' => null,
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
