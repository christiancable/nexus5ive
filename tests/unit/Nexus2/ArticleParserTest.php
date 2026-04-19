<?php

namespace Tests\Unit\Nexus2;

use App\Nexus2\ArticleParser;
use RuntimeException;
use Tests\TestCase;

class ArticleParserTest extends TestCase
{
    private string $fixturesPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixturesPath = __DIR__.'/fixtures';
    }

    public function test_parse_returns_expected_structure(): void
    {
        $parser = new ArticleParser($this->fixturesPath.'/test_article.dat');
        $data = $parser->parse();

        $this->assertArrayHasKey('preamble', $data);
        $this->assertArrayHasKey('posts', $data);
    }

    public function test_parses_preamble(): void
    {
        $parser = new ArticleParser($this->fixturesPath.'/test_article.dat');
        $data = $parser->parse();

        $this->assertStringContainsString('This is the preamble text.', $data['preamble']);
        $this->assertStringContainsString('It can span multiple lines.', $data['preamble']);
    }

    public function test_parses_correct_number_of_posts(): void
    {
        $parser = new ArticleParser($this->fixturesPath.'/test_article.dat');
        $data = $parser->parse();

        $this->assertCount(3, $data['posts']);
    }

    public function test_parses_timestamp(): void
    {
        $parser = new ArticleParser($this->fixturesPath.'/test_article.dat');
        $data = $parser->parse();

        $this->assertEquals('Mon Jun 02 14:13:11 1997', $data['posts'][0]['timestamp']);
        $this->assertEquals('Tue Jun 03 09:00:00 1997', $data['posts'][1]['timestamp']);
        $this->assertEquals('Wed Jun 04 15:30:00 1997', $data['posts'][2]['timestamp']);
    }

    public function test_parses_from_with_popname(): void
    {
        $parser = new ArticleParser($this->fixturesPath.'/test_article.dat');
        $data = $parser->parse();

        $this->assertEquals('Fraggle', $data['posts'][0]['nick']);
        $this->assertEquals('{The cool one}', $data['posts'][0]['popname']);
    }

    public function test_parses_from_without_popname(): void
    {
        $parser = new ArticleParser($this->fixturesPath.'/test_article.dat');
        $data = $parser->parse();

        $this->assertEquals('Anonymous', $data['posts'][1]['nick']);
        $this->assertNull($data['posts'][1]['popname']);
    }

    public function test_parses_from_with_highlight_markup(): void
    {
        $parser = new ArticleParser($this->fixturesPath.'/test_article.dat');
        $data = $parser->parse();

        // Raw markup preserved by parser
        $this->assertEquals('Blew', $data['posts'][2]['nick']);
        $this->assertEquals('@Do@H @Do@H', $data['posts'][2]['popname']);
    }

    public function test_parses_subject(): void
    {
        $parser = new ArticleParser($this->fixturesPath.'/test_article.dat');
        $data = $parser->parse();

        $this->assertEquals('First post subject', $data['posts'][0]['subject']);
        $this->assertNull($data['posts'][1]['subject']);
        $this->assertEquals('{Important} announcement', $data['posts'][2]['subject']);
    }

    public function test_parses_body(): void
    {
        $parser = new ArticleParser($this->fixturesPath.'/test_article.dat');
        $data = $parser->parse();

        $this->assertStringContainsString('This is the body of the first post.', $data['posts'][0]['body']);
        $this->assertStringContainsString('It has multiple lines.', $data['posts'][0]['body']);
    }

    public function test_body_preserves_raw_highlight_markup(): void
    {
        $parser = new ArticleParser($this->fixturesPath.'/test_article.dat');
        $data = $parser->parse();

        $this->assertStringContainsString('@highlighted', $data['posts'][2]['body']);
        $this->assertStringContainsString('{this is a phrase}', $data['posts'][2]['body']);
    }

    public function test_trims_leading_and_trailing_blank_lines_from_body(): void
    {
        $parser = new ArticleParser($this->fixturesPath.'/test_article.dat');
        $data = $parser->parse();

        // Body should not start or end with blank lines
        foreach ($data['posts'] as $post) {
            if ($post['body'] === '') {
                continue;
            }
            $lines = explode("\n", $post['body']);
            $this->assertNotEquals('', trim($lines[0]), 'Body should not start with blank line');
            $this->assertNotEquals('', trim(end($lines)), 'Body should not end with blank line');
        }
    }

    public function test_minimal_article_no_preamble(): void
    {
        $parser = new ArticleParser($this->fixturesPath.'/test_article_minimal.dat');
        $data = $parser->parse();

        $this->assertEquals('', $data['preamble']);
        $this->assertCount(1, $data['posts']);
        $this->assertEquals('TestUser', $data['posts'][0]['nick']);
        $this->assertEquals('Just a nick', $data['posts'][0]['popname']);
        $this->assertEquals('Single post body.', $data['posts'][0]['body']);
    }

    public function test_preamble_only_no_posts(): void
    {
        $parser = new ArticleParser($this->fixturesPath.'/test_article_preamble_only.dat');
        $data = $parser->parse();

        $this->assertStringContainsString('Just some text with no posts.', $data['preamble']);
        $this->assertCount(0, $data['posts']);
    }

    public function test_throws_exception_for_missing_file(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File not found');

        new ArticleParser('/nonexistent/file.dat');
    }
}
