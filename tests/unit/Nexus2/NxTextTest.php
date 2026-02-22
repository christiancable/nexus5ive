<?php

namespace Tests\Unit\Nexus2;

use App\Nexus2\NxText;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class NxTextTest extends TestCase
{
    #[DataProvider('stripHighlightsProvider')]
    public function test_strip_highlights(string $input, string $expected): void
    {
        $this->assertEquals($expected, NxText::stripHighlights($input));
    }

    public static function stripHighlightsProvider(): array
    {
        return [
            'plain text unchanged' => [
                'Hello world',
                'Hello world',
            ],
            'empty string' => [
                '',
                '',
            ],
            'single char highlight with @' => [
                '@Hello',
                'Hello',
            ],
            'multiple @ highlights' => [
                '@Do@H @Do@H',
                'DoH DoH',
            ],
            'phrase highlight with braces' => [
                '{About the BBS}',
                'About the BBS',
            ],
            'mixed @ and brace highlights' => [
                '@General {Noticeboard}',
                'General Noticeboard',
            ],
            'nested text around highlights' => [
                'Welcome to {the BBS} today',
                'Welcome to the BBS today',
            ],
            '@ at end of string is kept' => [
                'trailing@',
                'trailing@',
            ],
            'unclosed brace is stripped' => [
                '{unclosed text',
                'unclosed text',
            ],
            'brace only contains markup chars' => [
                '{}',
                '',
            ],
        ];
    }

    #[DataProvider('toConsoleProvider')]
    public function test_to_console(string $input, string $expected): void
    {
        $this->assertEquals($expected, NxText::toConsole($input));
    }

    public static function toConsoleProvider(): array
    {
        return [
            'plain text unchanged' => [
                'Hello world',
                'Hello world',
            ],
            'single char highlight' => [
                '@Hello',
                '<fg=yellow>H</>'.'ello',
            ],
            'phrase highlight' => [
                '{About the BBS}',
                '<fg=yellow>About the BBS</>',
            ],
            'multiple @ highlights' => [
                '@Do@H',
                '<fg=yellow>D</>o<fg=yellow>H</>',
            ],
        ];
    }

    #[DataProvider('toMarkdownProvider')]
    public function test_to_markdown(string $input, string $expected): void
    {
        $this->assertEquals($expected, NxText::toMarkdown($input));
    }

    public static function toMarkdownProvider(): array
    {
        return [
            'plain text unchanged' => [
                'Hello world',
                'Hello world',
            ],
            'single char highlight with @' => [
                '@Hello',
                '**H**ello',
            ],
            'multiple @ highlights' => [
                '@Do@H',
                '**D**o**H**',
            ],
            'phrase highlight with braces' => [
                '{About the BBS}',
                '**About the BBS**',
            ],
            'mixed @ and brace highlights' => [
                '@General {Noticeboard}',
                '**G**eneral **Noticeboard**',
            ],
            'text around highlights' => [
                'Welcome to {the BBS} today',
                'Welcome to **the BBS** today',
            ],
            'unclosed brace closed at end of line' => [
                '{unclosed text',
                '**unclosed text**',
            ],
        ];
    }

    public function test_unclosed_brace_closed_at_end_of_line(): void
    {
        $input = "{unclosed on line one\nsecond line";
        $result = NxText::toConsole($input);

        $this->assertEquals(
            "<fg=yellow>unclosed on line one</>\nsecond line",
            $result
        );
    }

    public function test_multiline_each_line_independent(): void
    {
        $input = "{highlighted line\nnormal line\n{another highlight}";
        $result = NxText::toConsole($input);

        $lines = explode("\n", $result);
        $this->assertEquals('<fg=yellow>highlighted line</>', $lines[0]);
        $this->assertEquals('normal line', $lines[1]);
        $this->assertEquals('<fg=yellow>another highlight</>', $lines[2]);
    }

    public function test_strip_highlights_multiline_unclosed(): void
    {
        $input = "{open line\nnext line";
        $result = NxText::stripHighlights($input);

        $this->assertEquals("open line\nnext line", $result);
    }
}
