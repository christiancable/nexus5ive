<?php

namespace Tests\Unit\View\Components;

use App\View\Components\UserCard;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UserCardTest extends TestCase
{
    private UserCard $component;

    protected function setUp(): void
    {
        parent::setUp();
        $this->component = new UserCard;
    }

    // classy()

    #[Test]
    public function classy_returns_text_secondary_for_score_below_10(): void
    {
        $this->assertEquals('text-secondary', $this->component->classy(0));
        $this->assertEquals('text-secondary', $this->component->classy(9));
    }

    #[Test]
    public function classy_returns_text_dark_for_score_between_10_and_99(): void
    {
        $this->assertEquals('text-dark', $this->component->classy(10));
        $this->assertEquals('text-dark', $this->component->classy(99));
    }

    #[Test]
    public function classy_returns_text_info_for_score_between_100_and_999(): void
    {
        $this->assertEquals('text-info', $this->component->classy(100));
        $this->assertEquals('text-info', $this->component->classy(999));
    }

    #[Test]
    public function classy_returns_text_primary_for_score_between_1000_and_9999(): void
    {
        $this->assertEquals('text-primary', $this->component->classy(1000));
        $this->assertEquals('text-primary', $this->component->classy(9999));
    }

    #[Test]
    public function classy_returns_text_success_for_score_between_10000_and_99999(): void
    {
        $this->assertEquals('text-success', $this->component->classy(10000));
        $this->assertEquals('text-success', $this->component->classy(99999));
    }

    #[Test]
    public function classy_returns_text_danger_for_score_at_or_above_100000(): void
    {
        $this->assertEquals('text-danger', $this->component->classy(100000));
        $this->assertEquals('text-danger', $this->component->classy(999999));
    }

    // headingBackground()

    #[Test]
    public function heading_background_is_light_for_zero_score(): void
    {
        $this->assertEquals('bg-light', $this->component->headingBackground(0));
    }

    #[Test]
    public function heading_background_is_info_for_nonzero_score(): void
    {
        $this->assertEquals('bg-info', $this->component->headingBackground(1));
        $this->assertEquals('bg-info', $this->component->headingBackground(100));
    }

    // headingForeground()

    #[Test]
    public function heading_foreground_is_secondary_for_zero_score(): void
    {
        $this->assertEquals('text-secondary', $this->component->headingForeground(0));
    }

    #[Test]
    public function heading_foreground_is_white_for_nonzero_score(): void
    {
        $this->assertEquals('text-white', $this->component->headingForeground(1));
        $this->assertEquals('text-white', $this->component->headingForeground(500));
    }
}
