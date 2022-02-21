<?php

namespace Tests\Unit\Nexus2;

use Tests\TestCase;
use App\Nexus2\Helpers\Key;

class KeyTest extends TestCase
{
    /**
     * @test
     * @dataProvider potentialFilesAndExpectedResults
     */
    public function GetKeyForFileWorks($file, $location, $bbsroot, $expectedResult)
    {
        /* 
            $expectedResult should use DIRECTORY_SEPARATOR
            so this needs to be calculated here
        */
        $expectedResult = strtr(
            $expectedResult, 
            ['/' => DIRECTORY_SEPARATOR,
            '\\' => DIRECTORY_SEPARATOR]
        );
        $this->assertEquals($expectedResult, Key::GetKeyForFile($file, $location, $bbsroot));
    }

    public function potentialFilesAndExpectedResults()
    {
        return [
            'relative file' => [
                $file = 'spec\spec.mnu',
                $location = 'tests/Fixtures/Nexus2/Menus/8BIT.MNU',
                $bbsroot = '',
                $expectedResult = 'tests/Fixtures/Nexus2/Menus/spec/spec.mnu',
            ],

            'relative file with bbsroot' => [
                $file = 'spec\spec.mnu',
                $location = 'tests/Fixtures/Nexus2/Menus/8BIT.MNU',
                $bbsroot = 'untracked/ucl_info/BBS',
                $expectedResult = 'tests/Fixtures/Nexus2/Menus/spec/spec.mnu',
            ],

            'absolute file' => [
                $file = '\sections\Fraggle\spec\spec.mnu',
                $location = 'tests/Fixtures/Nexus2/Menus/8BIT.MNU',
                $bbsroot = '',
                $expectedResult = '\sections\Fraggle\spec\spec.mnu',
            ],

            'absolute file with bbsroot' => [
                $file = '\sections\Fraggle\spec\spec.mnu',
                $location = 'tests/Fixtures/Nexus2/Menus/8BIT.MNU',
                $bbsroot = 'untracked\ucl_info/BBS',
                $expectedResult = 'untracked\ucl_info\BBS\sections\Fraggle\spec\spec.mnu',
            ],

            'relative file with dots' => [
                $file = '../Chat',
                $location = 'tests/Fixtures/Nexus2/Menus/8BIT.MNU',
                $bbsroot = '',
                $expectedResult = 'tests/Fixtures/Nexus2/Chat',
            ],

            'absolute file with dots and bbsroot' => [
                $file = '/storage/files/sections/../Chat',
                $location = 'tests/Fixtures/Nexus2/Menus/8BIT.MNU',
                $bbsroot = 'untracked\ucl_info\BBS',
                $expectedResult = 'untracked\ucl_info\BBS\storage\files\Chat',
            ],
        ];
    }
}
