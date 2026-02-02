<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix timestamp columns that have invalid '0000-00-00 00:00:00' defaults.
     * This is required for MySQL strict mode compatibility.
     */
    public function up(): void
    {
        // Must modify all timestamp columns in each table in a single statement
        // because MySQL validates the entire table when altering any column
        DB::statement('ALTER TABLE `sections`
            MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL');

        DB::statement('ALTER TABLE `topics`
            MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL');

        DB::statement('ALTER TABLE `posts`
            MODIFY `time` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL');

        DB::statement('ALTER TABLE `users`
            MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL');

        DB::statement('ALTER TABLE `comments`
            MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL');

        DB::statement('ALTER TABLE `views`
            MODIFY `latest_view_date` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - the old defaults were invalid
    }
};
