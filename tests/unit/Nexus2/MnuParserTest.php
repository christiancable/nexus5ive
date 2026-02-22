<?php

namespace Tests\Unit\Nexus2;

use App\Nexus2\MnuParser;
use RuntimeException;
use Tests\TestCase;

class MnuParserTest extends TestCase
{
    private string $fixturesPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixturesPath = __DIR__.'/fixtures';
    }

    public function testParseReturnsExpectedStructure(): void
    {
        $parser = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $data = $parser->parse();

        $this->assertArrayHasKey('header', $data);
        $this->assertArrayHasKey('owners', $data);
        $this->assertArrayHasKey('directives', $data);
        $this->assertArrayHasKey('items', $data);
    }

    public function testParsesHeader(): void
    {
        $parser = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $data = $parser->parse();

        $this->assertEquals('@Test @Menu', $data['header']);
    }

    public function testParsesOwners(): void
    {
        $parser = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $data = $parser->parse();

        $this->assertEquals(['fraggle', 'valis'], $data['owners']);
    }

    public function testParsesDirectives(): void
    {
        $parser = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $data = $parser->parse();

        $commands = array_column($data['directives'], 'command');
        // .if / .else / .endif are control flow — they are not stored in directives
        $this->assertNotContains('if', $commands);
        $this->assertNotContains('endif', $commands);
        // Other directives (e.g. .pagebreak) are stored
        $this->assertContains('pagebreak', $commands);
    }

    public function testParsesArticleItems(): void
    {
        $parser = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $data = $parser->parse();

        $articles = array_filter($data['items'], fn ($i) => $i['type'] === 'article');
        $articles = array_values($articles);

        $this->assertGreaterThanOrEqual(3, count($articles));

        // First article: a 0 100 o whatson * Whats On
        $this->assertEquals(0, $articles[0]['read']);
        $this->assertEquals(100, $articles[0]['write']);
        $this->assertEquals('o', $articles[0]['key']);
        $this->assertEquals('whatson', $articles[0]['file']);
        $this->assertEquals('*', $articles[0]['flags']);
        $this->assertEquals('Whats On', $articles[0]['info']);
    }

    public function testParsesArticleWithHigherPrivileges(): void
    {
        $parser = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $data = $parser->parse();

        $articles = array_filter($data['items'], fn ($i) => $i['type'] === 'article');
        $articles = array_values($articles);

        // Third article: a 128 200 d secret *u {Secret File}
        $secret = array_values(array_filter($articles, fn ($a) => $a['file'] === 'secret'));
        $this->assertCount(1, $secret);
        $this->assertEquals(128, $secret[0]['read']);
        $this->assertEquals(200, $secret[0]['write']);
        $this->assertEquals('d', $secret[0]['key']);
        $this->assertEquals('*u', $secret[0]['flags']);
        $this->assertEquals('{Secret File}', $secret[0]['info']);
    }

    public function testParsesFolderItems(): void
    {
        $parser = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $data = $parser->parse();

        $folders = array_filter($data['items'], fn ($i) => $i['type'] === 'folder');
        $folders = array_values($folders);

        $this->assertCount(1, $folders);
        $this->assertEquals(0, $folders[0]['read']);
        $this->assertEquals('b', $folders[0]['key']);
        $this->assertEquals('\sections\sub\sub.mnu', $folders[0]['file']);
        $this->assertEquals('*', $folders[0]['flags']);
        $this->assertEquals('{Sub Menu}', $folders[0]['info']);
    }

    public function testParsesCommentItems(): void
    {
        $parser = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $data = $parser->parse();

        $comments = array_filter($data['items'], fn ($i) => $i['type'] === 'comment');
        $comments = array_values($comments);

        $this->assertCount(1, $comments);
        $this->assertEquals(0, $comments[0]['read']);
        $this->assertEquals('Just a comment', $comments[0]['info']);
    }

    public function testParsesMcommentItems(): void
    {
        $parser = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $data = $parser->parse();

        $mcomments = array_filter($data['items'], fn ($i) => $i['type'] === 'mcomment');
        $mcomments = array_values($mcomments);

        $this->assertGreaterThanOrEqual(1, count($mcomments));
        $this->assertEquals('{Welcome to the test menu}', $mcomments[0]['info']);
    }

    public function testSkipsCommentLines(): void
    {
        $parser = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $data = $parser->parse();

        // The fixture has 3 comment lines (# ; /) — none should appear in items or directives
        $allInfo = array_column($data['items'], 'info');
        foreach ($allInfo as $info) {
            $this->assertStringNotContainsString('this is a comment', $info);
            $this->assertStringNotContainsString('another comment', $info);
            $this->assertStringNotContainsString('yet another comment', $info);
        }
    }

    public function testSkipsBlankLines(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile, "\n\nH Test\n\na 0 100 x test * Info\n\n");

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            $this->assertEquals('Test', $data['header']);
            $this->assertCount(1, $data['items']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testDotPrefixedItemNotTreatedAsDirective(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile, ".a 180 180 d diary *u A dot-prefixed article\n");

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            $this->assertCount(1, $data['items']);
            $this->assertEquals('article', $data['items'][0]['type']);
            $this->assertEquals(180, $data['items'][0]['read']);
            $this->assertEquals(180, $data['items'][0]['write']);
            $this->assertEquals('diary', $data['items'][0]['file']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testDotIfNotTreatedAsInternalItem(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile, ".if user fraggle\na 0 100 x test * Test\n.endif\n");

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            // .if should not produce any 'internal' type items (i.e. treated as an 'i' line)
            $internals = array_filter($data['items'], fn ($i) => $i['type'] === 'internal');
            $this->assertEmpty($internals);

            // The item is inside .if user fraggle — condition is false, so it is excluded
            $this->assertCount(0, $data['items']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testNullPrivLevelReturnsAllItems(): void
    {
        $parser = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $data = $parser->parse();

        // Should include the sysop-only item (read=250)
        $sysopItems = array_filter($data['items'], fn ($i) => ($i['read'] ?? 0) === 250);
        $this->assertNotEmpty($sysopItems);
    }

    public function testPrivLevelFiltersHighReadItems(): void
    {
        $parser = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $data = $parser->parse(100);

        // All items should have read <= 100
        foreach ($data['items'] as $item) {
            $this->assertLessThanOrEqual(100, $item['read'],
                "Item '{$item['info']}' has read={$item['read']} which exceeds priv level 100");
        }
    }

    public function testPrivLevelZeroShowsOnlyGuestItems(): void
    {
        $parser = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $data = $parser->parse(0);

        foreach ($data['items'] as $item) {
            $this->assertEquals(0, $item['read'],
                "Item '{$item['info']}' should not be visible at priv level 0");
        }
    }

    public function testPrivLevel255ShowsAllItems(): void
    {
        $parser = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $allData = $parser->parse();

        $parser2 = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $sysopData = $parser2->parse(255);

        $this->assertCount(count($allData['items']), $sysopData['items']);
    }

    public function testPrivLevelFiltersDotPrefixedItems(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile, ".a 180 180 d diary *u Secret diary\na 0 100 x public * Public\n");

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse(100);

            $this->assertCount(1, $data['items']);
            $this->assertEquals('public', $data['items'][0]['file']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testThrowsExceptionForMissingFile(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File not found');

        new MnuParser('/nonexistent/file.mnu');
    }

    // -------------------------------------------------------------------------
    // .if condition tests
    // -------------------------------------------------------------------------

    public function testIfUserConditionExcludesItems(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile, ".if user fraggle\na 0 100 x test * Test\n.endif\n");

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            // Import user is not fraggle — item must be excluded
            $this->assertCount(0, $data['items']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testIfUserListConditionExcludesItems(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile, ".if user alice bob charlie\na 0 100 x test * Test\n.endif\n");

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            // Import user is not in the list
            $this->assertCount(0, $data['items']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testIfSysopConditionExcludesItems(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile, ".if sysop\na 0 100 x test * Test\n.endif\n");

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            $this->assertCount(0, $data['items']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testIfOwnerConditionExcludesItems(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile, ".if owner\na 0 100 x test * Test\n.endif\n");

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            $this->assertCount(0, $data['items']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testIfNotUserIncludesItems(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile, ".if not user fraggle\na 0 100 x test * Test\n.endif\n");

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            // Import user is NOT fraggle — negated condition is true — item included
            $this->assertCount(1, $data['items']);
            $this->assertEquals('test', $data['items'][0]['file']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testIfBangUserIncludesItems(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        // !user with no space between ! and keyword
        file_put_contents($tmpFile, ".if !user fraggle\na 0 100 x test * Test\n.endif\n");

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            $this->assertCount(1, $data['items']);
            $this->assertEquals('test', $data['items'][0]['file']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testIfNotUserListIncludesItems(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile, ".if not user dummy easy_tiger jimi\nf 0 x \\sections\\buffy.mnu * Buffy\n.endif\n");

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            // Visible to everyone except those named users — import user is not in the list
            $this->assertCount(1, $data['items']);
            $this->assertEquals('folder', $data['items'][0]['type']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testIfElseIncludesElseBranch(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile,
            ".if user admin\n".
            "a 0 100 x admin_only * Admin file\n".
            ".else\n".
            "a 0 100 y normal * Normal file\n".
            ".endif\n"
        );

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            // .if user admin is false, so the .else branch executes
            $this->assertCount(1, $data['items']);
            $this->assertEquals('normal', $data['items'][0]['file']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testIfElseExcludesIfBranchWhenConditionFalse(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile,
            ".if user admin\n".
            "a 0 100 x admin_only * Admin file\n".
            ".else\n".
            "a 0 100 y normal * Normal file\n".
            ".endif\n"
        );

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            $files = array_column($data['items'], 'file');
            $this->assertNotContains('admin_only', $files);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testNestedIfBothFalseExcludesItems(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile,
            ".if user outer\n".
            ".if user inner\n".
            "a 0 100 x nested * Nested\n".
            ".endif\n".
            ".endif\n"
        );

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            $this->assertCount(0, $data['items']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testNestedIfOuterFalseExcludesInnerItems(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile,
            ".if user outer\n".
            ".if not user anyone\n".
            "a 0 100 x nested * Nested\n".
            ".endif\n".
            ".endif\n".
            "a 0 100 z outside * Outside\n"
        );

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            // Outer .if user outer = false, so inner is never evaluated
            // Only the item outside both blocks appears
            $this->assertCount(1, $data['items']);
            $this->assertEquals('outside', $data['items'][0]['file']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testIfFolderIsExcluded(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile, ".if user fraggle\nf 0 x \\sections\\sub.mnu * Sub\n.endif\n");

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            $this->assertCount(0, $data['items']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testItemsOutsideIfAreAlwaysIncluded(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile,
            "a 0 100 x before * Before\n".
            ".if user x\n".
            "a 0 100 y hidden * Hidden\n".
            ".endif\n".
            "a 0 100 z after * After\n"
        );

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            $this->assertCount(2, $data['items']);
            $files = array_column($data['items'], 'file');
            $this->assertContains('before', $files);
            $this->assertContains('after', $files);
            $this->assertNotContains('hidden', $files);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testNoscanPatternItemOutsideIfIsVisible(): void
    {
        // Common Nexus 2 pattern: .if inside only sets .noscan, item itself is outside
        $tmpFile = tempnam(sys_get_temp_dir(), 'mnu');
        file_put_contents($tmpFile,
            ".if user dummy snookie\n".
            ".noscan\n".
            ".endif\n".
            "a 0 100 x topic * Public Topic\n".
            ".if user dummy snookie\n".
            ".scan\n".
            ".endif\n"
        );

        try {
            $parser = new MnuParser($tmpFile);
            $data = $parser->parse();

            // The article is outside all .if blocks — it is always visible
            $this->assertCount(1, $data['items']);
            $this->assertEquals('topic', $data['items'][0]['file']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testIfConditionInFixtureExcludesHiddenItem(): void
    {
        // The main fixture has .if user fraggle valis wrapping the 'hidden' article
        $parser = new MnuParser($this->fixturesPath.'/test_menu.mnu');
        $data = $parser->parse();

        $files = array_column($data['items'], 'file');
        $this->assertNotContains('hidden', $files);
    }
}
