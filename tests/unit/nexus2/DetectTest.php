<?php

namespace Tests\Unit\Nexus2;

use Tests\TestCase;
use App\Nexus2\Helpers\Detect;

class DetectTest extends TestCase
{
    protected $validArticle;
    protected $validMenu;
    
    
    public function loadFixtures()
    {
        $this->validArticle = file_get_contents("tests/Fixtures/Nexus2/Articles/DARK");
        $this->validMenu = file_get_contents("tests/Fixtures/Nexus2/Menus/8BIT.MNU");
    }

    /**
     * @test
     * @group nx2
     * @dataProvider potentialMenusAndExpectedResults
     **/
    public function isMenuDetectsMenus($input, $expectedResult)
    {
        $this->assertEquals($expectedResult, Detect::isMenu($input));
    }

    /**
     * @test
     * @group nx2
     * @dataProvider potentialFilesAndExpectedResults
     **/
    public function isArticleDetectsArticles($input, $expectedResult)
    {
        $this->assertEquals($expectedResult, Detect::isArticle($input));
    }

    public function potentialMenusAndExpectedResults()
    {
        $this->loadFixtures();

        return [
            'blank string' => [
                $input = '',
                $expectedOutput = false,
            ],
            'article not a menu' => [
                $input = $this->validArticle,
                $expectedOutput = false,
            ],
            'valid menu' => [
                $input = $this->validMenu,
                $expectedOutput = true,
            ],
            'text' => [
                $input = <<< TXT
this is not an article or a menu, just some text
TXT
,
                $expectedOutput = false,
            ],
        ];
    }

    public function potentialFilesAndExpectedResults()
    {
        $this->loadFixtures();
        return [
            'blank string' => [
                $input = '',
                $expectedOutput = false,
            ],
            'valid article' => [
                $input = $this->validArticle,
                $expectedOutput = true,
            ],
            'menu not a article' => [
                $input = $this->validMenu,
                $expectedOutput = false,
            ],
            'text' => [
                $input = <<< TXT
this is not an article or a menu, just some text
TXT
,
                $expectedOutput = false,
            ],
        ];
    }
}
