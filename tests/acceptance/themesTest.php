<?php
// @codingStandardsIgnoreFile
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Section;

class ThemesTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function a_new_user_has_the_default_theme() {
        // given we have a default theme
        $theme = App\Theme::FirstOrFail();

        // when we create a new user
        $user = factory(User::class)->create();

        // the new user has the default theme
        $this->assertEquals($user->theme->id, $theme->id);
    }

    /**
     * @test
     */
    public function a_user_can_see_which_theme_they_use() {
        // given we have a default theme
        $theme = App\Theme::FirstOrFail();

        // and we have a user
        $user = factory(User::class)->create();

        // we need a default section to visit the profile page
        factory(Section::class)
            ->create([
                'parent_id' => null,
                'user_id' => $user->id,
            ]);

        // when the user views their profile
        // the user can see which theme they have   
        $this->actingAs($user)
            ->visitRoute('users.show', $user->username)
            ->see($theme->name);
    }

    /**
     * @test
     */
    public function a_user_can_change_their_theme() {
        // given we have a user
        // when the user views their profile
        // and they select a different theme
        // then their theme changes in the database
    }
}
