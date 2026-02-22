<?php

namespace App\Nexus2;

class NxText
{
    /**
     * Strip Nexus 2 highlight markup from a string.
     *
     * @x followed by a single character highlights that character.
     * {text} highlights the enclosed text.
     *
     * This returns the plain text with markup characters removed.
     */
    public static function stripHighlights(string $text): string
    {
        return self::processHighlights($text);
    }

    /**
     * Convert Nexus 2 highlight markup to Markdown bold.
     *
     * @x followed by a single character → **x**
     * {text} → **text**
     */
    public static function toMarkdown(string $text): string
    {
        return self::processHighlights($text, '**', '**');
    }

    /**
     * Convert Nexus 2 highlight markup to Symfony console colour tags.
     */
    public static function toConsole(string $text): string
    {
        return self::processHighlights($text, '<fg=yellow>', '</>');
    }

    private static function processHighlights(string $text, string $open = '', string $close = ''): string
    {
        $lines = explode("\n", $text);
        $processed = [];

        foreach ($lines as $line) {
            $result = '';
            $len = strlen($line);
            $i = 0;
            $inBlock = false;

            while ($i < $len) {
                if ($line[$i] === '@' && $i + 1 < $len) {
                    $i++;
                    $result .= $open.$line[$i].$close;
                    $i++;
                } elseif ($line[$i] === '{') {
                    $i++;
                    $result .= $open;
                    $inBlock = true;
                } elseif ($line[$i] === '}') {
                    $i++;
                    $result .= $close;
                    $inBlock = false;
                } else {
                    $result .= $line[$i];
                    $i++;
                }
            }

            if ($inBlock && $close !== '') {
                $result .= $close;
            }

            $processed[] = $result;
        }

        return implode("\n", $processed);
    }
}
