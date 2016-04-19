<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nexus\Helpers\NxCodeHelper;

class NxCodeHelperTest extends TestCase
{
    
    private $youTubeHTMLStart = <<< 'HTML'
<div class="video-wrapper">
      <iframe id="youtube-player" src="
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
          $expectedOutput = "{$this->youTubeHTMLStart}//www.youtube.com/embed/dQw4w9WgXcQ{$this->youTubeHTMLStop}",
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
}
