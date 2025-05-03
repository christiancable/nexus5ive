<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    /** @use HasFactory<\Database\Factories\ReportFactory> */
    use HasFactory;

    protected $casts = [
        'reported_content_snapshot' => 'json',
    ];

    public function reportable()
    {
        return $this->morphTo();
    }

    /**
     * the user who reported this - optional
     * as this could be an annoy report
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }


    /**
     * the moderator dealing with this report
     */
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }
}
