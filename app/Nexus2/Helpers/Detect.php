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

^[#/;].*				{ BEGIN(0);		return IGNOREIT;		}
[Aa][^ \t]*[ \t]*		{ BEGIN(NORMAL); return ARTICLETYPE;	}
[Ff][^ \t]*[ \t]*		{ BEGIN(NORMAL); return FOLDERTYPE;		}
[Cc][^ \t]*[ \t]*		{ BEGIN(NORMAL); return COMMENTTYPE;	}
[Mm][^ \t]*[ \t]*		{ BEGIN(NORMAL); return MCOMMENTTYPE;	}
[Rr][^ \t]*[ \t]*		{ BEGIN(NORMAL); return RUNTYPE;		}
[Ii][^ \t]*[ \t]*		{ BEGIN(NORMAL); return INTERNALTYPE;	}
[Hh][^ \t]*[ \t]*		{ BEGIN(NORMAL); return HEADERTYPE;		}
"."						{ BEGIN(COMMAND);}



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
        $header = substr($file, 0, 2);
        if ("1b01" !== bin2hex($header)) {
            return false;
        }

        if (strlen($file) === 0) {
            return false;
        }

        return true;
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
        $menuItems = [
            // f 0 Y amstrad\amst.mnu * Amstrad section, especially for Amstrads!
            'folder' => "/^f\s*\d*\s[a-z]\s\S*\s\*\s\S.*$/im",
            
            // a 100 100 U uses U Alternative @Uses for your machine!!
            'article' => "/^a\s*\d*\s*\d*\s[a-z]\s*\S*\s\S*\s*\S\s*.*$/im",
            
            // h ***** SPLENDUDSVILLE ******
            'heading' => "/^h\s*\S*.*$/im",

            // . commands - this does not work???
            'dot' => '/^\.\S.*$/im',
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
