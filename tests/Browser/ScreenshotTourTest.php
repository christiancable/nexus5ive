<?php

/**
 * UI Screenshot Tour
 *
 * Produces paired desktop (1280×900) and mobile (390×844) screenshots
 * covering the main user journeys as a normal member with no moderator
 * or admin privileges.
 *
 * Run with:
 *   sail php vendor/bin/pest --browser chrome tests/Browser/ScreenshotTourTest.php
 *
 * Output lands in tests/Browser/Screenshots/
 */

use App\Helpers\ViewHelper;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Comment;
use App\Models\Mention;
use App\Models\Mode;
use App\Models\Post;
use App\Models\Section;
use App\Models\Theme;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;

// Take the same screenshot at desktop and mobile widths.
// Mobile is always viewport-only (false) because the fixed-bottom nav bar
// is composited at every scroll step in full-page mode, placing it mid-page.
function snap(mixed $page, string $name): void
{
    $page->resize(1280, 900)->screenshot(false, $name . '-desktop');
    // Reset scroll so the mobile capture always starts from the top of the page.
    $page->resize(390, 844);
    $page->script('window.scrollTo(0, 0)');
    $page->wait(0.1)->screenshot(false, $name . '-mobile');
}

beforeEach(function () {
    // Content authors / section moderators — never the user taking screenshots
    $this->alice = User::factory()->create([
        'name' => 'Alice Liddell',
        'username' => 'alice',
        'popname' => 'Down The Rabbit Hole',
        'about' => 'Long-time member. Literature, philosophy, and the occasional tea party.',
        'location' => 'Wonderland',
        'favouriteMovie' => 'Pan\'s Labyrinth',
        'favouriteMusic' => 'Kate Bush, Joanna Newsom',
    ]);

    $this->bob = User::factory()->create([
        'name' => 'Bob Hoskins',
        'username' => 'bob',
        'popname' => 'It\'s Good To Talk',
        'about' => 'Film fan and amateur chef.',
        'location' => 'London',
        'favouriteMovie' => 'The Long Good Friday',
        'favouriteMusic' => 'The Clash',
    ]);

    // The person whose perspective all screenshots are taken from.
    // Plain member — no moderator role anywhere.
    $this->viewer = User::factory()->create([
        'name' => 'Charlie Bucket',
        'username' => 'charlie',
        'popname' => 'Golden Ticket',
        'about' => 'New member, big fan of films and cooking.',
        'location' => 'Loompaland (formerly)',
        'password' => Hash::make('password'),
    ]);

    // Section tree — moderated by alice and bob, not viewer
    $this->home = Section::factory()->for($this->alice, 'moderator')->create([
        'parent_id' => null,
        'title' => 'The Lounge',
        'intro' => 'The main hub. All conversations start here.',
    ]);

    $this->subSection = Section::factory()
        ->for($this->bob, 'moderator')
        ->for($this->home, 'parent')
        ->create([
            'title' => 'General Chat',
            'intro' => 'Anything and everything. No rules except be kind.',
        ]);

    $this->techSection = Section::factory()
        ->for($this->alice, 'moderator')
        ->for($this->home, 'parent')
        ->create([
            'title' => 'Tech Talk',
            'intro' => 'Gadgets, code, and the internet of things.',
        ]);

    // A topic with rich posts — the viewer is subscribed here with older read progress
    $this->topic = Topic::factory()
        ->for($this->subSection, 'section')
        ->create([
            'title' => 'Welcome to Nexus — introduce yourself!',
            'intro' => 'Tell us who you are and what brings you here.',
        ]);

    // Posts visible to the viewer (written before their last-read time)
    $this->richPost = Post::factory()
        ->for($this->topic)
        ->for($this->alice, 'author')
        ->create([
            'title' => 'Hello everyone — Alice here!',
            'text' => implode("\n\n", [
                "Delighted to be here. I've been lurking for a while but finally decided to post.",
                "A few things about me:\n\n- Big fan of **Lewis Carroll** and surreal fiction\n- Amateur photographer (film, not digital)\n- I make a very good Victoria sponge",
                "> \"Curiouser and curiouser!\" — Alice's Adventures in Wonderland",
                "Here's a picture that sums up my general outlook on life:",
                "![A curious cat](https://upload.wikimedia.org/wikipedia/commons/thumb/1/1e/Sleeping_cat_on_her_back.jpg/640px-Sleeping_cat_on_her_back.jpg)",
                "If you want to chat, feel free to drop me a [private message](/chat/).",
            ]),
            'popname' => 'Down The Rabbit Hole',
            'time' => now()->subHours(5),
        ]);

    Post::factory()
        ->for($this->topic)
        ->for($this->bob, 'author')
        ->create([
            'title' => 'Re: Hello everyone!',
            'text' => implode("\n\n", [
                "@alice — wonderful to meet you! I've been here for years and the community is brilliant.",
                "> Big fan of Lewis Carroll and surreal fiction",
                "Same! Have you read _The Annotated Alice_ by Martin Gardner? Absolutely worth it.",
                "Also — Victoria sponge _or_ coffee cake? That's the real question.",
            ]),
            'popname' => 'It\'s Good To Talk',
            'time' => now()->subHours(4),
        ]);

    // Subscribe viewer and mark them as having read up to 3 hours ago
    ViewHelper::subscribeToTopic($this->viewer, $this->topic);
    $view = \App\Models\View::where('topic_id', $this->topic->id)
        ->where('user_id', $this->viewer->id)
        ->first();
    if ($view) {
        $view->latest_view_date = now()->subHours(3);
        $view->save();
    }

    // Unread post — posted after the viewer's last-read time
    $this->unreadPost = Post::factory()
        ->for($this->topic)
        ->for($this->alice, 'author')
        ->create([
            'title' => 'Coffee cake, obviously',
            'text' => "Charlie! Welcome aboard. Coffee cake wins every time. Fight me.\n\n- Chandler Bing would agree\n- Joey would eat both",
            'popname' => 'Down The Rabbit Hole',
            'time' => now()->subHour(),
        ]);

    // A second topic with an unread post so Latest view shows more activity
    $this->techTopic = Topic::factory()
        ->for($this->techSection, 'section')
        ->create([
            'title' => 'Favourite tools and apps of 2025',
            'intro' => 'Share the software that\'s made your year.',
        ]);

    ViewHelper::subscribeToTopic($this->viewer, $this->techTopic);
    $techView = \App\Models\View::where('topic_id', $this->techTopic->id)
        ->where('user_id', $this->viewer->id)
        ->first();
    if ($techView) {
        $techView->latest_view_date = now()->subDays(2);
        $techView->save();
    }

    Post::factory()
        ->for($this->techTopic)
        ->for($this->bob, 'author')
        ->create([
            'title' => 'Still using Obsidian daily',
            'text' => implode("\n\n", [
                "Two years in and I still open **Obsidian** before anything else in the morning.",
                "My setup:\n\n- Daily notes with a template\n- Dataview for tracking reading progress\n- Canvas for planning larger projects",
                "Honourable mentions: _Raycast_, _Tot_, and the humble `grep` command.",
            ]),
            'popname' => 'It\'s Good To Talk',
            'time' => now()->subDay(),
        ]);

    // Profile comments on alice and bob so their profiles look lived-in
    Comment::factory()->create([
        'user_id' => $this->alice->id,
        'author_id' => $this->bob->id,
        'text' => 'Coffee cake. Always coffee cake.',
    ]);

    Comment::factory()->create([
        'user_id' => $this->alice->id,
        'author_id' => $this->viewer->id,
        'text' => 'Love the photography work, Alice!',
    ]);

    Comment::factory()->create([
        'user_id' => $this->bob->id,
        'author_id' => $this->alice->id,
        'text' => 'Great to finally see you posting! Long overdue.',
    ]);

    // A private conversation between viewer and alice with several messages
    $this->chat = Chat::create([
        'owner_id' => $this->viewer->id,
        'partner_id' => $this->alice->id,
        'is_read' => true,
    ]);

    foreach ([
        [$this->alice->id,  "Hi Charlie! Saw your intro post — welcome aboard. How did you find us?"],
        [$this->viewer->id, "Thanks Alice! A friend mentioned it. Been lurking for a while before I plucked up the courage to post."],
        [$this->alice->id,  "Ha, we all do that. The community is really lovely once you get into it. Are you into films as well as cooking?"],
        [$this->viewer->id, "Both! Though my cooking is much better than my film taste. I'll watch anything with a decent soundtrack."],
        [$this->alice->id,  "That's the right approach. You'd get on well with Bob — he's the film expert around here."],
        [$this->viewer->id, "I'll drop him a message. Thanks for the intro 😊"],
    ] as [$sender, $text]) {
        ChatMessage::create([
            'chat_id'     => $this->chat->id,
            'sender_id'   => $sender,
            'message_text' => $text,
        ]);
    }

    // -----------------------------------------------------------------------
    // Notification state — three pending notifications for the viewer:
    //   1. An unread profile comment (comment.read = false)
    //   2. An @mention in a post
    //   3. An incoming unread chat from alice (alice owns, viewer is partner)
    // -----------------------------------------------------------------------

    // 1. Unread comment on the viewer's own profile
    Comment::factory()->create([
        'user_id'   => $this->viewer->id,
        'author_id' => $this->alice->id,
        'text'      => 'Great first post Charlie — love the bit about soundtracks!',
        'read'      => false,
    ]);

    // 2. @mention — alice mentions charlie in a post
    $mentionPost = Post::factory()
        ->for($this->topic)
        ->for($this->alice, 'author')
        ->create([
            'title' => 'Speaking of films…',
            'text'  => "@charlie — you'd love this one. Have you seen _Spirited Away_?",
            'popname' => 'Down The Rabbit Hole',
            'time'  => now()->subMinutes(10),
        ]);

    $mention = new Mention;
    $mention->user_id = $this->viewer->id;
    $mention->post_id = $mentionPost->id;
    $mention->save();

    // 3. Incoming unread chat — alice sent a follow-up message
    $incomingChat = Chat::create([
        'owner_id'  => $this->alice->id,
        'partner_id' => $this->viewer->id,
        'is_read'   => false,
    ]);

    foreach ([
        [$this->alice->id,  "PS — Bob says hi too. You'll fit right in here 😊"],
        [$this->alice->id,  "Also: there's a film night thread in Tech Talk you might enjoy."],
    ] as [$sender, $text]) {
        ChatMessage::create([
            'chat_id'      => $incomingChat->id,
            'sender_id'    => $sender,
            'message_text' => $text,
        ]);
    }

    // Active BBS mode — provides the welcome text shown on the login screen.
    // The AddBBSModeToView middleware caches this as 'bbs_mode', so we flush
    // the cache entry after creating it to ensure the middleware picks it up.
    $theme = Theme::firstOrFail();
    Mode::create([
        'name'     => 'Default',
        'theme_id' => $theme->id,
        'active'   => true,
        'override' => false,
        'welcome'  => implode("\n\n", [
            "## Welcome to Nexus",
            "Nexus has been running since 2001, which makes it positively ancient by internet standards. We're a small, friendly community of long-term members.",
            "**New here?** Introduce yourself in [General Chat](/section/latest/) and say hello.",
            "> _\"The internet is not something you just dump something on. It's not a big truck.\"_ — Ted Stevens",
            "To join, ask an existing member to send you an invite, or contact the admin at the address below.",
        ]),
    ]);
    Cache::forget('bbs_mode');
});

test('01 login page', function () {
    $page = visit('/login');
    snap($page, '01-login-page');
});

test('02 home section listing', function () {
    actingAs($this->viewer);

    $page = visit('/');
    snap($page, '02-home-section-listing');
});

test('03 sub-section with topics', function () {
    actingAs($this->viewer);

    $page = visit('/section/' . $this->subSection->id);
    snap($page, '03-section-with-topics');
});

test('04 reading a topic with posts', function () {
    actingAs($this->viewer);

    $page = visit('/topic/' . $this->topic->id)->resize(1280, 900);
    $page->screenshot(true, '04-reading-a-topic-desktop');
    $page->resize(390, 844);
    $page->script('window.scrollTo(0, 0)');
    $page->wait(0.1)->screenshot(false, '04-reading-a-topic-mobile');
});

test('05 post compose form', function () {
    actingAs($this->viewer);

    $page = visit('/topic/' . $this->topic->id)->resize(1280, 900);
    $page->script('window.scrollTo(0, document.body.scrollHeight)');
    $page->wait(0.3)->screenshot(false, '05-post-compose-form-desktop');

    $page->resize(390, 844);
    $page->script('window.scrollTo(0, document.body.scrollHeight)');
    $page->wait(0.3)->screenshot(false, '05-post-compose-form-mobile');
});

test('06 formatting help popover', function () {
    actingAs($this->viewer);

    // Popover appears on md+ screens only
    $page = visit('/topic/' . $this->topic->id)->resize(1280, 900);
    $page->script('window.scrollTo(0, document.body.scrollHeight)');
    $page->wait(0.3)
        ->click('a[data-bs-toggle="popover"]')
        ->wait(0.5)
        ->screenshot(false, '06-formatting-help-popover-desktop');

    // On mobile the help is in a collapsible instead — open it
    $page->resize(390, 844);
    $page->script('window.scrollTo(0, document.body.scrollHeight)');
    $page->wait(0.3)
        ->click('a[data-bs-toggle="collapse"]')
        ->wait(0.5)
        ->screenshot(false, '06-formatting-help-collapse-mobile');
});

test('07 post preview tab', function () {
    actingAs($this->viewer);

    $page = visit('/topic/' . $this->topic->id)
        ->resize(1280, 900)
        ->type('[wire\\:model="title"]', 'Just joined — hello!')
        ->type('[wire\\:model="text"]', "Hi everyone! So glad to be here.\n\n> Coffee cake, obviously\n\nHa! I'll bring biscuits.\n\nAlso: **bold**, _italic_, and here's a link [to my favourite site](https://example.com).")
        ->click('#profile-tab')
        ->wait(0.6);
    $page->script('window.scrollTo(0, document.body.scrollHeight)');
    $page->wait(0.2)->screenshot(false, '07-post-preview-desktop');

    $page->resize(390, 844);
    $page->script('window.scrollTo(0, document.body.scrollHeight)');
    $page->wait(0.2)->screenshot(false, '07-post-preview-mobile');
});

test('08 editing an existing post', function () {
    // Edit controls appear inline for the most recent post within 300 s
    $freshPost = Post::factory()
        ->for($this->topic)
        ->for($this->viewer, 'author')
        ->create([
            'title' => 'Just joined — hello!',
            'text' => "Hi everyone! So glad to be here. Quick typo to fix…",
            'popname' => 'Golden Ticket',
            'time' => now(),
        ]);

    actingAs($this->viewer);

    $page = visit('/topic/' . $this->topic->id)->resize(1280, 900);
    $page->screenshot(true, '08-editing-a-post-desktop');
    $page->resize(390, 844);
    $page->script('window.scrollTo(0, 0)');
    $page->wait(0.1)->screenshot(false, '08-editing-a-post-mobile');
});

test('09 user profile — another member', function () {
    actingAs($this->viewer);

    $page = visit('/users/' . $this->alice->username)->resize(1280, 900);
    $page->screenshot(true, '09-user-profile-desktop');
    $page->resize(390, 844);
    $page->script('window.scrollTo(0, 0)');
    $page->wait(0.1)->screenshot(false, '09-user-profile-mobile');
});

test('10 adding a comment on a user profile', function () {
    actingAs($this->viewer);

    $page = visit('/users/' . $this->alice->username)
        ->resize(1280, 900)
        ->type('input[name="text"]', 'Love the cat photo, Alice!');
    snap($page, '10-adding-profile-comment');
});

test('11 own profile page', function () {
    actingAs($this->viewer);

    $page = visit('/users/' . $this->viewer->username)->resize(1280, 900);
    $page->screenshot(true, '11-own-profile-desktop');
    $page->resize(390, 844);
    $page->script('window.scrollTo(0, 0)');
    $page->wait(0.1)->screenshot(false, '11-own-profile-mobile');
});

test('12 users list', function () {
    actingAs($this->viewer);

    $page = visit('/users/');
    snap($page, '12-users-list');
});

test('13 search interface', function () {
    actingAs($this->viewer);

    $page = visit('/search');
    snap($page, '13-search-interface');
});

test('14 search results for a keyword', function () {
    actingAs($this->viewer);

    $page = visit('/search/' . rawurlencode('coffee cake'));
    snap($page, '14-search-keyword-results');
});

test('15 search results for an exact phrase', function () {
    actingAs($this->viewer);

    $page = visit('/search/' . rawurlencode('"Lewis Carroll"'));
    snap($page, '15-search-phrase-results');
});

test('16 latest unread topics', function () {
    actingAs($this->viewer);

    $page = visit('/section/latest');
    snap($page, '16-latest-unread-topics');
});

test('17 private chat — conversation loaded', function () {
    actingAs($this->viewer);

    // Select alice from the dropdown so the existing conversation is shown
    $page = visit('/chat/')
        ->resize(1280, 900)
        ->select('#usersDropdown', $this->alice->id)
        ->wait(0.8);
    snap($page, '17-chat-with-conversation');
});

test('18 private chat — composing a reply', function () {
    actingAs($this->viewer);

    $page = visit('/chat/')
        ->resize(1280, 900)
        ->select('#usersDropdown', $this->alice->id)
        ->wait(0.8)
        ->type('@chat-input', 'Will do — thanks again for the warm welcome!')
        ->screenshot(false, '18-chat-composing-reply-desktop');

    $page->resize(390, 844);
    $page->script('window.scrollTo(0, 0)');
    $page->wait(0.1)->screenshot(false, '18-chat-composing-reply-mobile');
});

// -----------------------------------------------------------------------
// Notification screenshots
// -----------------------------------------------------------------------

test('19 notifications — toolbar badge and profile dropdown', function () {
    actingAs($this->viewer);

    // Desktop: profile menu dropdown shows comment + chat badges
    $page = visit('/')
        ->resize(1280, 900)
        ->wait(0.5)
        ->click('#profiledropdown')
        ->wait(0.3)
        ->screenshot(false, '19-notifications-profile-dropdown-desktop');

    // Mobile: hamburger has notification badge; open the menu to show it
    $page->resize(390, 844);
    $page->script('window.scrollTo(0, 0)');
    $page->wait(0.1)
        ->click('.navbar-toggler')
        ->wait(0.3)
        ->screenshot(false, '19-notifications-toolbar-mobile');
});

test('20 notifications — mentions bell dropdown', function () {
    actingAs($this->viewer);

    // The bell alert icon only appears when mentions exist
    $page = visit('/')
        ->resize(1280, 900)
        ->wait(0.5)
        ->click('[dusk="mentions-menu-toggle"]')
        ->wait(0.3)
        ->screenshot(false, '20-notifications-mentions-dropdown-desktop');

    // Mobile: open the hamburger menu first so the bell is visible
    $page->resize(390, 844);
    $page->script('window.scrollTo(0, 0)');
    $page->wait(0.1)
        ->click('.navbar-toggler')
        ->wait(0.3)
        ->screenshot(false, '20-notifications-mentions-mobile');
});

test('21 notifications — viewing the profile comment', function () {
    actingAs($this->viewer);

    // Own profile page — unread comment from alice is visible
    $page = visit('/users/' . $this->viewer->username)->resize(1280, 900);
    $page->screenshot(true, '21-notifications-profile-comment-desktop');

    $page->resize(390, 844);
    $page->script('window.scrollTo(0, 0)');
    $page->wait(0.1)->screenshot(false, '21-notifications-profile-comment-mobile');
});

test('22 notifications — viewing an unread chat message', function () {
    actingAs($this->viewer);

    // The chat page — unread message from alice is loaded
    $page = visit('/chat/')
        ->resize(1280, 900)
        ->select('#usersDropdown', $this->alice->id)
        ->wait(0.8);
    snap($page, '22-notifications-unread-chat');
});
