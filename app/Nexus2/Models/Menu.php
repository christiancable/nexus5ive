<?php

namespace App\Nexus2\Models;





class Menu
{
    private $file;

    public $owner = '';
    public $header = '';
    public $menus = [];
    public $articles = [];
    public $comments = [];
    public $path = '';
    
    function __construct(string $file, string $path) {
        $this->hydrate($file);
        $this->path = $path;
    }

    public function parseMenu($file)
    {
        
        return [
            'owner' => $this->parseOwnerLine($file),
            'header' => $this->parseHeaderLine($file),
            'articles' => $this->parseArticleLines($file),
            'menus' => $this->parseMenuLines($file),
            'comments' => [],
        ];
    }

    /**
     * @see https://regex101.com/r/MpXoKW/1
     */public function parseArticleLines($file): array
    {
        $re = '/^a\s+(?<read>\d+)\s+(?<write>\d+)\s+\w\s(?<file>\w+)\s+(\S)\s+(?<name>.+)$/ismU';
        preg_match_all($re, $file, $matches, PREG_SET_ORDER, 0);
        $articleLines = [];
        foreach ($matches as $articleLine) {
            $articleLines[] = [
                'read' => $articleLine['read'],
                'write' => $articleLine['write'],
                'file' => $articleLine['file'],
                'name'=> trim($articleLine['name']),
            ];
        }
    
        return $articleLines;
    }
    
    /**
     * @see https://regex101.com/r/tmTBBm/1 
     */public function parseMenuLines($file): array
    {
        $re = '/^f\s+(?<read>\d+)\s+\S\s+(?<file>\S+)\s+(\S)\s+(?<name>.+)$/ismU';
        preg_match_all($re, $file, $matches, PREG_SET_ORDER, 0);
        $menuLines = [];
        foreach ($matches as $menuLine) {
            $menuLines[] = [
                'read' => $menuLine['read'],
                'file' => $menuLine['file'],
                'name'=> trim($menuLine['name']),
            ];
        }
        return $menuLines;
    }
    
    public function parseHeaderLine(): string
    {
        return '';

    }

    public function parseOwnerLine(): string
    {
        return '';
    }


    /**
     * hydrate
     *
     * @param string $file
     */
    public function hydrate(string $file)
    {
        $importedMenu =  $this->parseMenu($file);

        $this->menus = $importedMenu['menus'];
        $this->articles = $importedMenu['articles'];

        
        /*
        $this->username    = $importedUser['Nick'];
        $this->name        = $importedUser['RealName'];
        $this->popname     = $importedUser['PopName'];
        $this->email       = $importedUser['UserId'] . '@nexus2.imported';
        $this->totalVisits = $importedUser['NoOfTimesOn'];
        $this->totalPosts  = $importedUser['NoOfEdits'];
        $this->created_at  = $created;
        $this->latestLogin = $lastOn;
        */
    }
}
