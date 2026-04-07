<?php

namespace Tests;

/**
 * Base test case for browser tests.
 *
 * Excludes the themes table from DatabaseTruncation because the default theme
 * is seeded by a migration (not a dedicated seeder) and must survive between tests.
 */
abstract class BrowserTestCase extends TestCase
{
    /**
     * Tables that should not be truncated between tests.
     *
     * @var array<string>
     */
    protected array $exceptTables = ['themes'];
}
