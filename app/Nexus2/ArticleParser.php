<?php

namespace App\Nexus2;

use RuntimeException;

class ArticleParser
{
    private const ESC = "\x1b";

    private const MARKER_TIMESTAMP = "\x1b\x01";

    private const MARKER_FROM = "\x1b\x02";

    private const MARKER_SUBJECT = "\x1b\x03";

    private string $content;

    public function __construct(string $filePath)
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException("File not found: {$filePath}");
        }

        $this->content = file_get_contents($filePath);
    }

    public function parse(): array
    {
        $lines = preg_split('/\r?\n/', $this->content);

        $preamble = [];
        $posts = [];
        $currentPost = null;

        foreach ($lines as $line) {
            if (str_starts_with($line, self::MARKER_TIMESTAMP)) {
                // Start of a new post — save the previous one
                if ($currentPost !== null) {
                    $currentPost['body'] = $this->trimBody($currentPost['body']);
                    $posts[] = $currentPost;
                }

                $currentPost = [
                    'timestamp' => trim(substr($line, 2)),
                    'nick' => null,
                    'popname' => null,
                    'subject' => null,
                    'body' => [],
                ];

                continue;
            }

            if ($currentPost !== null && str_starts_with($line, self::MARKER_FROM)) {
                $from = trim(substr($line, 2));
                $this->parseFrom($from, $currentPost);

                continue;
            }

            if ($currentPost !== null && str_starts_with($line, self::MARKER_SUBJECT)) {
                $currentPost['subject'] = trim(substr($line, 2));

                continue;
            }

            if ($currentPost === null) {
                // Before the first post — this is preamble text
                $preamble[] = $line;
            } else {
                $currentPost['body'][] = $line;
            }
        }

        // Save the last post
        if ($currentPost !== null) {
            $currentPost['body'] = $this->trimBody($currentPost['body']);
            $posts[] = $currentPost;
        }

        return [
            'preamble' => $this->trimBody($preamble),
            'posts' => $posts,
        ];
    }

    /**
     * Parse the "From" line format: "PopName) Nick"
     */
    private function parseFrom(string $from, array &$post): void
    {
        $parenPos = strrpos($from, ')');

        if ($parenPos !== false) {
            $post['popname'] = trim(substr($from, 0, $parenPos));
            $post['nick'] = trim(substr($from, $parenPos + 1));
        } else {
            $post['nick'] = $from;
        }
    }

    /**
     * Trim leading and trailing blank lines from a body array, return as string.
     */
    private function trimBody(array $lines): string
    {
        // Remove leading blank lines
        while (! empty($lines) && trim($lines[0]) === '') {
            array_shift($lines);
        }

        // Remove trailing blank lines
        while (! empty($lines) && trim(end($lines)) === '') {
            array_pop($lines);
        }

        return implode("\n", $lines);
    }
}
