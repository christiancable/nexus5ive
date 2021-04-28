<?php

Namespace Tests\Unit\Nexus2;

use Tests\TestCase;
use App\Nexus2\Helpers\Highlighter;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

// phpcs:disable Generic.Files.LineLength
class HighlighterTest extends TestCase
{
    

    public function testHightLighterReplacesAtWithSingleBold()
    {
        $input = 'H@ello';
        $expectedOutput = 'H**e**llo';
        $highlighter = new Highlighter();
        $output = $highlighter->highlight($input);

        $this->assertEquals($output, $expectedOutput);
    }

    public function testHightlighterReplacesCurlyBraces()
    {
        $input = 'Hello how {are} you';
        $expectedOutput = 'Hello how **are** you';
        $highlighter = new Highlighter();
        $output = $highlighter->highlight($input);

        $this->assertEquals($output, $expectedOutput);
    }

    public function testHighligherHighlightsRestOfLineForUnclosedBraces()
    {
        $input = 'Hello how {are you';
        $expectedOutput = 'Hello how **are you**';
        $highlighter = new Highlighter();
        $output = $highlighter->highlight($input);

        $this->assertEquals($output, $expectedOutput);
    }

    public function testHighligherHighlightsMultipleBraces()
    {
        $input = 'Hello {how} are {you}';
        $expectedOutput = 'Hello **how** are **you**';
        $highlighter = new Highlighter();
        $output = $highlighter->highlight($input);

        $this->assertEquals($output, $expectedOutput);
    }
}