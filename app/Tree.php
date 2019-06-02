<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class Tree extends Model
{
    /**
    * a flat index of all sections and topics
    * @todo add in indication of age of sections and topics
    * 
    * @return array
    */
    public static function tree() 
    {
        $locations = Section::with('topics:id,title,intro,section_id')->orderBy('id', 'DESC')->get(['id','title','intro']);
        $destinations = [];
        
        $keyIndex = 0;
        foreach ($locations as $section) {
            $keyIndex++;
            $destinations[]= [
                'key'   => $keyIndex,
                'id'    => $section['id'],
                'title' => $section['title'],
                'intro' => $section['intro'],
                'is_section' => true,
            ];
            foreach($section['topics'] as $topic) {
                $keyIndex++;
                $destinations[]= [
                    'key'   => $keyIndex,
                    'id'    => $topic['id'],
                    'title' => $topic['title'],
                    'intro' => $topic['intro'],
                    'is_section' => false,
                ];
            }
        }
        
        return $destinations;  
    }
    
    public static function rebuild()
    {
        Log::debug("Rebuilding Tree Cache");

        Cache::forget('tree');
        Cache::rememberForever('tree', function () {
            return Tree::tree();
        });
    }
}
