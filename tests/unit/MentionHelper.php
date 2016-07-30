<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Nexus\Helpers\MentionHelper;

class MentionHelperTest extends TestCase
{
    
    /**
     *
     * @dataProvider providerdentifyMentionedUsersFindsUsernames
     **/
    public function testIdentifyMentionsFindsUsernames($input, $expectedOutput)
    {
        $output = MentionHelper::identifyMentions($input);
        $this->assertEquals($output, $expectedOutput);
    }

    public function providerdentifyMentionedUsersFindsUsernames()
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
}
