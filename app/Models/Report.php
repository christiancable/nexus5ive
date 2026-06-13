<?php

namespace App\Models;

use App\Helpers\TopicHelper;
use Database\Factories\ReportFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read Model $reportable
 */
class Report extends Model
{
    /** @use HasFactory<ReportFactory> */
    use HasFactory, SoftDeletes;

    protected $casts = [
        'reported_content_snapshot' => 'json',
    ];

    public const STATUSES = [
        'new' => 'New',
        'under_review' => 'Under Review',
        'closed' => 'Closed',
    ];

    public const REASONS = [
        'spam' => 'Spam',
        'harassment' => 'Harassment',
        'hate_speech' => 'Hate Speech',
        'illegal_content' => 'Illegal Content',
        'other' => 'Other',
    ];

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * the user who reported this - optional
     * as this could be an annoy report
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * the moderator dealing with this report
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * reports still in progress
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNot('status', 'closed');
    }

    /**
     * reports that have been dealt with
     */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', 'closed');
    }

    /**
     * link to content if relevant
     */
    public function getReportableLinkAttribute(): ?string
    {
        if ($this->reportable instanceof Post) {
            return TopicHelper::routeToPost($this->reportable);
        }

        return null;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'new' => 'bg-warning text-dark',
            'under_review' => 'bg-info text-dark',
            'closed' => 'bg-dark',
            default => 'bg-light text-dark',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    public function getReasonLabelAttribute(): string
    {
        return self::REASONS[$this->reason] ?? 'Unknown';
    }

    /**
     * show a preview of the reported content
     *
     * @todo show different previews for the type of report
     */
    public function getSnapshotTextAttribute(): string
    {
        return $this->reported_content_snapshot['text'] ?? 'No content';
    }

    // a report can have many moderation notes
    public function moderationNotes(): HasMany
    {
        return $this->hasMany(ModerationNote::class);
    }
}
