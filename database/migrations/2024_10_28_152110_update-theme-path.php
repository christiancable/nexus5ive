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
        $themes = Theme::all();

        // vite requires new path for homegrown themes
        foreach ($themes as $theme) {
            if (strpos($theme->path, '/css') === 0) {
                $newPath = str_replace('/css', 'resources/sass', $theme->path);
                $newPath = preg_replace('/\.css$/', '.scss', $newPath);
                
                // Update the theme's path
                $theme->path = $newPath;
                $theme->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
