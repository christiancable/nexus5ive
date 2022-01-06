<?php

/**
 * Article
 *
 * @author Christian Cable <christiancable@gmail.com>
 *
 * a nexus2 article is a plain text file which may contain comments from users
 * at a particular date and time
 */

namespace App\Nexus2\Models;

class Article
{
    private const DATE_LINE_START       = "\e\x01";
    private const USER_LINE_START       = "\e\x02";
    private const SUBJECT_LINE_START    = "\e\x03";

    private $comments = [];
    private $content = '';
    private $path = null;
    private $preamble = '';

    public function __construct(string $filename = null)
    {
        $this->path = $filename;

        if (null != $this->path) {
            $this->parseFromFile($this->path);
        }
    }


    public function parse(string $string): void
    {
        $this->content = $string;
        $rawPosts = explode(self::DATE_LINE_START, $this->content);

        foreach ($rawPosts as $rawPost) {
            if ($rawPost != '') {
                if ($this->isComment($rawPost)) {
                    $this->comments[] = $this->parsePost($rawPost);
                } else {
                    $this->preamble .= $rawPost;
                }
            }
        }
    }

    /**
     * Does the string appear to be a comment? A very lazy check
     *
     * @param string $rawPost - a string which might
     *                        be a nexus2 comment
     *
     * @return bool
     */
    private function isComment(string $rawPost): bool
    {
        $lines = preg_split('/\R/', $rawPost);
        return  strstr($lines[1], self::USER_LINE_START);
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
            $content = file_get_contents($filename);
        } catch (\Throwable $th) {
            throw new \Exception("Article File Not Found", 1);
        }
        if (false === $content) {
            throw new \Exception("Article File Not Found", 1);
        }
        $this->parse($content);
    }

    /**
     * Parses a string into a username and popname
     *
     * @param string $userline - string to parse
     *
     * @return array username and popname
     */
    public function parseUserandPopname(string $userline): array
    {
        $pattern = '/' . self::USER_LINE_START .
            '(?<popname>.*)(\) )(?<username>.*)$/';
        preg_match_all($pattern, $userline, $matches, PREG_SET_ORDER, 0);

        // do we have the expected matches??
        // dd($matches[0]);
        return [
            'username' => $matches[0]['username'],
            'popname' => $matches[0]['popname'],
        ];
    }

    public function parsePost($rawPost)
    {
        /* post format, note subject line is optional
        \e\x01 date
        \e\x02 popname) username
        \e\x03 subject
        body
        */
        $post = [
            'date'      => '',
            'username'  => '',
            'popname'   => '',
            'subject'   => '',
            'body'      => '',
        ];

        $lines = preg_split('/\R/', $rawPost);

        $post['date'] = trim(ltrim($lines[0], self::DATE_LINE_START));
        unset($lines[0]);

        if (strstr($lines[1], self::USER_LINE_START)) {
            $userandpopname = $this->parseUserandPopname($lines[1]);
            $post['username'] = $userandpopname['username'];
            $post['popname'] = $userandpopname['popname'];
            unset($lines[1]);
        }

        if (strstr($lines[2], self::SUBJECT_LINE_START)) {
            $post['subject'] = trim(ltrim($lines[2], self::SUBJECT_LINE_START));
            unset($lines[2]);
        }

        $post['body'] = trim(implode("\n", $lines));

        return $post;
    }

    /**
     * Getter for comments
     *
     * @return array of Comment arrays
     */
    public function comments()
    {
        return $this->comments;
    }

    /**
     * Getter for the first comment if it exists
     *
     * @return array - first comment in file
     */
    public function first(): ?array
    {
        return $this->comments[0] ?? null;
    }

    /**
     * Getter for date of earliest comment if it exists
     *
     * @return string
     */
    public function date(): ?string
    {
        return $this->first()['date'] ?? null;
    }

    /**
     * Getter for article text which is not part of a comment
     * often used for preamble to text files
     *
     * @return string
     */
    public function preamble(): string
    {
        return $this->preamble;
    }

    /**
     * Getter for the filename of the article if known
     *
     * @return string
     */
    public function path(): ?string
    {
        return $this->path;
    }

    /**
     * the unique key for this article
     * for articles this is the path relative to
     * the bbs root
     *
     * @return string
     */
    public function key(): ?string
    {
        return $this->path();
    }
}
