<?php

namespace Tests\Browser;

use App\User;
use App\Theme;
use App\Section;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ThemesTest extends DuskTestCase
{
    use DatabaseMigrations;

    public $defaultTheme;
    public $user;
    public $home;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultTheme = Theme::FirstOrFail();
        $this->user = User::factory()->create();
        $this->home = Section::factory()
        ->create([
            'parent_id' => null,
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * @test
     */
    public function newUserHasDefaultTheme()
    {
        // given we have a default theme
        // and a new user
        // the new user has the default theme
        $this->assertEquals($this->user->theme->id, $this->defaultTheme->id);
    }

     /**
     * @test
     */
    public function userCanSeeWhichThemeTheyUse()
    {

        $user = $this->user;
        $defaultTheme = $this->defaultTheme;

        $this->browse(function ($browser) use ($user, $defaultTheme) {
            // when the user views their profile
            // the user can see which theme they have
            $browser->loginAs($user)
                ->visit('/users/' . $user->username)
                ->assertSee($defaultTheme->name);
        });
    }

    /**
     * @test
     */
    public function userCanChangeTheme()
    {
        // GIVEN we have a user and a default theme
        $user = $this->user;

        // AND we have an alternative theme
        $alternativeTheme = Theme::factory()->create();

        $this->browse(function ($browser) use ($user, $alternativeTheme) {
            // WHEN the user views their profile
            // and they select a different theme
            $browser->loginAs($user)
                ->visit('/users/' . $user->username)
                ->select('theme_id', $alternativeTheme->name)
                ->press('Save Changes');


            // THEN they can see they have the alternative theme selected
            $browser->loginAs($user)
                ->visit('/users/' . $user->username)
                ->assertSee($alternativeTheme->ucname);
        });
    }
}
