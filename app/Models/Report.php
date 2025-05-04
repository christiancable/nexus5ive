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

    public const STATUSES = [
        'pending' => 'Pending',
        'under_review' => 'Under Review',
        'reviewed' => 'Reviewed',
        'dismissed' => 'Dismissed',
        'action_taken' => 'Action Taken',
        'closed' => 'Closed',
    ];

    public const REASONS = [
        'spam' => 'Spam',
        'harassment' => 'Harassment',
        'hate_speech' => 'Hate Speech',
        'illegal_content' => 'Illegal Content',
        'other' => 'Other',
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

    /**
     * reports still in progress
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['pending', 'under_review']);
    }

    /**
     * reports that have been dealt with
     */
    public function scopeClosed($query)
    {
        return $query->whereNotIn('status', ['pending', 'under_review']);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'pending' => 'bg-warning text-dark',
            'under_review' => 'bg-info text-dark',
            'reviewed' => 'bg-primary',
            'dismissed' => 'bg-secondary',
            'action_taken' => 'bg-success',
            'closed' => 'bg-dark',
            default => 'bg-light text-dark',
        };
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    public function getReasonLabelAttribute()
    {
        return self::REASONS[$this->reason] ?? 'Unknown';
    }

    /**
     * show a preview of the reported content
     *
     * @todo show different previews for the type of report
     */
    public function getSnapshotTextAttribute()
    {
        return $this->reported_content_snapshot['text'] ?? 'No content';
    }
}
