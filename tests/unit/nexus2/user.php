<?php

namespace Tests\Unit\Nexus2;

use Tests\TestCase;
use App\Helpers\Nexus2\User as Nexus2User;

class User extends TestCase
{
    /**
     * @test
     * @dataProvider providerUserDataBasesAndUsers
     **/
    public function parseUDBparsesUDBstrings($udb, $expected)
    {
        $parsedUser = Nexus2User::parseUDB($udb);
        foreach (array_keys($expected) as $key) {
            $this->assertEquals($parsedUser[$key], $expected[$key]);
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
}
