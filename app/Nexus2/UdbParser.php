<?php

namespace App\Nexus2;

use RuntimeException;

class UdbParser
{
    public const STRUCT_SIZE = 527;

    private const RIGHTS_LABELS = [
        0 => 'Guest',
        64 => 'Normal',
        128 => 'Moderator',
        255 => 'Sysop',
    ];

    private const SEX_LABELS = [
        0 => 'Unknown',
        1 => 'Male',
        2 => 'Female',
    ];

    private const HIDE_LABELS = [
        0 => 'All (invisible)',
        1 => 'Some',
        2 => 'None (visible)',
    ];

    private const TIME_MODE_LABELS = [
        0 => '12h',
        1 => '24h',
        2 => 'None',
        3 => '12h + Beep',
        4 => '24h + Beep',
    ];

    private const ENABLED_LABELS = [
        0 => 'Enabled',
        1 => 'Disabled',
        2 => 'Banned',
    ];

    private const PRIV_FLAGS = [
        1 => 'Expel',
        2 => 'Star Send',
        4 => 'Down BBS',
        8 => 'View Logs',
        16 => 'Change Account',
        32 => 'View Account',
        64 => 'Anonymous View',
        128 => 'Sysop',
    ];

    private const BAN_FLAGS = [
        1 => 'PopName',
        2 => 'Mail',
        4 => 'Print',
        8 => 'Run',
        16 => 'Send',
        32 => 'Comment',
        64 => 'Edit',
        128 => 'Chat',
    ];

    private const BAN2_FLAGS = [
        1 => 'Talker',
        2 => 'Use BBS',
    ];

    /**
     * Flag indices (1-indexed into the 250-byte flags area at offset 277).
     * From DEFINES.H FLAG_* constants.
     */
    private const FLAG_SEX = 10;

    private const FLAG_HIDE = 9;

    private const FLAG_TIMEMODE = 11;

    private const FLAG_CHAT = 12;

    private const FLAG_MESSAGE = 13;

    private const FLAG_COMMENT = 14;

    private const FLAG_MAIL = 15;

    private const FLAG_NEWSTUFF = 16;

    private const FLAG_UPDATE = 17;

    private const FLAG_LOCKDELAY = 18;

    private const FLAG_VALIDATED = 19;

    private const FLAG_SEEALL = 20;

    private string $data;

    public function __construct(string $filePath)
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException("File not found: {$filePath}");
        }

        $this->data = file_get_contents($filePath);

        if (strlen($this->data) !== self::STRUCT_SIZE) {
            throw new RuntimeException(
                sprintf('Expected %d bytes, got %d', self::STRUCT_SIZE, strlen($this->data))
            );
        }
    }

    public function parse(): array
    {
        $fields = unpack(
            'A17Nick/'.
            'A30UserID/'.
            'A30RealName/'.
            'A41PopName/'.
            'CRights/'.          // uchar (1 byte)
            'VTotalEdits/'.      // ulong little-endian (4 bytes)
            'VTimeOn/'.          // ulong little-endian
            'VTimesOn/'.         // ulong little-endian
            'A21Password/'.
            'A30Dept/'.
            'A30Faculty/'.
            'A25Created/'.
            'A25LastOn/'.
            'A13HistoryFile/'.
            'vUserNo/'.          // uint16 little-endian (2 bytes)
            'C3Flags/'.          // uchar[3]
            'CPriv/'.            // UDBPUnion (1 byte)
            'vBan/'.             // UDBBUnion (2 bytes, uint)
            'vBanned/'.          // UDBBUnion (2 bytes, uint)
            'CMaxLogins',        // uchar (1 byte)
            $this->data
        );

        // Clean null bytes from string fields
        $stringFields = ['Nick', 'UserID', 'RealName', 'PopName', 'Password',
            'Dept', 'Faculty', 'Created', 'LastOn', 'HistoryFile'];

        foreach ($stringFields as $field) {
            $fields[$field] = $this->cleanString($fields[$field]);
        }

        // Read the full 250-byte flags area (offset 277) for flag index lookups
        $flagsRaw = array_values(unpack('C250', substr($this->data, 277)));

        // Decode rights level
        $fields['RightsLabel'] = self::RIGHTS_LABELS[$fields['Rights']] ?? 'Unknown';

        // Decode privilege bitfield
        $fields['PrivLabels'] = $this->decodeBitfield($fields['Priv'], self::PRIV_FLAGS);

        // Decode ban bitfields
        $banByte1 = $fields['Ban'] & 0xFF;
        $banByte2 = ($fields['Ban'] >> 8) & 0xFF;
        $fields['BanLabels'] = array_merge(
            $this->decodeBitfield($banByte1, self::BAN_FLAGS),
            $this->decodeBitfield($banByte2, self::BAN2_FLAGS)
        );

        $bannedByte1 = $fields['Banned'] & 0xFF;
        $bannedByte2 = ($fields['Banned'] >> 8) & 0xFF;
        $fields['BannedLabels'] = array_merge(
            $this->decodeBitfield($bannedByte1, self::BAN_FLAGS),
            $this->decodeBitfield($bannedByte2, self::BAN2_FLAGS)
        );

        // Decode flag values — FLAG_* constants are 0-indexed C array indices
        $fields['Sex'] = self::SEX_LABELS[$flagsRaw[self::FLAG_SEX]] ?? "Unknown ({$flagsRaw[self::FLAG_SEX]})";
        $fields['Hide'] = self::HIDE_LABELS[$flagsRaw[self::FLAG_HIDE]] ?? "Unknown ({$flagsRaw[self::FLAG_HIDE]})";
        $fields['TimeMode'] = self::TIME_MODE_LABELS[$flagsRaw[self::FLAG_TIMEMODE]] ?? "Unknown ({$flagsRaw[self::FLAG_TIMEMODE]})";
        $fields['Chat'] = self::ENABLED_LABELS[$flagsRaw[self::FLAG_CHAT]] ?? "Unknown ({$flagsRaw[self::FLAG_CHAT]})";
        $fields['Message'] = self::ENABLED_LABELS[$flagsRaw[self::FLAG_MESSAGE]] ?? "Unknown ({$flagsRaw[self::FLAG_MESSAGE]})";
        $fields['CommentFlag'] = self::ENABLED_LABELS[$flagsRaw[self::FLAG_COMMENT]] ?? "Unknown ({$flagsRaw[self::FLAG_COMMENT]})";
        $fields['Mail'] = self::ENABLED_LABELS[$flagsRaw[self::FLAG_MAIL]] ?? "Unknown ({$flagsRaw[self::FLAG_MAIL]})";
        $fields['NewStuff'] = $flagsRaw[self::FLAG_NEWSTUFF];
        $fields['Update'] = $flagsRaw[self::FLAG_UPDATE];
        $fields['LockDelay'] = $flagsRaw[self::FLAG_LOCKDELAY];
        $fields['Validated'] = $flagsRaw[self::FLAG_VALIDATED] ? 'Yes' : 'No';
        $fields['SeeAll'] = $flagsRaw[self::FLAG_SEEALL] ? 'Yes' : 'No';

        return $fields;
    }

    private function cleanString(string $value): string
    {
        // Truncate at first null byte (C-string termination)
        $nullPos = strpos($value, "\0");
        if ($nullPos !== false) {
            $value = substr($value, 0, $nullPos);
        }

        return trim($value);
    }

    private function decodeBitfield(int $byte, array $flagMap): array
    {
        $labels = [];
        foreach ($flagMap as $bit => $label) {
            if ($byte & $bit) {
                $labels[] = $label;
            }
        }

        return $labels;
    }
}
