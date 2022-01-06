<?php

namespace App\Nexus2\Models;

/*
this class is for parsing with user data from nexus2

example usage
$legacyUser = new App\Nexus2\Models\User($path);


$legacyUser->username()
$legacyUser->info()
$legacyUser->comments()

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
// define S_NICK                  17
// define S_USERID                30
// define S_REALNAME              30
// define S_POPNAME               41
// define S_PASSWORD              21
// define S_DEPT                  30
// define S_FACULTY               30
// define S_FLAGS                 250
// define S_HISTORYFILE           13
// define S_ACTION                41

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

    // private static $dateTimeFormat = 'D d/n/y * G:i:s';

    private const COMMENTS_FILE         = 'comments.txt';
    private const INFO_FILE             = 'info.txt';
    private const USER_DATABASE_FILE    = 'NEXUS.UDB';

    private $username = '';
    private $userId = '';
    private $realName = '';
    private $popName = '';
    private $rights = '';
    private $noOfEdits = '';
    private $totalTimeOn = '';
    private $noOfTimesOn = '';
    private $password = '';
    private $dept = '';
    private $faculty = '';
    private $created = '';
    private $lastOn = '';
    private $historyFile = '';
    private $BBSNo = '';
    private $flags = '';
    private $info = '';

    private $comments = [];

    public function __construct(string $path = null)
    {
        if (null == $path) {
            return;
        }
        $userdir = rtrim($path, '/');

        // user database
        $udbfile = $userdir . DIRECTORY_SEPARATOR . self::USER_DATABASE_FILE;
        if (file_exists($udbfile)) {
            $this->hydrate(file_get_contents($udbfile));
        } else {
            throw new \Exception("User Database File Not Found", 1);
        }

        $commentsfile = $userdir . DIRECTORY_SEPARATOR . self::COMMENTS_FILE;
        if (file_exists($commentsfile)) {
            $this->comments = $this->parseComments(file_get_contents($commentsfile));
        }

        $infofile = $userdir . DIRECTORY_SEPARATOR . self::INFO_FILE;
        if (file_exists($infofile)) {
            $this->info = $this->parseInfo(file_get_contents($infofile));
        }
    }


    /**
     * parseUDB
     *
     * @param  string $udb NEXUS.UDB as a string
     * @return array parsed user info
     */
    public function parseUDB(string $udb): array
    {
        $unpacked = unpack(self::$format, $udb);
        $user = $unpacked;

        return $user;
    }


    public function parseInfo(string $info): string
    {
        // double the new lines to make this markdown friendly
        return str_replace("\r\n", "\r\n\r\n", $info);
    }

    public function parseCommentLine(string $commentLine): ?array
    {
        $re = '/{(?<username>.*)} : (?<body>.*)/m';

        $result = preg_match($re, $commentLine, $matches);
        if (!$result) {
            return null;
        }
        return [
            'username'  => $matches['username'] ?? '',
            'body'      => $matches['body'] ?? '',
        ];
    }
    /**
     * parseComments
     *
     * @param  string $comments COMMENTS.TXT as a string
     * @return array of comments and users
     */
    public function parseComments(string $comments): array
    {
        $commentLines = array_reverse(explode(PHP_EOL, $comments));

        $comments = [];
        foreach ($commentLines as $line) {
            $comment = $this->parseCommentLine($line);
            if ($comment) {
                $comments[] = $comment;
            }
        }
        return $comments;
    }

    /**
     * hydrate
     *
     * @param string $UDB
     */
    public function hydrate(string $UDB)
    {
        $importedUser =  $this->parseUDB($UDB);
        $this->username = $importedUser['Nick'];
        $this->userId = $importedUser['UserId'];
        $this->realName = $importedUser['RealName'];
        $this->popName = $importedUser['PopName'];
        $this->rights = $importedUser['Rights'];
        $this->noOfEdits = $importedUser['NoOfEdits'];
        $this->totalTimeOn = $importedUser['TotalTimeOn'];
        $this->noOfTimesOn = $importedUser['NoOfTimesOn'];
        $this->password = $importedUser['Password'];
        $this->dept = $importedUser['Dept'];
        $this->faculty = $importedUser['Faculty'];
        $this->created = $importedUser['Created'];
        $this->lastOn = $importedUser['LastOn'];
        $this->historyFile = $importedUser['HistoryFile'];
        $this->BBSNo = $importedUser['BBSNo'];
        $this->flags = $importedUser['Flags'];
    }

    public function username()
    {
        return $this->username;
    }

    public function userId()
    {
        return $this->userId;
    }

    public function realName()
    {
        return $this->realName;
    }

    public function popName()
    {
        return $this->popName;
    }

    public function rights()
    {
        return $this->rights;
    }

    public function noOfEdits()
    {
        return $this->noOfEdits;
    }

    public function totalTimeOn()
    {
        return $this->totalTimeOn;
    }

    public function noOfTimesOn()
    {
        return $this->noOfTimesOn;
    }

    public function password()
    {
        return $this->password;
    }

    public function dept()
    {
        return $this->dept;
    }

    public function faculty()
    {
        return $this->faculty;
    }

    public function created()
    {
        return $this->created;
    }

    public function lastOn()
    {
        return $this->lastOn;
    }

    public function historyFile()
    {
        return $this->historyFile;
    }

    public function BBSNo()
    {
        return $this->BBSNo;
    }

    public function flags()
    {
        return $this->flags;
    }

    public function comments()
    {
        return $this->comments;
    }

    public function info()
    {
        return $this->info;
    }

    /**
     * the unique key for this user
     * for users this is the username
     *
     * @return string
     */
    public function key(): string
    {
        return $this->username();
    }
}
