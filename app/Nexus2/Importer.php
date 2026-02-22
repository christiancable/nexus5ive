<?php

namespace App\Nexus2;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Importer
{
    private Command $command;

    private string $bbsDir;

    private bool $dryRun;

    private ?int $parentSectionId;

    private bool $mergeExistingUsers;

    private int $privLevel;

    /** @var array<string, int> lowercase nick => user_id */
    private array $nickMap = [];

    private int $usersMerged = 0;

    private int $usersImported = 0;

    private int $usersSkipped = 0;

    private int $sectionsImported = 0;

    private int $sectionsSkipped = 0;

    private int $topicsImported = 0;

    private int $topicsSkipped = 0;

    private int $postsImported = 0;

    private int $postsSkipped = 0;

    private int $commentsImported = 0;

    private int $commentsSkipped = 0;

    /** @var array<string, true> legacy keys already seen (for dry-run dedup) */
    private array $visited = [];

    private string $currentFile = '';

    public function __construct(Command $command, string $bbsDir, bool $dryRun = false, ?int $parentSectionId = null, bool $mergeExistingUsers = false, int $privLevel = 100)
    {
        $this->command = $command;
        $this->bbsDir = rtrim($bbsDir, '/');
        $this->dryRun = $dryRun;
        $this->parentSectionId = $parentSectionId;
        $this->mergeExistingUsers = $mergeExistingUsers;
        $this->privLevel = $privLevel;
    }

    public function importAll(): void
    {
        $usrDir = $this->bbsDir.'/USR';

        $this->command->info('Phase 1: Importing users...');
        $this->importUsers($usrDir);
        $merged = $this->usersMerged > 0 ? ", {$this->usersMerged} merged" : '';
        $this->command->info("  Users: {$this->usersImported} imported, {$this->usersSkipped} skipped{$merged}");

        $this->command->info('Phase 2: Importing sections, topics, and posts...');
        $this->importSections();
        $this->command->info("  Sections: {$this->sectionsImported} imported, {$this->sectionsSkipped} skipped");
        $this->command->info("  Topics: {$this->topicsImported} imported, {$this->topicsSkipped} skipped");
        $this->command->info("  Posts: {$this->postsImported} imported, {$this->postsSkipped} skipped");

        $this->command->info('Phase 3: Importing comments...');
        $this->importComments($usrDir);
        $this->command->info("  Comments: {$this->commentsImported} imported, {$this->commentsSkipped} skipped");
    }

    public function importUsers(string $usrDir): void
    {
        // Pre-populate nickMap with existing Nexus5ive users
        foreach (User::all(['id', 'username']) as $user) {
            $this->nickMap[strtolower($user->username)] = $user->id;
        }

        $dirs = glob($usrDir.'/[0-9]*', GLOB_ONLYDIR);
        sort($dirs, SORT_NATURAL);

        // Pre-parse all UDB files so we can sort duplicates by LastOn
        $entries = [];
        foreach ($dirs as $dir) {
            $udbPath = $dir.'/NEXUS.UDB';
            if (! file_exists($udbPath)) {
                continue;
            }

            $dirNum = basename($dir);
            $legacyKey = "user:{$dirNum}";

            if (Nexus2Import::exists('user', $legacyKey)) {
                $modelId = Nexus2Import::modelId('user', $legacyKey);
                $existingUser = User::find($modelId);
                if ($existingUser) {
                    $this->nickMap[strtolower($existingUser->username)] = $existingUser->id;
                }
                $this->usersSkipped++;

                continue;
            }

            try {
                $this->currentFile = $udbPath;
                $parser = new UdbParser($udbPath);
                $data = $parser->parse();
            } catch (\RuntimeException $e) {
                $this->command->warn("  Skipping {$udbPath}: {$e->getMessage()}");

                continue;
            }

            $nick = trim($data['Nick']);
            if ($nick === '') {
                continue;
            }

            $entries[] = ['dir' => $dir, 'dirNum' => $dirNum, 'legacyKey' => $legacyKey, 'data' => $data, 'nick' => $nick];
        }

        // Sort so the most recently active account for each nick is processed first
        usort($entries, function ($a, $b) {
            $dateA = $this->parseNexus2Date($a['data']['LastOn']);
            $dateB = $this->parseNexus2Date($b['data']['LastOn']);

            // Nulls sort last
            if (! $dateA && ! $dateB) {
                return 0;
            }
            if (! $dateA) {
                return 1;
            }
            if (! $dateB) {
                return -1;
            }

            return $dateB->timestamp <=> $dateA->timestamp;
        });

        foreach ($entries as $entry) {
            $dir = $entry['dir'];
            $dirNum = $entry['dirNum'];
            $legacyKey = $entry['legacyKey'];
            $data = $entry['data'];
            $nick = $entry['nick'];

            // Check if this nick matches an existing user
            $lower = strtolower($nick);
            $existingUser = isset($this->nickMap[$lower]) ? User::find($this->nickMap[$lower]) : null;

            if ($existingUser && $this->mergeExistingUsers) {
                if ($this->dryRun) {
                    $this->command->line("  [dry-run] Would merge user: {$nick} (dir {$dirNum}) → existing #{$existingUser->id} ({$existingUser->username})");
                } else {
                    Nexus2Import::track('user', $legacyKey, $existingUser->id);
                    $this->command->line("  Merged user: {$nick} → existing #{$existingUser->id} ({$existingUser->username})");
                }
                $this->usersMerged++;

                continue;
            }

            if ($this->dryRun) {
                $this->command->line("  [dry-run] Would import user: {$nick} (dir {$dirNum})");
                $this->nickMap[$lower] = -1;
                $this->usersImported++;

                continue;
            }

            $username = $this->resolveUsername($nick);

            // Read INFO.TXT if present
            $about = null;
            $infoPath = $dir.'/INFO.TXT';
            if (file_exists($infoPath)) {
                $infoContent = trim(file_get_contents($infoPath));
                if ($infoContent !== '') {
                    $about = $this->cleanText($infoContent);
                }
            }

            $popname = $this->cleanText($data['PopName']);
            $realName = trim($data['RealName']) !== '' ? $data['RealName'] : $nick;

            $user = new User;
            $user->username = $username;
            $user->name = $realName;
            $emailStub = trim($data['UserID']) !== '' ? $data['UserID'] : $username;
            $user->email = strtolower($emailStub).'@legacy.nexus2';
            $user->password = Hash::make(Str::random(64));
            $user->email_verified_at = now();
            $user->popname = $popname;
            $user->about = $about;
            $user->totalVisits = (int) $data['TimesOn'];
            $user->totalPosts = (int) $data['TotalEdits'];
            $user->administrator = $data['Rights'] === 255;
            $user->theme_id = 1;
            $user->created_at = $this->parseNexus2Date($data['Created']);
            $user->latestLogin = $this->parseNexus2Date($data['LastOn']);
            $user->save();

            $this->nickMap[strtolower($nick)] = $user->id;
            if (strtolower($username) !== strtolower($nick)) {
                $this->nickMap[strtolower($username)] = $user->id;
            }

            Nexus2Import::track('user', $legacyKey, $user->id);
            $this->usersImported++;
        }
    }

    public function importSections(): void
    {
        $iniPath = $this->bbsDir.'/ONSTUFF/NEXUS.INI';
        $this->currentFile = $iniPath;
        $ini = file_get_contents($iniPath);

        $mainMenuPath = null;
        foreach (preg_split('/\r?\n/', $ini) as $line) {
            if (preg_match('/^MainMenu\s+(.+)/i', trim($line), $m)) {
                $mainMenuPath = $this->resolveBackslashPath(trim($m[1]));

                break;
            }
        }

        if (! $mainMenuPath) {
            $this->command->error('  Could not find MainMenu in NEXUS.INI');

            return;
        }

        $fullPath = $this->bbsDir.'/'.$mainMenuPath;
        $fullPath = $this->findCaseInsensitive($fullPath);

        if (! $fullPath || ! file_exists($fullPath)) {
            $this->command->error("  Main menu file not found: {$mainMenuPath}");

            return;
        }

        $this->importMnu($fullPath, $this->parentSectionId, null);
    }

    public function importMnu(string $mnuPath, ?int $parentId, ?string $referringTitle): void
    {
        $relativePath = $this->relativeLegacyPath($mnuPath);
        $legacyKey = "section:{$relativePath}";

        if (isset($this->visited[$legacyKey]) || Nexus2Import::exists('section', $legacyKey)) {
            if (! isset($this->visited[$legacyKey])) {
                $sectionId = Nexus2Import::modelId('section', $legacyKey);
                $this->visited[$legacyKey] = true;
                $this->sectionsSkipped++;

                // Still need to recurse into children for any new items
                $this->processMenuItems($mnuPath, $sectionId);
            }

            return;
        }

        $this->visited[$legacyKey] = true;

        try {
            $this->currentFile = $mnuPath;
            $parser = new MnuParser($mnuPath);
            $data = $parser->parse($this->privLevel);
        } catch (\RuntimeException $e) {
            $this->command->warn("  Skipping menu {$mnuPath}: {$e->getMessage()}");

            return;
        }

        $title = $this->cleanText($referringTitle)
            ?? $this->cleanText($data['header'])
            ?? pathinfo($mnuPath, PATHINFO_FILENAME);

        $ownerId = $this->resolveOwner($data['owners']);

        if ($this->dryRun) {
            $this->command->line("  [dry-run] Would import section: {$title}");
            $this->sectionsImported++;
            $this->processMenuItems($mnuPath, null);

            return;
        }

        $section = new Section;
        $section->title = $title;
        $section->intro = null;
        $section->user_id = $ownerId;
        $section->parent_id = $parentId;
        $section->weight = $this->sectionsImported;
        $section->allow_user_topics = false;
        $section->save();

        Nexus2Import::track('section', $legacyKey, $section->id);
        $this->sectionsImported++;

        $this->processMenuItems($mnuPath, $section->id);
    }

    private function processMenuItems(string $mnuPath, ?int $sectionId): void
    {
        try {
            $this->currentFile = $mnuPath;
            $parser = new MnuParser($mnuPath);
            $data = $parser->parse($this->privLevel);
        } catch (\RuntimeException $e) {
            return;
        }

        $mnuDir = dirname($mnuPath);
        $itemWeight = 0;

        foreach ($data['items'] as $item) {
            if ($item['type'] === 'folder' && isset($item['file'])) {
                $subPath = $this->resolveRelativePath($mnuDir, $item['file']);
                $subPath = $this->findCaseInsensitive($subPath);

                if ($subPath && file_exists($subPath)) {
                    $this->importMnu($subPath, $sectionId, $item['info'] ?? null);
                } else {
                    $this->command->warn("  Submenu not found: {$item['file']} (from {$mnuPath})");
                }
            } elseif ($item['type'] === 'article' && isset($item['file']) && ($sectionId !== null || $this->dryRun)) {
                // Skip binary/downloadable items (B flag) — they are files for download, not text articles
                if (str_contains(strtoupper($item['flags'] ?? ''), 'B')) {
                    continue;
                }

                $this->importArticle($sectionId ?? 0, $item, $mnuDir, $mnuPath, $itemWeight);
                $itemWeight++;
            }
        }
    }

    public function importArticle(int $sectionId, array $item, string $mnuDir, string $mnuPath, int $weight): void
    {
        $relativeMnu = $this->relativeLegacyPath($mnuPath);
        $fileName = $item['file'];

        // Article files may use backslash paths (absolute from BBS root)
        if (str_contains($fileName, '\\')) {
            $articlePath = $this->bbsDir.'/'.$this->resolveBackslashPath($fileName);
        } else {
            $articlePath = $mnuDir.'/'.$fileName;
        }

        $articlePath = $this->findCaseInsensitive($articlePath);
        if (! $articlePath || ! file_exists($articlePath)) {
            return;
        }

        $topicKey = "topic:{$relativeMnu}:{$item['file']}";

        if (Nexus2Import::exists('topic', $topicKey)) {
            $this->topicsSkipped++;

            return;
        }

        try {
            $this->currentFile = $articlePath;
            $parser = new ArticleParser($articlePath);
            $data = $parser->parse();
        } catch (\RuntimeException $e) {
            $this->command->warn("  Skipping article {$articlePath}: {$e->getMessage()}");

            return;
        }

        $title = $this->cleanText($item['info']) ?? $item['file'];

        $preamble = $this->cleanText($data['preamble'], markdown: true);

        $flags = strtoupper($item['flags'] ?? '');
        $posts = $data['posts'];

        // A text-only article has a preamble but no posts.
        $textOnly = count($posts) === 0 && $preamble !== null;

        if ($this->dryRun) {
            $postCount = ($preamble !== null ? 1 : 0) + count($posts);
            $label = $textOnly ? 'text-only' : "{$postCount} posts";
            $this->command->line("  [dry-run] Would import topic: {$title} ({$label})");
            $this->topicsImported++;
            $this->postsImported += $postCount;

            return;
        }

        $topic = new Topic;
        $topic->title = $title;
        $topic->intro = '';
        $topic->section_id = $sectionId;
        $topic->readonly = $textOnly || str_contains($flags, 'R');
        $topic->secret = str_contains($flags, 'A');
        $topic->weight = $weight;
        $topic->save();

        Nexus2Import::track('topic', $topicKey, $topic->id);
        $this->topicsImported++;

        // Any preamble (whether or not there are also regular posts) becomes a
        // synthetic first post attributed to the SysOp user at Unix epoch+1.
        if ($preamble !== null) {
            $postKey = "post:{$relativeMnu}:{$item['file']}:preamble";

            if (! Nexus2Import::exists('post', $postKey)) {
                $newPost = new Post;
                $newPost->title = null;
                $newPost->text = $preamble;
                $newPost->time = Carbon::createFromTimestamp(1);
                $newPost->popname = null;
                $newPost->html = false;
                $newPost->user_id = $this->getOrCreateSysopUser();
                $newPost->topic_id = $topic->id;
                $newPost->save();

                Nexus2Import::track('post', $postKey, $newPost->id);
                $this->postsImported++;
            }
        }

        if ($textOnly) {
            return;
        }

        foreach ($data['posts'] as $index => $post) {
            $postKey = "post:{$relativeMnu}:{$item['file']}:{$index}";

            if (Nexus2Import::exists('post', $postKey)) {
                $this->postsSkipped++;

                continue;
            }

            $nick = trim($post['nick'] ?? '');
            $userId = $nick !== '' ? $this->getOrCreateUser($nick) : $this->getOrCreateUser('unknown');

            $postTime = $this->parseArticleTimestamp($post['timestamp']) ?? Carbon::createFromTimestamp(1);
            $postPopname = $this->cleanText($post['popname']);
            $postSubject = $this->cleanText($post['subject']);
            $postBody = $this->cleanText($post['body'], markdown: true) ?? '';

            $newPost = new Post;
            $newPost->title = $postSubject;
            $newPost->text = $postBody;
            $newPost->time = $postTime;
            $newPost->popname = $postPopname;
            $newPost->html = false;
            $newPost->user_id = $userId;
            $newPost->topic_id = $topic->id;
            $newPost->save();

            Nexus2Import::track('post', $postKey, $newPost->id);
            $this->postsImported++;
        }
    }

    public function importComments(string $usrDir): void
    {
        $dirs = glob($usrDir.'/[0-9]*', GLOB_ONLYDIR);
        sort($dirs, SORT_NATURAL);

        foreach ($dirs as $dir) {
            $commentsPath = $dir.'/COMMENTS.TXT';
            if (! file_exists($commentsPath)) {
                continue;
            }

            $dirNum = basename($dir);

            // Find the profile owner's user_id from the UDB import
            $profileUserId = Nexus2Import::modelId('user', "user:{$dirNum}");
            if (! $profileUserId && ! $this->dryRun) {
                continue;
            }

            $this->currentFile = $commentsPath;
            $content = trim(file_get_contents($commentsPath));
            if ($content === '') {
                continue;
            }

            // File is newest-first, reverse for chronological order
            $lines = array_reverse(explode("\n", $content));

            foreach ($lines as $lineIndex => $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                $legacyKey = "comment:{$dirNum}:{$lineIndex}";

                if (Nexus2Import::exists('comment', $legacyKey)) {
                    $this->commentsSkipped++;

                    continue;
                }

                // Parse "{Nick} : text" format
                if (! preg_match('/^\{([^}]+)\}\s*:\s*(.*)$/', $line, $m)) {
                    continue;
                }

                $authorNick = trim($m[1]);
                $commentText = trim($m[2]);

                if ($commentText === '') {
                    continue;
                }

                if ($this->dryRun) {
                    $this->commentsImported++;

                    continue;
                }

                $authorId = $this->getOrCreateUser($authorNick);

                $comment = new Comment;
                $comment->user_id = $profileUserId;
                $comment->author_id = $authorId;
                $comment->text = $this->cleanText($commentText) ?? $commentText;
                $comment->read = true;
                $comment->save();

                Nexus2Import::track('comment', $legacyKey, $comment->id);
                $this->commentsImported++;
            }
        }
    }

    public function getOrCreateUser(string $nick): int
    {
        // Strip highlight markup from nicks (article/comment authors may have them)
        $nick = NxText::stripHighlights($nick);
        $nick = str_replace(['{', '}'], '', $nick);
        $nick = trim($nick);
        if ($nick === '') {
            $nick = 'unknown';
        }

        $lower = strtolower($nick);

        if (isset($this->nickMap[$lower])) {
            return $this->nickMap[$lower];
        }

        // When merging, check if an existing Nexus5ive user matches this nick
        if ($this->mergeExistingUsers) {
            $existing = User::whereRaw('LOWER(username) = ?', [$lower])->first();
            if ($existing) {
                $this->nickMap[$lower] = $existing->id;

                return $existing->id;
            }
        }

        // Check if tracked as a placeholder already
        $placeholderKey = "user:placeholder:{$lower}";
        $existingId = Nexus2Import::modelId('user', $placeholderKey);
        if ($existingId) {
            $this->nickMap[$lower] = $existingId;

            return $existingId;
        }

        $username = $this->resolveUsername($nick);

        $user = new User;
        $user->username = $username;
        $user->name = $nick;
        $user->email = strtolower($username).'_placeholder@legacy.nexus2';
        $user->password = Hash::make(Str::random(64));
        $user->email_verified_at = now();
        $user->theme_id = 1;
        $user->save();

        Nexus2Import::track('user', $placeholderKey, $user->id);
        $this->nickMap[$lower] = $user->id;

        return $user->id;
    }

    private function resolveUsername(string $nick): string
    {
        $username = $nick;
        $lower = strtolower($username);

        if (isset($this->nickMap[$lower]) || User::whereRaw('LOWER(username) = ?', [$lower])->exists()) {
            $username = $nick.'_legacy';
        }

        return $username;
    }

    private function resolveOwner(array $owners): int
    {
        foreach ($owners as $nick) {
            if (strtolower($nick) === 'dummy') {
                return $this->getOrCreateSysopUser();
            }

            $lower = strtolower($nick);
            if (isset($this->nickMap[$lower])) {
                return $this->nickMap[$lower];
            }
        }

        // Fallback to first admin user or user id 1
        $admin = User::where('administrator', true)->first();

        return $admin ? $admin->id : 1;
    }

    private function getOrCreateSysopUser(): int
    {
        $key = 'user:sysop';
        $cached = Nexus2Import::modelId('user', $key);
        if ($cached) {
            return $cached;
        }

        $user = User::whereRaw('LOWER(username) = ?', ['sysop'])->first();

        if (! $user) {
            $user = new User;
            $user->username = 'SysOp';
            $user->name = 'System Operator';
            $user->email = 'sysop@legacy.nexus2';
            $user->popname = '++Beep Boop++';
            $user->location = 'Inside the computers';
            $user->about = 'This the BBS system account and not a real person';
            $user->password = Hash::make(Str::random(64));
            $user->email_verified_at = now();
            $user->theme_id = 1;
            $user->save();
        }

        Nexus2Import::track('user', $key, $user->id);

        return $user->id;
    }

    private function resolveBackslashPath(string $path): string
    {
        $path = str_replace('\\', '/', $path);

        return ltrim($path, '/');
    }

    private function resolveRelativePath(string $baseDir, string $filePath): string
    {
        // Absolute paths start with backslash — resolve from BBS root
        if (str_starts_with($filePath, '\\')) {
            return $this->bbsDir.'/'.$this->resolveBackslashPath($filePath);
        }

        // Relative paths may use backslashes as directory separators (DOS)
        $filePath = str_replace('\\', '/', $filePath);

        return $baseDir.'/'.$filePath;
    }

    private function relativeLegacyPath(string $fullPath): string
    {
        $prefix = $this->bbsDir.'/';
        if (str_starts_with($fullPath, $prefix)) {
            return substr($fullPath, strlen($prefix));
        }

        return $fullPath;
    }

    /**
     * Case-insensitive file lookup — Nexus 2 was DOS, filenames may differ in case.
     * Handles multi-component paths by resolving each segment case-insensitively.
     */
    private function findCaseInsensitive(string $path): ?string
    {
        if (file_exists($path)) {
            return $path;
        }

        // Find the longest existing prefix, then resolve remaining segments case-insensitively
        $resolved = $this->bbsDir;
        $remaining = substr($path, strlen($this->bbsDir) + 1);

        if ($remaining === false || $remaining === '') {
            return null;
        }

        $segments = explode('/', $remaining);

        foreach ($segments as $segment) {
            if (! is_dir($resolved)) {
                return null;
            }

            $target = strtolower($segment);
            $found = false;

            foreach (scandir($resolved) as $entry) {
                if (strtolower($entry) === $target) {
                    $resolved .= '/'.$entry;
                    $found = true;

                    break;
                }
            }

            if (! $found) {
                return null;
            }
        }

        return file_exists($resolved) ? $resolved : null;
    }

    /**
     * Parse Nexus 2 UDB date format: "Tue 31/10/75 at 14:12:32"
     */
    private function parseNexus2Date(?string $dateStr): ?Carbon
    {
        if (empty($dateStr)) {
            return null;
        }

        // Strip day name and "at" keyword
        $cleaned = preg_replace('/^\w+\s+/', '', $dateStr);
        $cleaned = str_replace(' at ', ' ', $cleaned);

        try {
            return Carbon::createFromFormat('d/m/y H:i:s', $cleaned);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse article post timestamp: "Mon Jun 02 14:13:11 1997"
     */
    private function parseArticleTimestamp(?string $timestamp): ?Carbon
    {
        if (empty($timestamp)) {
            return null;
        }

        try {
            $date = Carbon::parse($timestamp);

            // MySQL TIMESTAMP max is 2038-01-19 — clamp absurd future dates
            if ($date->year > 2037) {
                return null;
            }

            return $date;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Process Nexus 2 text for storage, returning null if nothing remains.
     *
     * When $markdown is true, highlights are converted to Markdown bold (**text**)
     * rather than stripped — use this for post body text.
     */
    private function cleanText(?string $text, bool $markdown = false): ?string
    {
        if ($text === null || trim($text) === '') {
            return null;
        }

        $text = $markdown ? NxText::toMarkdown($text) : NxText::stripHighlights($text);
        // Convert CP437 (DOS) to UTF-8 — handles box-drawing chars etc.
        if (! mb_check_encoding($text, 'UTF-8')) {
            $converted = @iconv('CP437', 'UTF-8//IGNORE', $text);
            if ($converted !== false) {
                $text = $converted;
            }
        }
        $text = trim($text);

        return $text !== '' ? $text : null;
    }

    public function getCurrentFile(): string
    {
        return $this->currentFile;
    }

    public function getCounts(): array
    {
        return [
            'users_imported' => $this->usersImported,
            'users_skipped' => $this->usersSkipped,
            'users_merged' => $this->usersMerged,
            'sections_imported' => $this->sectionsImported,
            'sections_skipped' => $this->sectionsSkipped,
            'topics_imported' => $this->topicsImported,
            'topics_skipped' => $this->topicsSkipped,
            'posts_imported' => $this->postsImported,
            'posts_skipped' => $this->postsSkipped,
            'comments_imported' => $this->commentsImported,
            'comments_skipped' => $this->commentsSkipped,
        ];
    }
}
