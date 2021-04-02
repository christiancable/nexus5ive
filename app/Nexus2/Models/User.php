<?php

namespace App\Nexus2\Models;

use Carbon\Carbon;

/*

example usage


$legacyUser = new App\Nexus2\Modules\User($udb, $info, $comments);

issue with udb
    throw exception

issue with info
    no info

issue with comments
    no comments


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


    public $username; 
    public $name; 
    public $popname; 
    public $email; 
    public $created_at; 
    public $latestLogin;
    public $totalEdits;
    public $totalPosts;

    public $comments = [];
    public $info;

    function __construct(string $udb, string $info = '', string $comments = '') {
        $this->hydrate($udb);
        $this->comments = $this->parseComments($comments);
        $this->info = $this->parseInfo($info);
    }


    /**
     * parseUDB
     *
     * @param string $udb NEXUS.UDB as a string
     * @return array parsed user info
     */
    public function parseUDB(string $udb): array
    {
        $unpacked = unpack(self::$format, $udb);
        $user = $unpacked;

        return $user;
    }


    public function parseInfo(string $info) : string
    {
        // double the new lines to make this markdown friendly
        return str_replace("\r\n", "\r\n\r\n", $info);
    }

    /**
     * parseComments
     *
     * @param string $comments COMMENTS.TXT as a string
     * @return array of comments and users
     */
    public static function parseComments(string $comments): array
    {
        return array_reverse(explode(PHP_EOL, $comments));
    }

    /**
     * hydrate
     *
     * imports a Nexus2 UDB if user does not already exist
     * returns user model ready for saving
     *
     * @param string $UDB
     */
    public function hydrate(string $UDB)
    {
        $importedUser =  $this->parseUDB($UDB);

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

        $this->username    = $importedUser['Nick'];
        $this->name        = $importedUser['RealName'];
        $this->popname     = $importedUser['PopName'];
        $this->email       = $importedUser['UserId'] . '@nexus2.imported';
        $this->totalVisits = $importedUser['NoOfTimesOn'];
        $this->totalPosts  = $importedUser['NoOfEdits'];
        $this->created_at  = $created;
        $this->latestLogin = $lastOn;
    }
}
