<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\Theme $theme
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Theme, $this>
     */
    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    /**
     * Scope a query to only include active mode
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
