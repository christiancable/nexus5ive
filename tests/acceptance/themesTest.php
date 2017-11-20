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

    public $defaultTheme;
    public $user;
    public $home;

    public function setUp() {
        parent::setUp();

        $this->defaultTheme = App\Theme::FirstOrFail();
        $this->user = factory(User::class)->create();
        $this->home = factory(Section::class)
            ->create([
                'parent_id' => null,
                'user_id' => $this->user->id,
            ]);
        
    }
    /**
     * @test
     */
    public function a_new_user_has_the_default_theme() {
        // given we have a default theme
        // and a new user
        // the new user has the default theme
        $this->assertEquals($this->user->theme->id, $this->defaultTheme->id);
    }

    /**
     * @test
     */
    public function a_user_can_see_which_theme_they_use() {
        // when the user views their profile
        // the user can see which theme they have   
        $this->actingAs($this->user)
            ->visitRoute('users.show', $this->user->username)
            ->see($this->defaultTheme->url);
    }

    /**
     * @test
     */
    public function a_user_can_change_their_theme() {
        // given we have a user
        // and a default theme 
        // and an alternative theme
        $alternativeTheme = factory(App\Theme::class)->create();

        // when the user views their profile
        // and they select a different theme
        $this->actingAs($this->user)
            ->visitRoute('users.show', $this->user->username)
            ->select($alternativeTheme->id, 'theme_id')
            ->press('Save Changes');

        // see the url to the newly chosen alternative theme in the page
        $this->actingAs($this->user)
            ->visitRoute('users.show', $this->user->username)
            ->see($alternativeTheme->url);

        // reload user to get saved info
        $updatedUser = App\User::findOrFail($this->user->id);
        
        // see the newly chosen alternative theme in their profile in the db
        $this->assertEquals($updatedUser->theme->id, $alternativeTheme->id);
    }
}
