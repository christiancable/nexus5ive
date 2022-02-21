<?php

namespace Tests\Unit\Nexus2;

use Tests\TestCase;
use App\Nexus2\Models\User as Nexus2User;

class User extends TestCase
{
    /**
     * @test
     * @group        nx2
     * @dataProvider providerUserDataBasesAndUsers
     **/
    public function userIsHydratedFromUDB($udb, $expected)
    {
        $user = new Nexus2User();
        $user->hydrate($udb); 
        $this->assertEquals($user->username(), $expected['Nick']);
        $this->assertEquals($user->realName(), $expected['RealName']);
        $this->assertEquals($user->popname(), $expected['PopName']);        
        $this->assertEquals($user->userID(), $expected['UserId']);        
    }

    /**
     * @test
     */public function commentsIsAnArrayOfUserNamesAndMessages() 
    {
        $user = new Nexus2User();
        $comments = $user->parseComments(file_get_contents("tests/Fixtures/Nexus2/Users/6/COMMENTS.TXT"));

        // expect that comments is an array and that each members of the array has a username and body
        $this->assertIsArray($comments);
        
        // has count of 14 - see fixture file
        $this->assertEquals(count($comments), 14);

        // check that each comment looks like a comment
        foreach ($comments as $comment) {
            $this->assertArrayHasKey('username', $comment);
            $this->assertArrayHasKey('body', $comment);
        }

}

public function providerUserDataBasesAndUsers()
{
    $handle = fopen("tests/Fixtures/Nexus2/Users/6/NEXUS.UDB", "rb");
    $contents = stream_get_contents($handle);
    fclose($handle);

    return [
        'fraggle' => [
            $input = $contents,
            $expectedOutput = [
                'Nick'          =>  'Fraggle',
                'UserId'        =>  'C.F.CABLE',
                'RealName'      =>  'Christian',
                'PopName'       =>  'totally tick tack',
                'Rights'        =>  '255',
                'NoOfEdits'     =>  '2053',
                'TotalTimeOn'   =>  '642446',
                'NoOfTimesOn'   =>  '65708',
                // 'Password'      =>  'fਇbm',
                'Dept'          =>  'angsthouse',
                'Faculty'       =>  'Part Time',
                'Created'       =>  'Tue 31/10/75 at 14:12:32',
                'LastOn'        =>  'Thu 3/6/99 at 17:27:05',
                // 'HistoryFile'   =>  'HISTORY.0',
                // 'BBSNo'         =>  '65542',
                // 'Flags'         =>  '45',
            ],
        ],
    ];
}

    // public function providerCommentsAndExpected()
    // {
    //     return [
    //         'empty comments file' => [
    //             'ss
    //         ]
    //         ];
    // }
}
