<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read Theme $theme
 */
class Mode extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['name', 'welcome', 'theme_id', 'active', 'override'];

    protected $casts = [
        'active' => 'bool',
        'override' => 'bool',
    ];

    /**
     * @return BelongsTo<Theme, $this>
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    /**
     * Scope a query to only include active mode
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}
