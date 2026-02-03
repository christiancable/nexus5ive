<?php

namespace Tests\Browser;

use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class ChatNotificationTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user1;

    protected $user2;

    protected $user3;

    protected $user4;

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
        $this->user3 = User::factory()->create();
        $this->user4 = User::factory()->create();
    }

    /**
     * SLOW (~12s): Multiple visit/click/waitFor sequences with assertMissing (implicit wait).
     */
    #[Test]
    #[Group('slow')]
    public function testUserCanSeeChatNotifications(): void
    {
        $this->browse(function ($browser) {
            $messageText = 'Hello, how are you?';

            $browser->loginAs($this->user1)
                // WHEN a user has no new chats
                ->visit('/')
                // THEN they see no chat notifications
                ->click('#profiledropdown')
                ->waitFor('@profile-menu')
                ->assertMissing('@chat-notification-count')
                ->assertMissing('@chat-notifications');

            // WHEN the user is sent messages
            \App\Helpers\ChatHelper::sendMessage($this->user2->id, $this->user1->id, 'Hello from user 2');
            \App\Helpers\ChatHelper::sendMessage($this->user3->id, $this->user1->id, 'Hello from user 3');
            \App\Helpers\ChatHelper::sendMessage($this->user4->id, $this->user1->id, 'Hello from user 4');

            $browser->visit('/')
            // THEN they see notifications for each message and a count
                ->click('#profiledropdown')
                ->waitFor('@profile-menu')
                ->assertPresent('@chat-notification-count')
                ->assertSeeIn('@chat-notification-count', '3') // 3 messages
                ->assertPresent('@chat-notifications')
                ->assertSeeIn('@chat-notifications', $this->user2->username)
                ->assertSeeIn('@chat-notifications', $this->user3->username)
                ->assertSeeIn('@chat-notifications', $this->user4->username);

            // WHEN the user visits the chat for a message
            $browser->visit('/chat/'.$this->user2->username)
                ->click('#profiledropdown')
                ->waitFor('@profile-menu')
                ->assertPresent('@chat-notification-count')
                // THEN the chat notification count is reducted by one
                ->assertSeeIn('@chat-notification-count', '2') // 2 messages
                ->assertPresent('@chat-notifications')

                // AND the notification for that chat is removed
                ->assertDontSeeIn('@chat-notifications', $this->user2->username)

                // BUT the other notifications remain
                ->assertSeeIn('@chat-notifications', $this->user3->username)
                ->assertSeeIn('@chat-notifications', $this->user4->username);
        });
    }
}
