<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class View extends Model
{
    use SoftDeletes;

    protected $casts = [
        'unsubscribed' => 'boolean',
        'latest_view_date' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Topic, $this>
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Scope a query to only include views to subscribed topics.
     */
    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->where('unsubscribed', false);
    }
}
