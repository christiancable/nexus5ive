<?php

namespace Tests\Unit\Nexus2;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Section;
use App\Models\Theme;
use App\Models\Topic;
use App\Models\User;
use App\Nexus2\Importer;
use App\Nexus2\Nexus2Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImporterTest extends TestCase
{
    use RefreshDatabase;

    private string $fixturesPath;

    private string $bbsDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixturesPath = __DIR__.'/fixtures';
        $this->bbsDir = sys_get_temp_dir().'/nexus2_import_test_'.uniqid();

        // Disable model events during tests to avoid cache/tree rebuilds
        $models = [User::class, Section::class, Topic::class, Post::class, Comment::class];
        foreach ($models as $model) {
            $model::unsetEventDispatcher();
        }

        if (! Theme::find(1)) {
            Theme::factory()->create(['id' => 1]);
        }

        $this->buildFixtureBbs();
    }

    protected function tearDown(): void
    {
        // Re-enable events
        $models = [User::class, Section::class, Topic::class, Post::class, Comment::class];
        foreach ($models as $model) {
            $model::setEventDispatcher(app('events'));
        }

        $this->removeDirectory($this->bbsDir);
        parent::tearDown();
    }

    public function test_imports_users_from_udb(): void
    {
        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir);

        $importer->importUsers($this->bbsDir.'/USR');

        $user = User::where('email', 't.user1@legacy.nexus2')->first();
        $this->assertNotNull($user);
        $this->assertEquals('TestUser', $user->username);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals(42, $user->totalVisits);
        $this->assertEquals(1234, $user->totalPosts);
        $this->assertFalse($user->administrator);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_imports_sysop_as_administrator(): void
    {
        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir);

        $importer->importUsers($this->bbsDir.'/USR');

        $sysop = User::where('email', 's.admin1@legacy.nexus2')->first();
        $this->assertNotNull($sysop);
        $this->assertTrue($sysop->administrator);
    }

    public function test_popname_strips_highlights(): void
    {
        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir);

        $importer->importUsers($this->bbsDir.'/USR');

        $sysop = User::where('email', 's.admin1@legacy.nexus2')->first();
        $this->assertEquals('The Boss', $sysop->popname);
    }

    public function test_imports_info_txt_as_about(): void
    {
        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir);

        $importer->importUsers($this->bbsDir.'/USR');

        $user = User::where('email', 't.user1@legacy.nexus2')->first();
        $this->assertEquals('Hello I am a test user', $user->about);
    }

    public function test_username_conflict_appends_suffix(): void
    {
        User::factory()->create(['username' => 'TestUser']);

        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir);

        $importer->importUsers($this->bbsDir.'/USR');

        $imported = User::where('username', 'TestUser_legacy')->first();
        $this->assertNotNull($imported);
        $this->assertEquals('t.user1@legacy.nexus2', $imported->email);
    }

    public function test_merge_existing_users_maps_to_existing_account(): void
    {
        $existing = User::factory()->create(['username' => 'TestUser']);

        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir, mergeExistingUsers: true);

        $importer->importUsers($this->bbsDir.'/USR');
        $importer->importSections();

        // No _legacy account should be created
        $this->assertNull(User::where('username', 'TestUser_legacy')->first());

        // Posts from the legacy data should be assigned to the existing user
        $posts = Post::where('user_id', $existing->id)->get();
        $this->assertGreaterThan(0, $posts->count());
    }

    public function test_merge_existing_users_associates_comments(): void
    {
        $existing = User::factory()->create(['username' => 'SysopNick']);

        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir, mergeExistingUsers: true);

        $importer->importUsers($this->bbsDir.'/USR');
        $importer->importComments($this->bbsDir.'/USR');

        // Comments authored by SysopNick should point to the existing account
        $comments = Comment::where('author_id', $existing->id)->get();
        $this->assertGreaterThan(0, $comments->count());
    }

    public function test_idempotency_skips_duplicate_users(): void
    {
        $command = $this->createMockCommand();
        $importer1 = new Importer($command, $this->bbsDir);
        $importer1->importUsers($this->bbsDir.'/USR');

        $countAfterFirst = User::count();

        $importer2 = new Importer($command, $this->bbsDir);
        $importer2->importUsers($this->bbsDir.'/USR');

        $this->assertEquals($countAfterFirst, User::count());
    }

    public function test_imports_sections_from_mnu(): void
    {
        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir);

        $importer->importUsers($this->bbsDir.'/USR');
        $importer->importSections();

        $main = Section::where('title', 'Main Menu')->first();
        $this->assertNotNull($main);
        $this->assertNull($main->parent_id);

        $sub = Section::where('title', 'Sub Section')->first();
        $this->assertNotNull($sub);
        $this->assertEquals($main->id, $sub->parent_id);
    }

    public function test_imports_topics_and_posts_from_articles(): void
    {
        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir);

        $importer->importUsers($this->bbsDir.'/USR');
        $importer->importSections();

        $topic = Topic::where('title', 'Whats On')->first();
        $this->assertNotNull($topic);

        $posts = Post::where('topic_id', $topic->id)->get();
        $this->assertCount(2, $posts);

        $first = $posts->first();
        $this->assertEquals('Hello World', $first->title);
        $this->assertStringContains('This is the first post', $first->text);
    }

    public function test_priv_filters_high_read_article(): void
    {
        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir, privLevel: 100);

        $importer->importUsers($this->bbsDir.'/USR');
        $importer->importSections();

        // 'Sysop Only' has read=255, should not be imported at priv=100
        $this->assertNull(Topic::where('title', 'Sysop Only')->first());

        // 'Whats On' has read=0, should still be imported
        $this->assertNotNull(Topic::where('title', 'Whats On')->first());
    }

    public function test_priv_filters_high_read_folder_and_its_contents(): void
    {
        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir, privLevel: 100);

        $importer->importUsers($this->bbsDir.'/USR');
        $importer->importSections();

        // 'Private Section' is linked via a read=128 folder — should not be imported at priv=100
        $this->assertNull(Section::where('title', 'Private Section')->first());

        // Its topic should also be absent
        $this->assertNull(Topic::where('title', 'Secret Topic')->first());

        // Normal sub section (read=0 folder) should still be imported
        $this->assertNotNull(Section::where('title', 'Sub Section')->first());
    }

    public function test_text_only_article_becomes_readonly_topic_with_single_post(): void
    {
        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir);

        $importer->importUsers($this->bbsDir.'/USR');
        $importer->importSections();

        $topic = Topic::where('title', 'Info Page')->first();
        $this->assertNotNull($topic);
        $this->assertTrue((bool) $topic->readonly);
        $this->assertEquals('', $topic->intro);

        $posts = Post::where('topic_id', $topic->id)->get();
        $this->assertCount(1, $posts);
        $this->assertStringContains('Welcome to this section', $posts->first()->text);
    }

    public function test_preamble_imported_as_first_post_not_topic_intro(): void
    {
        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir);

        $importer->importUsers($this->bbsDir.'/USR');
        $importer->importSections();

        $topic = Topic::where('title', 'With Preamble')->first();
        $this->assertNotNull($topic);

        // Preamble must not be stored in intro
        $this->assertEquals('', $topic->intro);

        $posts = Post::where('topic_id', $topic->id)->orderBy('id')->get();

        // Two posts: the synthetic preamble post and the real post
        $this->assertCount(2, $posts);

        // First post is the preamble, attributed to SysOp at epoch+1
        $preamblePost = $posts->first();
        $this->assertStringContainsString('This is the preamble text', $preamblePost->text);
        $this->assertEquals(1, $preamblePost->time->timestamp);
        $sysop = User::where('username', 'SysOp')->first();
        $this->assertEquals($sysop->id, $preamblePost->user_id);

        // Second post is the real post with its original author and timestamp
        $realPost = $posts->last();
        $this->assertEquals('A Real Post', $realPost->title);
        $this->assertStringContainsString('This is actual post content', $realPost->text);
    }

    public function test_creates_placeholder_users_for_unknown_nicks(): void
    {
        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir);

        $importer->importUsers($this->bbsDir.'/USR');
        $importer->importSections();

        // The article fixture has a post from "UnknownNick" not in any UDB
        $placeholder = User::where('username', 'UnknownNick')->first();
        $this->assertNotNull($placeholder);
        $this->assertTrue(Nexus2Import::exists('user', 'user:placeholder:unknownnick'));
    }

    public function test_imports_comments(): void
    {
        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir);

        $importer->importUsers($this->bbsDir.'/USR');
        $importer->importComments($this->bbsDir.'/USR');

        $comments = Comment::all();
        $this->assertCount(2, $comments);

        // Comments should be in chronological order (file is reversed)
        $first = $comments->first();
        $this->assertEquals('nice to meet you', $first->text);
        $this->assertTrue($first->read);
    }

    public function test_section_owner_resolves_from_nick_map(): void
    {
        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir);

        $importer->importUsers($this->bbsDir.'/USR');
        $importer->importSections();

        $main = Section::where('title', 'Main Menu')->first();
        $testUser = User::where('email', 't.user1@legacy.nexus2')->first();

        $this->assertEquals($testUser->id, $main->user_id);
    }

    public function test_idempotency_skips_duplicate_sections_and_topics(): void
    {
        $command = $this->createMockCommand();

        $importer1 = new Importer($command, $this->bbsDir);
        $importer1->importUsers($this->bbsDir.'/USR');
        $importer1->importSections();

        $sectionCount = Section::count();
        $topicCount = Topic::count();
        $postCount = Post::count();

        $importer2 = new Importer($command, $this->bbsDir);
        $importer2->importUsers($this->bbsDir.'/USR');
        $importer2->importSections();

        $this->assertEquals($sectionCount, Section::count());
        $this->assertEquals($topicCount, Topic::count());
        $this->assertEquals($postCount, Post::count());
    }

    public function test_dry_run_creates_no_records(): void
    {
        $command = $this->createMockCommand();
        $importer = new Importer($command, $this->bbsDir, dryRun: true);

        $importer->importAll();

        $this->assertEquals(0, User::count());
        $this->assertEquals(0, Section::count());
        $this->assertEquals(0, Topic::count());
        $this->assertEquals(0, Post::count());
        $this->assertEquals(0, Nexus2Import::count());
    }

    // --- Helper methods ---

    private function createMockCommand(): \Illuminate\Console\Command
    {
        $command = $this->createMock(\Illuminate\Console\Command::class);
        $command->method('info')->willReturn(null);
        $command->method('line')->willReturn(null);
        $command->method('warn')->willReturn(null);
        $command->method('error')->willReturn(null);

        return $command;
    }

    private function buildFixtureBbs(): void
    {
        // Create BBS directory structure
        mkdir($this->bbsDir.'/USR/6', 0755, true);
        mkdir($this->bbsDir.'/USR/7', 0755, true);
        mkdir($this->bbsDir.'/SECTIONS/MENUS', 0755, true);
        mkdir($this->bbsDir.'/SECTIONS/SUB', 0755, true);
        mkdir($this->bbsDir.'/SECTIONS/PRIVSUB', 0755, true);
        mkdir($this->bbsDir.'/ONSTUFF', 0755, true);

        // Copy UDB fixtures
        copy($this->fixturesPath.'/test_user.udb', $this->bbsDir.'/USR/6/NEXUS.UDB');
        copy($this->fixturesPath.'/test_sysop.udb', $this->bbsDir.'/USR/7/NEXUS.UDB');

        // Create INFO.TXT for user 6
        file_put_contents($this->bbsDir.'/USR/6/INFO.TXT', 'Hello I am a test user');

        // Create COMMENTS.TXT for user 6 (newest-first like real data)
        file_put_contents($this->bbsDir.'/USR/6/COMMENTS.TXT',
            "{SysopNick} : welcome aboard\n{TestUser} : nice to meet you");

        // Create NEXUS.INI
        file_put_contents($this->bbsDir.'/ONSTUFF/NEXUS.INI',
            "MainMenu \\SECTIONS\\MENUS\\MAIN.MNU\n");

        // Create main menu
        // - sysoponly has read=255 (above default priv of 100, article should be filtered)
        // - privsubfolder has read=128 (above default priv of 100, entire submenu should be filtered)
        file_put_contents($this->bbsDir.'/SECTIONS/MENUS/MAIN.MNU', implode("\n", [
            '.owner TestUser',
            'H Main Menu',
            'a 0 100 o whatson * Whats On',
            'a 255 255 s sysoponly * Sysop Only',
            'f 0 s \SECTIONS\SUB\SUB.MNU * Sub Section',
            'f 128 p \SECTIONS\PRIVSUB\PRIVSUB.MNU * Private Section',
        ]));

        // Create sub menu
        file_put_contents($this->bbsDir.'/SECTIONS/SUB/SUB.MNU', implode("\n", [
            '.owner SysopNick',
            'H Sub Section',
            'a 0 100 d discuss * Discussion',
            'a 0 100 o infopage * Info Page',
            'a 0 100 w withpreamble * With Preamble',
        ]));

        // Create article files with ESC markers
        $esc = "\x1b";
        file_put_contents($this->bbsDir.'/SECTIONS/MENUS/WHATSON', implode("\n", [
            "{$esc}\x01Mon Jun 02 14:13:11 1997",
            "{$esc}\x02A witty tagline) TestUser",
            "{$esc}\x03Hello World",
            'This is the first post',
            '',
            "{$esc}\x01Tue Jun 03 10:00:00 1997",
            "{$esc}\x02Mystery Person) UnknownNick",
            "{$esc}\x03Second Post",
            'This is from someone not in any UDB',
        ]));

        file_put_contents($this->bbsDir.'/SECTIONS/SUB/DISCUSS', implode("\n", [
            "{$esc}\x01Wed Jun 04 12:00:00 1997",
            "{$esc}\x02The Boss) SysopNick",
            "{$esc}\x03Admin Note",
            'This is an admin post',
        ]));

        // Text-only article — preamble but no ESC-delimited posts
        file_put_contents($this->bbsDir.'/SECTIONS/SUB/INFOPAGE',
            "Welcome to this section.\nPlease read the rules.");

        // Article with both a preamble and posts
        file_put_contents($this->bbsDir.'/SECTIONS/SUB/WITHPREAMBLE', implode("\n", [
            'This is the preamble text.',
            "{$esc}\x01Fri Jun 06 08:00:00 1997",
            "{$esc}\x02Friendly Person) TestUser",
            "{$esc}\x03A Real Post",
            'This is actual post content.',
        ]));

        // Private submenu — only reachable via the read=128 folder item
        file_put_contents($this->bbsDir.'/SECTIONS/PRIVSUB/PRIVSUB.MNU', implode("\n", [
            '.owner SysopNick',
            'H Private Section',
            'a 0 100 s secret * Secret Topic',
        ]));

        file_put_contents($this->bbsDir.'/SECTIONS/PRIVSUB/SECRET', implode("\n", [
            "{$esc}\x01Thu Jun 05 09:00:00 1997",
            "{$esc}\x02The Boss) SysopNick",
            "{$esc}\x03Secret Post",
            'This post should not be imported at default priv',
        ]));
    }

    private function removeDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($dir);
    }

    private function assertStringContains(string $needle, string $haystack): void
    {
        $this->assertTrue(
            str_contains($haystack, $needle),
            "Failed asserting that '{$haystack}' contains '{$needle}'"
        );
    }
}
