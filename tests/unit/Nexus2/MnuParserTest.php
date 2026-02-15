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
        $this->assertContains('if', $commands);
        $this->assertContains('endif', $commands);
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

            $commands = array_column($data['directives'], 'command');
            $this->assertContains('if', $commands);
            $this->assertContains('endif', $commands);
            $this->assertCount(1, $data['items']);
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
}
