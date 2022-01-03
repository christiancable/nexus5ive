<?php

namespace App\Nexus2\Models;

use Carbon\Carbon;

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

    // private static $dateTimeFormat = 'D d/n/y * G:i:s';

    const COMMENTS_FILE         = 'comments.txt';
    const INFO_FILE             = 'info.txt';
    const USER_DATABASE_FILE    = 'NEXUS.UDB';

    private $_username = '';
    private $_userId = '';
    private $_realName = '';
    private $_popName = '';
    private $_rights = '';
    private $_noOfEdits = '';
    private $_totalTimeOn = '';
    private $_noOfTimesOn = '';
    private $_password = '';
    private $_dept = '';
    private $_faculty = '';
    private $_created = '';
    private $_lastOn = '';
    private $_historyFile = '';
    private $_BBSNo = '';
    private $_flags = '';
    private $_info = '';

    private $_comments = [];

    function __construct(string $path = null)
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
            $this->_comments = $this->parseComments(file_get_contents($commentsfile));
        }

        $infofile = $userdir . DIRECTORY_SEPARATOR . self::INFO_FILE;
        if (file_exists($infofile)) {
            $this->_info = $this->parseInfo(file_get_contents($infofile));
        }
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
     * @param string $comments COMMENTS.TXT as a string
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
        $this->_username = $importedUser['Nick'];
        $this->_userId = $importedUser['UserId'];
        $this->_realName = $importedUser['RealName'];
        $this->_popName = $importedUser['PopName'];
        $this->_rights = $importedUser['Rights'];
        $this->_noOfEdits = $importedUser['NoOfEdits'];
        $this->_totalTimeOn = $importedUser['TotalTimeOn'];
        $this->_noOfTimesOn = $importedUser['NoOfTimesOn'];
        $this->_password = $importedUser['Password'];
        $this->_dept = $importedUser['Dept'];
        $this->_faculty = $importedUser['Faculty'];
        $this->_created = $importedUser['Created'];
        $this->_lastOn = $importedUser['LastOn'];
        $this->_historyFile = $importedUser['HistoryFile'];
        $this->_BBSNo = $importedUser['BBSNo'];
        $this->_flags = $importedUser['Flags'];
    }

    public function username()
    {
        return $this->_username;
    } 

    public function userId()
    {
        return $this->_userId;
    } 

    public function realName()
    {
        return $this->_realName;
    } 

    public function popName()
    {
        return $this->_popName;
    } 

    public function rights()
    {
        return $this->_rights;
    } 

    public function noOfEdits()
    {
        return $this->_noOfEdits;
    } 

    public function totalTimeOn()
    {
        return $this->_totalTimeOn;
    } 

    public function noOfTimesOn()
    {
        return $this->_noOfTimesOn;
    } 

    public function password()
    {
        return $this->_password;
    } 

    public function dept()
    {
        return $this->_dept;
    } 

    public function faculty()
    {
        return $this->_faculty;
    } 

    public function created()
    {
        return $this->_created;
    } 

    public function lastOn()
    {
        return $this->_lastOn;
    } 

    public function historyFile()
    {
        return $this->_historyFile;
    } 

    public function BBSNo()
    {
        return $this->_BBSNo;
    } 

    public function flags()
    {
        return $this->_flags;
    } 

    public function comments()
    {
        return $this->_comments;
    }

    public function info()
    {
        return $this->_info;
    }
}
