<?php

namespace Tests;

use App\Models\Theme;

/**
 * Base test case for browser tests.
 *
 * Recreates the default theme before each test because it is seeded by a
 * migration rather than a dedicated seeder, and DatabaseTruncation clears
 * the themes table between tests.
 */
abstract class BrowserTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Theme::firstOrCreate(
            ['name' => 'Default'],
            ['path' => 'resources/sass/app.scss']
        );
    }
}
