<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Settings;
use App\Models\Mode;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regularUser;

    private Theme $theme;

    private Mode $mode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->forTheme()->create(['administrator' => true]);
        $this->regularUser = User::factory()->forTheme()->create();

        $this->theme = Theme::first() ?? Theme::factory()->create();
        $this->mode = Mode::factory()->create([
            'theme_id' => $this->theme->id,
            'active' => true,
            'welcome' => 'Welcome to the BBS',
            'override' => false,
        ]);
    }

    #[Test]
    public function admin_can_render_settings_component(): void
    {
        Livewire::actingAs($this->admin)
            ->test(Settings::class)
            ->assertOk();
    }

    #[Test]
    public function admin_can_save_mode_settings(): void
    {
        Livewire::actingAs($this->admin)
            ->test(Settings::class)
            ->set('welcome', 'Updated welcome message')
            ->set('override', true)
            ->set('selectedTheme', $this->theme->id)
            ->call('save')
            ->assertRedirect(route('theme.index'));

        $this->assertDatabaseHas('modes', [
            'id' => $this->mode->id,
            'welcome' => 'Updated welcome message',
        ]);
    }

    #[Test]
    public function admin_can_set_bbs_mode(): void
    {
        $inactiveMode = Mode::factory()->create([
            'theme_id' => $this->theme->id,
            'active' => false,
        ]);

        Livewire::actingAs($this->admin)
            ->test(Settings::class)
            ->set('selectedMode', $inactiveMode->id)
            ->call('changeCurrentMode')
            ->call('setBBSMode')
            ->assertRedirect(route('theme.index'));

        $this->assertDatabaseHas('modes', ['id' => $inactiveMode->id, 'active' => true]);
    }

    #[Test]
    public function non_admin_cannot_save_settings(): void
    {
        Livewire::actingAs($this->regularUser)
            ->test(Settings::class)
            ->set('welcome', 'Hacked')
            ->call('save')
            ->assertForbidden();
    }

    #[Test]
    public function non_admin_cannot_set_bbs_mode(): void
    {
        Livewire::actingAs($this->regularUser)
            ->test(Settings::class)
            ->call('setBBSMode')
            ->assertForbidden();
    }
}
