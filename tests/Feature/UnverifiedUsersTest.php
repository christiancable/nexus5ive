<?php

namespace Tests\Feature;

use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UnverifiedUsersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function whenAUserIsVerifiedTheyAreAddedToTheVerifiedUserList()
    {
        $unverifiedUser = factory(User::class)->create([
           'email_verified_at' => null
        ]);
        $userCount = User::verified()->get()->count();

        $unverifiedUser->email_verified_at = Carbon::now();
        $unverifiedUser->save();
        $newUserCount = User::verified()->get()->count();

        $this->assertEquals($userCount + 1, $newUserCount);
    }

    /**
     * @test
     */
    public function unverifiedUsersDoNotAppearInVerifiedUserList()
    {
        $unverifiedUser = factory(User::class)->create([
           'email_verified_at' => null
        ]);

        $allUsers = User::verified()->get();

        $count = $allUsers->where('id', $unverifiedUser->id)->count();

        $this->assertEquals($count, 0);
    }

    /**
     * @test
     */
    public function verifiedUsersDoNotAppearInUnverifiedUserList()
    {
        $verifiedUser = factory(User::class)->create([
           'email_verified_at' => Carbon::now()
        ]);

        $unverifiedUsers = User::unverified()->get();

        $count = $unverifiedUsers->where('id', $verifiedUser->id)->count();

        $this->assertEquals($count, 0);
    }
}
