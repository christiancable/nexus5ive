<?php

namespace Tests\Browser;

use App\Models\Section;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class ThemesTest extends DuskTestCase
{
    use DatabaseMigrations;

    public $defaultTheme;

    public $user;

    public $home;

    public $alternativeTheme;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultTheme = Theme::FirstOrFail();
        $this->alternativeTheme = Theme::factory()->create();
        $this->user = User::factory()->create();
        $this->home = Section::factory()
            ->create(
                [
                    'parent_id' => null,
                    'user_id' => $this->user->id,
                ]
            );
    }

    #[Test]
    public function newUserHasDefaultTheme(): void
    {
        // given we have a default theme
        // and a new user
        // the new user has the default theme
        $this->assertEquals($this->user->theme->id, $this->defaultTheme->id);
    }

    #[Test]
    public function userCanSeeWhichThemeTheyUse(): void
    {

        $user = $this->user;
        $defaultTheme = $this->defaultTheme;

        $this->browse(
            function ($browser) use ($user, $defaultTheme) {
                // when the user views their profile
                // the user can see which theme they have
                $browser->loginAs($user)
                    ->visit('/users/'.$user->username)
                    ->assertSelected('@theme_select', $defaultTheme->id);
            }
        );
    }

    #[Test]
    public function userCanChangeTheme(): void
    {
        // GIVEN we have a user and a default theme
        $user = $this->user;

        // AND we have an alternative theme
        $alternativeTheme = $this->alternativeTheme;

        $this->browse(
            function ($browser) use ($user, $alternativeTheme) {
                // WHEN the user views their profile
                // and they select a different theme
                $browser->loginAs($user)
                    ->visit('/users/'.$user->username)
                    ->assertSelectHasOption('@theme_select', $alternativeTheme->id)
                    ->select('@theme_select', $alternativeTheme->id)
                    ->press('Save Changes')
                    ->visit('/users/'.$user->username)
                    // THEN we see that theme is selected on next visit
                    ->assertSelected('@theme_select', $alternativeTheme->id);
            }
        );
    }
}
