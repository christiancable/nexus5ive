<?php

namespace App\Nexus2\Models;

use App\Nexus2\Helpers\Key;

class Menu
{
    // phpcs:disable Generic.Files.LineLength
    private const ARTICLE_PATTERN   = '/^a\s+(?<read>\d+)\s+(?<write>\d+)\s+(?<shortcut>\w)\s(?<file>\w+)\s+\S\s+(?<name>.+)$/ismU';

    private const MENU_PATTERN      = '/^f\s+(?<read>\d+)\s+(?<shortcut>\S)\s+(?<file>\S+)\s+(\S)\s+(?<name>.+)$/ismU';
    private const OWNER_PATTERN     = '/^.owner\s+(?<owner>.*)$/ismU';
    private const TITLE_PATTERN     = '/^.title\s+(?<file>.*)$/ismU';
    private const HEADING_PATTERN   = '/^H\s+(?<heading>.*)$/ismU';
    private const MESSAGE_PATTERN   = '/^M\s+(?<read>\d+)((\s+)(?<message>.*))?$/ismU';
    private const DOT_PATTERN       = '/^(?<command>\.\w+)((\s)(?<operand>.*))?$/ismU';
    private const COMMENT_PATTERN   = '/^#(?<comment>.*)?$/ismU';
    // phpcs:enable 

    private $content;
    private $path;

    // debug
    private $raw = [];
    private $ownerlines = [];

    private $dotlines = [];
    private $messagelines = [];
    private $commentlines = [];
    private $articles = [];
    private $menus = [];
    private $heading = '';
    private $owners = [];
    private $title = '';
    private $bbsroot = null;

    public function __construct(string $filename = null, string $bbsroot = null)
    {
        $this->path = $filename;

        if (null !== $bbsroot) {
            $this->bbsroot = $bbsroot;
        }

        if (null !== $this->path) {
            $this->parseFromFile($this->path);
        }
    }

    /**
     * Parses the file into an article
     *
     * @param string $filename - path to a file
     *
     * @return void
     */
    private function parseFromFile(string $filename): void
    {
        try {
            $this->content = file_get_contents($filename);
        } catch (\Throwable $th) {
            throw new \Exception("Menu File Not Found", 1);
        }
        if (false === $this->content) {
            throw new \Exception("Menu File Not Found", 1);
        }

        $this->parse($this->content);
    }

    private function parse(string $rawMenu)
    {
        /*
        foreach line
            sniff what sort of line it is
            parse line
        */

        $lines = preg_split('/\R/', $rawMenu);
        foreach ($lines as $line) {
            $type = $this->detectType($line);

            switch ($type) {
                case 'owner': // .owner <username> <username> <username>
                    $this->owners = $this->parseOwnerLine($line);
                    break;

                case 'heading': // H <heading>
                    $this->heading = $this->parseHeadingLine($line);
                    break;

                case 'title': // .title <filename>  - inline text for ascii art
                    $this->title = $this->parseTitleLine($line);
                    break;

                case 'article': // a <read_level> <write_level> <menu_key> <filename> * <title>
                    $this->articles[] = $this->parseArticleLine($line);
                    break;

                case 'message': // m <read_level> <optional text>
                    // @todo parse
                    $this->messagelines[] = $line;
                    break;

                case 'menu': // f <read_level> <menu_key> <filename> * <title>
                    $this->menus[] = $this->parseMenuLine($line);
                    break;

                case 'dotcommand': // .* unsupported dot comments (.if .quit .endif)
                    // @todo parse?
                    $this->dotlines[] = $line;
                    break;

                case 'comment': // #
                    // @todo parse?
                    $this->commentlines[] = $line;
                    break;

                default:
                    // currently unknown
                    $this->raw[] = $line;
                    // code...
                    break;
            }
        }
    }

    /**
     * DetectType
     * detects they type of a given line from a menu
     *
     * @param string $string - a line of text
     *
     * @return string
     */
    public function detectType(string $string): string
    {
        // article
        $result = preg_match(self::ARTICLE_PATTERN, $string);
        if (1 ===  $result) {
            return 'article';
        }

        // menu
        $result = preg_match(self::MENU_PATTERN, $string);
        if (1 ===  $result) {
            return 'menu';
        }

        // owner line
        $result = preg_match(self::OWNER_PATTERN, $string);
        if (1 ===  $result) {
            return 'owner';
        }

        // heading
        $result = preg_match(self::HEADING_PATTERN, $string);
        if (1 ===  $result) {
            return 'heading';
        }

        // title
        $result = preg_match(self::TITLE_PATTERN, $string);
        if (1 ===  $result) {
            return 'title';
        }

        // message
        $result = preg_match(self::MESSAGE_PATTERN, $string);
        if (1 ===  $result) {
            return 'message';
        }

        // dotcommand
        $result = preg_match(self::DOT_PATTERN, $string);
        if (1 ===  $result) {
            return 'dotcommand';
        }

        // comments
        $result = preg_match(self::COMMENT_PATTERN, $string);
        if (1 ===  $result) {
            return 'comment';
        }

        return 'unknown';
    }

    public function articles()
    {
        return $this->articles;
    }

    public function menus()
    {
        return $this->menus;
    }

    public function heading()
    {
        return $this->heading;
    }

    public function owners()
    {
        return $this->owners;
    }

    public function title()
    {
        try {
            $title = file_get_contents(Key::getKeyForFile($this->title, $this->path, $this->bbsroot));
        } catch (\Throwable $th) {
            $title = '';
        }
        return $title;
    }

    /**
     * ParseArticleLine
     * parse a menu line with an article link
     *
     * @see    https://regex101.com/r/MpXoKW/1
     * @param  mixed $line
     * @return array
     */
    public function parseArticleLine($line): ?array
    {
        $result = preg_match(self::ARTICLE_PATTERN, $line, $matches);
        if (1 !=  $result) {
            return null;
        }
        return [
            'file'      => Key::getKeyForFile($matches['file'], $this->path, $this->bbsroot),
            'name'      => $matches['name'],
            'write'     => $matches['write'],
            'read'      => $matches['read'],
            'shortcut'  => $matches['shortcut'],
        ];
    }

    /**
     * ParseMenuLine
     * parse a menu line with an menu link
     *
     * @see    https://regex101.com/r/tmTBBm/1
     * @param  string $line
     * @return array
     */
    public function parseMenuLine($line): ?array
    {
        $result = preg_match(self::MENU_PATTERN, $line, $matches);
        if (1 !=  $result) {
            return null;
        }

        return [
            'file'      => Key::getKeyForFile($matches['file'], $this->path, $this->bbsroot),
            'name'      => $matches['name'],
            'read'      => $matches['read'],
            'shortcut'  => $matches['shortcut'],
        ];
    }




    public function parseHeadingLine(string $line): string
    {
        $result = preg_match(self::HEADING_PATTERN, $line, $matches);
        if (1 !=  $result) {
            return '';
        }

        return $matches['heading'];
    }

    public function parseOwnerLine(string $line): array
    {
        $result = preg_match(self::OWNER_PATTERN, $line, $matches);
        if (1 !=  $result) {
            return [];
        }

        return explode(' ', $matches['owner']);
    }

    public function parseTitleLine(string $line): string
    {
        $result = preg_match(self::TITLE_PATTERN, $line, $matches);
        if (1 !=  $result) {
            return '';
        }

        //@todo get the content of $matches['file']
        return $matches['file'];
    }

    /**
     * unique key for this menu
     * for menus the unique key is the path of the file
     * relative to the bbs root
     *
     * @return string
     */
    public function key(): string
    {
        return $this->path;
    }

    public function debug()
    {
        return $this->content;
    }

    public function raw()
    {
        return [
        'raw'           => $this->raw,
        'dotlines'      => $this->dotlines,
        'commentlines'  => $this->commentlines,
        'messagelines'  => $this->messagelines,
        ];
    }
}
