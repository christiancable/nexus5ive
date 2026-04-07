<?php

use Illuminate\Foundation\Testing\DatabaseTruncation;
use Tests\BrowserTestCase;

// BrowserTestCase excludes 'themes' from truncation (default theme is migration-seeded).
uses(BrowserTestCase::class, DatabaseTruncation::class)->in('Browser');

// Global browser timeout (ms) — generous for Docker
pest()->browser()->timeout(20000);
