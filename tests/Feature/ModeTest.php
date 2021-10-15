<?php

namespace Tests\Feature;

use App\Mode;
use App\User;
use App\Section;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ModeTest extends TestCase
{
    use RefreshDatabase;

    /**
    * @test
    * @group mode
    */
    public function sysopsCanAccessAdminSection()
    {
        $sysop = User::factory()->create(['administrator' => true]);

        $this->actingAs($sysop)->get('/admin')->assertSuccessful();
    }

    /**
    * @test
    * @group mode
    */
    public function nonSysopsCannotAccessAdminSection()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin')->assertStatus(403);
    }
}
