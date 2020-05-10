<?php

namespace App\Helpers\Nexus2;

/*

this class is for parsing with user data from nexus2

including
COMMENTS.TXT
NEXUS.UDB
INFO.TXT

excluding
MAIL
MAIL*.PAI
NEXUS.CFG

nexus2 UDB format notes

DEFINES.H
#define S_NICK                  17
#define S_USERID                30
#define S_REALNAME              30
#define S_POPNAME               41
#define S_PASSWORD              21
#define S_DEPT                  30
#define S_FACULTY               30
#define S_FLAGS                 250
#define S_HISTORYFILE           13
#define S_ACTION                41

USERS.H
typedef struct _UDB
{
    char            Nick[S_NICK];
    char            UserId[S_USERID];
    char            RealName[S_REALNAME];
    char            PopName[S_POPNAME];
    unsigned char   Rights;
    unsigned long   NoOfEdits;
    unsigned long   TotalTimeOn;
    unsigned long   NoOfTimesOn;
    char            Password[S_PASSWORD];
    char            Dept[S_DEPT];
    char            Faculty[S_FACULTY];
    char            Created[25];
    char            LastOn[25];
    char            HistoryFile[S_HISTORYFILE];
    unsigned int    BBSNo;
    unsigned char   Flags[S_FLAGS];
} UDB;


    'Z17Nick' .
    'Z30UserId' .
    'Z30RealName' .
    'Z41PopName' .
    'CRights' .
    'LNoOfEdits' .
    'LTotalTimeOn' .
    'LNoOfTimesOn' .
    'Z21Password' .
    'Z30Dept' .
    'Z30Faculty' .
    'Z25Created' .
    'Z25LastOn' .
    'Z13HistoryFile' .
    'IBBSNo' .
    'CFlags';
*/

class User
{
    private static $format =
        'Z17Nick' . '/' .
        'Z30UserId' . '/' .
        'Z30RealName' . '/' .
        'Z41PopName' . '/' .
        'CRights' . '/' .
        'LNoOfEdits' . '/' .
        'LTotalTimeOn' . '/' .
        'LNoOfTimesOn' . '/' .
        'Z21Password' . '/' .
        'Z30Dept' . '/' .
        'Z30Faculty' . '/' .
        'Z25Created' . '/' .
        'Z25LastOn' . '/' .
        'Z13HistoryFile' . '/' .
        'IBBSNo' . '/' .
        'CFlags';

    private static $blank = [
        'Nick'          =>  '',
        'UserId'        =>  '',
        'RealName'      =>  '',
        'PopName'       =>  '',
        'Rights'        =>  '',
        'NoOfEdits'     =>  '',
        'TotalTimeOn'   =>  '',
        'NoOfTimesOn'   =>  '',
        'Password'      =>  '',
        'Dept'          =>  '',
        'Faculty'       =>  '',
        'Created'       =>  '',
        'LastOn'        =>  '',
        'HistoryFile'   =>  '',
        'BBSNo'         =>  '',
        'Flags'         =>  '',
    ];

    /**
     * parseUDB
     *
     * @param string $udb NEXUS.UDB as a string
     * @return array parsed user info
     */
    public static function parseUDB(string $udb): array
    {
        $unpacked = unpack(self::$format, $udb);
        $user = $unpacked;

        return $user;
    }


    public static function parseInfo(string $info) : string
    {
        return $info;
    }

    public static function parseComments(string $comments): array
    {
        $comments = [];
        return $comments;
    }
}
