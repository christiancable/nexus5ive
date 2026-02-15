<?php

namespace Tests\Unit\Nexus2;

use App\Nexus2\NxText;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class NxTextTest extends TestCase
{
    #[DataProvider('stripHighlightsProvider')]
    public function testStripHighlights(string $input, string $expected): void
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
    public function testToConsole(string $input, string $expected): void
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
                '<fg=yellow>H</>' . 'ello',
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

    public function testUnclosedBraceClosedAtEndOfLine(): void
    {
        $input = "{unclosed on line one\nsecond line";
        $result = NxText::toConsole($input);

        $this->assertEquals(
            "<fg=yellow>unclosed on line one</>\nsecond line",
            $result
        );
    }

    public function testMultilineEachLineIndependent(): void
    {
        $input = "{highlighted line\nnormal line\n{another highlight}";
        $result = NxText::toConsole($input);

        $lines = explode("\n", $result);
        $this->assertEquals('<fg=yellow>highlighted line</>', $lines[0]);
        $this->assertEquals('normal line', $lines[1]);
        $this->assertEquals('<fg=yellow>another highlight</>', $lines[2]);
    }

    public function testStripHighlightsMultilineUnclosed(): void
    {
        $input = "{open line\nnext line";
        $result = NxText::stripHighlights($input);

        $this->assertEquals("open line\nnext line", $result);
    }
}
