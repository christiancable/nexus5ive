<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Theme;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // for each theme which has a path starting with 'https://bootswatch.com/4/' replace 'https://bootswatch.com/4/' with 'https://bootswatch.com/5/'
        Theme::where('path', 'like', 'https://bootswatch.com/4/%')->get()->each(function ($theme) {
            $theme->update([
                'path' => str_replace('https://bootswatch.com/4/', 'https://bootswatch.com/5/', $theme->path),
            ]);
            $theme->save();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Theme::where('path', 'like', 'https://bootswatch.com/5/%')->get()->each(function ($theme) {
            $theme->update([
                'path' => str_replace('https://bootswatch.com/5/', 'https://bootswatch.com/4/', $theme->path),
            ]);
            $theme->save();
        });
    }
};
