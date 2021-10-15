<?php

namespace Tests\Feature;

use App\Mode;
use App\User;
use App\Section;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // we need a home section for the breadcrumbs
        $owner = User::factory()->create();
        Section::factory()->create([
            'parent_id' => null,
            'user_id' => $owner->id,
        ]);

        // we need at least one mode
        $defaultMode = Mode::factory()->create();
    }

    /**
     * Only administrators can access the admin section
     *
     * @test
     * @group mode
     */
    public function sysopsCanAccessAdminSection()
    {
        $sysop = User::factory()->create(['administrator' => true]);
        $this->actingAs($sysop)->get('/admin')->assertSuccessful();
    }

    /**
     * Non-administrators cannot access the admin section
     *
     * @test
     * @group mode
     */
    public function nonSysopsCannotAccessAdminSection()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin')->assertStatus(403);
    }
}
