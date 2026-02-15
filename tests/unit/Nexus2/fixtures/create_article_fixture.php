<?php

/**
 * One-time script to generate a test article fixture with ESC markers.
 * Run: php tests/unit/Nexus2/fixtures/create_article_fixture.php
 */

$ESC = "\x1b";
$TIMESTAMP = $ESC . "\x01";
$FROM = $ESC . "\x02";
$SUBJECT = $ESC . "\x03";

// Article with preamble and multiple posts
$lines = [];

// Preamble
$lines[] = 'This is the preamble text.';
$lines[] = 'It can span multiple lines.';
$lines[] = '';

// Post 1: has popname, subject, and body
$lines[] = $TIMESTAMP . 'Mon Jun 02 14:13:11 1997';
$lines[] = $FROM . '{The cool one}) Fraggle';
$lines[] = $SUBJECT . 'First post subject';
$lines[] = '';
$lines[] = 'This is the body of the first post.';
$lines[] = 'It has multiple lines.';
$lines[] = '';

// Post 2: no subject, no popname
$lines[] = $TIMESTAMP . 'Tue Jun 03 09:00:00 1997';
$lines[] = $FROM . 'Anonymous';
$lines[] = '';
$lines[] = 'A post without a subject or popname.';
$lines[] = '';

// Post 3: has subject with highlights, body with highlights
$lines[] = $TIMESTAMP . 'Wed Jun 04 15:30:00 1997';
$lines[] = $FROM . '@Do@H @Do@H) Blew';
$lines[] = $SUBJECT . '{Important} announcement';
$lines[] = '';
$lines[] = 'Check out this @highlighted text.';
$lines[] = 'And {this is a phrase} that is highlighted.';
$lines[] = '';

$content = implode("\n", $lines);
file_put_contents(__DIR__ . '/test_article.dat', $content);
echo "Created test_article.dat (" . strlen($content) . " bytes)\n";

// Also create a minimal article with no preamble and single post
$lines2 = [];
$lines2[] = $TIMESTAMP . 'Fri May 23 14:40:13 1997';
$lines2[] = $FROM . 'Just a nick) TestUser';
$lines2[] = '';
$lines2[] = 'Single post body.';
$lines2[] = '';

$content2 = implode("\n", $lines2);
file_put_contents(__DIR__ . '/test_article_minimal.dat', $content2);
echo "Created test_article_minimal.dat (" . strlen($content2) . " bytes)\n";

// Empty article (just preamble, no posts)
$content3 = "Just some text with no posts.\n";
file_put_contents(__DIR__ . '/test_article_preamble_only.dat', $content3);
echo "Created test_article_preamble_only.dat (" . strlen($content3) . " bytes)\n";
