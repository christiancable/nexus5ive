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

    /**
     * Parse the menu file.
     *
     * @param  int|null  $privLevel  If provided, items with read > privLevel are filtered out.
     *
     * Conditional blocks (.if/.else/.endif) are evaluated as an unprivileged anonymous user:
     *   - user <names>  → false (import user is not any specific named user)
     *   - sysop/owner/author → false
     *   - hasprivs/exists/load/day/time → false (no special privs or runtime state)
     *   - privlevel <relop> N → evaluated against default level 100
     *   - NOT/! prefix negates the result
     * Items and sub-menus inside inactive .if blocks are excluded from the result.
     */
    public function parse(?int $privLevel = null): array
    {
        $result = [
            'header' => null,
            'owners' => [],
            'directives' => [],
            'items' => [],
        ];

        // IF stack: each entry is ['condition' => bool, 'in_else' => bool]
        // A block is active when condition XOR in_else is true for every level.
        $ifStack = [];

        foreach ($this->lines as $lineNum => $line) {
            $line = rtrim($line);

            if ($line === '' || $line[0] === '#' || $line[0] === '/' || $line[0] === ';') {
                continue;
            }

            if ($line[0] === '.') {
                $after = substr($line, 1);
                $parts = preg_split('/\s+/', $after, 2);
                $keyword = strtolower($parts[0] ?? '');

                // .if / .else / .endif are control flow — not stored as directives
                if ($keyword === 'if') {
                    $condition = $this->evaluateCondition($parts[1] ?? '');
                    $ifStack[] = ['condition' => $condition, 'in_else' => false];
                    continue;
                }

                if ($keyword === 'else') {
                    if (! empty($ifStack)) {
                        $ifStack[count($ifStack) - 1]['in_else'] = true;
                    }
                    continue;
                }

                if ($keyword === 'endif') {
                    array_pop($ifStack);
                    continue;
                }

                // All other directives only take effect when the if-stack is active
                if ($this->isIfActive($ifStack)) {
                    $this->parseDirective($line, $result, $lineNum + 1, $privLevel);
                }

                continue;
            }

            if (! $this->isIfActive($ifStack)) {
                continue;
            }

            $prefix = strtolower($line[0]);

            if ($prefix === 'h') {
                $result['header'] = trim(substr($line, 1));

                continue;
            }

            if (isset(self::ITEM_TYPES[$prefix])) {
                $item = $this->parseItem($prefix, $line, $lineNum + 1);
                if ($item !== null && ($privLevel === null || $item['read'] <= $privLevel)) {
                    $result['items'][] = $item;
                }
            }
        }

        return $result;
    }

    /**
     * Returns true only when all levels of the if-stack are currently active.
     *
     * @param  array<int, array{condition: bool, in_else: bool}>  $ifStack
     */
    private function isIfActive(array $ifStack): bool
    {
        foreach ($ifStack as $entry) {
            $active = $entry['in_else'] ? ! $entry['condition'] : $entry['condition'];
            if (! $active) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evaluate a .if condition expression as an unprivileged anonymous user.
     *
     * Supports: user <names>, sysop, owner, author, hasprivs, exists, load,
     *           day, time, privlevel <relop> <n>
     * Boolean connectors: && (and), || (or)
     * Negation prefix:    ! or not
     */
    private function evaluateCondition(string $expression): bool
    {
        // Tokenise, expanding !keyword (no space) into ['!', 'keyword']
        $rawTokens = preg_split('/\s+/', trim($expression), -1, PREG_SPLIT_NO_EMPTY);
        $tokens = [];
        foreach ($rawTokens as $t) {
            if (strlen($t) > 1 && str_starts_with($t, '!')) {
                $tokens[] = '!';
                $tokens[] = substr($t, 1);
            } else {
                $tokens[] = $t;
            }
        }

        if (empty($tokens)) {
            return false;
        }

        $pos = 0;
        $result = $this->parseSingleCondition($tokens, $pos);

        while ($pos < count($tokens)) {
            $op = strtolower($tokens[$pos]);

            if ($op === '&&' || $op === 'and') {
                $pos++;
                $right = $this->parseSingleCondition($tokens, $pos);
                $result = $result && $right;
            } elseif ($op === '||' || $op === 'or') {
                $pos++;
                $right = $this->parseSingleCondition($tokens, $pos);
                $result = $result || $right;
            } else {
                break;
            }
        }

        return $result;
    }

    /**
     * Parse and evaluate a single condition, advancing $pos past consumed tokens.
     *
     * @param  string[]  $tokens
     */
    private function parseSingleCondition(array $tokens, int &$pos): bool
    {
        if ($pos >= count($tokens)) {
            return false;
        }

        // Optional NOT prefix
        $negate = false;
        if ($tokens[$pos] === '!' || strtolower($tokens[$pos]) === 'not') {
            $negate = true;
            $pos++;
        }

        if ($pos >= count($tokens)) {
            return $negate;
        }

        $condType = strtolower($tokens[$pos++]);
        $result = false;

        if ($condType === 'user') {
            // Consume all names until a boolean operator or end of tokens
            while ($pos < count($tokens)) {
                $t = strtolower($tokens[$pos]);
                if ($t === '&&' || $t === '||' || $t === 'and' || $t === 'or') {
                    break;
                }
                $pos++;
            }
            // Import user is never any named user
            $result = false;
        } elseif (in_array($condType, ['sysop', 'owner', 'author'], true)) {
            $result = false;
        } elseif ($condType === 'hasprivs') {
            if ($pos < count($tokens)) {
                $pos++; // consume privileges string
            }
            $result = false;
        } elseif ($condType === 'exists') {
            if ($pos < count($tokens)) {
                $pos++; // consume filename
            }
            $result = false;
        } elseif (in_array($condType, ['load', 'time'], true)) {
            if ($pos < count($tokens)) {
                $pos++; // consume relop
            }
            if ($pos < count($tokens)) {
                $pos++; // consume value
            }
            $result = false;
        } elseif ($condType === 'day') {
            if ($pos < count($tokens)) {
                $pos++; // consume day name
            }
            $result = false;
        } elseif ($condType === 'privlevel') {
            $relop = $tokens[$pos++] ?? '>';
            $n = (int) ($tokens[$pos++] ?? 0);
            $userLevel = 100; // Default user privilege level
            $result = match ($relop) {
                '<'  => $userLevel < $n,
                '>'  => $userLevel > $n,
                '<=' => $userLevel <= $n,
                '>=' => $userLevel >= $n,
                '==' => $userLevel == $n,
                '!=' => $userLevel != $n,
                default => false,
            };
        }

        return $negate ? ! $result : $result;
    }

    private const DIRECTIVE_COMMANDS = [
        'owner', 'dontscan', 'noscan', 'doscan', 'scan',
        'pagebreak', 'title', 'flags', 'repeat', 'log', 'include', 'overlay',
        'use', 'quit',
    ];

    private function parseDirective(string $line, array &$result, int $lineNum, ?int $privLevel = null): void
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
            if ($item !== null && ($privLevel === null || $item['read'] <= $privLevel)) {
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
