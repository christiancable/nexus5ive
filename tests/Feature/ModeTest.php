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
        $defaultMode = Mode::factory()->forTheme()->create();
    }

    /**
     * Only administrators can access the theme section
     */
    #[Test]
    #[Group('mode')]
    public function sysops_can_access_theme_section(): void
    {
        $sysop = User::factory()->forTheme()->create(['administrator' => true]);
        $this->actingAs($sysop)->get(route('theme.index'))->assertSuccessful();
    }

    /**
     * Non-administrators cannot access the admin section
     */
    #[Test]
    #[Group('mode')]
    public function non_sysops_cannot_access_admin_section(): void
    {
        $user = User::factory()->forTheme()->create();
        $this->actingAs($user)->get(route('theme.index'))->assertStatus(403);
    }
}
