<?php

namespace App\Helpers\Nexus2;

use Carbon\Carbon;
use App\User as NewUser;

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

    private static $dateTimeFormat = 'D d/n/y * G:i:s';

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
     * @var $files - array of nexus 2 user files
     */
    private static $files = [
        'usb'           => 'NEXUS.UDB',
        'comments'      => 'COMMENTS.TXT',
        'info'          => 'INFO.TXT'
    ];



    /**
     * IO
     */

    /**
     * findOrCreate - imports a user or creates a new user if existing legacy user not found
     * 
     */
    public static function findOrCreate() {

    }

    
    /**
     * Parsing
     */

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
        // double the new lines to make this markdown friendly
        $info = str_replace("\r\n", "\r\n\r\n", $info);

        // translate old nexus highlights
        $info = str_replace("{", "**", $info);
        $info = str_replace("}", "**", $info);
        return $info;
    }

    /**
     * parseComments
     *
     * @param string $comments COMMENTS.TXT as a string
     * @return array of comments and users
     */
    public static function parseComments(string $comments): array
    {
        $rawComments = array_reverse(explode("\r\n", $comments));
        
        $parsedComments = array_map( function ($line) {
            $re = '/{(?\'username\'.*)} : (?\'comment\'.*)$/m';
            if (preg_match_all($re, $line, $matches, PREG_SET_ORDER, 0)) {
                if (array_key_exists('username', $matches[0]) && array_key_exists('comment', $matches[0])) {
                    return ['username' => $matches[0]['username'], 'comment' => $matches[0]['comment']];
                }
            }
        }, $rawComments);

        $parsedComments = array_filter($parsedComments, function ($line) {
            if ($line && array_key_exists('username', $line)) {
                return $line;
            }
        });

        return $parsedComments;
    }

    /**
     * importUserDataBase
     *
     * imports a Nexus2 UDB if user does not already exist
     * returns user model ready for saving
     *
     * @param string $UDB
     * @return App\User || false
     */
    public static function importUserDataBase(string $UDB)
    {
        $importedUser =  self::parseUDB($UDB);

        // does user already exists?
        $existingUser = NewUser::where('username', $importedUser['Nick'])->first();

        if ($existingUser) {
            return false;
        }

        // create carbon dates because some legacy dates are not actual dates
        try {
            $created = Carbon::createFromFormat(self::$dateTimeFormat, $importedUser['Created']);
        } catch (\Throwable $th) {
            $created = Carbon::now();
        }
        try {
            $lastOn = Carbon::createFromFormat(self::$dateTimeFormat, $importedUser['LastOn']);
        } catch (\Throwable $th) {
            $lastOn = null;
        }

        $newUser = factory(NewUser::class)->make([
            'username'      => $importedUser['Nick'],
            'name'          => $importedUser['RealName'],
            'popname'       => $importedUser['PopName'],
            'email'         => $importedUser['UserId'] . '@nexus2.imported',
            'created_at'    => $created,
            'latestLogin'   => $lastOn,
        ]);
        
        return $newUser;
    }
}
