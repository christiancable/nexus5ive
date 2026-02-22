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

        if ($this->isBinary($this->content)) {
            throw new RuntimeException("File appears to be binary: {$filePath}");
        }

        // Strip null bytes — legacy text files occasionally contain C-string padding
        $this->content = str_replace("\x00", '', $this->content);
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

            if (str_starts_with($line, self::MARKER_FROM)) {
                // A From marker without a preceding timestamp marker means the first
                // post was written without a timestamp (older article format).
                if ($currentPost === null) {
                    $currentPost = [
                        'timestamp' => null,
                        'nick' => null,
                        'popname' => null,
                        'subject' => null,
                        'body' => [],
                    ];
                }

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
     * Detect known binary file formats by magic bytes.
     */
    private function isBinary(string $content): bool
    {
        // ZIP / JAR / EPUB (PK\x03\x04)
        if (str_starts_with($content, "PK\x03\x04")) {
            return true;
        }

        // DOS/Windows EXE or COM (MZ header)
        if (str_starts_with($content, 'MZ')) {
            return true;
        }

        // gzip (\x1f\x8b)
        if (str_starts_with($content, "\x1f\x8b")) {
            return true;
        }

        // LZH archive (-lh)
        if (strlen($content) >= 5 && substr($content, 2, 3) === '-lh') {
            return true;
        }

        // ARJ archive (\x60\xea)
        if (str_starts_with($content, "\x60\xea")) {
            return true;
        }

        return false;
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
