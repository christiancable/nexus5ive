<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModerationNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'report_id',
        'user_id',
        'user_name',
        'note',
    ];

    // Relationships
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
