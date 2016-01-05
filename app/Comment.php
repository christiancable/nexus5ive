<?php

namespace Nexus;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id','author_id','text','read'];

    protected $casts = [
        'read' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo('Nexus\User', 'author_id');
    }

    public function user()
    {
        return $this->belongsTo('Nexus\User', 'user_id');
    }
}
