<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nexus\Helpers\MentionHelper;

class MentionHelperTest extends TestCase
{
    
    /**
     *
     * @dataProvider provideridentifyMentionedUsersFindsUsernames
     **/
    public function testIdentifyMentionsFindsUsernames($input, $expectedOutput)
    {
        $output = MentionHelper::identifyMentions($input);
        $this->assertEquals($output, $expectedOutput);
    }

    public function provideridentifyMentionedUsersFindsUsernames()
    {
        return array(
            'blank post' => array(
                $input = '',
                $expectedOutput = array(),
            ),
            'single mention' => array(
                $input = 'hey @christiancable how are you?',
                $expectedOutput = array('christiancable'),
            ),
            'multiple mentions' => array(
                $input = 'hey @christiancable have you seen @AgentOrange',
                $expectedOutput = array('christiancable', 'AgentOrange'),
            ),
        );
    }
    
    /**
    *
    * @dataProvider providerHighlightMentionsHighlights
    **/
    public function testHighlightMentionsHighlights($input, $expectedOutput)
    {
        $output = MentionHelper::highlightMentions($input);
        $this->assertEquals($output, $expectedOutput);
    }

    public function providerHighlightMentionsHighlights()
    {
        return array(
            'blank post' => array(
                $input = '',
                $expectedOutput = '',
            ),
            'single mention' => array(
                $input = 'hey @christiancable how are you?',
                $expectedOutput = 'hey <span class="text-muted">@</span><mark><strong>christiancable</strong></mark> how are you?',
            ),
            'multiple mentions' => array(
                $input = 'hey @christiancable have you seen @AgentOrange',
                $expectedOutput = 'hey <span class="text-muted">@</span><mark><strong>christiancable</strong></mark> have you seen <span class="text-muted">@</span><mark><strong>AgentOrange</strong></mark>',
            ),
        );
    }

}
