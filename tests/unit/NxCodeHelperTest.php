<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nexus\Helpers\NxCodeHelper;
use Nexus\Helpers\MarkdownHelper;

class NxCodeHelperTest extends TestCase
{
    
    private $youTubeHTMLStart = <<< 'HTML'
<div class="video-wrapper">
      <iframe id="youtube-player" src="//www.youtube.com/embed/
HTML;

    private $youTubeHTMLStop = <<< 'HTML'
?rel=0&showinfo=0&autohide=1" frameborder="0" allowfullscreen></iframe>
    </div>
HTML;

    /**
     * test to see if [youtube-] [-youtube] tags
     * are replaced with suitable youtube embed HTML
     *
     * @dataProvider providerYouTubeTagsAddEmbedCode
     */
    public function testYouTubeTagsAddEmbedCode($input, $expectedOutput)
    {
        $output = NxCodeHelper::embedYouTube($input);
        $this->assertEquals($output, $expectedOutput);
    }

    public function providerYouTubeTagsAddEmbedCode()
    {
        return array(
        'blank text' => array(
          $input = '',
          $expectedOutput = '',
        ),

        'single valid youtube tag' => array(
          $input = '[youtube-]https://www.youtube.com/watch?v=dQw4w9WgXcQ[-youtube]',
          $expectedOutput = "{$this->youTubeHTMLStart}dQw4w9WgXcQ{$this->youTubeHTMLStop}",
        ),

        'text with 2 valid youtube tags' => array(
          $input = <<< 'HTML'
look here is a video [youtube-]https://www.youtube.com/watch?v=bDOZbvE01Fk[-youtube] and here is another 
[youtube-]https://www.youtube.com/watch?v=dQw4w9WgXcQ[-youtube]
HTML
        ,
          $expectedOutput = <<< HTML
look here is a video {$this->youTubeHTMLStart}bDOZbvE01Fk{$this->youTubeHTMLStop} and here is another 
{$this->youTubeHTMLStart}dQw4w9WgXcQ{$this->youTubeHTMLStop}
HTML
        ,
        ),

        'youtube tag with no content' => array(
          $input = '[youtube-][-youtube]',
          $expectedOutput = '',
        ),

         'youtube tag with invalid content' => array(
            $input = '[youtube-]https://vimeo.com/87031388[-youtube]',
            $expectedOutput = '',
            ),
          );
    }

    /**
     * test custom markdown extensions
     *
     * @dataProvider providerMarkdownExtensions
     */
    public function testMarkdownExtensions($input, $expectedOutput)
    {
        $output = MarkdownHelper::markdown($input);
        $this->assertEquals($output, $expectedOutput);
    }

    public function providerMarkdownExtensions()
    {
        return array(
            'blank text' => array(
                $input = '',
                $expectedOutput = '',
            ),
            'external link' => array(
                $input = '[a link](http://example.com)',
                $expectedOutput = '<p><a href="http://example.com" target="_blank">a link</a></p>',
            ),
            'internal link' => array(
                $input = '[a link](/users)',
                $expectedOutput = '<p><a href="/users">a link</a></p>',
            ),
        );
    }
}
