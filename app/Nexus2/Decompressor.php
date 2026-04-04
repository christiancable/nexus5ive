<?php

namespace App\Nexus2;

/**
 * Decompresses Nexus 2 compressed text files (the 'k' flag in MNU articles).
 *
 * The format is a simple byte-level scheme from COMPRESS.C:
 *
 *   0x00–0x7F  Literal byte (output as-is)
 *   0x80–0xFD  Digram: look up two-character pair from DATA table (index = byte - 128)
 *   0xFE       Extended: next byte is a literal high-byte character (two bytes consumed)
 *   0xFF       Run-length: [char][count] — output char repeated count times (three bytes consumed)
 */
class Decompressor
{
    /**
     * Digram lookup table — Data[] from COMPRESS.C.
     * Indices 0–125 map to bytes 0x80–0xFD in the compressed stream.
     *
     * @var array<int, string>
     */
    private const DATA = [
        'e ', '  ', 's ', ' a', 'on', ' t', 'er', 'in', 'an', 'd ',  //   0–9  → 0x80–0x89
        'th', 'es', 'he', 'or', 'n ', 're', 'ti', 'te', ' o', 'is',  //  10–19 → 0x8a–0x93
        'nd', 'at', 't ', ' i', 'le', 'r ', 'io', 'ar', 'to', 'ic',  //  20–29 → 0x94–0x9d
        'nt', 'st', 'en', 'al', 'ed', 'it', 'o ', 'ri', 'y ', 'ne',  //  30–39 → 0x9e–0xa7
        'et', ' s', 'se', 'ss', 'ou', 'l ', 'of', 'li', 'ro', 'ma',  //  40–49 → 0xa8–0xb1
        ' f', 'ct', 'f ', 'co', 'ec', ' c', 'om', 'ng', 'il', 'ub',  //  50–59 → 0xb2–0xbb
        'ca', 'ni', 've', 'di', 'el', 'me', 'fo', ' p', ' S', 'ac',  //  60–69 → 0xbc–0xc5
        'ta', ' b', 'ew', 'si', 'na', 'ur', 'ce', 'be', ' e', 'rs',  //  70–79 → 0xc6–0xcf
        'ie', 'su', 'tr', 'ns', 'ch', ' m', ' C', 'de', 'll', 'g ',  //  80–89 → 0xd0–0xd9
        'ai', 'ra', 'ut', 'a ', ' n', 'as', 'ea', ' w', 'la', 'r ',  //  90–99 → 0xda–0xe3
        'bl', 'ws', ' I', 'ts', 'us', 'sc', ' N', ' A', 'ib', ' T',  // 100–109 → 0xe4–0xed
        ' d', 'ue', 'ge', 'hi', 'Co', 'rt', 'rn', 'Ne', 'nc', 'vi',  // 110–119 → 0xee–0xf7
        '', '', 'ee', 'An', 'Th', '',                                  // 120–125 → 0xf8–0xfd
    ];

    /**
     * Decompress a full Nexus 2 compressed file.
     *
     * The original C tool used DOS text mode (CRLF→LF on read), so CRLF pairs
     * in the compressed binary are normalised to LF before decompression.
     */
    public static function decompress(string $data): string
    {
        // Normalise DOS line endings to match what the C compressor saw
        $data = str_replace("\r\n", "\n", $data);

        // Strip DOS EOF marker (Ctrl+Z = 0x1A) — the compressor avoids emitting
        // this byte in run-length counts, but it may appear as a file terminator
        $data = str_replace("\x1a", '', $data);

        /** @var array<int, int> $bytes */
        $bytes = array_values(unpack('C*', $data) ?: []);
        $len = count($bytes);
        $out = '';
        $pos = 0;

        while ($pos < $len) {
            $byte = $bytes[$pos];

            if ($byte === 0xFE) {
                // Extended: next byte is a literal character (may be > 127)
                if ($pos + 1 < $len) {
                    $out .= chr($bytes[$pos + 1]);
                }
                $pos += 2;
            } elseif ($byte === 0xFF) {
                // Run-length: [char][count]
                if ($pos + 2 < $len) {
                    $out .= str_repeat(chr($bytes[$pos + 1]), $bytes[$pos + 2]);
                }
                $pos += 3;
            } elseif ($byte >= 0x80) {
                // Digram substitution
                $out .= self::DATA[$byte - 0x80] ?? '';
                $pos++;
            } else {
                // Literal byte
                $out .= chr($byte);
                $pos++;
            }
        }

        return $out;
    }
}
