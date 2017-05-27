<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Helpers\MentionHelper;

class MentionHelperTest extends TestCase
{
    
    /**
     *
     * @dataProvider provideridentifyMentionedUsersFindsUsernames
     **/
    public function testIdentifyMentionsFindsUsernames($input, $expectedOutput)
    {
        $output = MentionHelper::identifyMentions($input);
        $this->assertEquals($expectedOutput, $output);
    }

    public function provideridentifyMentionedUsersFindsUsernames()
    {
        return [
            'blank post' => [
                $input = '',
                $expectedOutput = [],
            ],
            'single mention' => [
                $input = 'hey @christiancable how are you?',
                $expectedOutput = ['christiancable'],
            ],
            'multiple mentions' => [
                $input = 'hey @christiancable have you seen @AgentOrange',
                $expectedOutput = ['christiancable', 'AgentOrange'],
            ],
            'email address which should not be matched' => [
                $input = 'my email is christian@nexus5.org.uk',
                $expectedOutput = [],
            ],
        ];
    }
    
    /**
    *
    * @dataProvider providerHighlightMentionsHighlights
    **/
    public function testHighlightMentionsHighlights($input, $expectedOutput)
    {
        $output = MentionHelper::highlightMentions($input);
        $this->assertEquals($expectedOutput, $output);
    }

    public function providerHighlightMentionsHighlights()
    {
        return [
            'blank post' => [
                $input = '',
                $expectedOutput = '',
            ],
            'single mention' => [
                $input = 'hey @christiancable how are you?',
                $expectedOutput = 'hey <span class="text-muted">@</span><mark><strong><a href="/users/christiancable">christiancable</a></strong></mark> how are you?',
            ],
            'multiple mentions' => [
                $input = 'hey @christiancable have you seen @AgentOrange',
                $expectedOutput = 'hey <span class="text-muted">@</span><mark><strong><a href="/users/christiancable">christiancable</a></strong></mark> have you seen <span class="text-muted">@</span><mark><strong><a href="/users/AgentOrange">AgentOrange</a></strong></mark>',
            ],
            'mention with html' => [
                $input = '<p>@christiancable</p>',
                $expectedOutput = '<p><span class="text-muted">@</span><mark><strong><a href="/users/christiancable">christiancable</a></strong></mark></p>',
            ],
        ];
    }
}
