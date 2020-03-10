<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Tree
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tree newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tree newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tree query()
 * @mixin \Eloquent
 */
class Tree extends Model
{
    /**
     * a flat index of all sections and topics
     *
     * @todo add in indication of age of sections and topics
     *
     * @return array
     */
    public static function tree()
    {
        $locations = Section::with('topics:id,title,intro,section_id')->orderBy('title')->get(['id','title','intro']);
        $destinations = [];
        $oldenDays = now()->subMonths(12);
        $keyIndex = 0;
        foreach ($locations as $section) {
            $keyIndex++;
            if ($section['most_recent_post']
                && $section['most_recent_post']->updated_at->greaterThanOrEqualTo($oldenDays)
            ) {
                $recent = true;
            } else {
                $recent = false;
            }
            $destinations[]= [
                'key'   => $keyIndex,
                'id'    => $section['id'],
                'title' => $section['title'],
                'intro' => $section['intro'],
                'is_section' => true,
                'is_recent' => $recent
            ];
            foreach ($section['topics'] as $topic) {
                $keyIndex++;
                if ($topic['most_recent_post_time']
                    && $topic['most_recent_post_time']->greaterThanOrEqualTo($oldenDays)
                ) {
                    $recent = true;
                } else {
                    $recent = false;
                }
                
                $destinations[]= [
                    'key'   => $keyIndex,
                    'id'    => $topic['id'],
                    'title' => $topic['title'],
                    'intro' => $topic['intro'],
                    'is_section' => false,
                    'is_recent' => $recent
                ];
            }
        }
        
        return $destinations;
    }
    
    public static function rebuild()
    {
        Log::debug("Rebuilding Tree Cache");
        
        Cache::forget('tree');
        Cache::rememberForever(
            'tree',
            function () {
                return Tree::tree();
            }
        );
    }
}
