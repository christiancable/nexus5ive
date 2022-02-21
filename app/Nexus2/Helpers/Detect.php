<?php

namespace App\Nexus2\Helpers;

/*


A = article
F = folder - link to another menu
C = comment - ???
M = message on the screen - just text
R = run - runs a dos command
I
H
. = command - a command to the nexus parser

^[#/;].*                { BEGIN(0);     return IGNOREIT;        }
[Aa][^ \t]*[ \t]*       { BEGIN(NORMAL); return ARTICLETYPE;    }
[Ff][^ \t]*[ \t]*       { BEGIN(NORMAL); return FOLDERTYPE;     }
[Cc][^ \t]*[ \t]*       { BEGIN(NORMAL); return COMMENTTYPE;    }
[Mm][^ \t]*[ \t]*       { BEGIN(NORMAL); return MCOMMENTTYPE;   }
[Rr][^ \t]*[ \t]*       { BEGIN(NORMAL); return RUNTYPE;        }
[Ii][^ \t]*[ \t]*       { BEGIN(NORMAL); return INTERNALTYPE;   }
[Hh][^ \t]*[ \t]*       { BEGIN(NORMAL); return HEADERTYPE;     }
"."                     { BEGIN(COMMAND);}



*/
class Detect
{

    public static function sniff(string $file): string
    {
        if (self::isArticle($file)) {
            return 'article';
        }

        if (self::isMenu($file)) {
            return 'menu';
        }


        return 'unknown';
    }


   /**
    * isArticle
    * is $file a nexus2 conversation file
    * @return bool
    */
    public static function isArticle(string $file): bool
    {
        if (strlen($file) === 0) {
            return false;
        }

        // most articles start off with a post
        $header = substr($file, 0, 2);
        if ("1b01" == bin2hex($header)) {
            return true;
        }

        // some articles have ascii at the top so check the rest of the file
        // for what looks like a post
        if (preg_match('/^\x1b\x01/im', $file)) {
            return true;
        }

        return false;
    }


   /**
    * isMenu
    * is $file a nexus2 menu?

    * @param string $file
    * @return void
    */
    public static function isMenu(string $file): bool
    {

        // c 0
        $comment = "";

        // m 0
        $message = "";

        // a lazy list of patterns for menu lines
        // menus must link to an article or another menu otherwise they are just not a menu
        $menuItems = [
            // f 0 Y amstrad\amst.mnu * Amstrad section, especially for Amstrads!
            'folder' => "/^f\s*\d*\s[a-z]\s\S*\s\*\s\S.*$/im",

            // a 100 100 U uses U Alternative @Uses for your machine!!
            'artice' => '/^a\s(?<read>\d*)\s(?<write>\d*)\s\w\s(?<file>\w*)\s(.)\s(?<name>.*)$/ismU',

            // h ***** SPLENDUDSVILLE ******
            // 'heading' => "/^h\s*\S*.*$/im",

            // . commands - this does not work???
            // 'dot' => '/^\.\S.*$/im',
        ];

        // .owner dummy vevaphon the_dud nightcrawler seventhson seeker
        $command = "";

        if (self::isArticle($file)) {
            return false;
        }

        if (strlen($file) === 0) {
            return false;
        }

        foreach ($menuItems as $type => $pattern) {
            if (preg_match($pattern, $file)) {
                return true;
            }
        }

        return false;
    }
}
