<?php

namespace Tests\Feature;

use App\Models\Mode;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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
     */
    #[Test]
    #[Group('mode')]
    public function sysopsCanAccessAdminSection(): void
    {
        $sysop = User::factory()->create(['administrator' => true]);
        $this->actingAs($sysop)->get('/admin')->assertSuccessful();
    }

    /**
     * Non-administrators cannot access the admin section
     */
    #[Test]
    #[Group('mode')]
    public function nonSysopsCannotAccessAdminSection(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin')->assertStatus(403);
    }
}
