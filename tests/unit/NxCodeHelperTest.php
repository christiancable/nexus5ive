<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Helpers\NxCodeHelper;
use App\Helpers\MarkdownHelper;

class NxCodeHelperTest extends BrowserKitTestCase
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
        return [
        'blank text' => [
          $input = '',
          $expectedOutput = '',
        ],

        'single valid youtube tag' => [
          $input = '[youtube-]https://www.youtube.com/watch?v=dQw4w9WgXcQ[-youtube]',
          $expectedOutput = "{$this->youTubeHTMLStart}dQw4w9WgXcQ{$this->youTubeHTMLStop}",
        ],

        'text with 2 valid youtube tags' => [
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
        ],

        'youtube tag with no content' => [
          $input = '[youtube-][-youtube]',
          $expectedOutput = '',
        ],

        'youtube tag with invalid content' => [
          $input = '[youtube-]https://vimeo.com/87031388[-youtube]',
          $expectedOutput = '',
          ],

        'Red Hot Chili Peppers - Give It Away - ID with an underscore' => [
          $input = '[youtube-]https://youtu.be/Mr_uHJPUlO8[-youtube]',
         $expectedOutput = <<< HTML
{$this->youTubeHTMLStart}Mr_uHJPUlO8{$this->youTubeHTMLStop}
HTML
          ,
          ],
        ];
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
        return [
            'blank text' => [
                $input = '',
                $expectedOutput = '',
            ],
            'external link' => [
                $input = '[a link](http://example.com)',
                $expectedOutput = '<p><a href="http://example.com" target="_blank">a link</a></p>',
            ],
            'internal link' => [
                $input = '[a link](/users)',
                $expectedOutput = '<p><a href="/users">a link</a></p>',
            ],
            'inline internal link' => [
                $input = 'http://example.com',
                $expectedOutput = '<p><a href="http://example.com" target="_blank">http://example.com</a></p>',
            ],
        ];
    }


    /**
     * test custom markdown extensions
     *
     * @dataProvider providerNxCodeSpoilers
     */
    public function testNxCodeSpoiler($input, $expectedOutput)
    {
        $output = NxCodeHelper::spoilerTags($input);
        $this->assertEquals($output, $expectedOutput);
    }

    public function providerNxCodeSpoilers()
    {
        return [
            'spoiler tag' => [
                $input = 'Oh my [spoiler-]Brad Pitt is Edward Norton![-spoiler]',
                $expectedOutput = 'Oh my <span class="spoiler">Brad Pitt is Edward Norton!</span>',
            ],
            'multiple spoiler tags' => [
                $input = 'Oh my [spoiler-]Brad Pitt is Edward Norton![-spoiler] and [spoiler-]it was Earth all along[-spoiler]',
                $expectedOutput = 'Oh my <span class="spoiler">Brad Pitt is Edward Norton!</span> and <span class="spoiler">it was Earth all along</span>',
            ],
        ];
    }

    /**
     * test addition of lazy load class
     *
     * @dataProvider providerNxCodeLazyLoad
     */
    public function testNxLazyLoad($input, $expectedOutput)
    {
        $output = NxCodeHelper::lazyLoadClass($input);
        $this->assertEquals($output, $expectedOutput);
    }

    public function providerNxCodeLazyLoad()
    {
        return [
            'img tag' => [
                $input = '<img src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank" />',
                $expectedOutput = '<img src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank"  class="b-lazy"/>',
            ],
            'multiple img tags' => [
                $input = '<img src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank"/> and then this happened <img src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank"/>',
                $expectedOutput = '<img src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank" class="b-lazy"/> and then this happened <img src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank" class="b-lazy"/>',
            ],
        ];
    }
}
