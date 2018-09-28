<?php

namespace Tests\Unit;

use Tests\BrowserKitTestCase;
use App\Helpers\NxCodeHelper;
use App\Helpers\MarkdownHelper;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

// phpcs:disable Generic.Files.LineLength
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
     * test to see if youtube links
     * are replaced with suitable youtube embed HTML
     *
     * @dataProvider providerYouTubeLinksAddEmbedCode
     */
    public function testYouTubeLinksAddEmbedCode($input, $expectedOutput)
    {
        $output = NxCodeHelper::embedYouTube($input);
        $this->assertEquals($output, $expectedOutput);
    }

    public function providerYouTubeLinksAddEmbedCode()
    {
        return [
        'blank text' => [
          $input = '',
          $expectedOutput = '',
        ],

        'single valid youtube link' => [
          $input = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
          $expectedOutput = "{$this->youTubeHTMLStart}dQw4w9WgXcQ{$this->youTubeHTMLStop}",
        ],

        'text with 2 valid youtube links' => [
          $input = <<< 'HTML'
look here is a video https://www.youtube.com/watch?v=bDOZbvE01Fk and here is another 
https://www.youtube.com/watch?v=dQw4w9WgXcQ
HTML
        ,
          $expectedOutput = <<< HTML
look here is a video {$this->youTubeHTMLStart}bDOZbvE01Fk{$this->youTubeHTMLStop} and here is another 
{$this->youTubeHTMLStart}dQw4w9WgXcQ{$this->youTubeHTMLStop}
HTML
        ,
        ],

        'no content' => [
          $input = '',
          $expectedOutput = '',
        ],

        'not a youtube link' => [
          $input = 'this is a video https://vimeo.com/87031388',
          $expectedOutput = 'this is a video https://vimeo.com/87031388',
          ],

        'Red Hot Chili Peppers - Give It Away - ID with an underscore' => [
          $input = 'https://youtu.be/Mr_uHJPUlO8',
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
                $input = <<< TEXT
Oh my [spoiler-]Brad Pitt is Edward Norton![-spoiler] and [spoiler-]it was Earth all along[-spoiler]
TEXT
            ,
                $expectedOutput = <<< HTML
Oh my <span class="spoiler">Brad Pitt is Edward Norton!</span> and <span class="spoiler">it was Earth all along</span>
HTML
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
        $output = NxCodeHelper::lazyLoadClass($input, 'placeholder.jpg');
        $this->assertEquals($output, $expectedOutput);
    }

    public function providerNxCodeLazyLoad()
    {
        return [
            'img tag' => [
                $input = '<img src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank" />',
                $expectedOutput = <<< HTML
<img class="b-lazy" src="placeholder.jpg" data-src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank" />
HTML
            ],
            'multiple img tags' => [
                $input = <<< HTML
<img src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank"/> and then this happened <img src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank"/>
HTML
                ,
                $expectedOutput = <<< HTML
<img class="b-lazy" src="placeholder.jpg" data-src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank"/> and then this happened <img class="b-lazy" src="placeholder.jpg" data-src="http://imageshack.com/a/img923/5082/NdPfqk.png" alt="image" target="_blank"/>
HTML
            ],
        ];
    }
}
