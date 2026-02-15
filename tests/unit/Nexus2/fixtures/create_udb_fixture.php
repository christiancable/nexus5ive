<?php

/**
 * One-time script to generate a test UDB fixture (527 bytes).
 * Run: php tests/unit/Nexus2/fixtures/create_udb_fixture.php
 */

$data = str_repeat("\0", 527);

// Helper to write a null-terminated string at offset
function writeString(string &$data, int $offset, int $size, string $value): void
{
    $bytes = substr($value . str_repeat("\0", $size), 0, $size);
    for ($i = 0; $i < $size; $i++) {
        $data[$offset + $i] = $bytes[$i];
    }
}

function writeUint8(string &$data, int $offset, int $value): void
{
    $data[$offset] = chr($value & 0xFF);
}

function writeUint16LE(string &$data, int $offset, int $value): void
{
    $data[$offset] = chr($value & 0xFF);
    $data[$offset + 1] = chr(($value >> 8) & 0xFF);
}

function writeUint32LE(string &$data, int $offset, int $value): void
{
    $packed = pack('V', $value);
    for ($i = 0; $i < 4; $i++) {
        $data[$offset + $i] = $packed[$i];
    }
}

// String fields
writeString($data, 0, 17, 'TestUser');         // Nick
writeString($data, 17, 30, 'T.USER1');          // UserID
writeString($data, 47, 30, 'Test User');         // RealName
writeString($data, 77, 41, 'A witty tagline');   // PopName
writeUint8($data, 118, 128);                     // Rights = Moderator
writeUint32LE($data, 119, 1234);                 // TotalEdits
writeUint32LE($data, 123, 5678);                 // TimeOn (minutes)
writeUint32LE($data, 127, 42);                   // TimesOn
writeString($data, 131, 21, 'hashedpw');          // Password
writeString($data, 152, 30, 'Computer Science');  // Dept
writeString($data, 182, 30, 'Engineering');       // Faculty
writeString($data, 212, 25, 'Mon 1/1/97 at 09:00:00');  // Created
writeString($data, 237, 25, 'Fri 5/6/99 at 17:30:00');  // LastOn
writeString($data, 262, 13, 'HISTORY.3');         // HistoryFile
writeUint16LE($data, 275, 42);                    // UserNo

// Flags area (offset 277, 250 bytes)
// Bytes 0-2: Flags[0..2] (unused, connno, usermode)
// Byte 3 (FLAG_PRIVS): privilege bitfield
writeUint8($data, 277 + 3, 0b00001001); // Expel + ViewLogs

// Bytes 4-5 (FLAG_BANPRIV): ban bitfield (2 bytes as uint16 LE)
writeUint8($data, 277 + 4, 0b00000010); // Ban: Mail
writeUint8($data, 277 + 5, 0);          // Ban byte 2: none

// Bytes 6-7 (FLAG_BANNED): banned bitfield
writeUint8($data, 277 + 6, 0);
writeUint8($data, 277 + 7, 0);

// Byte 8 (FLAG_MAXLOGIN)
writeUint8($data, 277 + 8, 3);   // MaxLogins = 3

// Flag values
writeUint8($data, 277 + 9, 2);   // FLAG_HIDE = 9: None (visible)
writeUint8($data, 277 + 10, 1);  // FLAG_SEX = 10: Male
writeUint8($data, 277 + 11, 1);  // FLAG_TIMEMODE = 11: 24h
writeUint8($data, 277 + 12, 0);  // FLAG_CHAT = 12: Enabled
writeUint8($data, 277 + 13, 0);  // FLAG_MESSAGE = 13: Enabled
writeUint8($data, 277 + 14, 0);  // FLAG_COMMENT = 14: Enabled
writeUint8($data, 277 + 15, 0);  // FLAG_MAIL = 15: Enabled
writeUint8($data, 277 + 19, 1);  // FLAG_VALIDATED = 19: Yes
writeUint8($data, 277 + 20, 0);  // FLAG_SEEALL = 20: No

file_put_contents(__DIR__ . '/test_user.udb', $data);
echo "Created test_user.udb (" . strlen($data) . " bytes)\n";

// Also create a sysop fixture
$sysop = str_repeat("\0", 527);
writeString($sysop, 0, 17, 'SysopNick');
writeString($sysop, 17, 30, 'S.ADMIN1');
writeString($sysop, 47, 30, 'System Admin');
writeString($sysop, 77, 41, '{The Boss}');
writeUint8($sysop, 118, 255);                     // Rights = Sysop
writeUint32LE($sysop, 119, 99999);
writeUint32LE($sysop, 123, 50000);
writeUint32LE($sysop, 127, 500);
writeString($sysop, 131, 21, 'sysoppw');
writeString($sysop, 152, 30, '');
writeString($sysop, 182, 30, '');
writeString($sysop, 212, 25, 'Tue 1/10/96 at 08:00:00');
writeString($sysop, 237, 25, 'Sat 12/6/99 at 23:59:59');
writeString($sysop, 262, 13, 'HISTORY.0');
writeUint16LE($sysop, 275, 1);

// Sysop privileges: all bits set
writeUint8($sysop, 277 + 3, 0xFF);
writeUint8($sysop, 277 + 8, 255); // MaxLogins
writeUint8($sysop, 277 + 9, 2);   // Hide: None
writeUint8($sysop, 277 + 10, 1);  // Sex: Male
writeUint8($sysop, 277 + 11, 1);  // TimeMode: 24h
writeUint8($sysop, 277 + 19, 1);  // Validated: Yes
writeUint8($sysop, 277 + 20, 1);  // SeeAll: Yes

file_put_contents(__DIR__ . '/test_sysop.udb', $sysop);
echo "Created test_sysop.udb (" . strlen($sysop) . " bytes)\n";
