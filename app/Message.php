<?php

namespace Nexus;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['user_id','author_id','text','read'];

    protected $casts = [
        'read' => 'boolean',
    ];

    protected $dates = ['time'];

    public function author()
    {
        return $this->belongsTo('Nexus\User', 'author_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('Nexus\User');
    }
}
