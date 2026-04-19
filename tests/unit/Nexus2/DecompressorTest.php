<?php

namespace Tests\Unit\Nexus2;

use App\Nexus2\Decompressor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DecompressorTest extends TestCase
{
    // ── Literal bytes ────────────────────────────────────────────────────────

    #[Test]
    public function literal_ascii_bytes_pass_through_unchanged(): void
    {
        $this->assertSame('Hello', Decompressor::decompress('Hello'));
    }

    #[Test]
    public function newline_passes_through_as_literal(): void
    {
        $this->assertSame("line1\nline2", Decompressor::decompress("line1\nline2"));
    }

    // ── CRLF normalisation ───────────────────────────────────────────────────

    #[Test]
    public function crlf_is_normalised_to_lf(): void
    {
        $this->assertSame("line1\nline2", Decompressor::decompress("line1\r\nline2"));
    }

    #[Test]
    public function dos_eof_marker_is_stripped(): void
    {
        $this->assertSame('hello', Decompressor::decompress("hello\x1a"));
    }

    // ── Digram substitution (0x80–0xFD) ─────────────────────────────────────

    #[Test]
    public function byte_0x80_decodes_to_first_digram(): void
    {
        // Data[0] = "e " → byte 0x80
        $this->assertSame('e ', Decompressor::decompress("\x80"));
    }

    #[Test]
    public function byte_0x8a_decodes_to_th(): void
    {
        // Data[10] = "th" → byte 0x8a
        $this->assertSame('th', Decompressor::decompress("\x8a"));
    }

    #[Test]
    public function byte_0xfc_decodes_to_th_digram(): void
    {
        // Data[124] = "Th" → byte 0xfc
        $this->assertSame('Th', Decompressor::decompress("\xfc"));
    }

    #[Test]
    public function empty_digram_entries_produce_empty_string(): void
    {
        // Data[120] = "" → byte 0xf8
        $this->assertSame('', Decompressor::decompress("\xf8"));
    }

    // ── Extended escape (0xFE) ───────────────────────────────────────────────

    #[Test]
    public function extended_escape_outputs_following_byte_literally(): void
    {
        // 0xFE 0xC0 → chr(0xC0)
        $this->assertSame("\xc0", Decompressor::decompress("\xfe\xc0"));
    }

    #[Test]
    public function truncated_extended_escape_at_end_of_input_is_safe(): void
    {
        $this->assertSame('', Decompressor::decompress("\xfe"));
    }

    // ── Run-length encoding (0xFF) ───────────────────────────────────────────

    #[Test]
    public function run_length_repeats_char_correct_number_of_times(): void
    {
        // 0xFF 0x20 0x06 → 6 spaces
        $this->assertSame('      ', Decompressor::decompress("\xff\x20\x06"));
    }

    #[Test]
    public function run_length_of_one_produces_single_char(): void
    {
        $this->assertSame('X', Decompressor::decompress("\xff\x58\x01"));
    }

    #[Test]
    public function truncated_run_length_at_end_of_input_is_safe(): void
    {
        $this->assertSame('', Decompressor::decompress("\xff\x20"));
    }

    // ── Compressor 26/13 count split ────────────────────────────────────────
    // When the run count would be 26 (0x1A = DOS EOF) or 13 (0x0D = CR),
    // the compressor stores count-1 followed by an extra literal. The
    // decompressor handles this naturally without special logic.

    #[Test]
    public function run_of_26_encoded_as_25_plus_literal_decodes_correctly(): void
    {
        // Compressor stores: [0xFF, 'A', 25, 'A'] = 25 repeats + 1 literal = 26 'A's
        $this->assertSame(str_repeat('A', 26), Decompressor::decompress("\xff\x41\x19\x41"));
    }

    #[Test]
    public function run_of_13_encoded_as_12_plus_literal_decodes_correctly(): void
    {
        // Compressor stores: [0xFF, 'B', 12, 'B'] = 12 repeats + 1 literal = 13 'B's
        $this->assertSame(str_repeat('B', 13), Decompressor::decompress("\xff\x42\x0c\x42"));
    }

    // ── Combined sequence ────────────────────────────────────────────────────

    #[Test]
    public function mixed_sequence_decodes_correctly(): void
    {
        // Reconstruct the start of the BLAKE7 file first line:
        // \r\n + [0xFF 0x20 0x06] + 'B' + [0xE2] + 'k' + 'e' + "'" + [0x82] + '7'
        //   → \n + 6 spaces + B + "la" + k + e + ' + "s " + 7
        //   = "\n      Blake's 7"
        $compressed = "\r\n\xff\x20\x06\x42\xe2\x6b\x65\x27\x82\x37";
        $this->assertSame("\n      Blake's 7", Decompressor::decompress($compressed));
    }

    // ── Integration: real BLAKE7 file ────────────────────────────────────────

    #[Test]
    public function real_compressed_file_decompresses_to_readable_text(): void
    {
        $path = base_path('untracked/ucl_info/BBS/SECTIONS/SNOOKIE/ARCHIVES/EPISODE/BLAKE7');

        if (! file_exists($path)) {
            $this->markTestSkipped('BLAKE7 fixture not available');
        }

        $result = Decompressor::decompress(file_get_contents($path));

        $this->assertStringContainsString("Blake's 7", $result);
        $this->assertStringContainsString('Program Guide', $result);
        // Decompressed content should be plain ASCII — no bytes > 127
        $this->assertSame($result, mb_convert_encoding($result, 'UTF-8', 'ASCII'));
    }

    // ── DataProvider: individual digrams ─────────────────────────────────────

    #[DataProvider('digramProvider')]
    public function test_digram_decodes_correctly(int $byte, string $expected): void
    {
        $this->assertSame($expected, Decompressor::decompress(chr($byte)));
    }

    public static function digramProvider(): array
    {
        return [
            'e '  => [0x80, 'e '],
            '  '  => [0x81, '  '],
            'th'  => [0x8a, 'th'],
            'ro'  => [0xb0, 'ro'],
            ' A'  => [0xeb, ' A'],
            'Ne'  => [0xf5, 'Ne'],
            'Th'  => [0xfc, 'Th'],
        ];
    }
}
