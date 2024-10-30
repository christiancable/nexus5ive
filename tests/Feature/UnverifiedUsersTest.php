<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UnverifiedUsersTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function whenAUserIsVerifiedTheyAreAddedToTheVerifiedUserList(): void
    {
        $unverifiedUser = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $userCount = User::verified()->get()->count();

        $unverifiedUser->email_verified_at = Carbon::now();
        $unverifiedUser->save();
        $newUserCount = User::verified()->get()->count();

        $this->assertEquals($userCount + 1, $newUserCount);
    }

    #[Test]
    public function unverifiedUsersDoNotAppearInVerifiedUserList(): void
    {
        $unverifiedUser = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $allUsers = User::verified()->get();

        $count = $allUsers->where('id', $unverifiedUser->id)->count();

        $this->assertEquals($count, 0);
    }

    #[Test]
    public function verifiedUsersDoNotAppearInUnverifiedUserList(): void
    {
        $verifiedUser = User::factory()->create([
            'email_verified_at' => Carbon::now(),
        ]);

        $unverifiedUsers = User::unverified()->get();

        $count = $unverifiedUsers->where('id', $verifiedUser->id)->count();

        $this->assertEquals($count, 0);
    }
}
