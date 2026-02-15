<?php

namespace App\Nexus2;

use RuntimeException;

class MnuParser
{
    private const ITEM_TYPES = [
        'a' => 'article',
        'f' => 'folder',
        'c' => 'comment',
        'm' => 'mcomment',
        'r' => 'run',
        'i' => 'internal',
    ];

    private array $lines;

    public function __construct(string $filePath)
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException("File not found: {$filePath}");
        }

        $content = file_get_contents($filePath);
        $this->lines = preg_split('/\r?\n/', $content);
    }

    public function parse(): array
    {
        $result = [
            'header' => null,
            'owners' => [],
            'directives' => [],
            'items' => [],
        ];

        foreach ($this->lines as $lineNum => $line) {
            $line = rtrim($line);

            if ($line === '' || $line[0] === '#' || $line[0] === '/' || $line[0] === ';') {
                continue;
            }

            if ($line[0] === '.') {
                $this->parseDirective($line, $result, $lineNum + 1);

                continue;
            }

            $prefix = strtolower($line[0]);

            if ($prefix === 'h') {
                $result['header'] = trim(substr($line, 1));

                continue;
            }

            if (isset(self::ITEM_TYPES[$prefix])) {
                $item = $this->parseItem($prefix, $line, $lineNum + 1);
                if ($item !== null) {
                    $result['items'][] = $item;
                }
            }
        }

        return $result;
    }

    private const DIRECTIVE_COMMANDS = [
        'owner', 'if', 'endif', 'else', 'dontscan', 'noscan', 'doscan', 'scan',
        'pagebreak', 'title', 'flags', 'repeat', 'log', 'include', 'overlay',
        'use', 'quit',
    ];

    private function parseDirective(string $line, array &$result, int $lineNum): void
    {
        $after = substr($line, 1);
        $parts = preg_split('/\s+/', $after, 2);
        $firstWord = strtolower($parts[0] ?? '');

        // Check for known directives first (e.g. ".if" starts with "i" but isn't an internal item)
        if (in_array($firstWord, self::DIRECTIVE_COMMANDS)) {
            $args = $parts[1] ?? '';

            if ($firstWord === 'owner') {
                $result['owners'] = preg_split('/\s+/', trim($args));
            } else {
                $result['directives'][] = [
                    'command' => $firstWord,
                    'args' => trim($args),
                ];
            }

            return;
        }

        // Dot-prefixed item type (e.g. ".a 180 180 diary *u ...")
        $prefix = strtolower($after[0] ?? '');
        if (isset(self::ITEM_TYPES[$prefix])) {
            $item = $this->parseItem($prefix, $after, $lineNum);
            if ($item !== null) {
                $result['items'][] = $item;
            }

            return;
        }

        // Unknown directive
        $result['directives'][] = [
            'command' => $firstWord,
            'args' => trim($parts[1] ?? ''),
        ];
    }

    private function parseItem(string $prefix, string $line, int $lineNum): ?array
    {
        $type = self::ITEM_TYPES[$prefix];

        // Strip the prefix character and leading whitespace
        $rest = ltrim(substr($line, 1));

        if ($type === 'comment' || $type === 'mcomment') {
            return $this->parseComment($type, $rest, $lineNum);
        }

        if ($type === 'folder') {
            return $this->parseFolder($rest, $lineNum);
        }

        // article, run, internal all share: read write key file flags info
        return $this->parseArticle($type, $rest, $lineNum);
    }

    private function parseComment(string $type, string $rest, int $lineNum): array
    {
        $parts = preg_split('/\s+/', $rest, 2);

        return [
            'type' => $type,
            'line' => $lineNum,
            'read' => (int) ($parts[0] ?? 0),
            'info' => $parts[1] ?? '',
        ];
    }

    private function parseFolder(string $rest, int $lineNum): ?array
    {
        // Format: read key file flags info
        $tokens = preg_split('/\s+/', $rest);

        if (count($tokens) < 3) {
            return null;
        }

        $read = (int) array_shift($tokens);
        $key = array_shift($tokens);
        $file = array_shift($tokens);

        // Remaining tokens: flags then info (split at first multi-char token or after flags)
        [$flags, $info] = $this->extractFlagsAndInfo($tokens);

        return [
            'type' => 'folder',
            'line' => $lineNum,
            'read' => $read,
            'key' => $key,
            'file' => $file,
            'flags' => $flags,
            'info' => $info,
        ];
    }

    private function parseArticle(string $type, string $rest, int $lineNum): ?array
    {
        // Format: read write key file flags info
        $tokens = preg_split('/\s+/', $rest);

        if (count($tokens) < 4) {
            return null;
        }

        $read = (int) array_shift($tokens);
        $write = (int) array_shift($tokens);
        $key = array_shift($tokens);
        $file = array_shift($tokens);

        [$flags, $info] = $this->extractFlagsAndInfo($tokens);

        return [
            'type' => $type,
            'line' => $lineNum,
            'read' => $read,
            'write' => $write,
            'key' => $key,
            'file' => $file,
            'flags' => $flags,
            'info' => $info,
        ];
    }

    /**
     * The flags field is a single token between the filename and the display text.
     * Everything after the flags token is the display text (info).
     */
    private function extractFlagsAndInfo(array $tokens): array
    {
        if (empty($tokens)) {
            return ['', ''];
        }

        $flags = array_shift($tokens);
        $info = implode(' ', $tokens);

        return [$flags, $info];
    }
}
