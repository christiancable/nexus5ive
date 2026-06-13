<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_update_their_own_profile(): void
    {
        $user = User::factory()->forTheme()->create();

        $this->assertTrue($user->can('update', $user));
    }

    #[Test]
    public function user_cannot_update_another_users_profile(): void
    {
        $user = User::factory()->forTheme()->create();
        $other = User::factory()->forTheme()->create();

        $this->assertFalse($user->can('update', $other));
    }

    #[Test]
    public function guest_cannot_update_any_profile(): void
    {
        $guest = User::factory()->forTheme()->create(['is_guest' => true]);
        $other = User::factory()->forTheme()->create();

        $this->assertFalse($guest->can('update', $other));
        $this->assertFalse($guest->can('update', $guest));
    }
}
