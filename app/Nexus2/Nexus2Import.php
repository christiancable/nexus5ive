<?php

namespace App\Nexus2;

use Illuminate\Database\Eloquent\Model;

class Nexus2Import extends Model
{
    protected $table = 'nexus2_imports';

    protected $fillable = ['type', 'legacy_key', 'model_id'];

    public static function exists(string $type, string $legacyKey): bool
    {
        return static::where('type', $type)->where('legacy_key', $legacyKey)->exists();
    }

    public static function modelId(string $type, string $legacyKey): ?int
    {
        return static::where('type', $type)->where('legacy_key', $legacyKey)->value('model_id');
    }

    public static function track(string $type, string $legacyKey, int $modelId): void
    {
        static::create([
            'type' => $type,
            'legacy_key' => $legacyKey,
            'model_id' => $modelId,
        ]);
    }
}
