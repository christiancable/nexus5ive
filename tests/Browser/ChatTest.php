<?php

namespace Tests\Browser;

use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class ChatTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user1;

    protected $user2;

    protected function setUp(): void
    {
        parent::setUp();

        // set up bbs with a sysop and main menu
        $sysop = User::factory()->create();
        $home = Section::factory()->create([
            'parent_id' => null,
            'user_id' => $sysop->id,
        ]);

        // add users for testing
        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();
    }

    /**
     * SLOW (~11s): Multiple Livewire interactions with waitFor, pause(200), and assertions.
     */
    #[Test]
    #[Group('slow')]
    public function testUserCanMessageAnotherUser(): void
    {
        $user1 = $this->user1;
        $user2 = $this->user2;

        $this->browse(function ($browser) use ($user1, $user2) {
            $messageText = 'Hello, how are you?';

            $browser->loginAs($user1)
                ->visit('/chat/')
                // // WHEN a user sends a message to another user
                ->select('#usersDropdown', $user2->id)
                ->waitFor('@chat-input')
                ->type('@chat-input', $messageText)
                ->press('@chat-send-button')
                ->pause(200)
                // THEN the message is seen in the chat window
                ->with('@chat-messages', function ($browseMessages) use ($messageText) {
                    $browseMessages->assertSee($messageText);
                })
                // AND a chat between the users is seen in the chat list
                ->with('@chat-list', function ($browseList) use ($user2) {
                    $browseList->assertSee($user2->username);
                })->logout();
        });
    }
}
